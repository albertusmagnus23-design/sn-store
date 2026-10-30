@extends('layouts.public')

@section('title', 'Keranjang Belanja')

@section('content')
    <div class="min-h-screen">
        <header class="border-b border-slate-200 bg-white">
            <nav class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-4 sm:px-6 lg:px-8">
                <a href="{{ route('home') }}" class="text-lg font-extrabold tracking-tight text-slate-950">sn<span class="text-rose-700">store</span></a>
                <div class="flex items-center gap-3">
                    <a href="{{ route('products.index') }}" class="text-sm font-medium text-slate-600 hover:text-rose-700">Lanjut belanja</a>
                    <a href="{{ route('cart.index') }}" aria-current="page" class="rounded-full bg-rose-50 px-4 py-2 text-sm font-semibold text-rose-700">Keranjang ({{ $cartItemCount }})</a>
                </div>
            </nav>
        </header>

        <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 sm:py-12 lg:px-8">
            <div class="mb-8">
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-rose-700">Belanjaanmu</p>
                <h1 class="mt-2 text-3xl font-extrabold tracking-tight text-slate-950 sm:text-4xl">Keranjang Belanja</h1>
            </div>

            @if (session('status'))
                <div role="status" class="mb-6 rounded-xl bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">{{ session('status') }}</div>
            @endif
            @if ($errors->any())
                <div role="alert" class="mb-6 rounded-xl bg-rose-50 px-4 py-3 text-sm text-rose-800">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            @if ($cartItems->isEmpty())
                <section class="rounded-2xl border border-dashed border-slate-300 bg-white p-10 text-center">
                    <h2 class="text-lg font-bold text-slate-900">Keranjangmu masih kosong</h2>
                    <p class="mt-2 text-sm text-slate-500">Jelajahi katalog dan tambahkan produk yang kamu inginkan.</p>
                    <a href="{{ route('products.index') }}" class="mt-5 inline-flex rounded-full bg-rose-700 px-5 py-3 text-sm font-semibold text-white hover:bg-rose-800">Jelajahi produk</a>
                </section>
            @else
                <div class="grid gap-8 lg:grid-cols-[minmax(0,1fr)_340px]">
                    <section class="space-y-4" aria-label="Produk di keranjang">
                        @foreach ($cartItems as $cartItem)
                            <article class="flex flex-col gap-4 rounded-2xl border border-slate-200 bg-white p-4 sm:flex-row sm:items-center">
                                @if ($cartItem->product->image_path)
                                    <img src="{{ asset('storage/'.$cartItem->product->image_path) }}" alt="{{ $cartItem->product->name }}" class="h-24 w-24 rounded-xl bg-slate-100 object-cover">
                                @else
                                    <div class="flex h-24 w-24 items-center justify-center rounded-xl bg-slate-100 text-xs text-slate-400">Tanpa gambar</div>
                                @endif
                                <div class="min-w-0 flex-1">
                                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">{{ $cartItem->product->category->name }}</p>
                                    <h2 class="mt-1 font-bold text-slate-900">{{ $cartItem->product->name }}</h2>
                                    <p class="mt-1 text-sm text-slate-500">Rp {{ number_format((float) $cartItem->product->price, 0, ',', '.') }}</p>
                                    @if ($cartItem->product->available_stock < $cartItem->quantity)
                                        <p class="mt-2 text-xs font-medium text-rose-700">Stok tersedia berubah. Sesuaikan jumlah sebelum checkout.</p>
                                    @endif
                                </div>
                                <div class="flex items-center justify-between gap-4 sm:justify-end">
                                    <form method="POST" action="{{ route('cart.items.update', $cartItem) }}" class="flex items-center gap-2">
                                        @csrf
                                        @method('PUT')
                                        <label for="quantity-{{ $cartItem->id }}" class="sr-only">Jumlah {{ $cartItem->product->name }}</label>
                                        <input id="quantity-{{ $cartItem->id }}" name="quantity" type="number" min="1" max="{{ max(1, $cartItem->product->available_stock) }}" value="{{ $cartItem->quantity }}" class="w-20 rounded-lg border-slate-300 text-sm focus:border-rose-400 focus:ring-rose-200">
                                        <button type="submit" class="rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">Perbarui</button>
                                    </form>
                                    <form method="POST" action="{{ route('cart.items.destroy', $cartItem) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-sm font-semibold text-rose-700 hover:text-rose-900">Hapus</button>
                                    </form>
                                </div>
                            </article>
                        @endforeach
                    </section>

                    <aside class="h-fit rounded-2xl border border-slate-200 bg-white p-6">
                        <h2 class="text-lg font-bold text-slate-900">Ringkasan</h2>
                        <div class="mt-5 flex justify-between gap-4 border-t border-slate-100 pt-4 text-sm">
                            <span class="text-slate-500">Subtotal</span>
                            <span class="font-bold text-slate-900">Rp {{ number_format($cartTotal, 0, ',', '.') }}</span>
                        </div>
                        <p class="mt-2 text-xs text-slate-500">Biaya pengiriman dihitung terpisah.</p>
                        <a href="{{ route('checkout.create') }}" class="mt-6 inline-flex w-full justify-center rounded-xl bg-rose-700 px-5 py-3 text-sm font-bold text-white transition hover:bg-rose-800">Lanjut ke checkout</a>
                    </aside>
                </div>
            @endif
        </main>
    </div>
@endsection
