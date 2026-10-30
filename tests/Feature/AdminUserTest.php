<?php

use App\Models\User;
use Database\Seeders\RolesSeeder;

it('renders users from the database with current verification summaries', function () {
    $this->seed(RolesSeeder::class);
    $admin = User::factory()->create(['name' => 'Admin Database']);
    $admin->assignRole('admin');
    $customer = User::factory()->create([
        'name' => 'Pelanggan Tersimpan',
        'email' => 'pelanggan@example.com',
    ]);
    $customer->assignRole('customer');
    $unverifiedCustomer = User::factory()->unverified()->create([
        'name' => 'Akun Belum Verifikasi',
        'email' => 'belum-verifikasi@example.com',
    ]);
    $unverifiedCustomer->assignRole('customer');

    $response = $this->actingAs($admin)->get(route('admin.users.index'));

    $response
        ->assertOk()
        ->assertViewIs('pages.admin.users.index')
        ->assertViewHas('totalUsers', 3)
        ->assertViewHas('activeUsers', 2)
        ->assertViewHas('pendingUsers', 1)
        ->assertSee('Daftar user')
        ->assertSee('Admin Database')
        ->assertSee('Pelanggan Tersimpan')
        ->assertSee('pelanggan@example.com')
        ->assertSee('Akun Belum Verifikasi')
        ->assertSee('belum-verifikasi@example.com')
        ->assertSee('Admin')
        ->assertSee('Pelanggan')
        ->assertSee('Menunggu verifikasi')
        ->assertSee('Data user ditampilkan dari database.')
        ->assertDontSee('Andi Pratama')
        ->assertSee('aria-current="page"', false)
        ->assertSee(route('admin.users.index'));
});

it('redirects guests to login when they request admin users', function () {
    $response = $this->get(route('admin.users.index'));

    $response->assertRedirect(route('login'));
});
