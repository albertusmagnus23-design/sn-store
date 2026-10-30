<?php

use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;

it('shows active products and category filters on the public product page', function () {
    Storage::fake('public');
    $audioCategory = Category::factory()->create(['name' => 'Audio Pilihan']);
    $computerCategory = Category::factory()->create(['name' => 'Komputer']);
    $inactiveCategory = Category::factory()->create([
        'name' => 'Kategori Nonaktif',
        'is_active' => false,
    ]);
    Storage::disk('public')->put('products/headphone.png', 'image-content');

    Product::factory()->for($audioCategory)->create([
        'name' => 'Headphone Nova',
        'sku' => 'AUD-100',
        'price' => 1299900,
        'badge' => 'Terlaris',
        'stock' => 5,
        'image_path' => 'products/headphone.png',
    ]);
    Product::factory()->for($computerCategory)->create(['name' => 'Laptop Nova']);
    Product::factory()->for($inactiveCategory)->create(['name' => 'Produk Tersembunyi']);

    $this->get(route('products.index'))
        ->assertOk()
        ->assertSee('Semua Produk')
        ->assertSee('Audio Pilihan')
        ->assertSee('Komputer')
        ->assertSee('Headphone Nova')
        ->assertSee('Laptop Nova')
        ->assertSee('AUD-100')
        ->assertSee('Rp 1.299.900')
        ->assertSee('Terlaris')
        ->assertSee(Storage::disk('public')->url('products/headphone.png'))
        ->assertSee('Tambah Headphone Nova ke keranjang')
        ->assertSee('href="'.route('login').'"', false)
        ->assertDontSee('Produk Tersembunyi')
        ->assertDontSee('Kategori Nonaktif');
});

it('serves product image URLs from the current host', function () {
    $category = Category::factory()->create();
    Product::factory()->for($category)->create([
        'name' => 'Headphone Nova',
        'image_path' => 'products/headphone.png',
    ]);

    $this->get('http://store.test/products')
        ->assertOk()
        ->assertSee('src="http://store.test/storage/products/headphone.png"', false);
});

it('filters products by category and searches product names and SKUs', function () {
    $audioCategory = Category::factory()->create(['name' => 'Audio']);
    $computerCategory = Category::factory()->create(['name' => 'Komputer']);
    Product::factory()->for($audioCategory)->create([
        'name' => 'Wireless Headphone',
        'sku' => 'AUD-SEARCH',
    ]);
    Product::factory()->for($audioCategory)->create([
        'name' => 'Speaker Mini',
        'sku' => 'AUD-OTHER',
    ]);
    Product::factory()->for($computerCategory)->create([
        'name' => 'Wireless Keyboard',
        'sku' => 'KEY-SEARCH',
    ]);

    $this->get(route('products.index', [
        'category' => $audioCategory->slug,
        'q' => 'SEARCH',
    ]))
        ->assertOk()
        ->assertSee('Wireless Headphone')
        ->assertDontSee('Speaker Mini')
        ->assertDontSee('Wireless Keyboard')
        ->assertSee('name="category" value="'.$audioCategory->slug.'"', false)
        ->assertSee('value="SEARCH"', false);

    $this->get(route('products.index', ['q' => 'Wireless']))
        ->assertOk()
        ->assertSee('Wireless Headphone')
        ->assertSee('Wireless Keyboard')
        ->assertDontSee('Speaker Mini');
});

it('shows an empty state when no products match the filters', function () {
    $category = Category::factory()->create(['name' => 'Audio']);
    Product::factory()->for($category)->create(['name' => 'Headphone Nova']);

    $this->get(route('products.index', ['q' => 'Not Found']))
        ->assertOk()
        ->assertSee('Produk tidak ditemukan')
        ->assertSee('Hapus filter')
        ->assertDontSee('Headphone Nova');
});

it('escapes category and product content on the product page', function () {
    $category = Category::factory()->create([
        'name' => 'Audio <script>alert(1)</script>',
    ]);
    Product::factory()->for($category)->create([
        'name' => 'Headphone <script>alert(1)</script>',
    ]);

    $this->get(route('products.index'))
        ->assertOk()
        ->assertSee('Audio <script>alert(1)</script>')
        ->assertSee('Headphone <script>alert(1)</script>')
        ->assertDontSee('<script>', false);
});

it('paginates products while preserving search filters', function () {
    $category = Category::factory()->create(['name' => 'Audio']);
    Product::factory()
        ->count(13)
        ->for($category)
        ->sequence(fn ($sequence) => [
            'name' => 'Headphone '.$sequence->index,
            'sku' => 'AUD-'.str_pad((string) $sequence->index, 3, '0', STR_PAD_LEFT),
        ])
        ->create();

    $this->get(route('products.index', [
        'category' => $category->slug,
        'q' => 'Headphone',
    ]))
        ->assertOk()
        ->assertViewHas('products', fn ($products): bool => $products->count() === 12 && $products->total() === 13)
        ->assertSee('category='.$category->slug, false)
        ->assertSee('q=Headphone', false);
});

it('validates product search query parameters', function () {
    $this->get(route('products.index', ['q' => ['invalid']]))
        ->assertSessionHasErrors('q');
});
