<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="description" content="@yield('meta_description', 'Panel administrasi SN Store.')">

        <title>@yield('title', 'Dashboard') | Admin SN Store</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet">

        <style>
            [x-cloak] {
                display: none !important;
            }
        </style>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-stone-50 font-sans text-slate-900 antialiased">
        <div x-data="{ sidebarOpen: false }">
            <div
                x-cloak
                x-show="sidebarOpen"
                x-transition.opacity
                @click="sidebarOpen = false"
                class="fixed inset-0 z-40 bg-slate-950/50 backdrop-blur-sm lg:hidden"
                aria-hidden="true"
            ></div>

            <aside
                :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
                class="fixed inset-y-0 left-0 z-50 flex w-72 -translate-x-full flex-col border-r border-slate-200 bg-white transition-transform duration-300 lg:translate-x-0"
                aria-label="Sidebar admin"
            >
                <div class="flex h-20 items-center justify-between border-b border-slate-100 px-6">
                    <a href="{{ route('home') }}" class="flex items-center gap-3" aria-label="SN Store, halaman publik">
                        <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-rose-700 text-sm font-extrabold tracking-tight text-white shadow-sm shadow-rose-700/20">SN</span>
                        <span>
                            <span class="block text-lg font-extrabold tracking-tight text-slate-950">sn<span class="text-rose-700">store</span></span>
                            <span class="block text-[10px] font-bold uppercase tracking-[0.18em] text-slate-400">Admin panel</span>
                        </span>
                    </a>
                    <button
                        type="button"
                        @click="sidebarOpen = false"
                        class="rounded-lg p-2 text-slate-500 transition hover:bg-slate-100 lg:hidden"
                        aria-label="Tutup menu"
                    >
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m6 6 12 12M18 6 6 18" />
                        </svg>
                    </button>
                </div>

                <nav class="flex-1 space-y-8 overflow-y-auto px-4 py-7" aria-label="Navigasi admin">
                    <div>
                        <p class="px-3 text-[10px] font-bold uppercase tracking-[0.18em] text-slate-400">Menu utama</p>
                        <div class="mt-3 space-y-1">
                            <a
                                href="{{ route('dashboard') }}"
                                @if (request()->routeIs('dashboard')) aria-current="page" @endif
                                @class([
                                    'flex items-center gap-3 rounded-xl px-3 py-3 text-sm font-semibold transition',
                                    'bg-rose-50 text-rose-700' => request()->routeIs('dashboard'),
                                    'text-slate-600 hover:bg-slate-50 hover:text-rose-700' => ! request()->routeIs('dashboard'),
                                ])
                            >
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3.75h6.5v6.5h-6.5zM13.75 3.75h6.5v6.5h-6.5zM3.75 13.75h6.5v6.5h-6.5zM13.75 13.75h6.5v6.5h-6.5z" />
                                </svg>
                                Dashboard
                            </a>
                        </div>
                    </div>

                    <div>
                        <p class="px-3 text-[10px] font-bold uppercase tracking-[0.18em] text-slate-400">Manajemen</p>
                        <div class="mt-3 space-y-1">
                            <a
                                href="{{ route('admin.categories.index') }}"
                                @if (request()->routeIs('admin.categories.*')) aria-current="page" @endif
                                @class([
                                    'flex items-center gap-3 rounded-xl px-3 py-3 text-sm font-semibold transition',
                                    'bg-rose-50 text-rose-700' => request()->routeIs('admin.categories.*'),
                                    'text-slate-600 hover:bg-slate-50 hover:text-rose-700' => ! request()->routeIs('admin.categories.*'),
                                ])
                            >
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 5.25h6v6h-6zM13.5 5.25h6v6h-6zM4.5 14.25h6v4.5h-6zM13.5 14.25h6v4.5h-6z" />
                                </svg>
                                <span class="flex-1">Kategori</span>
                            </a>

                            <div class="flex cursor-not-allowed items-center gap-3 rounded-xl px-3 py-3 text-sm font-medium text-slate-400" aria-disabled="true" title="Modul produk segera hadir">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.75 7.25 7.25-3.5 7.25 3.5v9.5L12 20.25l-7.25-3.5zM4.75 7.25 12 11l7.25-3.75M12 11v9.25M8.25 5.5l7.5 3.75" />
                                </svg>
                                <span class="flex-1">Produk</span>
                                <span class="rounded-md bg-slate-100 px-1.5 py-1 text-[9px] font-bold uppercase tracking-wide text-slate-400">Segera</span>
                            </div>

                            <div class="flex cursor-not-allowed items-center gap-3 rounded-xl px-3 py-3 text-sm font-medium text-slate-400" aria-disabled="true" title="Modul transaksi segera hadir">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 5.25h16.5v13.5H3.75zM3.75 9h16.5M8.25 14.25h3.5" />
                                </svg>
                                <span class="flex-1">Transaksi</span>
                                <span class="rounded-md bg-slate-100 px-1.5 py-1 text-[9px] font-bold uppercase tracking-wide text-slate-400">Segera</span>
                            </div>

                            <div class="flex cursor-not-allowed items-center gap-3 rounded-xl px-3 py-3 text-sm font-medium text-slate-400" aria-disabled="true" title="Modul user segera hadir">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 7.5a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.5 20.25a7.5 7.5 0 0 1 15 0" />
                                </svg>
                                <span class="flex-1">User</span>
                                <span class="rounded-md bg-slate-100 px-1.5 py-1 text-[9px] font-bold uppercase tracking-wide text-slate-400">Segera</span>
                            </div>
                        </div>
                    </div>
                </nav>

                <div class="border-t border-slate-100 p-4">
                    <div class="rounded-2xl bg-slate-50 p-4">
                        <div class="flex items-center gap-3">
                            <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-white text-rose-700 shadow-sm">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3.75 14.5 9l5.75.75-4.25 4 .75 5.75L12 16.75l-4.75 2.75.75-5.75-4.25-4L9.5 9z" />
                                </svg>
                            </span>
                            <div>
                                <p class="text-xs font-bold text-slate-800">Ruang kendali toko</p>
                                <p class="mt-0.5 text-[11px] text-slate-500">Kelola SN Store dengan mudah.</p>
                            </div>
                        </div>
                    </div>
                    <p class="mt-4 text-center text-[10px] font-medium tracking-wide text-slate-400">SN STORE · ADMIN</p>
                </div>
            </aside>

            <div class="min-h-screen lg:pl-72">
                <header class="sticky top-0 z-30 flex h-20 items-center justify-between border-b border-slate-200/80 bg-white/90 px-4 backdrop-blur-md sm:px-6 lg:px-8">
                    <div class="flex items-center gap-3">
                        <button
                            type="button"
                            @click="sidebarOpen = true"
                            class="rounded-xl border border-slate-200 p-2.5 text-slate-600 transition hover:border-rose-200 hover:text-rose-700 lg:hidden"
                            aria-label="Buka menu"
                        >
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6.75h16M4 12h16M4 17.25h16" />
                            </svg>
                        </button>
                        <div>
                            <p class="text-xs font-medium text-slate-400">Admin / <span class="text-slate-600">@yield('breadcrumb', 'Dashboard')</span></p>
                            <p class="mt-0.5 hidden text-sm font-semibold text-slate-800 sm:block">Selamat datang di SN Store</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 sm:gap-5">
                        <a href="{{ route('home') }}" class="hidden items-center gap-2 rounded-xl px-3 py-2 text-sm font-semibold text-slate-600 transition hover:bg-rose-50 hover:text-rose-700 sm:flex">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 10.5 12 3.75l8.25 6.75v9.75h-6v-6h-4.5v6h-6z" />
                            </svg>
                            Lihat toko
                        </a>

                        <span class="hidden h-8 w-px bg-slate-200 sm:block" aria-hidden="true"></span>

                        <div class="flex items-center gap-3">
                            <span class="hidden text-right sm:block">
                                <span class="block max-w-40 truncate text-sm font-bold text-slate-800">{{ auth()->user()->name }}</span>
                                <span class="block text-xs text-slate-500">Administrator</span>
                            </span>
                            <span class="flex h-10 w-10 items-center justify-center rounded-full bg-rose-100 text-sm font-bold text-rose-700 ring-4 ring-rose-50" aria-hidden="true">
                                {{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}
                            </span>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="rounded-xl p-2.5 text-slate-500 transition hover:bg-rose-50 hover:text-rose-700" aria-label="Keluar">
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 7.5V5.25h-6v13.5h6V16.5M8.25 12h11.5m-3.5-3.5 3.5 3.5-3.5 3.5" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </header>

                <main class="mx-auto w-full max-w-7xl px-4 py-8 sm:px-6 sm:py-10 lg:px-8">
                    @yield('content')
                </main>
            </div>
        </div>
    </body>
</html>
