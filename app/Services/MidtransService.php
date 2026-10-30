<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use LogicException;

class MidtransService
{
    public function __construct(private OrderPaymentService $orderPaymentService) {}

    public function isConfigured(): bool
    {
        return filled(config('services.midtrans.merchant_id'))
            && filled(config('services.midtrans.server_key'))
            && filled(config('services.midtrans.client_key'));
    }

    public function createPaymentSession(Order $order): string
    {
        if (! $this->isConfigured()) {
            throw ValidationException::withMessages([
                'payment' => 'Pembayaran belum dikonfigurasi. Hubungi pengelola toko.',
            ]);
        }

        return Cache::lock('midtrans-snap-order-'.$order->id, 30)
            ->block(10, function () use ($order): string {
                $lockedOrder = Order::query()->findOrFail($order->id);

                if ($lockedOrder->status !== Order::STATUS_PENDING) {
                    throw ValidationException::withMessages([
                        'payment' => 'Pesanan ini tidak lagi menunggu pembayaran.',
                    ]);
                }

                if ($lockedOrder->payment_url !== null) {
                    return $lockedOrder->payment_url;
                }

                $amount = $this->wholeRupiahAmount($lockedOrder);

                try {
                    $response = Http::withBasicAuth(config('services.midtrans.server_key'), '')
                        ->acceptJson()
                        ->connectTimeout(3)
                        ->timeout(15)
                        ->post(rtrim(config('services.midtrans.snap_url'), '/').'/snap/v1/transactions', [
                            'transaction_details' => [
                                'order_id' => $lockedOrder->order_number,
                                'gross_amount' => $amount,
                            ],
                            'customer_details' => [
                                'first_name' => $lockedOrder->customer_name,
                                'email' => $lockedOrder->customer_email,
                                'phone' => $lockedOrder->customer_phone,
                            ],
                            'item_details' => $lockedOrder->items->map(fn ($item): array => [
                                'id' => $item->product_sku,
                                'price' => (int) round((float) $item->unit_price),
                                'quantity' => $item->quantity,
                                'name' => mb_substr($item->product_name, 0, 50),
                            ])->all(),
                            'expiry' => [
                                'start_time' => now()->setTimezone('Asia/Jakarta')->format('Y-m-d H:i:s O'),
                                'unit' => 'hour',
                                'duration' => 24,
                            ],
                            'callbacks' => [
                                'finish' => route('orders.show', $lockedOrder),
                            ],
                        ]);
                } catch (ConnectionException $exception) {
                    Log::warning('Midtrans Snap session request failed to connect.', [
                        'order_id' => $lockedOrder->order_number,
                        'exception' => $exception::class,
                    ]);

                    throw ValidationException::withMessages([
                        'payment' => 'Layanan pembayaran tidak dapat dihubungi. Silakan coba kembali.',
                    ]);
                }

                if (! $response->successful()) {
                    Log::warning('Midtrans Snap rejected a session request.', [
                        'order_id' => $lockedOrder->order_number,
                        'http_status' => $response->status(),
                    ]);

                    throw ValidationException::withMessages([
                        'payment' => 'Midtrans belum dapat membuat sesi pembayaran. Silakan coba kembali.',
                    ]);
                }

                $token = $response->json('token');
                $redirectUrl = $response->json('redirect_url');

                if (! is_string($token) || $token === '' || ! is_string($redirectUrl) || ! $this->isValidRedirectUrl($redirectUrl)) {
                    Log::error('Midtrans Snap returned an invalid session response.', [
                        'order_id' => $lockedOrder->order_number,
                    ]);

                    throw ValidationException::withMessages([
                        'payment' => 'Midtrans mengembalikan sesi pembayaran yang tidak valid. Silakan coba kembali.',
                    ]);
                }

                DB::transaction(function () use ($lockedOrder, $token, $redirectUrl): void {
                    $currentOrder = Order::query()->lockForUpdate()->findOrFail($lockedOrder->id);

                    if ($currentOrder->status !== Order::STATUS_PENDING) {
                        throw new LogicException('Order status changed while creating a Midtrans session.');
                    }

                    $currentOrder->forceFill([
                        'payment_token' => $token,
                        'payment_url' => $redirectUrl,
                    ])->save();
                });

                return $redirectUrl;
            });
    }

    /**
     * @param  array{order_id: string, status_code: string, gross_amount: string, signature_key: string, merchant_id: string, transaction_status: string, fraud_status?: string, transaction_id?: string}  $notification
     */
    public function handleNotification(array $notification): void
    {
        $serverKey = config('services.midtrans.server_key');
        $merchantId = config('services.midtrans.merchant_id');

        if (! is_string($serverKey) || $serverKey === '' || ! is_string($merchantId) || $merchantId === '') {
            Log::error('Midtrans notification received without configured credentials.');

            abort(503, 'Payment notification verification is not configured.');
        }

        $signature = hash(
            'sha512',
            $notification['order_id'].$notification['status_code'].$notification['gross_amount'].$serverKey,
        );

        if (! hash_equals($signature, $notification['signature_key'])
            || ! hash_equals($merchantId, $notification['merchant_id'])) {
            abort(401, 'Invalid Midtrans notification signature.');
        }

        $order = Order::query()->where('order_number', $notification['order_id'])->firstOrFail();

        if ($this->normalizeAmount((string) $order->total) !== $this->normalizeAmount($notification['gross_amount'])) {
            abort(422, 'Midtrans notification amount does not match the order.');
        }

        $status = strtolower($notification['transaction_status']);

        if (in_array($status, ['capture', 'settlement'], true)) {
            $fraudStatus = strtolower($notification['fraud_status'] ?? '');

            if ($status === 'capture' && $fraudStatus === 'deny') {
                $this->orderPaymentService->markAsFailed($order, $notification['transaction_id'] ?? null);

                return;
            }

            if ($notification['status_code'] !== '200') {
                abort(422, 'Midtrans notification does not confirm a successful payment.');
            }

            if ($fraudStatus !== 'accept') {
                return;
            }

            if (! filled($notification['transaction_id'] ?? null)) {
                abort(422, 'Midtrans notification is missing the transaction reference.');
            }

            $this->orderPaymentService->markAsPaid($order, $notification['transaction_id']);

            return;
        }

        if (in_array($status, ['deny', 'cancel', 'expire', 'failure'], true)) {
            $this->orderPaymentService->markAsFailed($order, $notification['transaction_id'] ?? null);
        }
    }

    private function wholeRupiahAmount(Order $order): int
    {
        $amount = (string) $order->total;

        if (! preg_match('/^[1-9]\d*\.00$/', $amount)) {
            throw ValidationException::withMessages([
                'payment' => 'Total pesanan tidak valid untuk pembayaran Midtrans.',
            ]);
        }

        return (int) substr($amount, 0, -3);
    }

    private function normalizeAmount(string $amount): ?string
    {
        if (! preg_match('/^(\d+)(?:\.(\d{1,2}))?$/', $amount, $matches)) {
            return null;
        }

        $whole = ltrim($matches[1], '0');
        $fraction = str_pad($matches[2] ?? '', 2, '0');

        return ($whole === '' ? '0' : $whole).'.'.$fraction;
    }

    private function isValidRedirectUrl(string $redirectUrl): bool
    {
        $host = parse_url($redirectUrl, PHP_URL_HOST);
        $allowedHost = parse_url(config('services.midtrans.snap_url'), PHP_URL_HOST);

        return parse_url($redirectUrl, PHP_URL_SCHEME) === 'https'
            && $host === $allowedHost;
    }
}
