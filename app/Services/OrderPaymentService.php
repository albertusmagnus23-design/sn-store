<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use LogicException;

class OrderPaymentService
{
    public function markAsPaid(Order $order, string $paymentReference): Order
    {
        $paymentReference = trim($paymentReference);

        if ($paymentReference === '') {
            throw new InvalidArgumentException('Payment reference cannot be empty.');
        }

        return DB::transaction(function () use ($order, $paymentReference): Order {
            $lockedOrder = Order::query()->lockForUpdate()->findOrFail($order->id);

            if ($lockedOrder->status === Order::STATUS_PAID) {
                if ($lockedOrder->payment_reference !== $paymentReference) {
                    throw new LogicException('Order has already been paid with a different payment reference.');
                }

                return $lockedOrder;
            }

            if ($lockedOrder->status !== Order::STATUS_PENDING) {
                throw new LogicException('Only pending orders can be marked as paid.');
            }

            $items = $lockedOrder->items()->orderBy('product_id')->get();
            $products = Product::query()
                ->whereIn('id', $items->pluck('product_id'))
                ->orderBy('id')
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            foreach ($items as $item) {
                $product = $products->get($item->product_id);

                if ($product === null || $product->stock < $item->quantity || $product->reserved_stock < $item->quantity) {
                    throw new LogicException('Reserved inventory is inconsistent for this order.');
                }
            }

            foreach ($items as $item) {
                $product = $products->get($item->product_id);
                $product->decrement('stock', $item->quantity);
                $product->decrement('reserved_stock', $item->quantity);
            }

            $lockedOrder->forceFill([
                'status' => Order::STATUS_PAID,
                'payment_reference' => $paymentReference,
                'paid_at' => now(),
            ])->save();

            return $lockedOrder;
        });
    }

    public function markAsFailed(Order $order, ?string $paymentReference = null): Order
    {
        return DB::transaction(function () use ($order, $paymentReference): Order {
            $lockedOrder = Order::query()->lockForUpdate()->findOrFail($order->id);

            if (in_array($lockedOrder->status, [Order::STATUS_PAID, Order::STATUS_FAILED, Order::STATUS_CANCELLED], true)) {
                return $lockedOrder;
            }

            $this->releaseReservations($lockedOrder);

            $lockedOrder->forceFill([
                'status' => Order::STATUS_FAILED,
                'payment_reference' => $paymentReference,
            ])->save();

            return $lockedOrder;
        });
    }

    public function cancelPendingOrder(Order $order): Order
    {
        return Cache::lock('midtrans-snap-order-'.$order->id, 30)
            ->block(10, function () use ($order): Order {
                return DB::transaction(function () use ($order): Order {
                    $lockedOrder = Order::query()->lockForUpdate()->findOrFail($order->id);

                    if ($lockedOrder->status === Order::STATUS_CANCELLED) {
                        return $lockedOrder;
                    }

                    if ($lockedOrder->status !== Order::STATUS_PENDING) {
                        throw new LogicException('Only pending orders can be cancelled.');
                    }

                    if ($lockedOrder->payment_token !== null) {
                        throw new LogicException('An order with an active Midtrans payment session cannot be cancelled by the customer.');
                    }

                    $this->releaseReservations($lockedOrder);
                    $lockedOrder->status = Order::STATUS_CANCELLED;
                    $lockedOrder->save();

                    return $lockedOrder;
                });
            });
    }

    private function releaseReservations(Order $order): void
    {
        $items = $order->items()->orderBy('product_id')->get();
        $products = Product::query()
            ->whereIn('id', $items->pluck('product_id'))
            ->orderBy('id')
            ->lockForUpdate()
            ->get()
            ->keyBy('id');

        foreach ($items as $item) {
            $product = $products->get($item->product_id);

            if ($product === null || $product->reserved_stock < $item->quantity) {
                throw new LogicException('Reserved inventory is inconsistent for this order.');
            }
        }

        foreach ($items as $item) {
            $products->get($item->product_id)->decrement('reserved_stock', $item->quantity);
        }
    }
}
