<?php

use App\Models\CartItem;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Services\CheckoutService;
use App\Services\OrderPaymentService;

test('guest is redirected to login when adding a product to the cart', function () {
    $product = Product::factory()->create(['stock' => 3]);

    $this->post(route('cart.items.store', $product), ['quantity' => 1])
        ->assertRedirect(route('login'));

    $this->assertDatabaseCount('cart_items', 0);
});

test('authenticated user can add a product to cart and increase its quantity', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create(['stock' => 5]);

    $this->actingAs($user)
        ->post(route('cart.items.store', $product), ['quantity' => 2])
        ->assertRedirect(route('cart.index'));

    $this->actingAs($user)
        ->post(route('cart.items.store', $product), ['quantity' => 1])
        ->assertRedirect(route('cart.index'));

    $this->assertDatabaseHas('cart_items', [
        'user_id' => $user->id,
        'product_id' => $product->id,
        'quantity' => 3,
    ]);
});

test('product with no available stock cannot be added to cart', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create(['stock' => 0]);

    $this->actingAs($user)
        ->from(route('products.index'))
        ->post(route('cart.items.store', $product), ['quantity' => 1])
        ->assertSessionHasErrors('quantity');

    $this->assertDatabaseCount('cart_items', 0);
});

test('product reserved by another order cannot be added to cart', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create([
        'stock' => 3,
        'reserved_stock' => 3,
    ]);

    $this->actingAs($user)
        ->from(route('products.index'))
        ->post(route('cart.items.store', $product), ['quantity' => 1])
        ->assertSessionHasErrors('quantity');

    $this->assertDatabaseCount('cart_items', 0);
});

test('user cannot update another users cart item', function () {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();
    $product = Product::factory()->create(['stock' => 4]);
    $cartItem = $owner->cartItems()->create([
        'product_id' => $product->id,
        'quantity' => 1,
    ]);

    $this->actingAs($otherUser)
        ->put(route('cart.items.update', $cartItem), ['quantity' => 2])
        ->assertNotFound();

    expect($cartItem->fresh()->quantity)->toBe(1);
});

test('cart quantity cannot exceed currently available stock', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create(['stock' => 2]);
    $cartItem = $user->cartItems()->create([
        'product_id' => $product->id,
        'quantity' => 1,
    ]);

    $this->actingAs($user)
        ->from(route('cart.index'))
        ->put(route('cart.items.update', $cartItem), ['quantity' => 3])
        ->assertSessionHasErrors('quantity');

    expect($cartItem->fresh()->quantity)->toBe(1);
});

test('checkout is redirected to login for a guest', function () {
    $this->get(route('checkout.create'))
        ->assertRedirect(route('login'));

    $this->post(route('checkout.store'), [])
        ->assertRedirect(route('login'));
});

test('checkout form is prefilled with the signed in users contact details', function () {
    $user = User::factory()->create([
        'phone' => '+628123456789',
        'address' => 'Jl. Merdeka No. 10',
    ]);
    $product = Product::factory()->create(['stock' => 1]);
    $user->cartItems()->create([
        'product_id' => $product->id,
        'quantity' => 1,
    ]);

    $this->actingAs($user)
        ->get(route('checkout.create'))
        ->assertOk()
        ->assertSee('value="'.$user->name.'"', false)
        ->assertSee('value="'.$user->phone.'"', false)
        ->assertSee('value="'.$user->email.'"', false)
        ->assertSee($user->address);
});

test('checkout creates a pending order and reserves stock without reducing it', function () {
    $user = User::factory()->create([
        'phone' => null,
        'address' => null,
    ]);
    $product = Product::factory()->create([
        'name' => 'Wireless Headphones',
        'sku' => 'AUDIO-101',
        'price' => '12500.35',
        'stock' => 5,
    ]);
    $user->cartItems()->create([
        'product_id' => $product->id,
        'quantity' => 2,
    ]);

    $response = $this->actingAs($user)
        ->post(route('checkout.store'), [
            'name' => 'Checkout Customer',
            'phone' => '+628123456789',
            'email' => $user->email,
            'address' => 'Jl. Melati No. 4, Jakarta',
        ]);

    $order = Order::query()->firstOrFail();
    $response
        ->assertRedirect(route('orders.show', $order))
        ->assertSessionHasNoErrors();
    $this->get(route('orders.show', $order))
        ->assertOk()
        ->assertSee($order->order_number)
        ->assertSee('Menunggu pembayaran');

    $this->assertDatabaseHas('orders', [
        'id' => $order->id,
        'user_id' => $user->id,
        'customer_name' => 'Checkout Customer',
        'customer_phone' => '+628123456789',
        'customer_email' => $user->email,
        'customer_address' => 'Jl. Melati No. 4, Jakarta',
        'total' => '25000.00',
        'status' => Order::STATUS_PENDING,
    ]);
    $this->assertDatabaseHas('order_items', [
        'order_id' => $order->id,
        'product_id' => $product->id,
        'product_name' => 'Wireless Headphones',
        'product_sku' => 'AUDIO-101',
        'unit_price' => '12500.00',
        'quantity' => 2,
        'line_total' => '25000.00',
    ]);
    $this->assertDatabaseHas('products', [
        'id' => $product->id,
        'stock' => 5,
        'reserved_stock' => 2,
    ]);
    $this->assertDatabaseCount('cart_items', 0);
    expect($user->fresh()->phone)->toBe('+628123456789');
    expect($user->fresh()->address)->toBe('Jl. Melati No. 4, Jakarta');
});

test('checkout rejects a cart whose stock has changed', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create(['stock' => 3]);
    $user->cartItems()->create([
        'product_id' => $product->id,
        'quantity' => 2,
    ]);
    $product->update(['stock' => 1]);

    $this->actingAs($user)
        ->from(route('checkout.create'))
        ->post(route('checkout.store'), [
            'name' => $user->name,
            'phone' => '+628123456789',
            'email' => $user->email,
            'address' => 'Alamat pembeli',
        ])
        ->assertSessionHasErrors('cart');

    $this->assertDatabaseCount('orders', 0);
    expect($product->fresh()->reserved_stock)->toBe(0);
    expect(CartItem::query()->where('user_id', $user->id)->value('quantity'))->toBe(2);
});

test('checkout cannot reserve inventory already held for another pending order', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create([
        'stock' => 3,
        'reserved_stock' => 2,
    ]);
    $user->cartItems()->create([
        'product_id' => $product->id,
        'quantity' => 2,
    ]);

    $this->actingAs($user)
        ->from(route('checkout.create'))
        ->post(route('checkout.store'), [
            'name' => $user->name,
            'phone' => '+628123456789',
            'email' => $user->email,
            'address' => 'Alamat pembeli',
        ])
        ->assertSessionHasErrors('cart');

    $this->assertDatabaseCount('orders', 0);
    expect($product->fresh()->reserved_stock)->toBe(2);
});

test('checkout validates all required recipient details before creating an order', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create(['stock' => 1]);
    $user->cartItems()->create([
        'product_id' => $product->id,
        'quantity' => 1,
    ]);

    $this->actingAs($user)
        ->from(route('checkout.create'))
        ->post(route('checkout.store'), [])
        ->assertSessionHasErrors(['name', 'phone', 'email', 'address']);

    $this->assertDatabaseCount('orders', 0);
});

test('verified payment reduces stock once and repeated confirmation is idempotent', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create([
        'price' => '9000.00',
        'stock' => 4,
    ]);
    $user->cartItems()->create([
        'product_id' => $product->id,
        'quantity' => 2,
    ]);
    $order = app(CheckoutService::class)->createPendingOrder($user, [
        'name' => $user->name,
        'phone' => '+628123456789',
        'email' => $user->email,
        'address' => 'Alamat pembeli',
    ]);
    $paymentService = app(OrderPaymentService::class);

    $paymentService->markAsPaid($order, 'gateway-payment-123');
    $paymentService->markAsPaid($order->fresh(), 'gateway-payment-123');

    expect($order->fresh()->status)->toBe(Order::STATUS_PAID);
    expect($order->fresh()->payment_reference)->toBe('gateway-payment-123');
    expect($product->fresh()->stock)->toBe(2);
    expect($product->fresh()->reserved_stock)->toBe(0);
});

test('failed payment releases reserved stock without reducing inventory', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create(['stock' => 4]);
    $user->cartItems()->create([
        'product_id' => $product->id,
        'quantity' => 2,
    ]);
    $order = app(CheckoutService::class)->createPendingOrder($user, [
        'name' => $user->name,
        'phone' => '+628123456789',
        'email' => $user->email,
        'address' => 'Alamat pembeli',
    ]);

    app(OrderPaymentService::class)->markAsFailed($order, 'gateway-payment-failed');

    expect($order->fresh()->status)->toBe(Order::STATUS_FAILED);
    expect($product->fresh()->stock)->toBe(4);
    expect($product->fresh()->reserved_stock)->toBe(0);
});

test('customer can cancel a pending order and release its stock reservation', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create(['stock' => 3]);
    $user->cartItems()->create([
        'product_id' => $product->id,
        'quantity' => 2,
    ]);
    $order = app(CheckoutService::class)->createPendingOrder($user, [
        'name' => $user->name,
        'phone' => '+628123456789',
        'email' => $user->email,
        'address' => 'Alamat pembeli',
    ]);

    $this->actingAs($user)
        ->post(route('orders.cancel', $order))
        ->assertRedirect(route('orders.show', $order));

    expect($order->fresh()->status)->toBe(Order::STATUS_CANCELLED);
    expect($product->fresh()->stock)->toBe(3);
    expect($product->fresh()->reserved_stock)->toBe(0);
});

test('customer cannot cancel an order after a Midtrans session has been created', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create(['stock' => 3]);
    $user->cartItems()->create([
        'product_id' => $product->id,
        'quantity' => 2,
    ]);
    $order = app(CheckoutService::class)->createPendingOrder($user, [
        'name' => $user->name,
        'phone' => '+628123456789',
        'email' => $user->email,
        'address' => 'Alamat pembeli',
    ]);
    $order->forceFill([
        'payment_token' => 'snap-token-123',
        'payment_url' => 'https://app.sandbox.midtrans.com/snap/v2/vtweb/snap-token-123',
    ])->save();

    $this->actingAs($user)
        ->post(route('orders.cancel', $order))
        ->assertRedirect(route('orders.show', $order))
        ->assertSessionHasErrors('payment');

    expect($order->fresh()->status)->toBe(Order::STATUS_PENDING);
    expect($product->fresh()->stock)->toBe(3);
    expect($product->fresh()->reserved_stock)->toBe(2);
});

test('user cannot view another users order', function () {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();
    $product = Product::factory()->create(['stock' => 1]);
    $owner->cartItems()->create([
        'product_id' => $product->id,
        'quantity' => 1,
    ]);
    $order = app(CheckoutService::class)->createPendingOrder($owner, [
        'name' => $owner->name,
        'phone' => '+628123456789',
        'email' => $owner->email,
        'address' => 'Alamat pembeli',
    ]);

    $this->actingAs($otherUser)
        ->get(route('orders.show', $order))
        ->assertNotFound();
    $this->actingAs($otherUser)
        ->post(route('orders.cancel', $order))
        ->assertNotFound();

    expect($order->fresh()->status)->toBe(Order::STATUS_PENDING);
});
