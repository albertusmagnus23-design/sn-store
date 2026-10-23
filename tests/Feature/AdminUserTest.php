<?php

use App\Models\User;
use Database\Seeders\RolesSeeder;

it('renders sample users and marks the user menu active for a verified admin', function () {
    $this->seed(RolesSeeder::class);
    $user = User::factory()->create();
    $user->assignRole('admin');

    $response = $this->actingAs($user)->get(route('admin.users.index'));

    $response
        ->assertOk()
        ->assertViewIs('pages.admin.users.index')
        ->assertSee('Daftar user')
        ->assertSee('Andi Pratama')
        ->assertSee('andi.pratama@example.com')
        ->assertSee('Nadia Putri')
        ->assertSee('Bima Saputra')
        ->assertSee('Siti Rahma')
        ->assertSee('Rizky Maulana')
        ->assertSee('Menunggu verifikasi')
        ->assertSee('Data user ini hanya contoh dan belum terhubung ke basis data.')
        ->assertSee('aria-current="page"', false)
        ->assertSee(route('admin.users.index'));
});

it('redirects guests to login when they request admin users', function () {
    $response = $this->get(route('admin.users.index'));

    $response->assertRedirect(route('login'));
});
