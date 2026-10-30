<?php

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Services\CheckoutService;
use Illuminate\Http\Client\Request as ClientRequest;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    config([
        'services.midtrans.merchant_id' => 'TEST-MERCHANT',
        'services.midtrans.server_key' => 'test-server-key',
        'services.midtrans.client_key' => 'test-client-key',
        'services.midtrans.is_production' => false,
        'services.midtrans.snap_url' => 'https://app.sandbox.midtrans.com',
    ]);
});

it('creates a Midtrans Snap session and redirects the owner to hosted checkout', function () {
    Http::preventStrayRequests();
    Http::fake([
        'app.sandbox.midtrans.com/snap/v1/transactions' => Http::response([
            'token' => 'snap-token-123',
            'redirect_url' => 'https://app.sandbox.midtrans.com/snap/v2/vtweb/snap-token-123',
        ]),
    ]);
    $user = User::factory()->create();
    $product = Product::factory()->create([
        'name' => 'Bluetooth Speaker',
        'price' => '12999.50',
        'stock' => 3,
    ]);
    $user->cartItems()->create([
        'product_id' => $product->id,
        'quantity' => 2,
    ]);
    $order = app(CheckoutService::class)->createPendingOrder($user, [
        'name' => $user->name,
        'phone' => '+628123456789',
        'email' => $user->email,
        'address' => 'Jl. Merdeka No. 10',
    ]);

    $this->actingAs($user)
        ->post(route('orders.payment', $order))
        ->assertRedirect('https://app.sandbox.midtrans.com/snap/v2/vtweb/snap-token-123');

    $this->assertDatabaseHas('orders', [
        'id' => $order->id,
        'payment_token' => 'snap-token-123',
        'payment_url' => 'https://app.sandbox.midtrans.com/snap/v2/vtweb/snap-token-123',
        'status' => Order::STATUS_PENDING,
    ]);
    Http::assertSent(fn (ClientRequest $request): bool => $request->url() === 'https://app.sandbox.midtrans.com/snap/v1/transactions'
        && $request->hasHeader('Authorization', 'Basic '.base64_encode('test-server-key:'))
        && $request['transaction_details']['order_id'] === $order->order_number
        && $request['transaction_details']['gross_amount'] === 26000
        && $request['item_details'][0]['price'] === 13000);
});

it('reuses an existing Midtrans payment session instead of creating duplicates', function () {
    Http::preventStrayRequests();
    Http::fake([
        'app.sandbox.midtrans.com/snap/v1/transactions' => Http::response([
            'token' => 'snap-token-once',
            'redirect_url' => 'https://app.sandbox.midtrans.com/snap/v2/vtweb/snap-token-once',
        ]),
    ]);
    $user = User::factory()->create();
    $product = Product::factory()->create([
        'price' => '10000.00',
        'stock' => 1,
    ]);
    $user->cartItems()->create([
        'product_id' => $product->id,
        'quantity' => 1,
    ]);
    $order = app(CheckoutService::class)->createPendingOrder($user, [
        'name' => $user->name,
        'phone' => '+628123456789',
        'email' => $user->email,
        'address' => 'Alamat pembeli',
    ]);

    $this->actingAs($user)
        ->post(route('orders.payment', $order))
        ->assertRedirect('https://app.sandbox.midtrans.com/snap/v2/vtweb/snap-token-once');
    $this->actingAs($user)
        ->post(route('orders.payment', $order))
        ->assertRedirect('https://app.sandbox.midtrans.com/snap/v2/vtweb/snap-token-once');

    Http::assertSentCount(1);
});

it('does not contact Midtrans when credentials are not configured', function () {
    Http::preventStrayRequests();
    Http::fake();
    config([
        'services.midtrans.merchant_id' => null,
        'services.midtrans.server_key' => null,
        'services.midtrans.client_key' => null,
    ]);
    $user = User::factory()->create();
    $product = Product::factory()->create(['stock' => 1]);
    $user->cartItems()->create([
        'product_id' => $product->id,
        'quantity' => 1,
    ]);
    $order = app(CheckoutService::class)->createPendingOrder($user, [
        'name' => $user->name,
        'phone' => '+628123456789',
        'email' => $user->email,
        'address' => 'Alamat pembeli',
    ]);

    $this->actingAs($user)
        ->from(route('orders.show', $order))
        ->post(route('orders.payment', $order))
        ->assertSessionHasErrors('payment');

    Http::assertNothingSent();
    expect($order->fresh()->payment_token)->toBeNull();
});

it('keeps the order pending when Midtrans rejects Snap session creation', function () {
    Http::preventStrayRequests();
    Http::fake([
        'app.sandbox.midtrans.com/snap/v1/transactions' => Http::response([
            'message' => 'Invalid credentials',
        ], 401),
    ]);
    $user = User::factory()->create();
    $product = Product::factory()->create(['stock' => 1]);
    $user->cartItems()->create([
        'product_id' => $product->id,
        'quantity' => 1,
    ]);
    $order = app(CheckoutService::class)->createPendingOrder($user, [
        'name' => $user->name,
        'phone' => '+628123456789',
        'email' => $user->email,
        'address' => 'Alamat pembeli',
    ]);

    $this->actingAs($user)
        ->from(route('orders.show', $order))
        ->post(route('orders.payment', $order))
        ->assertSessionHasErrors('payment');

    expect($order->fresh()->status)->toBe(Order::STATUS_PENDING);
    expect($order->fresh()->payment_token)->toBeNull();
    expect($product->fresh()->stock)->toBe(1);
    expect($product->fresh()->reserved_stock)->toBe(1);
});

it('marks a paid order from a signed Midtrans notification and reduces stock exactly once', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create([
        'price' => '10000.00',
        'stock' => 5,
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
    $notification = [
        'order_id' => $order->order_number,
        'status_code' => '200',
        'gross_amount' => '20000.00',
        'merchant_id' => 'TEST-MERCHANT',
        'transaction_status' => 'settlement',
        'fraud_status' => 'accept',
        'transaction_id' => 'midtrans-transaction-123',
    ];
    $notification['signature_key'] = hash(
        'sha512',
        $notification['order_id'].$notification['status_code'].$notification['gross_amount'].'test-server-key',
    );

    $this->postJson(route('midtrans.notifications'), $notification)->assertOk();
    $this->postJson(route('midtrans.notifications'), $notification)->assertOk();

    expect($order->fresh()->status)->toBe(Order::STATUS_PAID);
    expect($order->fresh()->payment_reference)->toBe('midtrans-transaction-123');
    expect($product->fresh()->stock)->toBe(3);
    expect($product->fresh()->reserved_stock)->toBe(0);
});

it('rejects a Midtrans notification with an invalid signature', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create([
        'price' => '10000.00',
        'stock' => 5,
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

    $this->postJson(route('midtrans.notifications'), [
        'order_id' => $order->order_number,
        'status_code' => '200',
        'gross_amount' => '20000.00',
        'merchant_id' => 'TEST-MERCHANT',
        'transaction_status' => 'settlement',
        'fraud_status' => 'accept',
        'transaction_id' => 'forged-payment',
        'signature_key' => str_repeat('0', 128),
    ])->assertUnauthorized();

    expect($order->fresh()->status)->toBe(Order::STATUS_PENDING);
    expect($product->fresh()->stock)->toBe(5);
    expect($product->fresh()->reserved_stock)->toBe(2);
});

it('rejects a validly signed notification from a different merchant', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create([
        'price' => '10000.00',
        'stock' => 1,
    ]);
    $user->cartItems()->create([
        'product_id' => $product->id,
        'quantity' => 1,
    ]);
    $order = app(CheckoutService::class)->createPendingOrder($user, [
        'name' => $user->name,
        'phone' => '+628123456789',
        'email' => $user->email,
        'address' => 'Alamat pembeli',
    ]);
    $notification = [
        'order_id' => $order->order_number,
        'status_code' => '200',
        'gross_amount' => '10000.00',
        'merchant_id' => 'UNTRUSTED-MERCHANT',
        'transaction_status' => 'settlement',
        'fraud_status' => 'accept',
        'transaction_id' => 'midtrans-transaction-untrusted',
    ];
    $notification['signature_key'] = hash(
        'sha512',
        $notification['order_id'].$notification['status_code'].$notification['gross_amount'].'test-server-key',
    );

    $this->postJson(route('midtrans.notifications'), $notification)->assertUnauthorized();

    expect($order->fresh()->status)->toBe(Order::STATUS_PENDING);
    expect($product->fresh()->stock)->toBe(1);
});

it('rejects a signed Midtrans notification with a different order amount', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create(['stock' => 1]);
    $user->cartItems()->create([
        'product_id' => $product->id,
        'quantity' => 1,
    ]);
    $order = app(CheckoutService::class)->createPendingOrder($user, [
        'name' => $user->name,
        'phone' => '+628123456789',
        'email' => $user->email,
        'address' => 'Alamat pembeli',
    ]);
    $notification = [
        'order_id' => $order->order_number,
        'status_code' => '200',
        'gross_amount' => '1.00',
        'merchant_id' => 'TEST-MERCHANT',
        'transaction_status' => 'settlement',
        'fraud_status' => 'accept',
        'transaction_id' => 'midtrans-transaction-456',
    ];
    $notification['signature_key'] = hash(
        'sha512',
        $notification['order_id'].$notification['status_code'].$notification['gross_amount'].'test-server-key',
    );

    $this->postJson(route('midtrans.notifications'), $notification)->assertUnprocessable();

    expect($order->fresh()->status)->toBe(Order::STATUS_PENDING);
    expect($product->fresh()->stock)->toBe(1);
});

it('releases reserved stock when Midtrans reports an expired transaction', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create([
        'price' => '10000.00',
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
    $notification = [
        'order_id' => $order->order_number,
        'status_code' => '200',
        'gross_amount' => '20000.00',
        'merchant_id' => 'TEST-MERCHANT',
        'transaction_status' => 'expire',
        'fraud_status' => 'accept',
        'transaction_id' => 'midtrans-expired-123',
    ];
    $notification['signature_key'] = hash(
        'sha512',
        $notification['order_id'].$notification['status_code'].$notification['gross_amount'].'test-server-key',
    );

    $this->postJson(route('midtrans.notifications'), $notification)->assertOk();

    expect($order->fresh()->status)->toBe(Order::STATUS_FAILED);
    expect($product->fresh()->stock)->toBe(4);
    expect($product->fresh()->reserved_stock)->toBe(0);
});
