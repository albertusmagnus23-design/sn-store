<?php

use App\Models\User;
use Database\Seeders\AdminUserSeeder;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Support\Facades\Hash;

it('seeds the admin account with the requested credentials and role', function () {
    $this->seed(DatabaseSeeder::class);

    $admin = User::where('email', 'admin1@gmail.com')->firstOrFail();

    $this->assertDatabaseHas('users', [
        'name' => 'admin1',
        'email' => 'admin1@gmail.com',
    ]);
    expect(Hash::check('admin123', $admin->password))->toBeTrue();
    expect($admin->hasRole('admin'))->toBeTrue();
});

it('does not create duplicate admin accounts when seeded repeatedly', function () {
    $this->seed(AdminUserSeeder::class);
    $this->seed(AdminUserSeeder::class);

    $this->assertDatabaseCount('users', 1);
});
