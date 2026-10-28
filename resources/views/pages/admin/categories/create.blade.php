@extends('layouts.admin')

@section('title', $category->exists ? 'Edit Kategori' : 'Tambah Kategori')
@section('breadcrumb', $category->exists ? 'Kategori / Edit' : 'Kategori / Tambah')

@section('content')
    <div class="mx-auto max-w-4xl space-y-8">
        <div>
            <a href="{{ route('admin.categories.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-slate-500 transition hover:text-rose-700">
                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M17 10a.75.75 0 0 1-.75.75H5.56l3.22 3.22a.75.75 0 1 1-1.06 1.06l-4.5-4.5a.75.75 0 0 1 0-1.06l4.5-4.5a.75.75 0 1 1 1.06 1.06l-3.22 3.22h10.69A.75.75 0 0 1 17 10Z" clip-rule="evenodd" />
                </svg>
                Kembali ke kategori
            </a>
            <p class="mt-5 text-sm font-semibold text-rose-700">Katalog toko</p>
            <h1 class="mt-1 text-3xl font-extrabold tracking-tight text-slate-950 sm:text-4xl">
                {{ $category->exists ? 'Edit kategori' : 'Tambah kategori' }}
            </h1>
            <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-500">Lengkapi informasi kategori untuk mengelompokkan produk toko.</p>
        </div>

        <form
            method="POST"
            action="{{ $category->exists ? route('admin.categories.update', $category) : route('admin.categories.store') }}"
            class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm shadow-slate-900/[0.02]"
        >
            @csrf
            @if ($category->exists)
                @method('PUT')
            @endif

            <div class="border-b border-slate-100 px-5 py-5 sm:px-7">
                <h2 class="text-base font-bold text-slate-900">Informasi kategori</h2>
                <p class="mt-1 text-sm text-slate-500">Isi detail kategori di bawah ini.</p>
            </div>

            <div class="grid gap-6 px-5 py-6 sm:px-7 sm:py-7">
                <div class="grid gap-6 md:grid-cols-2">
                    <div>
                        <label for="category-name" class="block text-sm font-semibold text-slate-700">Nama kategori <span class="text-rose-600">*</span></label>
                        <input id="category-name" name="name" type="text" required maxlength="255" value="{{ old('name', $category->name) }}" placeholder="Contoh: Smartphone" aria-describedby="category-name-error" class="mt-2 w-full rounded-xl border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 placeholder:text-slate-400 focus:border-rose-400 focus:ring-4 focus:ring-rose-100">
                        <p class="mt-2 text-xs text-slate-500">Gunakan nama yang jelas dan mudah dikenali.</p>
                        @error('name')
                            <p id="category-name-error" class="mt-2 text-sm text-rose-700">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="category-slug" class="block text-sm font-semibold text-slate-700">Slug kategori <span class="text-rose-600">*</span></label>
                        <input id="category-slug" name="slug" type="text" required maxlength="255" value="{{ old('slug', $category->slug) }}" placeholder="Contoh: smartphone" aria-describedby="category-slug-error" class="mt-2 w-full rounded-xl border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 placeholder:text-slate-400 focus:border-rose-400 focus:ring-4 focus:ring-rose-100">
                        <p class="mt-2 text-xs text-slate-500">Gunakan huruf kecil, angka, dan tanda hubung.</p>
                        @error('slug')
                            <p id="category-slug-error" class="mt-2 text-sm text-rose-700">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="category-description" class="block text-sm font-semibold text-slate-700">Deskripsi</label>
                    <textarea id="category-description" name="description" rows="4" maxlength="2000" aria-describedby="category-description-error" placeholder="Tuliskan deskripsi singkat kategori..." class="mt-2 w-full resize-y rounded-xl border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 placeholder:text-slate-400 focus:border-rose-400 focus:ring-4 focus:ring-rose-100">{{ old('description', $category->description) }}</textarea>
                    <p class="mt-2 text-xs text-slate-500">Deskripsi membantu pelanggan memahami kategori ini.</p>
                    @error('description')
                        <p id="category-description-error" class="mt-2 text-sm text-rose-700">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <span class="block text-sm font-semibold text-slate-700">Status kategori</span>
                    <label class="mt-3 flex cursor-pointer items-start gap-3 rounded-xl border border-slate-200 p-4 transition hover:border-rose-200 hover:bg-rose-50/40">
                        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $category->exists ? $category->is_active : true)) class="mt-0.5 rounded border-slate-300 text-rose-700 focus:ring-rose-500">
                        <span>
                            <span class="block text-sm font-semibold text-slate-800">Aktif</span>
                            <span class="mt-1 block text-xs leading-5 text-slate-500">Kategori ditampilkan dan dapat digunakan pada katalog.</span>
                        </span>
                    </label>
                </div>
            </div>

            <div class="flex flex-col-reverse gap-3 border-t border-slate-100 bg-slate-50/70 px-5 py-5 sm:flex-row sm:justify-end sm:px-7">
                <a href="{{ route('admin.categories.index') }}" class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                    Batal
                </a>
                <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-rose-700 px-5 py-2.5 text-sm font-bold text-white shadow-sm shadow-rose-700/20 transition hover:bg-rose-800 focus:outline-none focus:ring-2 focus:ring-rose-500 focus:ring-offset-2">
                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M16.704 5.29a1 1 0 0 1 .006 1.414l-7.5 7.563a1 1 0 0 1-1.42.002L3.29 9.75a1 1 0 1 1 1.42-1.408l3.79 3.82 6.79-6.865a1 1 0 0 1 1.414-.006Z" clip-rule="evenodd" />
                    </svg>
                    {{ $category->exists ? 'Simpan perubahan' : 'Simpan kategori' }}
                </button>
            </div>
        </form>
    </div>
@endsection
