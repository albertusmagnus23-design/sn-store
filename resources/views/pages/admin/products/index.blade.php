@extends('layouts.admin')

@section('title', 'Produk')
@section('breadcrumb', 'Produk')

@php
    $products = [
        [
            'name' => 'Nova X Pro 256GB',
            'category' => 'Smartphone',
            'sku' => 'SN-PHN-001',
            'price' => 'Rp 12.499.000',
            'stock' => 24,
            'status' => 'Tersedia',
            'image' => 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?auto=format&fit=crop&w=160&q=80',
            'badge' => 'Terlaris',
        ],
        [
            'name' => 'AeroBook 14 Ultra',
            'category' => 'Laptop & Komputer',
            'sku' => 'SN-LPT-002',
            'price' => 'Rp 18.999.000',
            'stock' => 12,
            'status' => 'Tersedia',
            'image' => 'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?auto=format&fit=crop&w=160&q=80',
            'badge' => null,
        ],
        [
            'name' => 'Studio Headphones S2',
            'category' => 'Audio',
            'sku' => 'SN-AUD-003',
            'price' => 'Rp 2.799.000',
            'stock' => 38,
            'status' => 'Tersedia',
            'image' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?auto=format&fit=crop&w=160&q=80',
            'badge' => 'Favorit',
        ],
        [
            'name' => 'Pulse Watch Active',
            'category' => 'Wearable',
            'sku' => 'SN-WRB-004',
            'price' => 'Rp 3.249.000',
            'stock' => 16,
            'status' => 'Tersedia',
            'image' => 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?auto=format&fit=crop&w=160&q=80',
            'badge' => 'Baru',
        ],
        [
            'name' => 'SoundPod Mini',
            'category' => 'Audio',
            'sku' => 'SN-AUD-005',
            'price' => 'Rp 899.000',
            'stock' => 0,
            'status' => 'Stok habis',
            'image' => 'https://images.unsplash.com/photo-1608043152269-423dbba4e7e1?auto=format&fit=crop&w=160&q=80',
            'badge' => null,
        ],
        [
            'name' => 'Nova X Lite 128GB',
            'category' => 'Smartphone',
            'sku' => 'SN-PHN-006',
            'price' => 'Rp 5.799.000',
            'stock' => 8,
            'status' => 'Stok menipis',
            'image' => 'https://images.unsplash.com/photo-1598327105666-5b89351aff97?auto=format&fit=crop&w=160&q=80',
            'badge' => 'Hemat',
        ],
        [
            'name' => 'Keychron K2 Wireless',
            'category' => 'Aksesori',
            'sku' => 'SN-ACC-007',
            'price' => 'Rp 1.499.000',
            'stock' => 21,
            'status' => 'Tersedia',
            'image' => 'https://images.unsplash.com/photo-1587829741301-dc798b83add3?auto=format&fit=crop&w=160&q=80',
            'badge' => null,
        ],
        [
            'name' => 'ViewPro 27 4K',
            'category' => 'Monitor',
            'sku' => 'SN-MON-008',
            'price' => 'Rp 6.899.000',
            'stock' => 9,
            'status' => 'Stok menipis',
            'image' => 'https://images.unsplash.com/photo-1527443224154-c4a3942d3acf?auto=format&fit=crop&w=160&q=80',
            'badge' => 'Terbaru',
        ],
    ];

    $totalStock = collect($products)->sum('stock');
    $availableProducts = collect($products)->where('status', 'Tersedia')->count();
    $lowStockProducts = collect($products)->where('status', 'Stok menipis')->count();
@endphp

@section('content')
    <div class="space-y-8">
        <div class="flex flex-col justify-between gap-5 sm:flex-row sm:items-end">
            <div>
                <p class="text-sm font-semibold text-rose-700">Katalog toko</p>
                <h1 class="mt-1 text-3xl font-extrabold tracking-tight text-slate-950 sm:text-4xl">Produk</h1>
                <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-500">Lihat katalog, harga, dan ketersediaan produk di SN Store.</p>
            </div>
            <span class="inline-flex w-fit items-center gap-2 rounded-xl border border-amber-200 bg-amber-50 px-4 py-2.5 text-xs font-semibold text-amber-800">
                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M10 2a.75.75 0 0 1 .75.75v6.5h6.5a.75.75 0 0 1 0 1.5h-6.5v6.5a.75.75 0 0 1-1.5 0v-6.5h-6.5a.75.75 0 0 1 0-1.5h6.5v-6.5A.75.75 0 0 1 10 2Z" clip-rule="evenodd" />
                </svg>
                Data contoh
            </span>
        </div>

        <section class="grid gap-4 sm:grid-cols-3" aria-label="Ringkasan produk">
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm shadow-slate-900/[0.02]">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-sm font-medium text-slate-500">Total produk</p>
                        <p class="mt-2 text-3xl font-extrabold tracking-tight text-slate-950">{{ count($products) }}</p>
                    </div>
                    <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-rose-50 text-rose-700">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m4.75 7.25 7.25-3.5 7.25 3.5v9.5L12 20.25l-7.25-3.5zM4.75 7.25 12 11l7.25-3.75M12 11v9.25" />
                        </svg>
                    </span>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm shadow-slate-900/[0.02]">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-sm font-medium text-slate-500">Produk tersedia</p>
                        <p class="mt-2 text-3xl font-extrabold tracking-tight text-slate-950">{{ $availableProducts }}</p>
                    </div>
                    <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m5.25 12.75 4.5 4.5 9-10.5" />
                        </svg>
                    </span>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm shadow-slate-900/[0.02]">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-sm font-medium text-slate-500">Total stok</p>
                        <p class="mt-2 text-3xl font-extrabold tracking-tight text-slate-950">{{ $totalStock }}</p>
                    </div>
                    <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-sky-50 text-sky-700">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 7.25h15m-15 0 1.25 12h12.5l1.25-12M9 7.25V4.5h6v2.75" />
                        </svg>
                    </span>
                </div>
            </div>
        </section>

        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm shadow-slate-900/[0.02]" aria-labelledby="products-table-title">
            <div class="flex flex-col gap-3 border-b border-slate-100 px-5 py-5 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                <div>
                    <h2 id="products-table-title" class="text-base font-bold text-slate-900">Daftar produk</h2>
                    <p class="mt-1 text-sm text-slate-500">Produk contoh dari katalog SN Store.</p>
                </div>
                <span class="inline-flex w-fit items-center rounded-full bg-slate-100 px-3 py-1.5 text-xs font-semibold text-slate-600">
                    {{ count($products) }} produk
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[950px] text-left">
                    <thead class="bg-slate-50">
                        <tr class="text-[11px] font-bold uppercase tracking-wider text-slate-500">
                            <th scope="col" class="px-6 py-3.5">Produk</th>
                            <th scope="col" class="px-6 py-3.5">SKU</th>
                            <th scope="col" class="px-6 py-3.5">Kategori</th>
                            <th scope="col" class="px-6 py-3.5">Harga</th>
                            <th scope="col" class="px-6 py-3.5">Stok</th>
                            <th scope="col" class="px-6 py-3.5">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($products as $product)
                            <tr class="transition hover:bg-slate-50/70">
                                <th scope="row" class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <img src="{{ $product['image'] }}" alt="" loading="lazy" class="h-12 w-12 shrink-0 rounded-xl border border-slate-100 bg-slate-50 object-cover">
                                        <span class="min-w-0">
                                            <span class="block max-w-52 truncate text-sm font-semibold text-slate-800">{{ $product['name'] }}</span>
                                            @if ($product['badge'])
                                                <span class="mt-1 inline-flex rounded-full bg-rose-50 px-2 py-0.5 text-[10px] font-semibold text-rose-700">{{ $product['badge'] }}</span>
                                            @else
                                                <span class="mt-1 block text-xs font-normal text-slate-500">Katalog SN Store</span>
                                            @endif
                                        </span>
                                    </div>
                                </th>
                                <td class="px-6 py-4">
                                    <code class="rounded-md bg-slate-100 px-2 py-1 text-xs text-slate-600">{{ $product['sku'] }}</code>
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-600">{{ $product['category'] }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm font-semibold text-slate-800">{{ $product['price'] }}</td>
                                <td class="px-6 py-4 text-sm font-medium text-slate-700">{{ $product['stock'] }}</td>
                                <td class="px-6 py-4">
                                    @if ($product['status'] === 'Tersedia')
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700">
                                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                            Tersedia
                                        </span>
                                    @elseif ($product['status'] === 'Stok menipis')
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-700">
                                            <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>
                                            Stok menipis
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-600">
                                            <span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span>
                                            Stok habis
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="flex flex-col gap-2 border-t border-slate-100 bg-slate-50/60 px-5 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                <p class="text-xs leading-5 text-slate-500">Data produk ini hanya contoh dan belum terhubung ke basis data.</p>
                <p class="text-xs font-medium text-slate-500">Menampilkan {{ count($products) }} produk · {{ $lowStockProducts }} stok menipis</p>
            </div>
        </section>
    </div>
@endsection
