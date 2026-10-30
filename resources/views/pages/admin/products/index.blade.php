@extends('layouts.admin')

@section('title', 'Produk')
@section('breadcrumb', 'Produk')

@section('content')
    <div class="space-y-8">
        @if (session('status'))
            <div role="status" class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
                {{ session('status') }}
            </div>
        @endif

        <div class="flex flex-col justify-between gap-5 sm:flex-row sm:items-end">
            <div>
                <p class="text-sm font-semibold text-rose-700">Katalog toko</p>
                <h1 class="mt-1 text-3xl font-extrabold tracking-tight text-slate-950 sm:text-4xl">Produk</h1>
                <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-500">Lihat katalog, harga, dan ketersediaan produk di SN Store.</p>
            </div>
            <a href="{{ route('admin.products.create') }}" class="inline-flex w-fit items-center justify-center gap-2 rounded-xl bg-rose-700 px-4 py-2.5 text-sm font-bold text-white shadow-sm shadow-rose-700/20 transition hover:bg-rose-800 focus:outline-none focus:ring-2 focus:ring-rose-500 focus:ring-offset-2">
                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M10 3a.75.75 0 0 1 .75.75v5.5h5.5a.75.75 0 0 1 0 1.5h-5.5v5.5a.75.75 0 0 1-1.5 0v-5.5h-5.5a.75.75 0 0 1 0-1.5h5.5v-5.5A.75.75 0 0 1 10 3Z" clip-rule="evenodd" />
                </svg>
                Tambah produk
            </a>
        </div>

        <section class="grid gap-4 sm:grid-cols-3" aria-label="Ringkasan produk">
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm shadow-slate-900/[0.02]">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-sm font-medium text-slate-500">Total produk</p>
                        <p class="mt-2 text-3xl font-extrabold tracking-tight text-slate-950">{{ $products->count() }}</p>
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
                    <p class="mt-1 text-sm text-slate-500">Produk yang tersimpan di katalog SN Store.</p>
                </div>
                <span class="inline-flex w-fit items-center rounded-full bg-slate-100 px-3 py-1.5 text-xs font-semibold text-slate-600">
                    {{ $products->count() }} produk
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
                            <th scope="col" class="px-6 py-3.5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($products as $product)
                            <tr class="transition hover:bg-slate-50/70">
                                <th scope="row" class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        @if ($product->image_path)
                                            <img src="{{ asset('storage/'.$product->image_path) }}" alt="{{ $product->name }}" loading="lazy" class="h-12 w-12 shrink-0 rounded-xl border border-slate-100 bg-slate-50 object-cover">
                                        @else
                                            <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl border border-slate-100 bg-slate-50 text-slate-400" aria-label="Gambar tidak tersedia">
                                                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="m3.75 16.5 5.25-5.25 3 3 3.75-4.5 4.5 6.75M4.5 4.5h15a1.5 1.5 0 0 1 1.5 1.5v12a1.5 1.5 0 0 1-1.5 1.5h-15A1.5 1.5 0 0 1 3 18V6a1.5 1.5 0 0 1 1.5-1.5Z" />
                                                </svg>
                                            </span>
                                        @endif
                                        <span class="min-w-0">
                                            <span class="block max-w-52 truncate text-sm font-semibold text-slate-800">{{ $product->name }}</span>
                                            @if ($product->badge)
                                                <span class="mt-1 inline-flex rounded-full bg-rose-50 px-2 py-0.5 text-[10px] font-semibold text-rose-700">{{ $product->badge }}</span>
                                            @else
                                                <span class="mt-1 block text-xs font-normal text-slate-500">Katalog SN Store</span>
                                            @endif
                                        </span>
                                    </div>
                                </th>
                                <td class="px-6 py-4">
                                    <code class="rounded-md bg-slate-100 px-2 py-1 text-xs text-slate-600">{{ $product->sku }}</code>
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-600">{{ $product->category->name }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm font-semibold text-slate-800">Rp {{ number_format((float) $product->price, 0, ',', '.') }}</td>
                                <td class="px-6 py-4 text-sm font-medium text-slate-700">{{ $product->stock }}</td>
                                <td class="px-6 py-4">
                                    @if ($product->availability_status === 'Tersedia')
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700">
                                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                            Tersedia
                                        </span>
                                    @elseif ($product->availability_status === 'Stok menipis')
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
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('admin.products.edit', $product) }}" title="Edit produk" aria-label="Edit produk {{ $product->name }}" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 text-slate-500 transition hover:border-rose-200 hover:text-rose-700">
                                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m16.5 5.25 2.25 2.25M4.5 19.5l4.75-.95L19 8.8a2.12 2.12 0 0 0-3-3L6.25 15.5 5.3 20.25" />
                                            </svg>
                                        </a>
                                        <form method="POST" action="{{ route('admin.products.destroy', $product) }}" data-confirm-delete data-confirm-resource="produk" data-category-name="{{ $product->name }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" title="Hapus produk" aria-label="Hapus produk {{ $product->name }}" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 text-slate-500 transition hover:border-rose-200 hover:text-rose-700">
                                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 7.5h15m-13.5 0 .9 12h10.2l.9-12M9 7.5V4.5h6v3M10 11v5m4-5v5" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-sm text-slate-500">
                                    Belum ada produk tersimpan. <a href="{{ route('admin.products.create') }}" class="font-semibold text-rose-700 hover:text-rose-800">Tambah produk</a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="flex flex-col gap-2 border-t border-slate-100 bg-slate-50/60 px-5 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                <p class="text-xs leading-5 text-slate-500">Data produk diambil langsung dari database.</p>
                <p class="text-xs font-medium text-slate-500">Menampilkan {{ $products->count() }} produk · {{ $lowStockProducts }} stok menipis</p>
            </div>
        </section>
    </div>
@endsection
