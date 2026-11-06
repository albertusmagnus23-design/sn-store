<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ProductService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __invoke(Request $request, ProductService $productService): JsonResponse
    {
        $filters = $request->validate([
            'category' => ['sometimes', 'string', 'max:255'],
        ]);

        return ApiResponse::success(
            $productService->getPublicProducts($filters['category'] ?? null),
            'Data produk berhasil diambil.',
        );
    }

    public function show(string $productId, ProductService $productService): JsonResponse
    {
        $product = $productService->getPublicProduct((int) $productId);

        if ($product === null) {
            return ApiResponse::error('Produk tidak ditemukan.', 404);
        }

        return ApiResponse::success($product, 'Detail produk berhasil diambil.');
    }
}
