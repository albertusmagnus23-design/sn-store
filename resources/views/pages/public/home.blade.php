@extends('layouts.public')

@section('title', 'Belanja Elektronik')
@section('meta_description', 'Belanja gadget, audio, dan elektronik pilihan di SN Store. Produk original, harga kompetitif, dan pengiriman cepat.')

@php
    $slides = [
        [
            'eyebrow' => 'Teknologi yang menginspirasi',
            'title' => 'Upgrade cara kamu menikmati hari.',
            'description' => 'Temukan perangkat pilihan untuk bekerja, berkarya, dan menikmati setiap momen.',
            'image' => 'https://images.unsplash.com/photo-1519389950473-47ba0277781c?auto=format&fit=crop&w=2000&q=85',
            'alt' => 'Perangkat elektronik modern di atas meja kerja',
        ],
        [
            'eyebrow' => 'Pilihan terbaru',
            'title' => 'Performa hebat, untuk ide tanpa batas.',
            'description' => 'Laptop dan perangkat kerja pilihan yang siap menemani produktivitasmu.',
            'image' => 'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?auto=format&fit=crop&w=2000&q=85',
            'alt' => 'Laptop modern untuk bekerja dan berkarya',
        ],
        [
            'eyebrow' => 'Suara lebih hidup',
            'title' => 'Dengarkan dunia dengan cara baru.',
            'description' => 'Nikmati audio jernih dan desain nyaman dari koleksi audio pilihan kami.',
            'image' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?auto=format&fit=crop&w=2000&q=85',
            'alt' => 'Headphone audio premium berwarna hitam',
        ],
    ];
@endphp

@section('content')
    <div class="min-h-screen">
        <div class="bg-rose-700 px-4 py-2 text-center text-xs font-medium tracking-wide text-white sm:text-sm">
            Gratis ongkir untuk pesanan tertentu. <a href="#products" class="font-bold underline underline-offset-4">Belanja sekarang</a>
        </div>

        <header x-data="{ menuOpen: false, categoriesOpen: false }" class="relative z-30 border-b border-slate-200 bg-white">
            <nav aria-label="Navigasi utama" class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-4 sm:px-6 lg:px-8">
                <a href="{{ route('home') }}" class="flex shrink-0 items-center gap-2.5" aria-label="SN Store, beranda">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-rose-700 text-sm font-extrabold tracking-tight text-white shadow-sm shadow-rose-700/20">SN</span>
                    <span class="text-lg font-extrabold tracking-tight text-slate-950">sn<span class="text-rose-700">store</span></span>
                </a>

                <div class="hidden items-center gap-7 lg:flex">
                    <a href="{{ route('home') }}" class="text-sm font-semibold text-rose-700">Beranda</a>
                    <a href="{{ route('products.index') }}" class="text-sm font-medium text-slate-600 transition hover:text-rose-700">Produk</a>
                    <div class="relative" @click.outside="categoriesOpen = false">
                        <button type="button" @click="categoriesOpen = !categoriesOpen" :aria-expanded="categoriesOpen.toString()" class="flex items-center gap-1.5 text-sm font-medium text-slate-600 transition hover:text-rose-700">
                            Kategori
                            <svg class="h-4 w-4 transition" :class="{ 'rotate-180': categoriesOpen }" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M5.22 7.47a.75.75 0 0 1 1.06 0L10 11.19l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 8.53a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        <div x-cloak x-show="categoriesOpen" x-transition class="absolute left-0 top-full mt-3 w-52 rounded-2xl border border-slate-100 bg-white p-2 shadow-xl shadow-slate-900/10">
                            @forelse ($categories as $category)
                                <a href="{{ route('products.index', ['category' => $category->slug]) }}" class="block rounded-xl px-3 py-2.5 text-sm text-slate-600 transition hover:bg-rose-50 hover:text-rose-700">{{ $category->name }}</a>
                            @empty
                                <span class="block px-3 py-2.5 text-sm text-slate-500">Belum ada kategori.</span>
                            @endforelse
                        </div>
                    </div>
                </div>

                <form action="{{ route('products.index') }}" method="GET" role="search" class="hidden min-w-40 max-w-sm flex-1 lg:flex">
                    <label for="site-search" class="sr-only">Cari produk</label>
                    <div class="flex w-full items-center gap-2 rounded-full border border-slate-200 bg-slate-50 px-4 transition focus-within:border-rose-300 focus-within:bg-white focus-within:ring-4 focus-within:ring-rose-100">
                        <svg class="h-4 w-4 shrink-0 text-slate-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 1 0 3.44 9.79l3.64 3.64a.75.75 0 1 0 1.06-1.06l-3.64-3.64A5.5 5.5 0 0 0 9 3.5ZM5 9a4 4 0 1 1 8 0 4 4 0 0 1-8 0Z" clip-rule="evenodd" />
                        </svg>
                        <input id="site-search" name="q" type="search" placeholder="Cari produk..." class="w-full border-0 bg-transparent py-2.5 text-sm text-slate-700 placeholder:text-slate-400 focus:ring-0">
                        <button type="submit" class="sr-only">Cari</button>
                    </div>
                </form>

                <div class="hidden items-center gap-3 lg:flex">
                    @auth
                        <a href="{{ route('dashboard') }}" class="rounded-full bg-rose-700 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-rose-800">Dashboard</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="rounded-full border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:border-rose-200 hover:bg-rose-50 hover:text-rose-700">Logout</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="rounded-full px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-100">Login</a>
                        <a href="{{ route('register') }}" class="rounded-full bg-rose-700 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-rose-800">Daftar</a>
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

                <div class="flex items-center gap-2 lg:hidden">
                    <a href="{{ route('cart.index') }}" aria-label="Keranjang belanja, {{ $cartItemCount }} produk" class="relative flex h-10 w-10 items-center justify-center rounded-full border border-slate-200 text-slate-700">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l2.4 12.2a2 2 0 0 0 2 1.6h8.8a2 2 0 0 0 2-1.6L22 8H6" />
                            <circle cx="10" cy="20" r="1" />
                            <circle cx="18" cy="20" r="1" />
                        </svg>
                        <span class="absolute -right-1 -top-1 flex h-4 min-w-4 items-center justify-center rounded-full bg-rose-700 px-1 text-[10px] font-bold text-white">{{ $cartItemCount }}</span>
                    </a>
                    <button type="button" @click="menuOpen = !menuOpen" :aria-expanded="menuOpen.toString()" aria-label="Buka menu navigasi" class="flex h-10 w-10 items-center justify-center rounded-full border border-slate-200 text-slate-700">
                        <svg x-show="!menuOpen" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <path stroke-linecap="round" d="M4 7h16M4 12h16M4 17h16" />
                        </svg>
                        <svg x-cloak x-show="menuOpen" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <path stroke-linecap="round" d="m6 6 12 12M18 6 6 18" />
                        </svg>
                    </button>
                </div>
            </nav>

            <div x-cloak x-show="menuOpen" x-transition class="border-t border-slate-100 bg-white px-4 pb-5 pt-3 lg:hidden">
                <form action="{{ route('products.index') }}" method="GET" role="search" class="mb-3">
                    <label for="mobile-site-search" class="sr-only">Cari produk</label>
                    <input id="mobile-site-search" name="q" type="search" placeholder="Cari produk..." class="w-full rounded-full border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:border-rose-300 focus:ring-rose-100">
                </form>
                <div class="flex flex-col gap-1">
                    <a href="{{ route('home') }}" class="rounded-xl bg-rose-50 px-4 py-3 text-sm font-semibold text-rose-700">Beranda</a>
                    <a href="{{ route('products.index') }}" @click="menuOpen = false" class="rounded-xl px-4 py-3 text-sm font-medium text-slate-600 hover:bg-slate-50">Produk</a>
                    <div class="rounded-xl">
                        <button type="button" @click="categoriesOpen = !categoriesOpen" :aria-expanded="categoriesOpen.toString()" class="flex w-full items-center justify-between rounded-xl px-4 py-3 text-left text-sm font-medium text-slate-600 hover:bg-slate-50">
                            Kategori
                            <svg class="h-4 w-4 transition" :class="{ 'rotate-180': categoriesOpen }" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M5.22 7.47a.75.75 0 0 1 1.06 0L10 11.19l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 8.53a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        <div x-cloak x-show="categoriesOpen" x-transition class="flex flex-col gap-1 px-3 pb-2">
                            @forelse ($categories as $category)
                                <a href="{{ route('products.index', ['category' => $category->slug]) }}" @click="menuOpen = false; categoriesOpen = false" class="rounded-lg px-3 py-2 text-sm text-slate-500 hover:bg-rose-50 hover:text-rose-700">{{ $category->name }}</a>
                            @empty
                                <span class="px-3 py-2 text-sm text-slate-500">Belum ada kategori.</span>
                            @endforelse
                        </div>
                    </div>
                    @auth
                        <a href="{{ route('dashboard') }}" class="rounded-xl bg-rose-50 px-4 py-3 text-sm font-semibold text-rose-700 hover:bg-rose-100">Dashboard</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full rounded-xl px-4 py-3 text-left text-sm font-medium text-slate-600 hover:bg-slate-50">Logout</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="rounded-xl px-4 py-3 text-sm font-medium text-slate-600 hover:bg-slate-50">Login</a>
                        <a href="{{ route('register') }}" class="rounded-xl bg-rose-700 px-4 py-3 text-sm font-semibold text-white hover:bg-rose-800">Daftar</a>
                    @endauth
                </div>
            </div>
        </header>

        <main>
            <section x-data="{ activeSlide: 0, slideCount: {{ count($slides) }} }" aria-label="Promo pilihan SN Store" class="bg-white px-4 pb-10 pt-5 sm:px-6 sm:pb-14 sm:pt-8 lg:px-8">
                <div class="relative mx-auto max-w-7xl overflow-hidden rounded-[2rem] bg-slate-950 shadow-2xl shadow-rose-950/10">
                    @foreach ($slides as $index => $slide)
                        <div x-cloak x-show="activeSlide === {{ $index }}" class="relative min-h-[500px] sm:min-h-[520px]">
                            <img src="{{ $slide['image'] }}" alt="{{ $slide['alt'] }}" class="absolute inset-0 h-full w-full object-cover">
                            <div class="absolute inset-0 bg-gradient-to-r from-slate-950 via-slate-950/85 to-rose-950/35"></div>
                            <div class="relative flex min-h-[500px] max-w-2xl flex-col justify-center px-7 py-14 sm:min-h-[520px] sm:px-14 lg:px-20">
                                <span class="mb-5 inline-flex w-fit items-center gap-2 rounded-full border border-white/20 bg-white/10 px-3.5 py-2 text-xs font-semibold uppercase tracking-[0.18em] text-rose-100 backdrop-blur-sm">
                                    <span class="h-1.5 w-1.5 rounded-full bg-rose-400"></span>
                                    {{ $slide['eyebrow'] }}
                                </span>
                                <h1 class="max-w-xl text-4xl font-extrabold leading-[1.08] tracking-tight text-white sm:text-5xl lg:text-6xl">{{ $slide['title'] }}</h1>
                                <p class="mt-5 max-w-lg text-base leading-7 text-slate-200 sm:text-lg">{{ $slide['description'] }}</p>
                                <div class="mt-8 flex flex-wrap items-center gap-4">
                                    <a href="{{ route('products.index') }}" class="inline-flex items-center gap-2 rounded-full bg-rose-600 px-6 py-3.5 text-sm font-bold text-white shadow-lg shadow-rose-900/30 transition hover:bg-rose-500">
                                        Jelajahi produk
                                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                            <path fill-rule="evenodd" d="M3 10a.75.75 0 0 1 .75-.75h10.69L10.22 5.03a.75.75 0 1 1 1.06-1.06l5.5 5.5a.75.75 0 0 1 0 1.06l-5.5 5.5a.75.75 0 1 1-1.06-1.06l4.22-4.22H3.75A.75.75 0 0 1 3 10Z" clip-rule="evenodd" />
                                        </svg>
                                    </a>
                                    <span class="text-sm font-medium text-white/75">Produk pilihan, pengalaman terbaik.</span>
                                </div>
                            </div>
                        </div>
                    @endforeach

                    <div class="absolute bottom-6 right-6 flex items-center gap-2 sm:bottom-8 sm:right-10">
                        <button type="button" @click="activeSlide = (activeSlide + slideCount - 1) % slideCount" aria-label="Promo sebelumnya" class="flex h-10 w-10 items-center justify-center rounded-full border border-white/30 bg-slate-950/30 text-white backdrop-blur transition hover:bg-white hover:text-slate-900">
                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M12.78 15.53a.75.75 0 0 1-1.06 0l-5-5a.75.75 0 0 1 0-1.06l5-5a.75.75 0 1 1 1.06 1.06L8.31 10l4.47 4.47a.75.75 0 0 1 0 1.06Z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        @foreach ($slides as $index => $slide)
                            <button type="button" @click="activeSlide = {{ $index }}" aria-label="Tampilkan promo {{ $index + 1 }}" :aria-current="activeSlide === {{ $index }} ? 'true' : 'false'" :class="activeSlide === {{ $index }} ? 'w-7 bg-white' : 'w-2 bg-white/50 hover:bg-white/80'" class="h-2 rounded-full transition-all"></button>
                        @endforeach
                        <button type="button" @click="activeSlide = (activeSlide + 1) % slideCount" aria-label="Promo berikutnya" class="flex h-10 w-10 items-center justify-center rounded-full border border-white/30 bg-slate-950/30 text-white backdrop-blur transition hover:bg-white hover:text-slate-900">
                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M7.22 4.47a.75.75 0 0 1 1.06 0l5 5a.75.75 0 0 1 0 1.06l-5 5a.75.75 0 1 1-1.06-1.06L11.69 10 7.22 5.53a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                    <span class="absolute bottom-8 left-7 text-xs font-semibold tracking-widest text-white/70 sm:left-14 lg:left-20">0<span x-text="activeSlide + 1"></span> / 0{{ count($slides) }}</span>
                </div>
            </section>

            <section id="categories" class="px-4 py-10 sm:px-6 sm:py-14 lg:px-8">
                <div class="mx-auto max-w-7xl">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-[0.2em] text-rose-700">Jelajahi koleksi</p>
                            <h2 class="mt-2 text-2xl font-extrabold tracking-tight text-slate-950 sm:text-3xl">Belanja berdasarkan kategori</h2>
                        </div>
                        <a href="{{ route('products.index') }}" class="inline-flex w-fit items-center gap-2 text-sm font-bold text-rose-700 transition hover:text-rose-800">
                            Lihat semua produk
                            <span aria-hidden="true">&rarr;</span>
                        </a>
                    </div>
                    <div class="mt-7 grid grid-cols-2 gap-3 sm:grid-cols-4 sm:gap-4">
                        @forelse ($categories as $category)
                            <a href="{{ route('products.index', ['category' => $category->slug]) }}" class="group flex items-center gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm transition hover:-translate-y-1 hover:border-rose-200 hover:shadow-md sm:gap-4 sm:p-5">
                                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-rose-50 text-rose-700 transition group-hover:bg-rose-700 group-hover:text-white">
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><rect x="3.75" y="3.75" width="16.5" height="16.5" rx="2" /><path stroke-linecap="round" d="M8 8h8M8 12h8M8 16h4" /></svg>
                                </span>
                                <span>
                                    <span class="block text-sm font-bold text-slate-900">{{ $category->name }}</span>
                                    <span class="mt-1 block text-xs text-slate-500">{{ $category->description ?: 'Jelajahi produk kategori ini' }}</span>
                                </span>
                            </a>
                        @empty
                            <p class="col-span-full rounded-2xl border border-dashed border-slate-300 p-6 text-center text-sm text-slate-500">Belum ada kategori yang tersedia.</p>
                        @endforelse
                    </div>
                </div>
            </section>

            <section id="products" class="bg-white px-4 py-14 sm:px-6 sm:py-20 lg:px-8">
                <div class="mx-auto max-w-7xl">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-[0.2em] text-rose-700">Dikurasi untukmu</p>
                            <h2 class="mt-2 text-3xl font-extrabold tracking-tight text-slate-950 sm:text-4xl">Produk pilihan terbaru</h2>
                            <p class="mt-3 max-w-xl text-sm leading-6 text-slate-500 sm:text-base">Temukan perangkat favorit dengan kualitas yang bisa kamu andalkan setiap hari.</p>
                        </div>
                        <a href="{{ route('products.index') }}" class="inline-flex w-fit items-center gap-2 rounded-full border border-slate-200 px-5 py-2.5 text-sm font-semibold text-slate-700 transition hover:border-rose-200 hover:bg-rose-50 hover:text-rose-700">
                            Semua produk
                            <span aria-hidden="true">&rarr;</span>
                        </a>
                    </div>

                    <div class="mt-9 grid grid-cols-2 gap-3 sm:mt-10 sm:grid-cols-2 sm:gap-5 lg:grid-cols-5">
                        @forelse ($products as $product)
                            <article class="group overflow-hidden rounded-2xl border border-slate-200 bg-white transition duration-300 hover:-translate-y-1 hover:border-rose-200 hover:shadow-xl hover:shadow-slate-900/5">
                                <a href="{{ route('products.index', ['category' => $product->category->slug]) }}" class="relative block overflow-hidden bg-slate-100">
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
                                </a>
                                <div class="p-3.5 sm:p-4">
                                    <p class="text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-400 sm:text-[11px]">{{ $product->category->name }}</p>
                                    <h3 class="mt-1.5 min-h-10 text-sm font-bold leading-5 text-slate-900 sm:text-base">{{ $product->name }}</h3>
                                    <div class="mt-3 flex items-center justify-between gap-2">
                                        <p class="text-sm font-extrabold tracking-tight text-slate-950 sm:text-base">Rp {{ number_format((float) $product->price, 0, ',', '.') }}</p>
                                        @if ($product->available_stock > 0)
                                            @auth
                                                <form method="POST" action="{{ route('cart.items.store', $product) }}">
                                                    @csrf
                                                    <input type="hidden" name="quantity" value="1">
                                                    <button type="submit" aria-label="Tambah {{ $product->name }} ke keranjang" class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-rose-700 text-white transition hover:bg-rose-800">
                                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l2.4 12.2a2 2 0 0 0 2 1.6h8.8a2 2 0 0 0 2-1.6L22 8H6" />
                                                            <circle cx="10" cy="20" r="1" />
                                                            <circle cx="18" cy="20" r="1" />
                                                        </svg>
                                                    </button>
                                                </form>
                                            @else
                                                <a href="{{ route('login') }}" aria-label="Tambah {{ $product->name }} ke keranjang (login diperlukan)" class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-rose-700 text-white transition hover:bg-rose-800">
                                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l2.4 12.2a2 2 0 0 0 2 1.6h8.8a2 2 0 0 0 2-1.6L22 8H6" />
                                                        <circle cx="10" cy="20" r="1" />
                                                        <circle cx="18" cy="20" r="1" />
                                                    </svg>
                                                </a>
                                            @endauth
                                        @else
                                            <span class="text-xs font-semibold text-slate-400">Stok habis</span>
                                        @endif
                                    </div>
                                </div>
                            </article>
                        @empty
                            <p class="col-span-full rounded-2xl border border-dashed border-slate-300 p-8 text-center text-sm text-slate-500">Belum ada produk yang tersedia.</p>
                        @endforelse
                    </div>
                </div>
            </section>

            <section class="px-4 py-14 sm:px-6 sm:py-20 lg:px-8">
                <div class="relative mx-auto max-w-7xl overflow-hidden rounded-[2rem] bg-rose-700 px-7 py-12 sm:px-12 sm:py-14 lg:px-16">
                    <div class="absolute -right-10 -top-20 h-64 w-64 rounded-full border-[40px] border-white/10"></div>
                    <div class="absolute -bottom-32 right-1/4 h-64 w-64 rounded-full bg-rose-600/60 blur-2xl"></div>
                    <div class="relative flex flex-col gap-8 sm:flex-row sm:items-center sm:justify-between">
                        <div class="max-w-2xl">
                            <p class="text-xs font-bold uppercase tracking-[0.2em] text-rose-100">Saatnya upgrade</p>
                            <h2 class="mt-3 text-3xl font-extrabold leading-tight tracking-tight text-white sm:text-4xl">Teknologi terbaik, tinggal selangkah lagi.</h2>
                            <p class="mt-3 max-w-xl text-sm leading-6 text-rose-100 sm:text-base">Cari perangkat yang paling cocok untukmu dan nikmati pengalaman belanja yang mudah di SN Store.</p>
                        </div>
                        <a href="{{ route('products.index') }}" class="inline-flex w-fit shrink-0 items-center gap-2 rounded-full bg-white px-6 py-3.5 text-sm font-bold text-rose-700 shadow-lg shadow-rose-950/10 transition hover:bg-rose-50">
                            Mulai belanja
                            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M3 10a.75.75 0 0 1 .75-.75h10.69L10.22 5.03a.75.75 0 1 1 1.06-1.06l5.5 5.5a.75.75 0 0 1 0 1.06l-5.5 5.5a.75.75 0 1 1-1.06-1.06l4.22-4.22H3.75A.75.75 0 0 1 3 10Z" clip-rule="evenodd" />
                            </svg>
                        </a>
                    </div>
                </div>
            </section>
        </main>

        <footer class="bg-slate-950 px-4 pb-8 pt-14 text-slate-300 sm:px-6 sm:pt-16 lg:px-8">
            <div class="mx-auto max-w-7xl">
                <div class="grid gap-10 border-b border-white/10 pb-10 sm:grid-cols-2 lg:grid-cols-4 lg:gap-12">
                    <div class="sm:col-span-2 lg:col-span-2">
                        <a href="{{ route('home') }}" class="inline-flex items-center gap-2.5" aria-label="SN Store, beranda">
                            <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-rose-700 text-sm font-extrabold tracking-tight text-white">SN</span>
                            <span class="text-lg font-extrabold tracking-tight text-white">sn<span class="text-rose-400">store</span></span>
                        </a>
                        <p class="mt-5 max-w-md text-sm leading-7 text-slate-400">SN Store menghadirkan berbagai pilihan elektronik dan gadget berkualitas untuk mendukung aktivitas digitalmu. Belanja nyaman, pilihan terpercaya.</p>
                        <p class="mt-5 text-sm font-semibold text-white">Teknologi untuk semua.</p>
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-white">Jelajahi</h2>
                        <ul class="mt-4 flex flex-col gap-3 text-sm text-slate-400">
                            <li><a href="{{ route('home') }}" class="transition hover:text-white">Beranda</a></li>
                            <li><a href="{{ route('products.index') }}" class="transition hover:text-white">Semua produk</a></li>
                            <li><a href="#categories" class="transition hover:text-white">Kategori</a></li>
                            <li><a href="{{ route('login') }}" class="transition hover:text-white">Masuk</a></li>
                        </ul>
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-white">Hubungi kami</h2>
                        <ul class="mt-4 flex flex-col gap-3 text-sm text-slate-400">
                            <li><a href="mailto:halo@snstore.id" class="transition hover:text-white">halo@snstore.id</a></li>
                            <li><a href="tel:+62215550123" class="transition hover:text-white">+62 21 5550 123</a></li>
                            <li>Senin - Sabtu, 09.00 - 18.00 WIB</li>
                        </ul>
                    </div>
                </div>
                <div class="flex flex-col gap-3 pt-6 text-xs text-slate-500 sm:flex-row sm:items-center sm:justify-between">
                    <p>&copy; {{ now()->year }} SN Store. Hak cipta dilindungi.</p>
                    <p>Belanja elektronik dengan lebih nyaman.</p>
                </div>
            </div>
        </footer>
    </div>
@endsection
