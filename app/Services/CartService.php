<?php

namespace App\Services;

use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CartService
{
    public function add(User $user, Product $product, int $quantity): CartItem
    {
        return DB::transaction(function () use ($user, $product, $quantity): CartItem {
            $lockedProduct = Product::query()->lockForUpdate()->findOrFail($product->id);

            if (! $lockedProduct->category()->where('is_active', true)->exists()) {
                throw ValidationException::withMessages([
                    'product' => 'Produk ini tidak tersedia.',
                ]);
            }

            if ($lockedProduct->available_stock < 1) {
                throw ValidationException::withMessages([
                    'quantity' => 'Stok produk sedang habis.',
                ]);
            }

            $cartItem = $user->cartItems()
                ->where('product_id', $lockedProduct->id)
                ->lockForUpdate()
                ->first();
            $newQuantity = ($cartItem?->quantity ?? 0) + $quantity;

            if ($newQuantity > $lockedProduct->available_stock) {
                throw ValidationException::withMessages([
                    'quantity' => 'Jumlah melebihi stok produk yang tersedia.',
                ]);
            }

            if ($cartItem === null) {
                return $user->cartItems()->create([
                    'product_id' => $lockedProduct->id,
                    'quantity' => $quantity,
                ]);
            }

            $cartItem->quantity = $newQuantity;
            $cartItem->save();

            return $cartItem;
        });
    }

    public function update(CartItem $cartItem, int $quantity): CartItem
    {
        return DB::transaction(function () use ($cartItem, $quantity): CartItem {
            $product = Product::query()->lockForUpdate()->findOrFail($cartItem->product_id);

            if ($quantity > $product->available_stock) {
                throw ValidationException::withMessages([
                    'quantity' => 'Jumlah melebihi stok produk yang tersedia.',
                ]);
            }

            $cartItem->quantity = $quantity;
            $cartItem->save();

            return $cartItem;
        });
    }
}
