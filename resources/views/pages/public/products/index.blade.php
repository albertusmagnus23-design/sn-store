@extends('layouts.public')

@section('title', 'Semua Produk')
@section('meta_description', 'Cari dan jelajahi produk elektronik pilihan di SN Store.')

@section('content')
    <div class="min-h-screen">
        <header class="border-b border-slate-200 bg-white">
            <nav aria-label="Navigasi utama" class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-4 sm:px-6 lg:px-8">
                <a href="{{ route('home') }}" class="flex shrink-0 items-center gap-2.5" aria-label="SN Store, beranda">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-rose-700 text-sm font-extrabold tracking-tight text-white shadow-sm shadow-rose-700/20">SN</span>
                    <span class="text-lg font-extrabold tracking-tight text-slate-950">sn<span class="text-rose-700">store</span></span>
                </a>

                <div class="flex items-center gap-3">
                    <a href="{{ route('home') }}" class="hidden text-sm font-medium text-slate-600 transition hover:text-rose-700 sm:inline">Beranda</a>
                    <a href="{{ route('products.index') }}" aria-current="page" class="text-sm font-semibold text-rose-700">Produk</a>
                    @auth
                        <a href="{{ route('dashboard') }}" class="rounded-full bg-rose-700 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-rose-800">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="rounded-full px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-100">Login</a>
                    @endauth
                    <a href="{{ route('cart.index') }}" aria-label="Keranjang belanja, {{ $cartItemCount }} produk" class="relative flex h-10 w-10 items-center justify-center rounded-full border border-slate-200 text-slate-700 transition hover:border-rose-200 hover:bg-rose-50 hover:text-rose-700">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l2.4 12.2a2 2 0 0 0 2 1.6h8.8a2 2 0 0 0 2-1.6L22 8H6" />
                            <circle cx="10" cy="20" r="1" />
                            <circle cx="18" cy="20" r="1" />
                        </svg>
                        <span class="absolute -right-1 -top-1 flex h-4 min-w-4 items-center justify-center rounded-full bg-rose-700 px-1 text-[10px] font-bold text-white">{{ $cartItemCount }}</span>
                    </a>
                </div>
            </nav>
        </header>

        <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 sm:py-12 lg:px-8">
            <div class="mb-8">
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-rose-700">Katalog SN Store</p>
                <h1 class="mt-2 text-3xl font-extrabold tracking-tight text-slate-950 sm:text-4xl">Semua Produk</h1>
                <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-500">Temukan produk yang sesuai dengan kebutuhanmu melalui pencarian dan kategori.</p>
            </div>

            <div class="grid gap-8 lg:grid-cols-[240px_minmax(0,1fr)]">
                <aside aria-labelledby="category-filter-title" class="h-fit rounded-2xl border border-slate-200 bg-white p-5 shadow-sm lg:sticky lg:top-6">
                    <h2 id="category-filter-title" class="text-base font-bold text-slate-900">Kategori</h2>
                    <nav aria-label="Filter kategori produk" class="mt-4 flex flex-wrap gap-2 lg:flex-col lg:gap-1">
                        <a
                            href="{{ route('products.index', ['q' => $searchTerm ?: null]) }}"
                            @if ($selectedCategory === null) aria-current="page" @endif
                            class="rounded-xl px-3 py-2.5 text-sm font-medium transition {{ $selectedCategory === null ? 'bg-rose-50 text-rose-700' : 'text-slate-600 hover:bg-slate-50 hover:text-rose-700' }}"
                        >
                            Semua kategori
                        </a>
                        @foreach ($categories as $category)
                            <a
                                href="{{ route('products.index', ['category' => $category->slug, 'q' => $searchTerm ?: null]) }}"
                                @if ($selectedCategory === $category->slug) aria-current="page" @endif
                                class="rounded-xl px-3 py-2.5 text-sm font-medium transition {{ $selectedCategory === $category->slug ? 'bg-rose-50 text-rose-700' : 'text-slate-600 hover:bg-slate-50 hover:text-rose-700' }}"
                            >
                                {{ $category->name }}
                            </a>
                        @endforeach
                    </nav>
                </aside>

                <section aria-labelledby="product-list-title" class="min-w-0">
                    <form action="{{ route('products.index') }}" method="GET" role="search" class="mb-6">
                        @if ($selectedCategory)
                            <input type="hidden" name="category" value="{{ $selectedCategory }}">
                        @endif
                        <label for="product-search" class="sr-only">Cari nama atau SKU produk</label>
                        <div class="flex gap-2">
                            <input
                                id="product-search"
                                name="q"
                                type="search"
                                value="{{ $searchTerm }}"
                                placeholder="Cari nama atau SKU produk..."
                                class="min-w-0 flex-1 rounded-xl border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 placeholder:text-slate-400 focus:border-rose-300 focus:ring-rose-100"
                            >
                            <button type="submit" class="rounded-xl bg-rose-700 px-5 py-3 text-sm font-bold text-white transition hover:bg-rose-800 focus:outline-none focus:ring-2 focus:ring-rose-500 focus:ring-offset-2">Cari</button>
                        </div>
                    </form>

                    <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
                        <h2 id="product-list-title" class="text-lg font-bold text-slate-900">
                            @if ($selectedCategory)
                                {{ $categories->firstWhere('slug', $selectedCategory)?->name ?? 'Produk' }}
                            @else
                                Daftar Produk
                            @endif
                        </h2>
                        <p class="text-sm text-slate-500">Menampilkan {{ $products->count() }} dari {{ $products->total() }} produk</p>
                    </div>

                    <div class="grid grid-cols-2 gap-3 sm:grid-cols-2 sm:gap-5 xl:grid-cols-3">
                        @forelse ($products as $product)
                            <article class="group overflow-hidden rounded-2xl border border-slate-200 bg-white transition duration-300 hover:-translate-y-1 hover:border-rose-200 hover:shadow-xl hover:shadow-slate-900/5">
                                <div class="relative overflow-hidden bg-slate-100">
                                    @if ($product->image_path)
                                        <img src="{{ asset('storage/'.$product->image_path) }}" alt="{{ $product->name }}" loading="lazy" class="aspect-square w-full object-cover transition duration-500 group-hover:scale-105">
                                    @else
                                        <span class="flex aspect-square w-full items-center justify-center bg-slate-100 text-slate-400" aria-label="Gambar tidak tersedia">
                                            <svg class="h-10 w-10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m3.75 16.5 5.25-5.25 3 3 3.75-4.5 4.5 6.75M4.5 4.5h15a1.5 1.5 0 0 1 1.5 1.5v12a1.5 1.5 0 0 1-1.5 1.5h-15A1.5 1.5 0 0 1 3 18V6a1.5 1.5 0 0 1 1.5-1.5Z" />
                                            </svg>
                                        </span>
                                    @endif
                                    @if ($product->badge)
                                        <span class="absolute left-3 top-3 rounded-full bg-white/95 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide text-rose-700 shadow-sm sm:text-[11px]">{{ $product->badge }}</span>
                                    @endif
                                    @if ($product->available_stock < 1)
                                        <span class="absolute right-3 top-3 rounded-full bg-slate-900/80 px-2.5 py-1 text-[10px] font-bold text-white">Stok habis</span>
                                    @endif
                                </div>
                                <div class="p-3.5 sm:p-4">
                                    <p class="text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-400 sm:text-[11px]">{{ $product->category->name }}</p>
                                    <h3 class="mt-1.5 min-h-10 text-sm font-bold leading-5 text-slate-900 sm:text-base">{{ $product->name }}</h3>
                                    <p class="mt-1 text-xs text-slate-500">SKU: {{ $product->sku }}</p>
                                    <div class="mt-3 flex items-center justify-between gap-2">
                                        <p class="text-sm font-extrabold tracking-tight text-slate-950 sm:text-base">Rp {{ number_format((float) $product->price, 0, ',', '.') }}</p>
                                        @if ($product->available_stock > 0)
                                            @auth
                                                <form method="POST" action="{{ route('cart.items.store', $product) }}">
                                                    @csrf
                                                    <input type="hidden" name="quantity" value="1">
                                                    <button type="submit" aria-label="Tambah {{ $product->name }} ke keranjang" title="Tambah ke keranjang" class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-rose-700 text-white transition hover:bg-rose-800 focus:outline-none focus:ring-2 focus:ring-rose-500 focus:ring-offset-2">
                                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l2.4 12.2a2 2 0 0 0 2 1.6h8.8a2 2 0 0 0 2-1.6L22 8H6" />
                                                            <circle cx="10" cy="20" r="1" />
                                                            <circle cx="18" cy="20" r="1" />
                                                            <path stroke-linecap="round" d="M12 10v5m-2.5-2.5h5" />
                                                        </svg>
                                                    </button>
                                                </form>
                                            @else
                                                <a href="{{ route('login') }}" aria-label="Tambah {{ $product->name }} ke keranjang (login diperlukan)" title="Login untuk menambah ke keranjang" class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-rose-700 text-white transition hover:bg-rose-800">
                                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l2.4 12.2a2 2 0 0 0 2 1.6h8.8a2 2 0 0 0 2-1.6L22 8H6" />
                                                        <circle cx="10" cy="20" r="1" />
                                                        <circle cx="18" cy="20" r="1" />
                                                        <path stroke-linecap="round" d="M12 10v5m-2.5-2.5h5" />
                                                    </svg>
                                                </a>
                                            @endauth
                                        @else
                                            <button type="button" disabled aria-label="{{ $product->name }} stok habis" title="Stok habis" class="flex h-9 w-9 shrink-0 cursor-not-allowed items-center justify-center rounded-full bg-slate-300 text-white">
                                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l2.4 12.2a2 2 0 0 0 2 1.6h8.8a2 2 0 0 0 2-1.6L22 8H6" />
                                                <circle cx="10" cy="20" r="1" />
                                                <circle cx="18" cy="20" r="1" />
                                                <path stroke-linecap="round" d="M12 10v5m-2.5-2.5h5" />
                                            </svg>
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </article>
                        @empty
                            <div class="col-span-full rounded-2xl border border-dashed border-slate-300 bg-white p-10 text-center">
                                <p class="text-base font-semibold text-slate-800">Produk tidak ditemukan</p>
                                <p class="mt-2 text-sm text-slate-500">Coba kata kunci lain atau pilih kategori yang berbeda.</p>
                                <a href="{{ route('products.index') }}" class="mt-4 inline-flex rounded-full bg-rose-700 px-4 py-2 text-sm font-semibold text-white transition hover:bg-rose-800">Hapus filter</a>
                            </div>
                        @endforelse
                    </div>

                    @if ($products->hasPages())
                        <div class="mt-8">
                            {{ $products->links() }}
                        </div>
                    @endif
                </section>
            </div>
        </main>
    </div>
@endsection
