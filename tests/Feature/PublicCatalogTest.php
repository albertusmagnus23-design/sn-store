<?php

use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;

it('shows active database categories and products on the public homepage', function () {
    Storage::fake('public');
    $category = Category::factory()->create([
        'name' => 'Audio Database',
        'description' => 'Perangkat audio pilihan.',
    ]);
    $inactiveCategory = Category::factory()->create([
        'name' => 'Kategori Nonaktif',
        'is_active' => false,
    ]);
    Storage::disk('public')->put('products/audio.png', 'image-content');

    Product::factory()->for($category)->create([
        'name' => 'Headphone Database',
        'price' => 1234500,
        'badge' => 'Pilihan',
        'image_path' => 'products/audio.png',
    ]);
    Product::factory()->for($inactiveCategory)->create([
        'name' => 'Produk Kategori Nonaktif',
    ]);

    $this->get(route('home'))
        ->assertOk()
        ->assertSee('Audio Database')
        ->assertSee('Perangkat audio pilihan.')
        ->assertSee('Headphone Database')
        ->assertSee('Rp 1.234.500')
        ->assertSee('Pilihan')
        ->assertSee(Storage::disk('public')->url('products/audio.png'))
        ->assertDontSee('Kategori Nonaktif')
        ->assertDontSee('Produk Kategori Nonaktif')
        ->assertDontSee('Nova X Pro 256GB')
        ->assertDontSee('Andi Pratama');
});

it('shows empty states when the database has no public categories or products', function () {
    $this->get(route('home'))
        ->assertOk()
        ->assertSee('Belum ada kategori yang tersedia.')
        ->assertSee('Belum ada produk yang tersedia.');
});

it('escapes category and product content rendered on the public homepage', function () {
    $category = Category::factory()->create([
        'name' => 'Audio <script>alert(1)</script>',
        'description' => 'Pilihan <strong>terbaru</strong>',
    ]);
    Product::factory()->for($category)->create([
        'name' => 'Headphone <script>alert(1)</script>',
    ]);

    $this->get(route('home'))
        ->assertOk()
        ->assertSee('Audio <script>alert(1)</script>')
        ->assertSee('Pilihan <strong>terbaru</strong>')
        ->assertSee('Headphone <script>alert(1)</script>')
        ->assertDontSee('<script>', false)
        ->assertDontSee('<strong>', false);
});
