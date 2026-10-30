@extends('layouts.admin')

@section('title', $product->exists ? 'Edit Produk' : 'Tambah Produk')
@section('breadcrumb', $product->exists ? 'Produk / Edit' : 'Produk / Tambah')

@section('content')
    <div class="mx-auto max-w-4xl space-y-8">
        <div>
            <a href="{{ route('admin.products.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-slate-500 transition hover:text-rose-700">
                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M17 10a.75.75 0 0 1-.75.75H5.56l3.22 3.22a.75.75 0 1 1-1.06 1.06l-4.5-4.5a.75.75 0 0 1 0-1.06l4.5-4.5a.75.75 0 1 1 1.06 1.06l-3.22 3.22h10.69A.75.75 0 0 1 17 10Z" clip-rule="evenodd" />
                </svg>
                Kembali ke produk
            </a>
            <p class="mt-5 text-sm font-semibold text-rose-700">Katalog toko</p>
            <h1 class="mt-1 text-3xl font-extrabold tracking-tight text-slate-950 sm:text-4xl">
                {{ $product->exists ? 'Edit produk' : 'Tambah produk' }}
            </h1>
            <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-500">Lengkapi informasi produk, harga, stok, dan gambar katalog.</p>
        </div>

        <form
            method="POST"
            action="{{ $product->exists ? route('admin.products.update', $product) : route('admin.products.store') }}"
            enctype="multipart/form-data"
            class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm shadow-slate-900/[0.02]"
        >
            @csrf
            @if ($product->exists)
                @method('PUT')
            @endif

            <div class="border-b border-slate-100 px-5 py-5 sm:px-7">
                <h2 class="text-base font-bold text-slate-900">Informasi produk</h2>
                <p class="mt-1 text-sm text-slate-500">Isi detail produk di bawah ini.</p>
            </div>

            <div class="grid gap-6 px-5 py-6 sm:px-7 sm:py-7">
                <div class="grid gap-6 md:grid-cols-2">
                    <div>
                        <label for="product-name" class="block text-sm font-semibold text-slate-700">Nama produk <span class="text-rose-600">*</span></label>
                        <input id="product-name" name="name" type="text" required maxlength="255" value="{{ old('name', $product->name) }}" placeholder="Contoh: Nova X Pro 256GB" class="mt-2 w-full rounded-xl border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 placeholder:text-slate-400 focus:border-rose-400 focus:ring-4 focus:ring-rose-100">
                        @error('name')
                            <p class="mt-2 text-sm text-rose-700">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="product-sku" class="block text-sm font-semibold text-slate-700">SKU <span class="text-rose-600">*</span></label>
                        <input id="product-sku" name="sku" type="text" required maxlength="100" value="{{ old('sku', $product->sku) }}" placeholder="Contoh: SN-PHN-001" class="mt-2 w-full rounded-xl border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 placeholder:text-slate-400 focus:border-rose-400 focus:ring-4 focus:ring-rose-100">
                        @error('sku')
                            <p class="mt-2 text-sm text-rose-700">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid gap-6 md:grid-cols-2">
                    <div>
                        <label for="product-category" class="block text-sm font-semibold text-slate-700">Kategori <span class="text-rose-600">*</span></label>
                        <select id="product-category" name="category_id" required class="mt-2 w-full rounded-xl border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 focus:border-rose-400 focus:ring-4 focus:ring-rose-100">
                            <option value="">Pilih kategori</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" @selected((string) old('category_id', $product->category_id) === (string) $category->id)>
                                    {{ $category->name }}{{ $category->is_active ? '' : ' (Nonaktif)' }}
                                </option>
                            @endforeach
                        </select>
                        @if ($categories->isEmpty())
                            <p class="mt-2 text-xs text-amber-700">Buat kategori terlebih dahulu sebelum menambahkan produk.</p>
                        @endif
                        @error('category_id')
                            <p class="mt-2 text-sm text-rose-700">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="product-badge" class="block text-sm font-semibold text-slate-700">Badge</label>
                        <input id="product-badge" name="badge" type="text" maxlength="50" value="{{ old('badge', $product->badge) }}" placeholder="Contoh: Terlaris" class="mt-2 w-full rounded-xl border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 placeholder:text-slate-400 focus:border-rose-400 focus:ring-4 focus:ring-rose-100">
                        <p class="mt-2 text-xs text-slate-500">Opsional, tampil sebagai label pada katalog.</p>
                        @error('badge')
                            <p class="mt-2 text-sm text-rose-700">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid gap-6 md:grid-cols-2">
                    <div>
                        <label for="product-price" class="block text-sm font-semibold text-slate-700">Harga (Rp) <span class="text-rose-600">*</span></label>
                        <input id="product-price" name="price" type="number" required min="0" max="9999999999.99" step="0.01" value="{{ old('price', $product->price) }}" placeholder="Contoh: 12499000" class="mt-2 w-full rounded-xl border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 placeholder:text-slate-400 focus:border-rose-400 focus:ring-4 focus:ring-rose-100">
                        @error('price')
                            <p class="mt-2 text-sm text-rose-700">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="product-stock" class="block text-sm font-semibold text-slate-700">Stok <span class="text-rose-600">*</span></label>
                        <input id="product-stock" name="stock" type="number" required min="0" step="1" value="{{ old('stock', $product->stock) }}" placeholder="Contoh: 24" class="mt-2 w-full rounded-xl border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 placeholder:text-slate-400 focus:border-rose-400 focus:ring-4 focus:ring-rose-100">
                        <p class="mt-2 text-xs text-slate-500">Status stok dihitung otomatis berdasarkan jumlah stok.</p>
                        @error('stock')
                            <p class="mt-2 text-sm text-rose-700">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="product-image" class="block text-sm font-semibold text-slate-700">
                        Gambar produk
                        @unless ($product->exists)
                            <span class="text-rose-600">*</span>
                        @endunless
                    </label>
                    @if ($product->image_path)
                        <img src="{{ asset('storage/'.$product->image_path) }}" alt="{{ $product->name }}" class="mt-3 h-28 w-28 rounded-xl border border-slate-200 object-cover">
                    @endif
                    <input id="product-image" name="image" type="file" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" @if (! $product->exists) required @endif class="mt-2 block w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 file:mr-4 file:rounded-lg file:border-0 file:bg-rose-50 file:px-3 file:py-2 file:text-sm file:font-semibold file:text-rose-700 hover:file:bg-rose-100">
                    <p class="mt-2 text-xs text-slate-500">Format JPG, PNG, atau WEBP; ukuran maksimal 5 MB. File disimpan di local storage.</p>
                    @error('image')
                        <p class="mt-2 text-sm text-rose-700">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex flex-col-reverse gap-3 border-t border-slate-100 bg-slate-50/70 px-5 py-5 sm:flex-row sm:justify-end sm:px-7">
                <a href="{{ route('admin.products.index') }}" class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                    Batal
                </a>
                <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-rose-700 px-5 py-2.5 text-sm font-bold text-white shadow-sm shadow-rose-700/20 transition hover:bg-rose-800 focus:outline-none focus:ring-2 focus:ring-rose-500 focus:ring-offset-2">
                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M16.704 5.29a1 1 0 0 1 .006 1.414l-7.5 7.563a1 1 0 0 1-1.42.002L3.29 9.75a1 1 0 1 1 1.42-1.408l3.79 3.82 6.79-6.865a1 1 0 0 1 1.414-.006Z" clip-rule="evenodd" />
                    </svg>
                    {{ $product->exists ? 'Simpan perubahan' : 'Simpan produk' }}
                </button>
            </div>
        </form>
    </div>
@endsection
