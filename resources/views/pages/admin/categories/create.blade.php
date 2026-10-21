@extends('layouts.admin')

@section('title', 'Tambah Kategori')
@section('breadcrumb', 'Kategori / Tambah')

@section('content')
    <div class="mx-auto max-w-4xl space-y-8" x-data="{ demoNotice: false }">
        <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
            <div>
                <a href="{{ route('admin.categories.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-slate-500 transition hover:text-rose-700">
                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M17 10a.75.75 0 0 1-.75.75H5.56l3.22 3.22a.75.75 0 1 1-1.06 1.06l-4.5-4.5a.75.75 0 0 1 0-1.06l4.5-4.5a.75.75 0 1 1 1.06 1.06l-3.22 3.22h10.69A.75.75 0 0 1 17 10Z" clip-rule="evenodd" />
                    </svg>
                    Kembali ke kategori
                </a>
                <p class="mt-5 text-sm font-semibold text-rose-700">Katalog toko</p>
                <h1 class="mt-1 text-3xl font-extrabold tracking-tight text-slate-950 sm:text-4xl">Tambah kategori</h1>
                <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-500">Lengkapi informasi kategori untuk mengelompokkan produk toko.</p>
            </div>
            <span class="inline-flex w-fit items-center gap-2 rounded-xl border border-amber-200 bg-amber-50 px-3.5 py-2.5 text-xs font-semibold text-amber-800">
                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M10 2a.75.75 0 0 1 .75.75v6.5h6.5a.75.75 0 0 1 0 1.5h-6.5v6.5a.75.75 0 0 1-1.5 0v-6.5h-6.5a.75.75 0 0 1 0-1.5h6.5v-6.5A.75.75 0 0 1 10 2Z" clip-rule="evenodd" />
                </svg>
                Tampilan demo
            </span>
        </div>

        <div x-cloak x-show="demoNotice" x-transition role="status" class="flex items-start gap-3 rounded-2xl border border-sky-200 bg-sky-50 px-4 py-4 text-sm text-sky-900">
            <svg class="mt-0.5 h-5 w-5 shrink-0 text-sky-600" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0ZM9.25 8a.75.75 0 0 1 1.5 0v5a.75.75 0 0 1-1.5 0V8Zm.75-3a1 1 0 1 0 0 2 1 1 0 0 0 0-2Z" clip-rule="evenodd" />
            </svg>
            <p>Ini hanya tampilan demo. Data kategori belum disimpan.</p>
        </div>

        <form @submit.prevent="demoNotice = true" class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm shadow-slate-900/[0.02]">
            <div class="border-b border-slate-100 px-5 py-5 sm:px-7">
                <h2 class="text-base font-bold text-slate-900">Informasi kategori</h2>
                <p class="mt-1 text-sm text-slate-500">Isi detail kategori di bawah ini.</p>
            </div>

            <div class="grid gap-6 px-5 py-6 sm:px-7 sm:py-7">
                <div class="grid gap-6 md:grid-cols-2">
                    <div>
                        <label for="category-name" class="block text-sm font-semibold text-slate-700">Nama kategori <span class="text-rose-600">*</span></label>
                        <input id="category-name" name="name" type="text" required placeholder="Contoh: Smartphone" class="mt-2 w-full rounded-xl border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 placeholder:text-slate-400 focus:border-rose-400 focus:ring-4 focus:ring-rose-100">
                        <p class="mt-2 text-xs text-slate-500">Gunakan nama yang jelas dan mudah dikenali.</p>
                    </div>

                    <div>
                        <label for="category-slug" class="block text-sm font-semibold text-slate-700">Slug kategori <span class="text-rose-600">*</span></label>
                        <input id="category-slug" name="slug" type="text" required placeholder="Contoh: smartphone" class="mt-2 w-full rounded-xl border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 placeholder:text-slate-400 focus:border-rose-400 focus:ring-4 focus:ring-rose-100">
                        <p class="mt-2 text-xs text-slate-500">Format URL kategori, gunakan huruf kecil dan tanda hubung.</p>
                    </div>
                </div>

                <div>
                    <label for="category-description" class="block text-sm font-semibold text-slate-700">Deskripsi</label>
                    <textarea id="category-description" name="description" rows="4" placeholder="Tuliskan deskripsi singkat kategori..." class="mt-2 w-full resize-y rounded-xl border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 placeholder:text-slate-400 focus:border-rose-400 focus:ring-4 focus:ring-rose-100"></textarea>
                    <p class="mt-2 text-xs text-slate-500">Deskripsi membantu pelanggan memahami kategori ini.</p>
                </div>

                <div>
                    <span class="block text-sm font-semibold text-slate-700">Status kategori</span>
                    <label class="mt-3 flex cursor-pointer items-start gap-3 rounded-xl border border-slate-200 p-4 transition hover:border-rose-200 hover:bg-rose-50/40">
                        <input type="checkbox" name="is_active" checked class="mt-0.5 rounded border-slate-300 text-rose-700 focus:ring-rose-500">
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
                        <path fill-rule="evenodd" d="M16.704 5.29a1 1 0 0 1 .006 1.414l-7.5 7.563a1 1 0 0 1-1.42.002L3.29 9.75a1 1 0 0 1 1.42-1.408l3.79 3.82 6.79-6.865a1 1 0 0 1 1.414-.006Z" clip-rule="evenodd" />
                    </svg>
                    Simpan kategori
                </button>
            </div>
        </form>
    </div>
@endsection
