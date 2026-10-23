@extends('layouts.admin')

@section('title', 'Dashboard')
@section('breadcrumb', 'Dashboard')

@section('content')
    <div class="space-y-8">
        <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
            <div>
                <p class="text-sm font-semibold text-rose-700">Ringkasan toko</p>
                <h1 class="mt-1 text-3xl font-extrabold tracking-tight text-slate-950 sm:text-4xl">Dashboard</h1>
                <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-500">Pantau dan kelola kebutuhan toko dari satu tempat.</p>
            </div>
            <p class="text-sm font-medium text-slate-500">{{ now()->format('d F Y') }}</p>
        </div>

        <section class="relative isolate overflow-hidden rounded-3xl bg-gradient-to-br from-slate-950 via-slate-900 to-rose-950 px-6 py-8 shadow-xl shadow-slate-900/10 sm:px-9 sm:py-10" aria-labelledby="welcome-title">
            <div class="absolute -right-16 -top-24 -z-10 h-72 w-72 rounded-full bg-rose-500/15 blur-3xl" aria-hidden="true"></div>
            <div class="absolute -bottom-32 right-1/3 -z-10 h-64 w-64 rounded-full bg-indigo-500/10 blur-3xl" aria-hidden="true"></div>

            <div class="relative flex flex-col justify-between gap-8 lg:flex-row lg:items-center">
                <div class="max-w-2xl">
                    <span class="inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/10 px-3 py-1.5 text-xs font-semibold text-rose-100">
                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-400"></span>
                        Panel administrator
                    </span>
                    <h2 id="welcome-title" class="mt-5 text-2xl font-extrabold tracking-tight text-white sm:text-3xl">
                        Selamat datang kembali, {{ auth()->user()->name }}
                    </h2>
                    <p class="mt-3 max-w-xl text-sm leading-6 text-slate-300 sm:text-base">
                        Semua yang kamu butuhkan untuk mengelola SN Store akan hadir di sini. Mulai dari katalog produk hingga transaksi pelanggan.
                    </p>
                </div>
                <a href="{{ route('home') }}" class="inline-flex shrink-0 items-center justify-center gap-2 self-start rounded-xl bg-white px-5 py-3 text-sm font-bold text-slate-900 shadow-sm transition hover:bg-rose-50 focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-slate-900 lg:self-center">
                    Kunjungi toko
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 17 17 7M7 7h10v10" />
                    </svg>
                </a>
            </div>
        </section>

        <section aria-labelledby="modules-title">
            <div class="flex flex-col gap-1 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h2 id="modules-title" class="text-lg font-bold tracking-tight text-slate-900">Modul manajemen</h2>
                    <p class="mt-1 text-sm text-slate-500">Akses cepat ke bagian penting pengelolaan toko.</p>
                </div>
                <span class="mt-2 inline-flex w-fit items-center gap-2 rounded-full bg-amber-50 px-3 py-1.5 text-xs font-semibold text-amber-700 sm:mt-0">
                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M10 2a.75.75 0 0 1 .75.75v6.5h6.5a.75.75 0 0 1 0 1.5h-6.5v6.5a.75.75 0 0 1-1.5 0v-6.5h-6.5a.75.75 0 0 1 0-1.5h6.5v-6.5A.75.75 0 0 1 10 2Z" clip-rule="evenodd" />
                    </svg>
                    5 dari 5 halaman tersedia
                </span>
            </div>

            <div class="mt-5 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <a href="{{ route('admin.categories.index') }}" class="block rounded-2xl border border-slate-200 bg-white p-5 shadow-sm shadow-slate-900/[0.02] transition hover:-translate-y-0.5 hover:border-rose-200 hover:shadow-md">
                    <div class="flex items-start justify-between gap-3">
                        <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-violet-50 text-violet-700">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 5.25h6v6h-6zM13.5 5.25h6v6h-6zM4.5 14.25h6v4.5h-6zM13.5 14.25h6v4.5h-6z" />
                            </svg>
                        </span>
                        <span class="rounded-full bg-emerald-50 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide text-emerald-700">Aktif</span>
                    </div>
                    <h3 class="mt-5 text-base font-bold text-slate-900">Kategori</h3>
                    <p class="mt-1.5 text-sm leading-5 text-slate-500">Kelompokkan produk agar lebih mudah ditemukan pelanggan.</p>
                </a>

                <a href="{{ route('admin.products.index') }}" class="block rounded-2xl border border-slate-200 bg-white p-5 shadow-sm shadow-slate-900/[0.02] transition hover:-translate-y-0.5 hover:border-rose-200 hover:shadow-md">
                    <div class="flex items-start justify-between gap-3">
                        <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-rose-50 text-rose-700">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m4.75 7.25 7.25-3.5 7.25 3.5v9.5L12 20.25l-7.25-3.5zM4.75 7.25 12 11l7.25-3.75M12 11v9.25M8.25 5.5l7.5 3.75" />
                            </svg>
                        </span>
                        <span class="rounded-full bg-emerald-50 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide text-emerald-700">Aktif</span>
                    </div>
                    <h3 class="mt-5 text-base font-bold text-slate-900">Produk</h3>
                    <p class="mt-1.5 text-sm leading-5 text-slate-500">Atur katalog, harga, stok, dan informasi produk toko.</p>
                </a>

                <a href="{{ route('admin.transactions.index') }}" class="block rounded-2xl border border-slate-200 bg-white p-5 shadow-sm shadow-slate-900/[0.02] transition hover:-translate-y-0.5 hover:border-rose-200 hover:shadow-md">
                    <div class="flex items-start justify-between gap-3">
                        <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-amber-50 text-amber-700">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 5.25h16.5v13.5H3.75zM3.75 9h16.5M8.25 14.25h3.5" />
                            </svg>
                        </span>
                        <span class="rounded-full bg-emerald-50 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide text-emerald-700">Aktif</span>
                    </div>
                    <h3 class="mt-5 text-base font-bold text-slate-900">Transaksi</h3>
                    <p class="mt-1.5 text-sm leading-5 text-slate-500">Pantau pesanan pelanggan dan status pembayaran.</p>
                </a>

                <a href="{{ route('admin.users.index') }}" class="block rounded-2xl border border-slate-200 bg-white p-5 shadow-sm shadow-slate-900/[0.02] transition hover:-translate-y-0.5 hover:border-rose-200 hover:shadow-md">
                    <div class="flex items-start justify-between gap-3">
                        <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-sky-50 text-sky-700">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 7.5a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.5 20.25a7.5 7.5 0 0 1 15 0" />
                            </svg>
                        </span>
                        <span class="rounded-full bg-emerald-50 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide text-emerald-700">Aktif</span>
                    </div>
                    <h3 class="mt-5 text-base font-bold text-slate-900">User</h3>
                    <p class="mt-1.5 text-sm leading-5 text-slate-500">Kelola akun dan akses pengguna pada toko.</p>
                </a>
            </div>
        </section>

        <div class="grid gap-5 lg:grid-cols-3">
            <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm shadow-slate-900/[0.02] lg:col-span-2" aria-labelledby="activity-title">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <h2 id="activity-title" class="text-base font-bold text-slate-900">Aktivitas terbaru</h2>
                        <p class="mt-1 text-sm text-slate-500">Pembaruan toko akan ditampilkan di sini.</p>
                    </div>
                    <span class="rounded-lg bg-slate-50 p-2 text-slate-400">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.75V12l3.25 2M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                    </span>
                </div>
                <div class="mt-6 flex min-h-40 flex-col items-center justify-center rounded-xl border border-dashed border-slate-200 bg-slate-50/70 px-5 py-7 text-center">
                    <span class="flex h-11 w-11 items-center justify-center rounded-full bg-white text-slate-400 shadow-sm">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12m-12 5.25h12m-12 5.25h12M3.75 6.75h.01v.01h-.01zm0 5.25h.01v.01h-.01zm0 5.25h.01v.01h-.01z" />
                        </svg>
                    </span>
                    <p class="mt-3 text-sm font-semibold text-slate-700">Belum ada aktivitas</p>
                    <p class="mt-1 max-w-sm text-xs leading-5 text-slate-500">Aktivitas akan muncul setelah modul manajemen produk dan transaksi tersedia.</p>
                </div>
            </section>

            <aside class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm shadow-slate-900/[0.02]" aria-labelledby="shortcuts-title">
                <h2 id="shortcuts-title" class="text-base font-bold text-slate-900">Pintasan</h2>
                <p class="mt-1 text-sm text-slate-500">Akses halaman yang sering digunakan.</p>
                <div class="mt-5 space-y-3">
                    <a href="{{ route('home') }}" class="group flex items-center gap-3 rounded-xl border border-slate-100 p-3.5 transition hover:border-rose-100 hover:bg-rose-50/60">
                        <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-rose-50 text-rose-700 transition group-hover:bg-white">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 10.5 12 3.75l8.25 6.75v9.75h-6v-6h-4.5v6h-6z" />
                            </svg>
                        </span>
                        <span class="min-w-0 flex-1">
                            <span class="block text-sm font-semibold text-slate-800">Halaman toko</span>
                            <span class="mt-0.5 block text-xs text-slate-500">Lihat tampilan untuk pelanggan</span>
                        </span>
                        <svg class="h-4 w-4 text-slate-400 transition group-hover:translate-x-0.5 group-hover:text-rose-700" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 0 1 .02-1.06L10.94 10 7.23 6.29a.75.75 0 1 1 1.06-1.06l4.24 4.24a.75.75 0 0 1 0 1.06l-4.24 4.24a.75.75 0 0 1-1.08 0Z" clip-rule="evenodd" />
                        </svg>
                    </a>

                    <a href="{{ route('profile.edit') }}" class="group flex items-center gap-3 rounded-xl border border-slate-100 p-3.5 transition hover:border-rose-100 hover:bg-rose-50/60">
                        <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-100 text-slate-600 transition group-hover:bg-white group-hover:text-rose-700">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 7.5a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.5 20.25a7.5 7.5 0 0 1 15 0" />
                            </svg>
                        </span>
                        <span class="min-w-0 flex-1">
                            <span class="block text-sm font-semibold text-slate-800">Profil saya</span>
                            <span class="mt-0.5 block text-xs text-slate-500">Perbarui informasi akun</span>
                        </span>
                        <svg class="h-4 w-4 text-slate-400 transition group-hover:translate-x-0.5 group-hover:text-rose-700" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 0 1 .02-1.06L10.94 10 7.23 6.29a.75.75 0 1 1 1.06-1.06l4.24 4.24a.75.75 0 0 1 0 1.06l-4.24 4.24a.75.75 0 0 1-1.08 0Z" clip-rule="evenodd" />
                        </svg>
                    </a>
                </div>
                <div class="mt-5 flex items-center gap-2 rounded-xl bg-emerald-50 px-3.5 py-3 text-xs font-semibold text-emerald-800">
                    <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                    Halaman publik toko aktif
                </div>
            </aside>
        </div>
    </div>
@endsection
