<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\View\View;

class PublicHomeController extends Controller
{
    public function index(): View
    {
        $categories = Category::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $products = Product::query()
            ->with('category')
            ->whereHas('category', fn ($query) => $query->where('is_active', true))
            ->latest()
            ->get();

        return view('pages.public.home', [
            'categories' => $categories,
            'products' => $products,
            'cartItemCount' => request()->user()?->cartItems()->sum('quantity') ?? 0,
        ]);
    }
}
