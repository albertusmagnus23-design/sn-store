<?php

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Database\Seeders\RolesSeeder;

it('renders database orders and payment summaries for a verified admin', function () {
    $this->seed(RolesSeeder::class);
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $customer = User::factory()->create();
    $product = Product::factory()->create([
        'name' => 'Wireless Headphones',
        'price' => '12499000.00',
        'stock' => 4,
    ]);
    $paidOrder = Order::query()->create([
        'order_number' => 'SN-PAID-001',
        'user_id' => $customer->id,
        'customer_name' => 'Andi Pratama',
        'customer_phone' => '+628123456789',
        'customer_email' => $customer->email,
        'customer_address' => 'Jl. Merdeka No. 10',
        'total' => '12499000.00',
        'status' => Order::STATUS_PAID,
        'payment_reference' => 'PAYMENT-001',
        'paid_at' => now(),
    ]);
    $paidOrder->items()->create([
        'product_id' => $product->id,
        'product_name' => 'Wireless Headphones',
        'product_sku' => $product->sku,
        'unit_price' => '12499000.00',
        'quantity' => 1,
        'line_total' => '12499000.00',
    ]);
    $pendingOrder = Order::query()->create([
        'order_number' => 'SN-PENDING-002',
        'user_id' => $customer->id,
        'customer_name' => 'Nadia Putri',
        'customer_phone' => '+628123456789',
        'customer_email' => $customer->email,
        'customer_address' => 'Jl. Melati No. 2',
        'total' => '2000.00',
        'status' => Order::STATUS_PENDING,
    ]);
    $pendingOrder->items()->create([
        'product_id' => $product->id,
        'product_name' => 'Wireless Headphones',
        'product_sku' => $product->sku,
        'unit_price' => '2000.00',
        'quantity' => 1,
        'line_total' => '2000.00',
    ]);

    $response = $this->actingAs($admin)->get(route('admin.transactions.index'));

    $response
        ->assertOk()
        ->assertViewIs('pages.admin.transactions.index')
        ->assertSee('Daftar transaksi')
        ->assertSee('SN-PAID-001')
        ->assertSee('SN-PENDING-002')
        ->assertSee('Andi Pratama')
        ->assertSee('Wireless Headphones')
        ->assertSee('Rp 12.499.000')
        ->assertSee('Dibayar')
        ->assertSee('Menunggu')
        ->assertSee('Daftar pesanan tersimpan di basis data.')
        ->assertViewHas('totalTransactions', 2)
        ->assertViewHas('paidTransactions', 1)
        ->assertViewHas('pendingTransactions', 1)
        ->assertSee('aria-current="page"', false)
        ->assertSee(route('admin.transactions.index'));
});

it('shows an empty state when no orders have been placed', function () {
    $this->seed(RolesSeeder::class);
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $this->actingAs($admin)
        ->get(route('admin.transactions.index'))
        ->assertOk()
        ->assertSee('Belum ada transaksi.');
});

it('redirects guests to login when they request admin transactions', function () {
    $response = $this->get(route('admin.transactions.index'));

    $response->assertRedirect(route('login'));
});
