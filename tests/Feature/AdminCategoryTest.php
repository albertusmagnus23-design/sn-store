<?php

use App\Models\User;
use Database\Seeders\RolesSeeder;

it('renders sample categories for a verified admin and marks the category menu active', function () {
    $this->seed(RolesSeeder::class);
    $user = User::factory()->create();
    $user->assignRole('admin');

    $response = $this->actingAs($user)->get(route('admin.categories.index'));

    $response
        ->assertOk()
        ->assertViewIs('pages.admin.categories.index')
        ->assertSee('Daftar kategori')
        ->assertSee('Smartphone')
        ->assertSee('Laptop & Komputer')
        ->assertSee('Audio')
        ->assertSee('Wearable')
        ->assertSee('Aksesori')
        ->assertSee('Monitor')
        ->assertSee('Kamera')
        ->assertSee('Tambah kategori')
        ->assertSee(route('admin.categories.create'))
        ->assertSee('aria-label="Edit kategori Smartphone"', false)
        ->assertSee('aria-label="Hapus kategori Smartphone"', false)
        ->assertSee('Data ini hanya contoh dan belum tersimpan di basis data.')
        ->assertSee('aria-current="page"', false)
        ->assertSee(route('admin.categories.index'));
});

it('redirects guests to login when they request admin categories', function () {
    $response = $this->get(route('admin.categories.index'));

    $response->assertRedirect(route('login'));
});

it('renders the demo category form without saving data', function () {
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
        ->assertSee('Data kategori belum disimpan.');
});
