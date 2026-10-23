<?php

use App\Models\User;
use Database\Seeders\RolesSeeder;

it('renders sample transactions and marks the transaction menu active for a verified admin', function () {
    $this->seed(RolesSeeder::class);
    $user = User::factory()->create();
    $user->assignRole('admin');

    $response = $this->actingAs($user)->get(route('admin.transactions.index'));

    $response
        ->assertOk()
        ->assertViewIs('pages.admin.transactions.index')
        ->assertSee('Daftar transaksi')
        ->assertSee('TRX-2026-1024')
        ->assertSee('Andi Pratama')
        ->assertSee('Nova X Pro 256GB')
        ->assertSee('Rp 12.499.000')
        ->assertSee('Dibayar')
        ->assertSee('Menunggu')
        ->assertSee('Data transaksi ini hanya contoh dan belum terhubung ke basis data.')
        ->assertSee('aria-current="page"', false)
        ->assertSee(route('admin.transactions.index'));
});

it('redirects guests to login when they request admin transactions', function () {
    $response = $this->get(route('admin.transactions.index'));

    $response->assertRedirect(route('login'));
});
