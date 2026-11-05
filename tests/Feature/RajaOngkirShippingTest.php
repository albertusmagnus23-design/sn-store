<?php

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Client\Request as ClientRequest;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Laravolt\Indonesia\Models\City;
use Laravolt\Indonesia\Models\District;
use Laravolt\Indonesia\Models\Province;
use Laravolt\Indonesia\Models\Village;

function seedRajaOngkirShippingRegions(): array
{
    $province = Province::query()->create(['code' => '91', 'name' => 'PROVINSI UJI']);
    $city = City::query()->create(['code' => '9101', 'province_code' => $province->code, 'name' => 'KOTA UJI']);
    $district = District::query()->create(['code' => '9101001', 'city_code' => $city->code, 'name' => 'KECAMATAN UJI']);
    $village = Village::query()->create(['code' => '9101001001', 'district_code' => $district->code, 'name' => 'KELURAHAN UJI']);

    return [
        'province_code' => $province->code,
        'city_code' => $city->code,
        'district_code' => $district->code,
        'village_code' => $village->code,
        'postal_code' => '12345',
    ];
}

function fakeRajaOngkirShippingResponses(): void
{
    Cache::forget('rajaongkir-origin-city:makassar-sulawesi-selatan');
    Http::preventStrayRequests();
    Http::fake([
        'rajaongkir.komerce.id/api/v1/destination/province' => Http::response([
            'data' => [[
                'id' => 28,
                'name' => 'Sulawesi Selatan',
            ]],
        ]),
        'rajaongkir.komerce.id/api/v1/destination/city/28' => Http::response([
            'data' => [[
                'id' => 4321,
                'city_name' => 'Makassar',
            ]],
        ]),
        'rajaongkir.komerce.id/api/v1/destination/domestic-destination*' => Http::response([
            'data' => [[
                'id' => 7788,
                'subdistrict_name' => 'KECAMATAN UJI',
                'district_name' => 'KOTA UJI',
                'city_name' => 'KOTA UJI',
                'province_name' => 'PROVINSI UJI',
                'zip_code' => '12345',
            ]],
        ]),
        'rajaongkir.komerce.id/api/v1/calculate/domestic-cost' => Http::response([
            'data' => [
                [
                    'name' => 'JNE',
                    'code' => 'jne',
                    'service' => 'REG',
                    'description' => 'Layanan reguler',
                    'cost' => 15000,
                    'etd' => '2-3',
                ],
                [
                    'name' => 'Unknown Carrier',
                    'code' => 'unknown',
                    'service' => 'REG',
                    'description' => 'Layanan tidak dikenal',
                    'cost' => 1000,
                    'etd' => '1',
                ],
            ],
        ]),
    ]);
}

function getRajaOngkirQuote(object $test, User $user, array $regions): array
{
    config([
        'services.rajaongkir.api_key' => 'test-rajaongkir-key',
    ]);
    fakeRajaOngkirShippingResponses();

    return $test->actingAs($user)
        ->postJson(route('checkout.shipping-options'), $regions)
        ->assertOk()
        ->json();
}

beforeEach(function () {
    config([
        'services.rajaongkir.api_key' => 'test-rajaongkir-key',
    ]);
});

test('guests cannot request shipping quotes', function () {
    Http::preventStrayRequests();
    Http::fake();

    $this->postJson(route('checkout.shipping-options'), seedRajaOngkirShippingRegions())
        ->assertUnauthorized();

    Http::assertNothingSent();
});

test('shipping quotes use product weight, Makassar origin, and supported couriers', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create([
        'weight_grams' => 750,
        'stock' => 5,
    ]);
    $user->cartItems()->create([
        'product_id' => $product->id,
        'quantity' => 2,
    ]);
    $regions = seedRajaOngkirShippingRegions();
    fakeRajaOngkirShippingResponses();

    $response = $this->actingAs($user)
        ->postJson(route('checkout.shipping-options'), $regions);

    $response->assertOk()
        ->assertJsonStructure([
            'quote_id',
            'options' => [[
                'option_id',
                'code',
                'name',
                'service',
                'description',
                'cost',
                'etd',
            ]],
        ])
        ->assertJsonPath('options.0.code', 'jne')
        ->assertJsonPath('options.0.cost', 15000);

    Http::assertSent(fn (ClientRequest $request): bool => str_contains($request->url(), '/destination/domestic-destination')
        && str_contains($request->url(), 'search=KECAMATAN%20UJI'));
    Http::assertSent(fn (ClientRequest $request): bool => str_contains($request->url(), '/destination/province'));
    Http::assertSent(fn (ClientRequest $request): bool => str_contains($request->url(), '/destination/city/28'));
    Http::assertSent(fn (ClientRequest $request): bool => str_contains($request->url(), '/calculate/domestic-cost')
        && (string) $request['origin'] === '4321'
        && $request['originType'] === 'city'
        && (int) $request['destination'] === 7788
        && $request['destinationType'] === 'subdistrict'
        && (int) $request['weight'] === 1500
        && $request['courier'] === 'jne:jnt:tiki:pos:sicepat:anteraja');
});

test('checkout saves the selected provider quote as shipping fee and includes it in order total', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create([
        'price' => '10000.00',
        'weight_grams' => 500,
        'stock' => 3,
    ]);
    $user->cartItems()->create([
        'product_id' => $product->id,
        'quantity' => 2,
    ]);
    $regions = seedRajaOngkirShippingRegions();
    $quote = getRajaOngkirQuote($this, $user, $regions);

    $response = $this->actingAs($user)
        ->post(route('checkout.store'), [
            'name' => $user->name,
            'phone' => '+628123456789',
            'email' => $user->email,
            'street_address' => 'Alamat pembeli',
            ...$regions,
            'shipping_quote_id' => $quote['quote_id'],
            'shipping_option' => $quote['options'][0]['option_id'],
        ]);

    $order = Order::query()->firstOrFail();
    $response->assertRedirect(route('orders.show', $order));
    $this->assertDatabaseHas('orders', [
        'id' => $order->id,
        'shipping_fee' => 15000,
        'shipping_courier' => 'JNE',
        'shipping_service' => 'REG',
        'total' => '35000.00',
    ]);
    $this->assertDatabaseHas('products', [
        'id' => $product->id,
        'stock' => 3,
        'reserved_stock' => 2,
    ]);
});

test('checkout rejects a quote issued to another user', function () {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();
    $product = Product::factory()->create(['stock' => 3]);
    $owner->cartItems()->create([
        'product_id' => $product->id,
        'quantity' => 1,
    ]);
    $otherUser->cartItems()->create([
        'product_id' => $product->id,
        'quantity' => 1,
    ]);
    $regions = seedRajaOngkirShippingRegions();
    $quote = getRajaOngkirQuote($this, $owner, $regions);

    $this->actingAs($otherUser)
        ->from(route('checkout.create'))
        ->post(route('checkout.store'), [
            'name' => $otherUser->name,
            'phone' => '+628123456789',
            'email' => $otherUser->email,
            'street_address' => 'Alamat pembeli',
            ...$regions,
            'shipping_quote_id' => $quote['quote_id'],
            'shipping_option' => $quote['options'][0]['option_id'],
        ])
        ->assertSessionHasErrors('shipping');

    $this->assertDatabaseCount('orders', 0);
    expect($product->fresh()->reserved_stock)->toBe(0);
});

test('checkout rejects a quote after the cart weight changes', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create([
        'weight_grams' => 500,
        'stock' => 3,
    ]);
    $cartItem = $user->cartItems()->create([
        'product_id' => $product->id,
        'quantity' => 1,
    ]);
    $regions = seedRajaOngkirShippingRegions();
    $quote = getRajaOngkirQuote($this, $user, $regions);
    $cartItem->update(['quantity' => 2]);

    $this->actingAs($user)
        ->from(route('checkout.create'))
        ->post(route('checkout.store'), [
            'name' => $user->name,
            'phone' => '+628123456789',
            'email' => $user->email,
            'street_address' => 'Alamat pembeli',
            ...$regions,
            'shipping_quote_id' => $quote['quote_id'],
            'shipping_option' => $quote['options'][0]['option_id'],
        ])
        ->assertSessionHasErrors('shipping');

    $this->assertDatabaseCount('orders', 0);
    expect($product->fresh()->reserved_stock)->toBe(0);
});

test('shipping quote reports provider failures instead of returning a zero-cost option', function () {
    Cache::forget('rajaongkir-origin-city:makassar-sulawesi-selatan');
    Http::preventStrayRequests();
    Http::fake([
        'rajaongkir.komerce.id/api/v1/destination/province' => Http::response([
            'data' => [['id' => 28, 'name' => 'Sulawesi Selatan']],
        ]),
        'rajaongkir.komerce.id/api/v1/destination/city/28' => Http::response([
            'data' => [['id' => 4321, 'city_name' => 'Makassar']],
        ]),
        'rajaongkir.komerce.id/api/v1/destination/domestic-destination*' => Http::response([], 401),
    ]);
    $user = User::factory()->create();
    $product = Product::factory()->create();
    $user->cartItems()->create([
        'product_id' => $product->id,
        'quantity' => 1,
    ]);

    $this->actingAs($user)
        ->postJson(route('checkout.shipping-options'), seedRajaOngkirShippingRegions())
        ->assertServiceUnavailable()
        ->assertJsonPath('message', 'Layanan ongkir sedang tidak tersedia. Coba lagi.');

    Http::assertSentCount(3);
});

test('shipping quotes do not call the provider when credentials are missing', function () {
    Cache::forget('rajaongkir-origin-city:makassar-sulawesi-selatan');
    Http::preventStrayRequests();
    Http::fake();
    config([
        'services.rajaongkir.api_key' => null,
    ]);
    $user = User::factory()->create();
    $product = Product::factory()->create();
    $user->cartItems()->create([
        'product_id' => $product->id,
        'quantity' => 1,
    ]);

    $this->actingAs($user)
        ->postJson(route('checkout.shipping-options'), seedRajaOngkirShippingRegions())
        ->assertServiceUnavailable()
        ->assertJsonPath('message', 'RajaOngkir belum dikonfigurasi. Isi RAJAONGKIR_API_KEY di environment aplikasi.');

    Http::assertNothingSent();
});

test('shipping quotes fail explicitly when Makassar is missing from provider location data', function () {
    Cache::forget('rajaongkir-origin-city:makassar-sulawesi-selatan');
    Http::preventStrayRequests();
    Http::fake([
        'rajaongkir.komerce.id/api/v1/destination/province' => Http::response([
            'data' => [['id' => 28, 'name' => 'Sulawesi Selatan']],
        ]),
        'rajaongkir.komerce.id/api/v1/destination/city/28' => Http::response([
            'data' => [],
        ]),
    ]);
    config([
        'services.rajaongkir.api_key' => 'test-rajaongkir-key',
    ]);
    $user = User::factory()->create();
    $product = Product::factory()->create();
    $user->cartItems()->create([
        'product_id' => $product->id,
        'quantity' => 1,
    ]);

    $this->actingAs($user)
        ->postJson(route('checkout.shipping-options'), seedRajaOngkirShippingRegions())
        ->assertServiceUnavailable()
        ->assertJsonPath('message', 'Kota Makassar tidak ditemukan pada data RajaOngkir.');

    Http::assertSentCount(2);
});

test('checkout requires a provider-issued shipping quote', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create(['stock' => 2]);
    $user->cartItems()->create([
        'product_id' => $product->id,
        'quantity' => 1,
    ]);

    $this->actingAs($user)
        ->from(route('checkout.create'))
        ->post(route('checkout.store'), [
            'name' => $user->name,
            'phone' => '+628123456789',
            'email' => $user->email,
            'street_address' => 'Alamat pembeli',
            ...seedRajaOngkirShippingRegions(),
            'shipping_quote_id' => '00000000-0000-4000-8000-000000000000',
            'shipping_option' => str_repeat('a', 64),
        ])
        ->assertSessionHasErrors('shipping');

    $this->assertDatabaseCount('orders', 0);
});
