<?php

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Database\Seeders\RolesSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

it('shows database products and stock summaries to an admin', function () {
    Storage::fake('public');
    $this->seed(RolesSeeder::class);
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $category = Category::factory()->create(['name' => 'Audio']);
    Storage::disk('public')->put('products/headphones.png', 'image-content');

    Product::factory()->for($category)->create([
        'name' => 'Headphone Nova',
        'sku' => 'AUD-100',
        'price' => 12499000,
        'stock' => 24,
        'badge' => 'Terlaris',
        'image_path' => 'products/headphones.png',
    ]);
    Product::factory()->for($category)->create([
        'name' => 'Speaker Mini',
        'sku' => 'AUD-101',
        'stock' => 8,
    ]);
    Product::factory()->for($category)->create([
        'name' => 'Earbuds Lite',
        'sku' => 'AUD-102',
        'stock' => 0,
    ]);

    $this->actingAs($admin)
        ->get(route('admin.products.index'))
        ->assertOk()
        ->assertSee('Headphone Nova')
        ->assertSee('Speaker Mini')
        ->assertSee('Earbuds Lite')
        ->assertSee('Stok menipis')
        ->assertSee('Stok habis')
        ->assertSee('Tersedia')
        ->assertSee('Terlaris')
        ->assertSee('Rp 12.499.000')
        ->assertSee('3 produk')
        ->assertSee(Storage::disk('public')->url('products/headphones.png'))
        ->assertSee('32')
        ->assertDontSee('Product Alpha');
});

it('redirects guests to login when they request admin products', function () {
    $this->get(route('admin.products.index'))
        ->assertRedirect(route('login'));
});

it('renders a product form with categories for an admin', function () {
    $this->seed(RolesSeeder::class);
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $category = Category::factory()->create(['name' => 'Audio']);

    $this->actingAs($admin)
        ->get(route('admin.products.create'))
        ->assertOk()
        ->assertSee('Tambah Produk')
        ->assertSee('Audio')
        ->assertSee('enctype="multipart/form-data"', false);
});

it('creates a product with its uploaded image stored locally', function () {
    Storage::fake('public');
    $this->seed(RolesSeeder::class);
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $category = Category::factory()->create();

    $this->actingAs($admin)
        ->post(route('admin.products.store'), [
            'name' => 'Headphone Nova',
            'sku' => 'AUD-100',
            'category_id' => $category->id,
            'price' => 12499000,
            'stock' => 24,
            'badge' => 'Terlaris',
            'image' => UploadedFile::fake()->image('headphones.png'),
            'image_path' => 'products/injected.png',
        ])
        ->assertRedirect(route('admin.products.index'))
        ->assertSessionHas('status', 'Produk berhasil ditambahkan.');

    $product = Product::query()->where('sku', 'AUD-100')->firstOrFail();

    expect($product->name)->toBe('Headphone Nova')
        ->and($product->category_id)->toBe($category->id)
        ->and($product->price)->toBe('12499000.00')
        ->and($product->stock)->toBe(24)
        ->and($product->badge)->toBe('Terlaris')
        ->and($product->image_path)->toStartWith('products/')
        ->and($product->image_path)->not->toBe('products/injected.png');

    Storage::disk('public')->assertExists($product->image_path);
});

it('rejects invalid product fields and non-image uploads', function () {
    Storage::fake('public');
    $this->seed(RolesSeeder::class);
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $this->actingAs($admin)
        ->post(route('admin.products.store'), [
            'name' => '',
            'sku' => 'invalid/sku',
            'category_id' => 999,
            'price' => -1,
            'stock' => -1,
            'image' => UploadedFile::fake()->create('document.txt', 10, 'text/plain'),
        ])
        ->assertSessionHasErrors(['name', 'sku', 'category_id', 'price', 'stock', 'image']);

    expect(Product::query()->count())->toBe(0);
    Storage::disk('public')->assertDirectoryEmpty('products');
});

it('renders an existing product in its edit form', function () {
    $this->seed(RolesSeeder::class);
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $product = Product::factory()->create([
        'name' => 'Headphone Nova',
        'sku' => 'AUD-100',
    ]);

    $this->actingAs($admin)
        ->get(route('admin.products.edit', $product))
        ->assertOk()
        ->assertSee('Edit Produk')
        ->assertSee('Headphone Nova')
        ->assertSee('AUD-100');
});

it('updates a product and replaces its local image', function () {
    Storage::fake('public');
    $this->seed(RolesSeeder::class);
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    Storage::disk('public')->put('products/old-image.png', 'old-image');
    $product = Product::factory()->create([
        'sku' => 'AUD-100',
        'image_path' => 'products/old-image.png',
    ]);

    $this->actingAs($admin)
        ->put(route('admin.products.update', $product), [
            'name' => 'Updated Headphone',
            'sku' => $product->sku,
            'category_id' => $product->category_id,
            'price' => 899000,
            'stock' => 10,
            'badge' => 'Baru',
            'image' => UploadedFile::fake()->image('new-image.png'),
        ])
        ->assertRedirect(route('admin.products.index'))
        ->assertSessionHas('status', 'Produk berhasil diperbarui.');

    $product->refresh();

    expect($product->name)->toBe('Updated Headphone')
        ->and($product->price)->toBe('899000.00')
        ->and($product->stock)->toBe(10)
        ->and($product->badge)->toBe('Baru')
        ->and($product->image_path)->not->toBe('products/old-image.png');

    Storage::disk('public')->assertMissing('products/old-image.png');
    Storage::disk('public')->assertExists($product->image_path);
});

it('preserves the current image when a product is updated without a new image', function () {
    Storage::fake('public');
    $this->seed(RolesSeeder::class);
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    Storage::disk('public')->put('products/current-image.png', 'current-image');
    $product = Product::factory()->create([
        'image_path' => 'products/current-image.png',
    ]);

    $this->actingAs($admin)
        ->put(route('admin.products.update', $product), [
            'name' => $product->name,
            'sku' => $product->sku,
            'category_id' => $product->category_id,
            'price' => $product->price,
            'stock' => $product->stock,
        ])
        ->assertRedirect(route('admin.products.index'));

    expect($product->refresh()->image_path)->toBe('products/current-image.png');
    Storage::disk('public')->assertExists('products/current-image.png');
});

it('does not allow stock to be reduced below pending order reservations', function () {
    $this->seed(RolesSeeder::class);
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $product = Product::factory()->create([
        'stock' => 4,
        'reserved_stock' => 2,
    ]);

    $this->actingAs($admin)
        ->from(route('admin.products.edit', $product))
        ->put(route('admin.products.update', $product), [
            'name' => $product->name,
            'sku' => $product->sku,
            'category_id' => $product->category_id,
            'price' => $product->price,
            'stock' => 1,
        ])
        ->assertSessionHasErrors('stock');

    expect($product->fresh()->stock)->toBe(4);
});

it('does not allow deleting a product with pending order reservations', function () {
    $this->seed(RolesSeeder::class);
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $product = Product::factory()->create([
        'stock' => 4,
        'reserved_stock' => 2,
    ]);

    $this->actingAs($admin)
        ->from(route('admin.products.index'))
        ->delete(route('admin.products.destroy', $product))
        ->assertSessionHasErrors('product');

    $this->assertModelExists($product);
});

it('deletes a product and its local image', function () {
    Storage::fake('public');
    $this->seed(RolesSeeder::class);
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    Storage::disk('public')->put('products/to-delete.png', 'image-content');
    $product = Product::factory()->create([
        'image_path' => 'products/to-delete.png',
    ]);

    $this->actingAs($admin)
        ->delete(route('admin.products.destroy', $product))
        ->assertRedirect(route('admin.products.index'))
        ->assertSessionHas('status', 'Produk berhasil dihapus.');

    expect(Product::query()->find($product->id))->toBeNull();
    Storage::disk('public')->assertMissing('products/to-delete.png');
});

it('redirects non-admin users away from product management', function () {
    $this->seed(RolesSeeder::class);
    $customer = User::factory()->create();
    $customer->assignRole('customer');

    $this->actingAs($customer)
        ->get(route('admin.products.index'))
        ->assertRedirect(route('home'));
});

it('escapes product names in the admin listing', function () {
    $this->seed(RolesSeeder::class);
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    Product::factory()->create(['name' => '<script>alert(1)</script>']);

    $this->actingAs($admin)
        ->get(route('admin.products.index'))
        ->assertOk()
        ->assertDontSee('<script>alert(1)</script>', false)
        ->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false);
});
