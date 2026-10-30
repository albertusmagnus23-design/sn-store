@extends('layouts.admin')

@section('title', 'Kategori')
@section('breadcrumb', 'Kategori')

@section('content')
    <div class="space-y-8">
        @if (session('status'))
            <div role="status" class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
                {{ session('status') }}
            </div>
        @endif
        @if (session('error'))
            <div role="alert" class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-medium text-rose-800">
                {{ session('error') }}
            </div>
        @endif

        <div class="flex flex-col justify-between gap-5 sm:flex-row sm:items-end">
            <div>
                <p class="text-sm font-semibold text-rose-700">Katalog toko</p>
                <h1 class="mt-1 text-3xl font-extrabold tracking-tight text-slate-950 sm:text-4xl">Kategori</h1>
                <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-500">Kelola kategori untuk membantu pelanggan menemukan produk pilihan.</p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <span class="inline-flex items-center gap-2 rounded-xl border border-amber-200 bg-amber-50 px-4 py-2.5 text-xs font-semibold text-amber-800">
                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M10 2a.75.75 0 0 1 .75.75v6.5h6.5a.75.75 0 0 1 0 1.5h-6.5v6.5a.75.75 0 0 1-1.5 0v-6.5h-6.5a.75.75 0 0 1 0-1.5h6.5v-6.5A.75.75 0 0 1 10 2Z" clip-rule="evenodd" />
                    </svg>
                    Data tersimpan
                </span>
                <a href="{{ route('admin.categories.create') }}" class="inline-flex items-center justify-center gap-2 rounded-xl bg-rose-700 px-4 py-2.5 text-sm font-bold text-white shadow-sm shadow-rose-700/20 transition hover:bg-rose-800 focus:outline-none focus:ring-2 focus:ring-rose-500 focus:ring-offset-2">
                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M10 3a.75.75 0 0 1 .75.75v5.5h5.5a.75.75 0 0 1 0 1.5h-5.5v5.5a.75.75 0 0 1-1.5 0v-5.5h-5.5a.75.75 0 0 1 0-1.5h5.5v-5.5A.75.75 0 0 1 10 3Z" clip-rule="evenodd" />
                    </svg>
                    Tambah kategori
                </a>
            </div>
        </div>

        <section class="grid gap-4 sm:grid-cols-3" aria-label="Ringkasan kategori">
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm shadow-slate-900/[0.02]">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-sm font-medium text-slate-500">Total kategori</p>
                        <p class="mt-2 text-3xl font-extrabold tracking-tight text-slate-950">{{ $categories->count() }}</p>
                    </div>
                    <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-rose-50 text-rose-700">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 5.25h6v6h-6zM13.5 5.25h6v6h-6zM4.5 14.25h6v4.5h-6zM13.5 14.25h6v4.5h-6z" />
                        </svg>
                    </span>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm shadow-slate-900/[0.02]">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-sm font-medium text-slate-500">Kategori aktif</p>
                        <p class="mt-2 text-3xl font-extrabold tracking-tight text-slate-950">{{ $categories->where('is_active', true)->count() }}</p>
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
                        <p class="text-sm font-medium text-slate-500">Kategori nonaktif</p>
                        <p class="mt-2 text-3xl font-extrabold tracking-tight text-slate-950">{{ $categories->where('is_active', false)->count() }}</p>
                    </div>
                    <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-sky-50 text-sky-700">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m4.75 7.25 7.25-3.5 7.25 3.5v9.5L12 20.25l-7.25-3.5zM4.75 7.25 12 11l7.25-3.75M12 11v9.25" />
                        </svg>
                    </span>
                </div>
            </div>
        </section>

        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm shadow-slate-900/[0.02]" aria-labelledby="categories-table-title">
            <div class="flex flex-col gap-3 border-b border-slate-100 px-5 py-5 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                <div>
                    <h2 id="categories-table-title" class="text-base font-bold text-slate-900">Daftar kategori</h2>
                    <p class="mt-1 text-sm text-slate-500">Daftar kategori yang tersimpan di database.</p>
                </div>
                <span class="inline-flex w-fit items-center rounded-full bg-slate-100 px-3 py-1.5 text-xs font-semibold text-slate-600">
                    {{ $categories->count() }} kategori
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[900px] text-left">
                    <thead class="bg-slate-50">
                        <tr class="text-[11px] font-bold uppercase tracking-wider text-slate-500">
                            <th scope="col" class="px-6 py-3.5">Kategori</th>
                            <th scope="col" class="px-6 py-3.5">Slug</th>
                            <th scope="col" class="px-6 py-3.5">Status</th>
                            <th scope="col" class="px-6 py-3.5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($categories as $category)
                            @php
                                $statusClass = $category->is_active
                                    ? 'bg-emerald-50 text-emerald-700'
                                    : 'bg-slate-100 text-slate-600';
                                $statusDotClass = $category->is_active ? 'bg-emerald-500' : 'bg-slate-400';
                            @endphp
                            <tr class="transition hover:bg-slate-50/70">
                                <th scope="row" class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-rose-100 text-sm font-bold text-rose-700">
                                            {{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($category->name, 0, 1)) }}
                                        </span>
                                        <span>
                                            <span class="block text-sm font-semibold text-slate-800">{{ $category->name }}</span>
                                            <span class="mt-0.5 block text-xs font-normal text-slate-500">{{ $category->description ?: 'Tidak ada deskripsi.' }}</span>
                                        </span>
                                    </div>
                                </th>
                                <td class="px-6 py-4">
                                    <code class="rounded-md bg-slate-100 px-2 py-1 text-xs text-slate-600">{{ $category->slug }}</code>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusClass }}">
                                        <span class="h-1.5 w-1.5 rounded-full {{ $statusDotClass }}"></span>
                                        {{ $category->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('admin.categories.edit', $category) }}" title="Edit kategori" aria-label="Edit kategori {{ $category->name }}" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 text-slate-500 transition hover:border-rose-200 hover:text-rose-700">
                                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m16.5 5.25 2.25 2.25M4.5 19.5l4.75-.95L19 8.8a2.12 2.12 0 0 0-3-3L6.25 15.5 5.3 20.25" />
                                            </svg>
                                        </a>
                                        <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" data-confirm-delete data-confirm-resource="kategori" data-category-name="{{ $category->name }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" title="Hapus kategori" aria-label="Hapus kategori {{ $category->name }}" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 text-slate-500 transition hover:border-rose-200 hover:text-rose-700">
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
                                <td colspan="4" class="px-6 py-12 text-center text-sm text-slate-500">
                                    Belum ada kategori tersimpan di database.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="flex flex-col gap-2 border-t border-slate-100 bg-slate-50/60 px-5 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                <p class="text-xs leading-5 text-slate-500">Data kategori diambil langsung dari database.</p>
                <p class="text-xs font-medium text-slate-500">Menampilkan {{ $categories->count() }} kategori</p>
            </div>
        </section>
    </div>
@endsection
