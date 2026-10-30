<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddCartItemRequest;
use App\Http\Requests\UpdateCartItemRequest;
use App\Models\CartItem;
use App\Models\Product;
use App\Services\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    public function index(Request $request): View
    {
        $cartItems = $request->user()->cartItems()
            ->with(['product.category'])
            ->orderBy('id')
            ->get();

        return view('pages.public.cart.index', [
            'cartItems' => $cartItems,
            'cartTotal' => $cartItems->sum(fn (CartItem $item): float => (float) $item->product->price * $item->quantity),
            'cartItemCount' => $cartItems->sum('quantity'),
        ]);
    }

    public function store(AddCartItemRequest $request, Product $product, CartService $cartService): RedirectResponse
    {
        $cartService->add($request->user(), $product, $request->integer('quantity'));

        return redirect()->route('cart.index')->with('status', 'Produk berhasil ditambahkan ke keranjang.');
    }

    public function update(UpdateCartItemRequest $request, CartItem $cartItem, CartService $cartService): RedirectResponse
    {
        $ownedCartItem = $request->user()->cartItems()->whereKey($cartItem->id)->firstOrFail();
        $cartService->update($ownedCartItem, $request->integer('quantity'));

        return redirect()->route('cart.index')->with('status', 'Jumlah produk di keranjang berhasil diperbarui.');
    }

    public function destroy(Request $request, CartItem $cartItem): RedirectResponse
    {
        $request->user()->cartItems()->whereKey($cartItem->id)->firstOrFail()->delete();

        return redirect()->route('cart.index')->with('status', 'Produk dihapus dari keranjang.');
    }
}
