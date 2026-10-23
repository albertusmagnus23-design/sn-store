@extends('layouts.admin')

@section('title', 'User')
@section('breadcrumb', 'User')

@php
    $users = [
        [
            'name' => 'Andi Pratama',
            'email' => 'andi.pratama@example.com',
            'initials' => 'AP',
            'joined_at' => '12 Okt 2026',
            'orders' => 8,
            'status' => 'Aktif',
            'role' => 'Pelanggan',
            'avatar' => 'bg-violet-100 text-violet-700',
        ],
        [
            'name' => 'Nadia Putri',
            'email' => 'nadia.putri@example.com',
            'initials' => 'NP',
            'joined_at' => '10 Okt 2026',
            'orders' => 12,
            'status' => 'Aktif',
            'role' => 'Pelanggan',
            'avatar' => 'bg-rose-100 text-rose-700',
        ],
        [
            'name' => 'Bima Saputra',
            'email' => 'bima.saputra@example.com',
            'initials' => 'BS',
            'joined_at' => '08 Okt 2026',
            'orders' => 3,
            'status' => 'Aktif',
            'role' => 'Pelanggan',
            'avatar' => 'bg-sky-100 text-sky-700',
        ],
        [
            'name' => 'Siti Rahma',
            'email' => 'siti.rahma@example.com',
            'initials' => 'SR',
            'joined_at' => '04 Okt 2026',
            'orders' => 6,
            'status' => 'Aktif',
            'role' => 'Pelanggan',
            'avatar' => 'bg-amber-100 text-amber-700',
        ],
        [
            'name' => 'Rizky Maulana',
            'email' => 'rizky.maulana@example.com',
            'initials' => 'RM',
            'joined_at' => '29 Sep 2026',
            'orders' => 0,
            'status' => 'Menunggu verifikasi',
            'role' => 'Pelanggan',
            'avatar' => 'bg-emerald-100 text-emerald-700',
        ],
        [
            'name' => 'Dewi Anggraini',
            'email' => 'dewi.anggraini@example.com',
            'initials' => 'DA',
            'joined_at' => '25 Sep 2026',
            'orders' => 2,
            'status' => 'Aktif',
            'role' => 'Pelanggan',
            'avatar' => 'bg-indigo-100 text-indigo-700',
        ],
    ];

    $activeUsers = collect($users)->where('status', 'Aktif')->count();
    $pendingUsers = collect($users)->where('status', 'Menunggu verifikasi')->count();
    $totalOrders = collect($users)->sum('orders');
@endphp

@section('content')
    <div class="space-y-8">
        <div class="flex flex-col justify-between gap-5 sm:flex-row sm:items-end">
            <div>
                <p class="text-sm font-semibold text-rose-700">Manajemen akun</p>
                <h1 class="mt-1 text-3xl font-extrabold tracking-tight text-slate-950 sm:text-4xl">User</h1>
                <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-500">Lihat informasi dan aktivitas akun pengguna SN Store.</p>
            </div>
            <span class="inline-flex w-fit items-center gap-2 rounded-xl border border-amber-200 bg-amber-50 px-4 py-2.5 text-xs font-semibold text-amber-800">
                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M10 2a.75.75 0 0 1 .75.75v6.5h6.5a.75.75 0 0 1 0 1.5h-6.5v6.5a.75.75 0 0 1-1.5 0v-6.5h-6.5a.75.75 0 0 1 0-1.5h6.5v-6.5A.75.75 0 0 1 10 2Z" clip-rule="evenodd" />
                </svg>
                Data contoh
            </span>
        </div>

        <section class="grid gap-4 sm:grid-cols-3" aria-label="Ringkasan user">
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm shadow-slate-900/[0.02]">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-sm font-medium text-slate-500">Total user</p>
                        <p class="mt-2 text-3xl font-extrabold tracking-tight text-slate-950">{{ count($users) }}</p>
                    </div>
                    <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-rose-50 text-rose-700">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 7.5a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.5 20.25a7.5 7.5 0 0 1 15 0" />
                        </svg>
                    </span>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm shadow-slate-900/[0.02]">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-sm font-medium text-slate-500">User aktif</p>
                        <p class="mt-2 text-3xl font-extrabold tracking-tight text-slate-950">{{ $activeUsers }}</p>
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
                        <p class="text-sm font-medium text-slate-500">Total pesanan</p>
                        <p class="mt-2 text-3xl font-extrabold tracking-tight text-slate-950">{{ $totalOrders }}</p>
                    </div>
                    <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-sky-50 text-sky-700">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 5.25h16.5v13.5H3.75zM3.75 9h16.5M8.25 14.25h3.5" />
                        </svg>
                    </span>
                </div>
            </div>
        </section>

        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm shadow-slate-900/[0.02]" aria-labelledby="users-table-title">
            <div class="flex flex-col gap-3 border-b border-slate-100 px-5 py-5 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                <div>
                    <h2 id="users-table-title" class="text-base font-bold text-slate-900">Daftar user</h2>
                    <p class="mt-1 text-sm text-slate-500">Informasi akun dan aktivitas pelanggan.</p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-700">
                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                        {{ $activeUsers }} aktif
                    </span>
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 px-3 py-1.5 text-xs font-semibold text-amber-700">
                        <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>
                        {{ $pendingUsers }} menunggu verifikasi
                    </span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[900px] text-left">
                    <thead class="bg-slate-50">
                        <tr class="text-[11px] font-bold uppercase tracking-wider text-slate-500">
                            <th scope="col" class="px-6 py-3.5">Pengguna</th>
                            <th scope="col" class="px-6 py-3.5">Role</th>
                            <th scope="col" class="px-6 py-3.5">Bergabung</th>
                            <th scope="col" class="px-6 py-3.5">Pesanan</th>
                            <th scope="col" class="px-6 py-3.5">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($users as $user)
                            <tr class="transition hover:bg-slate-50/70">
                                <th scope="row" class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full text-xs font-bold {{ $user['avatar'] }}">
                                            {{ $user['initials'] }}
                                        </span>
                                        <span>
                                            <span class="block text-sm font-semibold text-slate-800">{{ $user['name'] }}</span>
                                            <span class="mt-0.5 block text-xs font-normal text-slate-500">{{ $user['email'] }}</span>
                                        </span>
                                    </div>
                                </th>
                                <td class="px-6 py-4">
                                    <span class="inline-flex rounded-lg bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-600">{{ $user['role'] }}</span>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-600">{{ $user['joined_at'] }}</td>
                                <td class="px-6 py-4 text-sm font-semibold text-slate-700">{{ $user['orders'] }}</td>
                                <td class="px-6 py-4">
                                    @if ($user['status'] === 'Aktif')
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700">
                                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                            Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-700">
                                            <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>
                                            Menunggu verifikasi
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="flex flex-col gap-2 border-t border-slate-100 bg-slate-50/60 px-5 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                <p class="text-xs leading-5 text-slate-500">Data user ini hanya contoh dan belum terhubung ke basis data.</p>
                <p class="text-xs font-medium text-slate-500">Menampilkan {{ count($users) }} user</p>
            </div>
        </section>
    </div>
@endsection
