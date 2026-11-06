<?php

use App\Models\User;
use Database\Seeders\RolesSeeder;
use Illuminate\Support\Facades\Hash;

it('registers a customer without accepting a role from the request', function () {
    $this->seed(RolesSeeder::class);

    $response = $this->postJson('/api/register', [
        'name' => 'API Customer',
        'email' => 'api-customer@example.com',
        'password' => 'SecurePassword123!',
        'password_confirmation' => 'SecurePassword123!',
        'role' => 'admin',
    ]);

    $response
        ->assertCreated()
        ->assertJsonPath('code', 201)
        ->assertJsonPath('status', 'success')
        ->assertJsonPath('message', 'Registrasi berhasil.')
        ->assertJsonPath('data.name', 'API Customer')
        ->assertJsonPath('data.email', 'api-customer@example.com')
        ->assertJsonMissingPath('data.password')
        ->assertJsonMissingPath('data.token');

    $user = User::query()->where('email', 'api-customer@example.com')->firstOrFail();

    $this->assertModelExists($user);
    expect(Hash::check('SecurePassword123!', $user->password))->toBeTrue()
        ->and($user->hasRole('customer'))->toBeTrue()
        ->and($user->hasRole('admin'))->toBeFalse();
});

it('returns 422 when API registration data is incomplete', function () {
    $this->postJson('/api/register', [])
        ->assertUnprocessable()
        ->assertJsonPath('code', 422)
        ->assertJsonPath('status', 'error')
        ->assertJsonStructure([
            'data' => ['errors' => ['name', 'email', 'password']],
        ]);
});

it('returns 422 when API registration password confirmation does not match', function () {
    $this->postJson('/api/register', [
        'name' => 'API Customer',
        'email' => 'api-customer@example.com',
        'password' => 'SecurePassword123!',
        'password_confirmation' => 'different-password',
    ])
        ->assertUnprocessable()
        ->assertJsonStructure([
            'data' => ['errors' => ['password']],
        ]);

    $this->assertDatabaseCount('users', 0);
});

it('returns 401 when API login credentials are incorrect', function () {
    $user = User::factory()->create();

    $this->postJson('/api/login', [
        'email' => $user->email,
        'password' => 'incorrect-password',
    ])
        ->assertUnauthorized()
        ->assertExactJson([
            'code' => 401,
            'status' => 'error',
            'message' => 'Email atau password salah.',
            'data' => null,
        ]);

    $this->assertDatabaseCount('personal_access_tokens', 0);
});

it('returns a bearer token on API login that authenticates protected endpoints', function () {
    $user = User::factory()->create([
        'email' => 'api-login@example.com',
        'password' => 'SecurePassword123!',
    ]);

    $response = $this->postJson('/api/login', [
        'email' => $user->email,
        'password' => 'SecurePassword123!',
    ]);

    $response
        ->assertOk()
        ->assertJsonPath('code', 200)
        ->assertJsonPath('status', 'success')
        ->assertJsonPath('message', 'Login berhasil.')
        ->assertJsonPath('data.token_type', 'Bearer')
        ->assertJsonPath('data.user.id', $user->id)
        ->assertJsonMissingPath('data.user.password');

    $token = $response->json('data.token');

    expect($token)->toBeString()->not->toBeEmpty();

    $this->withToken($token)
        ->getJson('/api/user')
        ->assertOk()
        ->assertJsonPath('data.id', $user->id)
        ->assertJsonPath('data.email', 'api-login@example.com')
        ->assertJsonMissingPath('data.password');

    $this->assertDatabaseCount('personal_access_tokens', 1);
});

it('returns 401 when the bearer token is missing or invalid', function () {
    $this->getJson('/api/user')
        ->assertUnauthorized()
        ->assertExactJson([
            'code' => 401,
            'status' => 'error',
            'message' => 'Autentikasi diperlukan.',
            'data' => null,
        ]);

    $this->withToken('invalid-token')
        ->getJson('/api/user')
        ->assertUnauthorized();
});
