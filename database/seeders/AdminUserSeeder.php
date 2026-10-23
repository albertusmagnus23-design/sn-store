<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call(RolesSeeder::class);

        $admin = User::updateOrCreate(
            ['email' => 'admin1@gmail.com'],
            [
                'name' => 'admin1',
                'password' => 'admin123',
            ],
        );

        $admin->assignRole('admin');
    }
}
