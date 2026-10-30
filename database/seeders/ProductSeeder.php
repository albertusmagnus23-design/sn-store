<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        if (Product::query()->exists()) {
            $this->command?->info('Produk sudah tersedia; seeder produk demo dilewati.');

            return;
        }

        $category = Category::query()->firstOrCreate(
            ['slug' => 'product-demo'],
            ['name' => 'Produk Demo', 'is_active' => true],
        );

        Product::factory()
            ->count(8)
            ->create(['category_id' => $category->id]);
    }
}
