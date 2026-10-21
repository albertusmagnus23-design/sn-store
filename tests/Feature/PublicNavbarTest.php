<?php

use App\Models\User;

it('shows login and registration links to guests in the public navigation', function () {
    $response = $this->get(route('home'));

    $response
        ->assertOk()
        ->assertSee('Login')
        ->assertSee('Daftar')
        ->assertSee(route('login'))
        ->assertSee(route('register'))
        ->assertDontSee('Dashboard');
});

it('shows a dashboard link to authenticated users in the public navigation', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('home'));

    $response
        ->assertOk()
        ->assertSee('Dashboard')
        ->assertSee(route('dashboard'))
        ->assertDontSee('Login')
        ->assertDontSee('Daftar');
});
