<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Product;
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
        return new Product(['stock' => 0]);
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
