@extends('layouts.public')

@section('title', 'Pesanan '.$order->order_number)

@section('content')
    <div class="min-h-screen">
        <header class="border-b border-slate-200 bg-white">
            <nav class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-4 sm:px-6 lg:px-8">
                <a href="{{ route('home') }}" class="text-lg font-extrabold tracking-tight text-slate-950">sn<span class="text-rose-700">store</span></a>
                <a href="{{ route('products.index') }}" class="text-sm font-semibold text-slate-600 hover:text-rose-700">Lanjut belanja</a>
            </nav>
        </header>

        <main class="mx-auto max-w-3xl px-4 py-10 sm:px-6 sm:py-14">
            @if (session('status'))
                <div role="status" class="mb-6 rounded-xl bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">{{ session('status') }}</div>
            @endif
            <section class="rounded-2xl border border-slate-200 bg-white p-6 sm:p-8">
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-rose-700">Pesanan berhasil dibuat</p>
                <h1 class="mt-2 text-2xl font-extrabold tracking-tight text-slate-950">Nomor pesanan {{ $order->order_number }}</h1>
                <p class="mt-3 text-sm text-slate-600">
                    Status:
                    <span class="font-bold {{ $order->status === 'paid' ? 'text-emerald-700' : ($order->status === 'failed' ? 'text-rose-700' : 'text-amber-700') }}">
                        {{ match ($order->status) { 'paid' => 'Lunas', 'failed' => 'Pembayaran gagal', 'cancelled' => 'Dibatalkan', default => 'Menunggu pembayaran' } }}
                    </span>
                </p>

                @if ($order->status === 'pending')
                    @error('payment')
                        <p role="alert" class="mt-4 rounded-xl bg-rose-50 p-4 text-sm leading-6 text-rose-800">{{ $message }}</p>
                    @enderror
                    @if ($midtransConfigured)
                        <div class="mt-5 rounded-xl bg-emerald-50 p-4 text-sm leading-6 text-emerald-900">
                            Pembayaran diproses dengan aman oleh Midtrans. Stok baru dikurangi setelah pembayaran berhasil diverifikasi.
                        </div>
                        <form method="POST" action="{{ route('orders.payment', $order) }}" class="mt-4">
                            @csrf
                            <button type="submit" class="rounded-xl bg-rose-700 px-5 py-3 text-sm font-bold text-white transition hover:bg-rose-800">Bayar dengan Midtrans</button>
                        </form>
                    @else
                        <div class="mt-5 rounded-xl bg-amber-50 p-4 text-sm leading-6 text-amber-900">
                            Midtrans belum dikonfigurasi. Tambahkan MIDTRANS_MERCHANT_ID, MIDTRANS_SERVER_KEY, dan MIDTRANS_CLIENT_KEY di environment aplikasi.
                        </div>
                    @endif
                    @if ($order->payment_token === null)
                        <form method="POST" action="{{ route('orders.cancel', $order) }}" class="mt-4">
                            @csrf
                            <button type="submit" class="rounded-xl border border-rose-200 px-4 py-2.5 text-sm font-semibold text-rose-700 transition hover:bg-rose-50">Batalkan pesanan</button>
                        </form>
                    @endif
                @endif

                <div class="mt-7 border-t border-slate-100 pt-5">
                    <h2 class="font-bold text-slate-900">Detail produk</h2>
                    <ul class="mt-4 space-y-4">
                        @foreach ($order->items as $item)
                            <li class="flex justify-between gap-4 text-sm">
                                <span class="text-slate-600">{{ $item->product_name }} <span class="text-slate-400">x{{ $item->quantity }}</span></span>
                                <span class="shrink-0 font-semibold text-slate-900">Rp {{ number_format((float) $item->line_total, 0, ',', '.') }}</span>
                            </li>
                        @endforeach
                    </ul>
                    <div class="mt-5 flex justify-between border-t border-slate-100 pt-4">
                        <span class="font-semibold text-slate-600">Total pesanan</span>
                        <span class="font-extrabold text-slate-950">Rp {{ number_format((float) $order->total, 0, ',', '.') }}</span>
                    </div>
                </div>

                <div class="mt-7 border-t border-slate-100 pt-5">
                    <h2 class="font-bold text-slate-900">Alamat pengiriman</h2>
                    <p class="mt-2 text-sm leading-6 text-slate-600">{{ $order->customer_name }}<br>{{ $order->customer_phone }}<br>{{ $order->customer_email }}<br>{{ $order->customer_address }}</p>
                </div>
            </section>
        </main>
    </div>
@endsection
