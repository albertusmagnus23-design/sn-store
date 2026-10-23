<?php

use App\Models\User;
use Database\Seeders\RolesSeeder;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

it('seeds customer and admin roles idempotently', function () {
    $this->seed();
    $this->seed(RolesSeeder::class);
    $this->seed(RolesSeeder::class);

    $this->assertDatabaseHas('roles', ['name' => 'customer', 'guard_name' => 'web']);
    $this->assertDatabaseHas('roles', ['name' => 'admin', 'guard_name' => 'web']);
    $this->assertDatabaseCount('roles', 2);
});

it('checks permissions inherited through assigned roles', function () {
    $user = User::factory()->create();
    $role = Role::create(['name' => 'catalog-manager', 'guard_name' => 'web']);
    $permission = Permission::create(['name' => 'products.view', 'guard_name' => 'web']);
    $role->givePermissionTo($permission);

    $user->assignRole($role);

    expect($user->hasRole('catalog-manager'))->toBeTrue()
        ->and($user->can('products.view'))->toBeTrue();
});

it('protects routes using the registered role and permission middleware aliases', function () {
    Route::get('/permission-test/role', fn () => response()->noContent())
        ->middleware(['auth', 'role:catalog-manager']);
    Route::get('/permission-test/permission', fn () => response()->noContent())
        ->middleware(['auth', 'permission:products.view']);
    Route::get('/permission-test/role-or-permission', fn () => response()->noContent())
        ->middleware(['auth', 'role_or_permission:catalog-manager|products.view']);

    $role = Role::create(['name' => 'catalog-manager', 'guard_name' => 'web']);
    $permission = Permission::create(['name' => 'products.view', 'guard_name' => 'web']);
    $role->givePermissionTo($permission);
    $manager = User::factory()->create();
    $manager->assignRole($role);
    $customer = User::factory()->create();

    $this->actingAs($manager)->get('/permission-test/role')->assertNoContent();
    $this->actingAs($manager)->get('/permission-test/permission')->assertNoContent();
    $this->actingAs($manager)->get('/permission-test/role-or-permission')->assertNoContent();
    $this->actingAs($customer)->get('/permission-test/role')->assertForbidden();
    $this->actingAs($customer)->get('/permission-test/permission')->assertForbidden();
    $this->actingAs($customer)->get('/permission-test/role-or-permission')->assertForbidden();
});
