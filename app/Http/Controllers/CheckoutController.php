<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckoutRequest;
use App\Models\CartItem;
use App\Services\CheckoutAddress;
use App\Services\CheckoutService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Laravolt\Indonesia\Models\Province;

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
            'cartTotal' => $cartItems->sum(fn (CartItem $item): int => $item->line_total_in_rupiah),
            'user' => $request->user(),
            'provinces' => Province::query()->orderBy('name')->get(['code', 'name']),
        ]);
    }

    public function store(CheckoutRequest $request, CheckoutAddress $checkoutAddress, CheckoutService $checkoutService): RedirectResponse
    {
        $customer = $request->validated();
        $customer['address'] = $checkoutAddress->format($customer);

        $order = $checkoutService->createPendingOrder(
            $request->user(),
            $customer,
            $customer['shipping_quote_id'],
            $customer['shipping_option'],
        );

        return redirect()->route('orders.show', $order)->with('status', 'Pesanan dibuat dan menunggu pembayaran.');
    }
}
