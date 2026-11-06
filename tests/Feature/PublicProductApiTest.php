<?php

use App\Models\Category;
use App\Models\Product;

it('returns public products with their active category data', function () {
    $category = Category::factory()->create([
        'name' => 'Audio',
        'slug' => 'audio',
        'description' => 'Perangkat audio.',
    ]);
    $inactiveCategory = Category::factory()->create([
        'name' => 'Tersembunyi',
        'slug' => 'tersembunyi',
        'is_active' => false,
    ]);

    Product::factory()->for($category)->create([
        'name' => 'Headphone Nova',
        'sku' => 'AUD-100',
        'price' => '125000.00',
        'stock' => 7,
        'reserved_stock' => 2,
        'weight_grams' => 450,
        'badge' => 'Terlaris',
        'created_at' => '2025-03-01 10:00:00',
        'updated_at' => '2025-03-02 11:30:00',
    ]);
    Product::factory()->for($inactiveCategory)->create([
        'name' => 'Produk Rahasia',
        'sku' => 'SECRET-001',
    ]);

    $this->getJson('/api/products')
        ->assertOk()
        ->assertJsonPath('code', 200)
        ->assertJsonPath('status', 'success')
        ->assertJsonPath('message', 'Data produk berhasil diambil.')
        ->assertJsonStructure([
            'data' => [[
                'id',
                'nama',
                'sku',
                'harga',
                'stok',
                'berat_gram',
                'badge',
                'gambar',
                'created_at',
                'updated_at',
                'kategori' => ['id', 'nama', 'slug', 'deskripsi'],
            ]],
        ])
        ->assertJsonPath('data.0.nama', 'Headphone Nova')
        ->assertJsonPath('data.0.harga', '125000.00')
        ->assertJsonPath('data.0.stok', 5)
        ->assertJsonPath('data.0.berat_gram', 450)
        ->assertJsonPath('data.0.kategori.id', $category->id)
        ->assertJsonPath('data.0.kategori.nama', 'Audio')
        ->assertJsonPath('data.0.kategori.slug', 'audio')
        ->assertJsonPath('data.0.created_at', '2025-03-01T10:00:00.000000Z')
        ->assertJsonPath('data.0.updated_at', '2025-03-02T11:30:00.000000Z')
        ->assertJsonMissingPath('data.0.reserved_stock')
        ->assertJsonMissingPath('data.1');
});

it('returns a public product detail with its category', function () {
    $category = Category::factory()->create([
        'name' => 'Audio',
        'slug' => 'audio',
        'description' => 'Perangkat audio.',
    ]);
    $product = Product::factory()->for($category)->create([
        'name' => 'Headphone Nova',
        'sku' => 'AUD-DETAIL',
        'price' => '125000.00',
        'stock' => 7,
        'reserved_stock' => 2,
        'weight_grams' => 450,
        'badge' => 'Terlaris',
    ]);

    $this->getJson(route('api.products.show', $product->id))
        ->assertOk()
        ->assertExactJson([
            'code' => 200,
            'status' => 'success',
            'message' => 'Detail produk berhasil diambil.',
            'data' => [
                'id' => $product->id,
                'nama' => 'Headphone Nova',
                'sku' => 'AUD-DETAIL',
                'harga' => '125000.00',
                'stok' => 5,
                'berat_gram' => 450,
                'badge' => 'Terlaris',
                'gambar' => null,
                'created_at' => $product->created_at->toISOString(),
                'updated_at' => $product->updated_at->toISOString(),
                'kategori' => [
                    'id' => $category->id,
                    'nama' => 'Audio',
                    'slug' => 'audio',
                    'deskripsi' => 'Perangkat audio.',
                    'created_at' => $category->created_at->toISOString(),
                    'updated_at' => $category->updated_at->toISOString(),
                ],
            ],
        ])
        ->assertJsonMissingPath('data.reserved_stock');
});

it('returns a standardized 404 response when the requested product does not exist', function () {
    $this->getJson('/api/products/999999')
        ->assertNotFound()
        ->assertExactJson([
            'code' => 404,
            'status' => 'error',
            'message' => 'Produk tidak ditemukan.',
            'data' => null,
        ]);
});

it('does not expose a product whose category is inactive in the detail endpoint', function () {
    $category = Category::factory()->create(['is_active' => false]);
    $product = Product::factory()->for($category)->create();

    $this->getJson(route('api.products.show', $product->id))
        ->assertNotFound()
        ->assertExactJson([
            'code' => 404,
            'status' => 'error',
            'message' => 'Produk tidak ditemukan.',
            'data' => null,
        ]);
});

it('returns no products when their category is inactive', function () {
    $category = Category::factory()->create(['is_active' => false]);
    Product::factory()->for($category)->create(['name' => 'Produk Tersembunyi']);

    $this->getJson('/api/products')
        ->assertOk()
        ->assertExactJson([
            'code' => 200,
            'status' => 'success',
            'message' => 'Data produk berhasil diambil.',
            'data' => [],
        ]);
});

it('filters public products by category slug', function () {
    $audioCategory = Category::factory()->create([
        'name' => 'Audio',
        'slug' => 'audio',
    ]);
    $computerCategory = Category::factory()->create([
        'name' => 'Komputer',
        'slug' => 'komputer',
    ]);
    Product::factory()->for($audioCategory)->create(['name' => 'Headphone Nova']);
    Product::factory()->for($computerCategory)->create(['name' => 'Laptop Nova']);

    $this->getJson('/api/products?category=audio')
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.kategori.slug', 'audio')
        ->assertJsonMissing(['nama' => 'Laptop Nova']);
});

it('rejects invalid public product category filters', function () {
    $this->getJson('/api/products?category[]=invalid')
        ->assertUnprocessable()
        ->assertExactJson([
            'code' => 422,
            'status' => 'error',
            'message' => 'Data yang dikirim tidak valid.',
            'data' => [
                'errors' => [
                    'category' => ['The category field must be a string.'],
                ],
            ],
        ]);
});
