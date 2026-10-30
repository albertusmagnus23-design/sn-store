<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\MidtransService;
use App\Services\OrderPaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use LogicException;

class OrderController extends Controller
{
    public function show(Request $request, Order $order, MidtransService $midtransService): View
    {
        $order = $request->user()->orders()
            ->with('items')
            ->whereKey($order->id)
            ->firstOrFail();

        return view('pages.public.orders.show', [
            'order' => $order,
            'midtransConfigured' => $midtransService->isConfigured(),
        ]);
    }

    public function pay(Request $request, Order $order, MidtransService $midtransService): RedirectResponse
    {
        $ownedOrder = $request->user()->orders()->whereKey($order->id)->firstOrFail();

        return redirect()->away($midtransService->createPaymentSession($ownedOrder));
    }

    public function cancel(Request $request, Order $order, OrderPaymentService $paymentService): RedirectResponse
    {
        $ownedOrder = $request->user()->orders()->whereKey($order->id)->firstOrFail();

        try {
            $paymentService->cancelPendingOrder($ownedOrder);
        } catch (LogicException $exception) {
            $ownedOrder->refresh();

            if ($ownedOrder->status === Order::STATUS_PENDING && $ownedOrder->payment_token !== null) {
                return redirect()
                    ->route('orders.show', $ownedOrder)
                    ->withErrors(['payment' => 'Pesanan tidak dapat dibatalkan setelah sesi pembayaran Midtrans dibuat.']);
            }

            if ($ownedOrder->status !== Order::STATUS_PENDING) {
                return redirect()
                    ->route('orders.show', $ownedOrder)
                    ->withErrors(['payment' => 'Pesanan yang sudah diproses tidak dapat dibatalkan.']);
            }

            throw $exception;
        }

        return redirect()
            ->route('orders.show', $ownedOrder)
            ->with('status', 'Pesanan dibatalkan dan stok yang dicadangkan telah dikembalikan.');
    }
}
