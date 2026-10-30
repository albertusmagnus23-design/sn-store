<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicProductController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['sometimes', 'string', 'max:100'],
            'category' => ['sometimes', 'string', 'max:255'],
        ]);
        $searchTerm = trim($filters['q'] ?? '');
        $selectedCategory = $filters['category'] ?? null;

        $products = Product::query()
            ->with('category')
            ->whereHas('category', function (Builder $query) use ($selectedCategory): void {
                $query->where('is_active', true)
                    ->when($selectedCategory, fn (Builder $query): Builder => $query->where('slug', $selectedCategory));
            })
            ->when($searchTerm !== '', function (Builder $query) use ($searchTerm): void {
                $query->where(function (Builder $query) use ($searchTerm): void {
                    $query->where('name', 'like', '%'.$searchTerm.'%')
                        ->orWhere('sku', 'like', '%'.$searchTerm.'%');
                });
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('pages.public.products.index', [
            'categories' => Category::query()
                ->where('is_active', true)
                ->orderBy('name')
                ->get(),
            'products' => $products,
            'searchTerm' => $searchTerm,
            'selectedCategory' => $selectedCategory,
            'cartItemCount' => $request->user()?->cartItems()->sum('quantity') ?? 0,
        ]);
    }
}
