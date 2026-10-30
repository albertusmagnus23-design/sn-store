<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckoutRequest;
use App\Models\CartItem;
use App\Services\CheckoutService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function create(Request $request): View
    {
        $cartItems = $request->user()->cartItems()
            ->with(['product.category'])
            ->orderBy('id')
            ->get();

        return view('pages.public.checkout', [
            'cartItems' => $cartItems,
            'cartTotal' => $cartItems->sum(fn (CartItem $item): float => (float) $item->product->price * $item->quantity),
            'user' => $request->user(),
        ]);
    }

    public function store(CheckoutRequest $request, CheckoutService $checkoutService): RedirectResponse
    {
        $order = $checkoutService->createPendingOrder($request->user(), $request->validated());

        return redirect()->route('orders.show', $order)->with('status', 'Pesanan dibuat dan menunggu pembayaran.');
    }
}
