<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;
use RuntimeException;

class ProductService
{
    /**
     * @return list<array{
     *     id: int,
     *     nama: string,
     *     sku: string,
     *     harga: string,
     *     stok: int,
     *     berat_gram: int,
     *     badge: ?string,
     *     gambar: ?string,
     *     created_at: ?string,
     *     updated_at: ?string,
     *     kategori: array{
     *         id: int,
     *         nama: string,
     *         slug: string,
     *         deskripsi: ?string,
     *         created_at: ?string,
     *         updated_at: ?string
     *     }
     * }>
     */
    public function getPublicProducts(?string $categorySlug = null): array
    {
        return Product::query()
            ->with('category:id,name,slug,description,created_at,updated_at')
            ->whereHas('category', fn (Builder $query) => $query
                ->where('is_active', true)
                ->when($categorySlug !== null, fn (Builder $query) => $query->where('slug', $categorySlug)))
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->get(['id', 'category_id', 'name', 'sku', 'price', 'stock', 'reserved_stock', 'weight_grams', 'badge', 'image_path', 'created_at', 'updated_at'])
            ->map(fn (Product $product): array => $this->formatPublicProduct($product))
            ->all();
    }

    /**
     * @return array{
     *     id: int,
     *     nama: string,
     *     sku: string,
     *     harga: string,
     *     stok: int,
     *     berat_gram: int,
     *     badge: ?string,
     *     gambar: ?string,
     *     created_at: ?string,
     *     updated_at: ?string,
     *     kategori: array{
     *         id: int,
     *         nama: string,
     *         slug: string,
     *         deskripsi: ?string,
     *         created_at: ?string,
     *         updated_at: ?string
     *     }
     * }|null
     */
    public function getPublicProduct(int $productId): ?array
    {
        $product = Product::query()
            ->with('category:id,name,slug,description,created_at,updated_at')
            ->whereHas('category', fn (Builder $query) => $query->where('is_active', true))
            ->whereKey($productId)
            ->first(['id', 'category_id', 'name', 'sku', 'price', 'stock', 'reserved_stock', 'weight_grams', 'badge', 'image_path', 'created_at', 'updated_at']);

        return $product === null ? null : $this->formatPublicProduct($product);
    }

    /**
     * @return array{
     *     id: int,
     *     nama: string,
     *     sku: string,
     *     harga: string,
     *     stok: int,
     *     berat_gram: int,
     *     badge: ?string,
     *     gambar: ?string,
     *     created_at: ?string,
     *     updated_at: ?string,
     *     kategori: array{
     *         id: int,
     *         nama: string,
     *         slug: string,
     *         deskripsi: ?string,
     *         created_at: ?string,
     *         updated_at: ?string
     *     }
     * }
     */
    private function formatPublicProduct(Product $product): array
    {
        return [
            'id' => $product->id,
            'nama' => $product->name,
            'sku' => $product->sku,
            'harga' => $product->price,
            'stok' => $product->available_stock,
            'berat_gram' => $product->weight_grams,
            'badge' => $product->badge,
            'gambar' => $product->image_path === null ? null : asset('storage/'.$product->image_path),
            'created_at' => $product->created_at?->toISOString(),
            'updated_at' => $product->updated_at?->toISOString(),
            'kategori' => [
                'id' => $product->category->id,
                'nama' => $product->category->name,
                'slug' => $product->category->slug,
                'deskripsi' => $product->category->description,
                'created_at' => $product->category->created_at?->toISOString(),
                'updated_at' => $product->category->updated_at?->toISOString(),
            ],
        ];
    }

    /**
     * @return array{
     *     products: Collection<int, Product>,
     *     totalStock: int,
     *     availableProducts: int,
     *     lowStockProducts: int
     * }
     */
    public function getIndexData(): array
    {
        $products = Product::query()
            ->with('category')
            ->orderBy('name')
            ->get();

        foreach ($products as $product) {
            $availableStock = $product->available_stock;
            $product->setAttribute('availability_status', match (true) {
                $availableStock === 0 => 'Stok habis',
                $availableStock <= 10 => 'Stok menipis',
                default => 'Tersedia',
            });
        }

        return [
            'products' => $products,
            'totalStock' => $products->sum('stock'),
            'availableProducts' => $products->where('availability_status', 'Tersedia')->count(),
            'lowStockProducts' => $products->where('availability_status', 'Stok menipis')->count(),
        ];
    }

    /**
     * @return array{categories: Collection<int, Category>}
     */
    public function getFormData(): array
    {
        return [
            'categories' => Category::query()->orderBy('name')->get(),
        ];
    }

    public function newProduct(): Product
    {
        return new Product(['stock' => 0, 'weight_grams' => 1000]);
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function create(array $attributes): Product
    {
        $image = $attributes['image'] ?? null;

        if (! $image instanceof UploadedFile) {
            throw new InvalidArgumentException('A validated product image is required.');
        }

        unset($attributes['image']);
        $attributes['image_path'] = $this->storeImage($image);

        return Product::create($attributes);
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function update(Product $product, array $attributes): void
    {
        $oldImagePath = $product->image_path;
        $image = $attributes['image'] ?? null;
        unset($attributes['image']);

        if ($image instanceof UploadedFile) {
            $attributes['image_path'] = $this->storeImage($image);
        }

        try {
            DB::transaction(function () use ($product, $attributes): void {
                $lockedProduct = Product::query()->lockForUpdate()->findOrFail($product->id);

                if (isset($attributes['stock']) && $attributes['stock'] < $lockedProduct->reserved_stock) {
                    throw ValidationException::withMessages([
                        'stock' => 'Stok tidak dapat lebih rendah dari jumlah yang sedang dicadangkan untuk pesanan.',
                    ]);
                }

                $lockedProduct->update($attributes);
            });
        } catch (ValidationException $exception) {
            if ($image instanceof UploadedFile) {
                $this->deleteImage($attributes['image_path']);
            }

            throw $exception;
        }

        if ($image instanceof UploadedFile && $oldImagePath !== null) {
            $this->deleteImage($oldImagePath);
        }
    }

    public function delete(Product $product): void
    {
        $imagePath = $product->image_path;
        DB::transaction(function () use ($product): void {
            $lockedProduct = Product::query()->lockForUpdate()->findOrFail($product->id);

            if ($lockedProduct->reserved_stock > 0) {
                throw ValidationException::withMessages([
                    'product' => 'Produk tidak dapat dihapus selama masih memiliki pesanan yang menunggu pembayaran.',
                ]);
            }

            $lockedProduct->delete();
        });

        if ($imagePath !== null) {
            $this->deleteImage($imagePath);
        }
    }

    private function storeImage(UploadedFile $image): string
    {
        $path = $image->store('products', 'public');

        if (! is_string($path)) {
            throw new RuntimeException('Gambar produk gagal disimpan ke local storage.');
        }

        return $path;
    }

    private function deleteImage(string $path): void
    {
        if (! Storage::disk('public')->delete($path)) {
            throw new RuntimeException('Gambar produk gagal dihapus dari local storage.');
        }
    }
}
