<?php

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Database\Seeders\RolesSeeder;

it('renders the dashboard in the admin view structure for a verified admin', function () {
    $this->seed(RolesSeeder::class);
    $user = User::factory()->create();
    $user->assignRole('admin');
    $customer = User::factory()->create();
    $category = Category::factory()->create();
    Product::factory()->create(['category_id' => $category->id]);
    Order::query()->create([
        'order_number' => 'SN-DASHBOARD-001',
        'user_id' => $customer->id,
        'customer_name' => $customer->name,
        'customer_phone' => '+628123456789',
        'customer_email' => $customer->email,
        'customer_address' => 'Jl. Merdeka No. 10',
        'total' => '12500.00',
        'status' => Order::STATUS_PENDING,
    ]);

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response
        ->assertOk()
        ->assertViewIs('pages.admin.dashboard.index')
        ->assertSee('Modul manajemen')
        ->assertSee('Kategori')
        ->assertSee('Produk')
        ->assertSee('Transaksi')
        ->assertSee('User')
        ->assertViewHas('categoryCount', 1)
        ->assertViewHas('productCount', 1)
        ->assertViewHas('transactionCount', 1)
        ->assertViewHas('userCount', 2);
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
