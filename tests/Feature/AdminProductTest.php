<?php

use App\Models\User;
use Database\Seeders\RolesSeeder;

it('renders sample products and marks the product menu active for a verified admin', function () {
    $this->seed(RolesSeeder::class);
    $user = User::factory()->create();
    $user->assignRole('admin');

    $response = $this->actingAs($user)->get(route('admin.products.index'));

    $response
        ->assertOk()
        ->assertViewIs('pages.admin.products.index')
        ->assertSee('Daftar produk')
        ->assertSee('Nova X Pro 256GB')
        ->assertSee('AeroBook 14 Ultra')
        ->assertSee('Studio Headphones S2')
        ->assertSee('Pulse Watch Active')
        ->assertSee('SoundPod Mini')
        ->assertSee('Keychron K2 Wireless')
        ->assertSee('Rp 12.499.000')
        ->assertSee('Stok menipis')
        ->assertSee('Data produk ini hanya contoh dan belum terhubung ke basis data.')
        ->assertSee('aria-current="page"', false)
        ->assertSee(route('admin.products.index'));
});

it('redirects guests to login when they request admin products', function () {
    $response = $this->get(route('admin.products.index'));

    $response->assertRedirect(route('login'));
});
