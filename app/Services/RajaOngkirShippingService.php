<?php

namespace App\Services;

use App\Models\CartItem;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Laravolt\Indonesia\Models\City;
use Laravolt\Indonesia\Models\District;
use Laravolt\Indonesia\Models\Province;

class RajaOngkirShippingService
{
    private const MAX_SHIPMENT_WEIGHT_GRAMS = 50_000_000;

    /**
     * @var array<string, string>
     */
    private const COURIERS = [
        'jne' => 'JNE',
        'jnt' => 'J&T',
        'tiki' => 'TIKI',
        'pos' => 'POS Indonesia',
        'sicepat' => 'SiCepat',
        'anteraja' => 'AnterAja',
    ];

    /**
     * @param  Collection<int, CartItem>  $cartItems
     * @param  array{province_code: string, city_code: string, district_code: string, village_code: string, postal_code: string}  $region
     * @return array{quote_id: string, options: list<array{option_id: string, code: string, name: string, service: string, description: string, cost: int, etd: string}>}
     */
    public function createQuote(User $user, Collection $cartItems, array $region): array
    {
        if ($cartItems->isEmpty()) {
            throw ValidationException::withMessages([
                'cart' => 'Keranjang belanja masih kosong.',
            ]);
        }

        $weightInGrams = $this->weightInGrams($cartItems);
        $originCityId = $this->findOriginCityId();
        $destinationId = $this->findDestinationId($region);
        $options = $this->calculateOptions($originCityId, $destinationId, $weightInGrams);

        if ($options === []) {
            throw ValidationException::withMessages([
                'shipping' => 'Tidak ada layanan pengiriman yang tersedia untuk alamat ini.',
            ]);
        }

        $quoteId = (string) Str::uuid();
        $locationFingerprint = $this->locationFingerprint($region);
        $cartFingerprint = $this->cartFingerprint($cartItems);

        Cache::put($this->cacheKey($quoteId), [
            'user_id' => $user->id,
            'cart_fingerprint' => $cartFingerprint,
            'location_fingerprint' => $locationFingerprint,
            'options' => $options,
        ], now()->addMinutes(15));

        return [
            'quote_id' => $quoteId,
            'options' => array_values($options),
        ];
    }

    /**
     * @param  Collection<int, CartItem>  $cartItems
     * @param  array{district_code: string, village_code: string, postal_code: string}  $region
     * @return array{code: string, name: string, service: string, description: string, cost: int, etd: string}
     */
    public function resolveOption(
        User $user,
        Collection $cartItems,
        array $region,
        string $quoteId,
        string $optionId,
    ): array {
        $quote = Cache::get($this->cacheKey($quoteId));

        if (! is_array($quote)
            || ($quote['user_id'] ?? null) !== $user->id
            || ($quote['cart_fingerprint'] ?? null) !== $this->cartFingerprint($cartItems)
            || ($quote['location_fingerprint'] ?? null) !== $this->locationFingerprint($region)) {
            throw ValidationException::withMessages([
                'shipping' => 'Kutipan ongkir sudah tidak berlaku. Silakan hitung ulang ongkir.',
            ]);
        }

        $options = $quote['options'] ?? null;
        $option = is_array($options) ? ($options[$optionId] ?? null) : null;

        if (! is_array($option)) {
            throw ValidationException::withMessages([
                'shipping' => 'Layanan pengiriman tidak valid. Silakan hitung ulang ongkir.',
            ]);
        }

        return $option;
    }

    /**
     * @param  Collection<int, CartItem>  $cartItems
     */
    private function weightInGrams(Collection $cartItems): int
    {
        $weightInGrams = 0;

        foreach ($cartItems as $cartItem) {
            $productWeight = (int) $cartItem->product->weight_grams;
            $quantity = (int) $cartItem->quantity;

            if ($productWeight < 1
                || $quantity < 1
                || $productWeight > intdiv(self::MAX_SHIPMENT_WEIGHT_GRAMS - $weightInGrams, $quantity)) {
                throw ValidationException::withMessages([
                    'shipping' => 'Berat total pesanan melebihi batas pengiriman yang dapat dihitung.',
                ]);
            }

            $weightInGrams += $productWeight * $quantity;
        }

        return $weightInGrams;
    }

    private function findOriginCityId(): int
    {
        return Cache::remember('rajaongkir-origin-city:makassar-sulawesi-selatan', now()->addDay(), function (): int {
            $provinces = $this->getLocationData('/destination/province', 'province lookup');
            $provinceId = null;

            foreach ($provinces as $province) {
                if (! is_array($province)
                    || $this->normalizeRegionName((string) ($province['name'] ?? $province['province_name'] ?? '')) !== 'SULAWESI SELATAN') {
                    continue;
                }

                $provinceId = $this->numericLocationId($province['id'] ?? null);
                break;
            }

            if ($provinceId === null) {
                Log::warning('RajaOngkir origin province was not found.', [
                    'province' => 'Sulawesi Selatan',
                ]);

                throw new RajaOngkirException('Provinsi Sulawesi Selatan tidak ditemukan pada data RajaOngkir.');
            }

            $cities = $this->getLocationData('/destination/city/'.$provinceId, 'origin city lookup');

            foreach ($cities as $city) {
                if (! is_array($city)
                    || $this->normalizeRegionName((string) ($city['city_name'] ?? $city['name'] ?? '')) !== 'MAKASSAR') {
                    continue;
                }

                $cityId = $this->numericLocationId($city['id'] ?? null);

                if ($cityId !== null) {
                    return $cityId;
                }
            }

            Log::warning('RajaOngkir origin city was not found.', [
                'province' => 'Sulawesi Selatan',
                'city' => 'Makassar',
            ]);

            throw new RajaOngkirException('Kota Makassar tidak ditemukan pada data RajaOngkir.');
        });
    }

    /**
     * @param  array{province_code: string, city_code: string, district_code: string, village_code: string, postal_code: string}  $region
     */
    private function findDestinationId(array $region): int
    {
        $apiKey = config('services.rajaongkir.api_key');

        if (! is_string($apiKey) || $apiKey === '') {
            throw new RajaOngkirException('RajaOngkir belum dikonfigurasi. Isi RAJAONGKIR_API_KEY di environment aplikasi.');
        }

        $province = Province::query()->where('code', $region['province_code'])->firstOrFail();
        $city = City::query()->where('code', $region['city_code'])->firstOrFail();
        $district = District::query()->where('code', $region['district_code'])->firstOrFail();

        try {
            $response = $this->request()->get('/destination/domestic-destination', [
                'search' => $district->name,
                'limit' => 50,
                'offset' => 0,
            ])->throw();
        } catch (ConnectionException|RequestException $exception) {
            Log::warning('RajaOngkir destination lookup failed.', [
                'http_status' => $exception instanceof RequestException ? $exception->response->status() : null,
                'exception' => $exception::class,
            ]);

            throw new RajaOngkirException('Layanan ongkir sedang tidak tersedia. Coba lagi.', previous: $exception);
        }

        $data = $this->responseData($response, 'destination lookup');
        $provinceName = $this->normalizeRegionName($province->name);
        $cityName = $this->normalizeRegionName($city->name);
        $districtName = $this->normalizeRegionName($district->name);

        foreach ($data as $destination) {
            if (! is_array($destination)) {
                continue;
            }

            $destinationId = $destination['id'] ?? null;
            $destinationProvince = $this->normalizeRegionName((string) ($destination['province_name'] ?? ''));
            $destinationCity = $this->normalizeRegionName((string) ($destination['city_name'] ?? ''));
            $destinationPostalCode = (string) ($destination['zip_code'] ?? '');
            $destinationAreas = [
                $this->normalizeRegionName((string) ($destination['district_name'] ?? '')),
                $this->normalizeRegionName((string) ($destination['subdistrict_name'] ?? '')),
            ];

            if ((is_int($destinationId) || (is_string($destinationId) && ctype_digit($destinationId)))
                && (int) $destinationId > 0
                && $destinationProvince === $provinceName
                && $destinationCity === $cityName
                && in_array($districtName, $destinationAreas, true)
                && $destinationPostalCode === $region['postal_code']) {
                return (int) $destinationId;
            }
        }

        throw ValidationException::withMessages([
            'postal_code' => 'Alamat tidak cocok dengan lokasi pengiriman RajaOngkir. Periksa kecamatan dan kode pos.',
        ]);
    }

    /**
     * @return array<string, array{option_id: string, code: string, name: string, service: string, description: string, cost: int, etd: string}>
     */
    private function calculateOptions(int $originCityId, int $destinationId, int $weightInGrams): array
    {
        try {
            $response = $this->request()
                ->asForm()
                ->post('/calculate/domestic-cost', [
                    'origin' => $originCityId,
                    'originType' => 'city',
                    'destination' => $destinationId,
                    'destinationType' => 'subdistrict',
                    'weight' => $weightInGrams,
                    'courier' => implode(':', array_keys(self::COURIERS)),
                ])
                ->throw();
        } catch (ConnectionException|RequestException $exception) {
            Log::warning('RajaOngkir shipping calculation failed.', [
                'http_status' => $exception instanceof RequestException ? $exception->response->status() : null,
                'exception' => $exception::class,
            ]);

            throw new RajaOngkirException('Layanan ongkir sedang tidak tersedia. Coba lagi.', previous: $exception);
        }

        $data = $this->responseData($response, 'shipping calculation');
        $options = [];

        foreach ($data as $index => $service) {
            if (! is_array($service)) {
                continue;
            }

            $code = strtolower((string) ($service['code'] ?? ''));
            $cost = $service['cost'] ?? null;
            $name = self::COURIERS[$code] ?? null;
            $serviceName = trim((string) ($service['service'] ?? ''));

            if ($name === null || ! is_numeric($cost) || (int) $cost < 0 || $serviceName === '') {
                continue;
            }

            $optionId = hash('sha256', implode('|', [
                $code,
                $serviceName,
                (string) $cost,
                (string) $index,
            ]));

            $options[$optionId] = [
                'option_id' => $optionId,
                'code' => $code,
                'name' => $name,
                'service' => $serviceName,
                'description' => (string) ($service['description'] ?? ''),
                'cost' => (int) $cost,
                'etd' => (string) ($service['etd'] ?? ''),
            ];
        }

        return $options;
    }

    private function request(): PendingRequest
    {
        $apiKey = config('services.rajaongkir.api_key');

        if (! is_string($apiKey) || $apiKey === '') {
            throw new RajaOngkirException('RajaOngkir belum dikonfigurasi. Isi RAJAONGKIR_API_KEY di environment aplikasi.');
        }

        return Http::baseUrl(config('services.rajaongkir.base_url'))
            ->withHeaders(['key' => $apiKey])
            ->acceptJson()
            ->connectTimeout(3)
            ->timeout(12);
    }

    /**
     * @return array<mixed>
     */
    private function getLocationData(string $path, string $operation): array
    {
        try {
            $response = $this->request()->get($path)->throw();
        } catch (ConnectionException|RequestException $exception) {
            Log::warning('RajaOngkir location lookup failed.', [
                'operation' => $operation,
                'http_status' => $exception instanceof RequestException ? $exception->response->status() : null,
                'exception' => $exception::class,
            ]);

            throw new RajaOngkirException('Layanan ongkir sedang tidak tersedia. Coba lagi.', previous: $exception);
        }

        return $this->responseData($response, $operation);
    }

    /**
     * @return array<mixed>
     */
    private function responseData(Response $response, string $operation): array
    {
        $metaCode = $response->json('meta.code');
        $data = $response->json('data');

        if (($metaCode !== null && (string) $metaCode !== '200') || ! is_array($data)) {
            Log::warning('RajaOngkir returned an invalid response.', [
                'operation' => $operation,
                'http_status' => $response->status(),
            ]);

            throw new RajaOngkirException('Layanan ongkir sedang tidak tersedia. Coba lagi.');
        }

        return $data;
    }

    private function normalizeRegionName(string $name): string
    {
        $normalized = mb_strtoupper(trim($name));
        $normalized = preg_replace('/^(KOTA ADMINISTRASI|KOTA|KABUPATEN ADMINISTRASI|KABUPATEN|KAB\\.)\\s+/u', '', $normalized) ?? $normalized;

        return preg_replace('/\\s+/u', ' ', $normalized) ?? $normalized;
    }

    private function numericLocationId(mixed $id): ?int
    {
        if ((! is_int($id) && (! is_string($id) || ! ctype_digit($id))) || (int) $id < 1) {
            return null;
        }

        return (int) $id;
    }

    /**
     * @param  Collection<int, CartItem>  $cartItems
     */
    private function cartFingerprint(Collection $cartItems): string
    {
        $snapshot = $cartItems
            ->map(fn (CartItem $cartItem): array => [
                'product_id' => (int) $cartItem->product_id,
                'quantity' => (int) $cartItem->quantity,
                'weight_grams' => (int) $cartItem->product->weight_grams,
            ])
            ->sortBy('product_id')
            ->values()
            ->all();

        return hash('sha256', json_encode($snapshot, JSON_THROW_ON_ERROR));
    }

    /**
     * @param  array<string, string>  $region
     */
    private function locationFingerprint(array $region): string
    {
        return hash('sha256', implode('|', [
            $region['district_code'],
            $region['village_code'],
            $region['postal_code'],
        ]));
    }

    private function cacheKey(string $quoteId): string
    {
        return 'rajaongkir-shipping-quote:'.$quoteId;
    }
}
