<?php

use App\Models\User;
use Database\Seeders\RolesSeeder;

it('renders the dashboard in the admin view structure for a verified admin', function () {
    $this->seed(RolesSeeder::class);
    $user = User::factory()->create();
    $user->assignRole('admin');

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response
        ->assertOk()
        ->assertViewIs('pages.admin.dashboard.index')
        ->assertSee('Modul manajemen')
        ->assertSee('Kategori')
        ->assertSee('Produk')
        ->assertSee('Transaksi')
        ->assertSee('User');
});

it('redirects customers to the public home page from admin pages', function () {
    $this->seed(RolesSeeder::class);
    $customer = User::factory()->unverified()->create();
    $customer->assignRole('customer');

    $this->actingAs($customer)
        ->get(route('dashboard'))
        ->assertRedirect(route('home'));

    $this->actingAs($customer)
        ->get(route('admin.products.index'))
        ->assertRedirect(route('home'));
});
