<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CheckoutService
{
    private const MAX_ORDER_TOTAL_IN_RUPIAH = 9_999_999_999_999;

    /**
     * @param  array{name: string, phone: string, email: string, address: string}  $customer
     */
    public function createPendingOrder(User $user, array $customer): Order
    {
        return DB::transaction(function () use ($user, $customer): Order {
            $cartItems = $user->cartItems()->orderBy('id')->lockForUpdate()->get();

            if ($cartItems->isEmpty()) {
                throw ValidationException::withMessages([
                    'cart' => 'Keranjang belanja masih kosong.',
                ]);
            }

            $products = Product::query()
                ->whereIn('id', $cartItems->pluck('product_id'))
                ->orderBy('id')
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            foreach ($cartItems as $cartItem) {
                $product = $products->get($cartItem->product_id);

                if ($product === null || ! $product->category()->where('is_active', true)->exists()) {
                    throw ValidationException::withMessages([
                        'cart' => 'Salah satu produk di keranjang sudah tidak tersedia.',
                    ]);
                }

                if ($cartItem->quantity > $product->available_stock) {
                    throw ValidationException::withMessages([
                        'cart' => "Stok {$product->name} tidak mencukupi. Perbarui jumlah produk di keranjang.",
                    ]);
                }
            }

            $user->fill([
                'name' => $customer['name'],
                'phone' => $customer['phone'],
                'email' => $customer['email'],
                'address' => $customer['address'],
            ]);

            if ($user->isDirty('email')) {
                $user->email_verified_at = null;
            }

            $user->save();

            $order = Order::query()->create([
                'order_number' => 'SN-'.Str::upper((string) Str::ulid()),
                'user_id' => $user->id,
                'customer_name' => $customer['name'],
                'customer_phone' => $customer['phone'],
                'customer_email' => $customer['email'],
                'customer_address' => $customer['address'],
                'total' => '0.00',
                'status' => Order::STATUS_PENDING,
            ]);

            $totalInRupiah = 0;

            foreach ($cartItems as $cartItem) {
                $product = $products->get($cartItem->product_id);
                $unitPriceInRupiah = $this->priceToRupiah($product->price);

                if ($unitPriceInRupiah > 0 && $cartItem->quantity > intdiv(self::MAX_ORDER_TOTAL_IN_RUPIAH, $unitPriceInRupiah)) {
                    throw ValidationException::withMessages([
                        'cart' => 'Total pesanan melebihi batas transaksi yang dapat diproses.',
                    ]);
                }

                $lineTotalInRupiah = $unitPriceInRupiah * $cartItem->quantity;

                if ($lineTotalInRupiah > self::MAX_ORDER_TOTAL_IN_RUPIAH - $totalInRupiah) {
                    throw ValidationException::withMessages([
                        'cart' => 'Total pesanan melebihi batas transaksi yang dapat diproses.',
                    ]);
                }

                $totalInRupiah += $lineTotalInRupiah;

                OrderItem::query()->create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'product_sku' => $product->sku,
                    'unit_price' => $this->formatRupiah($unitPriceInRupiah),
                    'quantity' => $cartItem->quantity,
                    'line_total' => $this->formatRupiah($lineTotalInRupiah),
                ]);

                $product->increment('reserved_stock', $cartItem->quantity);
            }

            $order->total = $this->formatRupiah($totalInRupiah);
            $order->save();
            $user->cartItems()->delete();

            return $order;
        });
    }

    private function priceToRupiah(string $price): int
    {
        [$whole, $fraction] = array_pad(explode('.', $price, 2), 2, '0');
        $cents = (int) str_pad(substr($fraction, 0, 2), 2, '0');

        return (int) $whole + ($cents >= 50 ? 1 : 0);
    }

    private function formatRupiah(int $amountInRupiah): string
    {
        return $amountInRupiah.'.00';
    }
}
