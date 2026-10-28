<?php

use App\Models\Category;
use App\Models\User;
use Database\Seeders\RolesSeeder;

it('renders categories from the database for a verified admin', function () {
    $this->seed(RolesSeeder::class);
    $user = User::factory()->create();
    $user->assignRole('admin');

    Category::create([
        'name' => 'Kategori Aktif',
        'slug' => 'kategori-aktif',
        'description' => 'Deskripsi kategori aktif.',
        'is_active' => true,
    ]);

    Category::create([
        'name' => 'Kategori Nonaktif',
        'slug' => 'kategori-nonaktif',
        'description' => null,
        'is_active' => false,
    ]);

    $response = $this->actingAs($user)->get(route('admin.categories.index'));

    $response
        ->assertOk()
        ->assertViewIs('pages.admin.categories.index')
        ->assertSee('Daftar kategori')
        ->assertSee('Kategori Aktif')
        ->assertSee('kategori-aktif')
        ->assertSee('Deskripsi kategori aktif.')
        ->assertSee('Kategori Nonaktif')
        ->assertSee('kategori-nonaktif')
        ->assertSee('Tidak ada deskripsi.')
        ->assertSee('Nonaktif')
        ->assertSee('2 kategori')
        ->assertSee('Data kategori diambil langsung dari database.')
        ->assertSee('Tambah kategori')
        ->assertSee(route('admin.categories.create'))
        ->assertSee('aria-label="Edit kategori Kategori Aktif"', false)
        ->assertSee('aria-label="Hapus kategori Kategori Aktif"', false)
        ->assertSee('data-confirm-delete data-category-name="Kategori Aktif"', false)
        ->assertDontSee('onsubmit="return confirm(', false)
        ->assertDontSee('Smartphone')
        ->assertSee('aria-current="page"', false)
        ->assertSee(route('admin.categories.index'));
});

it('shows an empty state when no categories are stored', function () {
    $this->seed(RolesSeeder::class);
    $user = User::factory()->create();
    $user->assignRole('admin');

    $response = $this->actingAs($user)->get(route('admin.categories.index'));

    $response
        ->assertOk()
        ->assertSee('Belum ada kategori tersimpan di database.')
        ->assertSee('Menampilkan 0 kategori');
});

it('redirects guests to login when they request admin categories', function () {
    $response = $this->get(route('admin.categories.index'));

    $response->assertRedirect(route('login'));
});

it('renders a category form for creating a category', function () {
    $this->seed(RolesSeeder::class);
    $user = User::factory()->create();
    $user->assignRole('admin');

    $response = $this->actingAs($user)->get(route('admin.categories.create'));

    $response
        ->assertOk()
        ->assertViewIs('pages.admin.categories.create')
        ->assertSee('Tambah kategori')
        ->assertSee('name="name"', false)
        ->assertSee('name="slug"', false)
        ->assertSee('Simpan kategori')
        ->assertSee('action="'.route('admin.categories.store').'"', false);
});

it('renders an existing category in the edit form', function () {
    $this->seed(RolesSeeder::class);
    $user = User::factory()->create();
    $user->assignRole('admin');
    $category = Category::create([
        'name' => 'Audio',
        'slug' => 'audio',
        'description' => 'Kategori audio.',
        'is_active' => true,
    ]);

    $response = $this->actingAs($user)->get(route('admin.categories.edit', $category));

    $response
        ->assertOk()
        ->assertViewIs('pages.admin.categories.create')
        ->assertSee('Edit kategori')
        ->assertSee('value="Audio"', false)
        ->assertSee('value="audio"', false)
        ->assertSee('Kategori audio.')
        ->assertSee('action="'.route('admin.categories.update', $category).'"', false)
        ->assertSee('name="_method" value="PUT"', false);
});

it('stores a category and redirects to the category list', function () {
    $this->seed(RolesSeeder::class);
    $user = User::factory()->create();
    $user->assignRole('admin');

    $response = $this->actingAs($user)->post(route('admin.categories.store'), [
        'name' => 'Audio',
        'slug' => 'audio',
        'description' => 'Headphone dan speaker.',
        'is_active' => '1',
    ]);

    $response
        ->assertRedirect(route('admin.categories.index'))
        ->assertSessionHas('status', 'Kategori berhasil ditambahkan.');
    $this->assertDatabaseHas('categories', [
        'name' => 'Audio',
        'slug' => 'audio',
        'description' => 'Headphone dan speaker.',
        'is_active' => true,
    ]);
});

it('validates required fields and unique lowercase slugs when storing a category', function () {
    $this->seed(RolesSeeder::class);
    $user = User::factory()->create();
    $user->assignRole('admin');
    Category::create([
        'name' => 'Kategori Lama',
        'slug' => 'kategori-lama',
        'is_active' => true,
    ]);

    $response = $this->actingAs($user)
        ->from(route('admin.categories.create'))
        ->post(route('admin.categories.store'), [
            'name' => '',
            'slug' => 'kategori-lama',
            'is_active' => 'bukan-boolean',
        ]);

    $response
        ->assertRedirect(route('admin.categories.create'))
        ->assertSessionHasErrors(['name', 'slug', 'is_active']);
    $this->assertDatabaseCount('categories', 1);
});

it('updates a category while allowing its existing slug', function () {
    $this->seed(RolesSeeder::class);
    $user = User::factory()->create();
    $user->assignRole('admin');
    $category = Category::create([
        'name' => 'Audio Lama',
        'slug' => 'audio',
        'description' => 'Deskripsi lama.',
        'is_active' => true,
    ]);

    $response = $this->actingAs($user)->put(route('admin.categories.update', $category), [
        'name' => 'Audio Premium',
        'slug' => 'audio',
        'description' => 'Deskripsi baru.',
        'is_active' => '1',
    ]);

    $response
        ->assertRedirect(route('admin.categories.index'))
        ->assertSessionHas('status', 'Kategori berhasil diperbarui.');
    $this->assertDatabaseHas('categories', [
        'id' => $category->id,
        'name' => 'Audio Premium',
        'slug' => 'audio',
        'description' => 'Deskripsi baru.',
        'is_active' => true,
    ]);
});

it('rejects a slug already used by another category when updating', function () {
    $this->seed(RolesSeeder::class);
    $user = User::factory()->create();
    $user->assignRole('admin');
    $category = Category::create([
        'name' => 'Audio Lama',
        'slug' => 'audio-lama',
        'is_active' => true,
    ]);
    Category::create([
        'name' => 'Audio',
        'slug' => 'audio',
        'is_active' => true,
    ]);

    $response = $this->actingAs($user)
        ->from(route('admin.categories.edit', $category))
        ->put(route('admin.categories.update', $category), [
            'name' => 'Audio Baru',
            'slug' => 'audio',
        ]);

    $response
        ->assertRedirect(route('admin.categories.edit', $category))
        ->assertSessionHasErrors('slug');
    $this->assertDatabaseHas('categories', [
        'id' => $category->id,
        'name' => 'Audio Lama',
        'slug' => 'audio-lama',
    ]);
});

it('deletes a category', function () {
    $this->seed(RolesSeeder::class);
    $user = User::factory()->create();
    $user->assignRole('admin');
    $category = Category::create([
        'name' => 'Kategori Hapus',
        'slug' => 'kategori-hapus',
        'is_active' => true,
    ]);

    $response = $this->actingAs($user)->delete(route('admin.categories.destroy', $category));

    $response
        ->assertRedirect(route('admin.categories.index'))
        ->assertSessionHas('status', 'Kategori berhasil dihapus.');
    $this->assertDatabaseMissing('categories', ['id' => $category->id]);
});

it('does not allow guests to create categories', function () {
    $response = $this->post(route('admin.categories.store'), [
        'name' => 'Audio',
        'slug' => 'audio',
    ]);

    $response->assertRedirect(route('login'));
    $this->assertDatabaseCount('categories', 0);
});

it('redirects non-admin users away from category management', function () {
    $this->seed(RolesSeeder::class);
    $user = User::factory()->create();
    $user->assignRole('customer');

    $response = $this->actingAs($user)->get(route('admin.categories.index'));

    $response->assertRedirect(route('home'));
});

it('escapes category content in the admin list', function () {
    $this->seed(RolesSeeder::class);
    $user = User::factory()->create();
    $user->assignRole('admin');
    Category::create([
        'name' => '<script>alert(1)</script>',
        'slug' => 'script-category',
        'description' => '<img src=x onerror=alert(1)>',
        'is_active' => true,
    ]);

    $response = $this->actingAs($user)->get(route('admin.categories.index'));

    $response
        ->assertOk()
        ->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false)
        ->assertSee('&lt;img src=x onerror=alert(1)&gt;', false)
        ->assertDontSee('<script>alert(1)</script>', false)
        ->assertDontSee('<img src=x onerror=alert(1)>', false);
});
