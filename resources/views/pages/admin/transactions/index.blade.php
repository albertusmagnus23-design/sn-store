@extends('layouts.admin')

@section('title', 'Transaksi')
@section('breadcrumb', 'Transaksi')

@php
    $transactions = [
        [
            'code' => 'TRX-2026-1024',
            'customer' => 'Andi Pratama',
            'initials' => 'AP',
            'date' => '23 Okt 2026, 10:42',
            'items' => 'Nova X Pro 256GB',
            'quantity' => 1,
            'total' => 'Rp 12.499.000',
            'payment' => 'Dibayar',
            'order' => 'Diproses',
        ],
        [
            'code' => 'TRX-2026-1023',
            'customer' => 'Nadia Putri',
            'initials' => 'NP',
            'date' => '23 Okt 2026, 09:18',
            'items' => 'Studio Headphones S2',
            'quantity' => 2,
            'total' => 'Rp 5.598.000',
            'payment' => 'Dibayar',
            'order' => 'Dikirim',
        ],
        [
            'code' => 'TRX-2026-1022',
            'customer' => 'Bima Saputra',
            'initials' => 'BS',
            'date' => '22 Okt 2026, 16:35',
            'items' => 'AeroBook 14 Ultra',
            'quantity' => 1,
            'total' => 'Rp 18.999.000',
            'payment' => 'Menunggu pembayaran',
            'order' => 'Menunggu',
        ],
        [
            'code' => 'TRX-2026-1021',
            'customer' => 'Siti Rahma',
            'initials' => 'SR',
            'date' => '22 Okt 2026, 14:07',
            'items' => 'Pulse Watch Active',
            'quantity' => 1,
            'total' => 'Rp 3.249.000',
            'payment' => 'Dibayar',
            'order' => 'Selesai',
        ],
        [
            'code' => 'TRX-2026-1020',
            'customer' => 'Rizky Maulana',
            'initials' => 'RM',
            'date' => '21 Okt 2026, 11:52',
            'items' => 'Nova X Lite 128GB',
            'quantity' => 1,
            'total' => 'Rp 5.799.000',
            'payment' => 'Dibayar',
            'order' => 'Diproses',
        ],
        [
            'code' => 'TRX-2026-1019',
            'customer' => 'Dewi Anggraini',
            'initials' => 'DA',
            'date' => '20 Okt 2026, 18:21',
            'items' => 'ViewPro 27 4K',
            'quantity' => 1,
            'total' => 'Rp 6.899.000',
            'payment' => 'Dibatalkan',
            'order' => 'Dibatalkan',
        ],
    ];

    $paidTransactions = collect($transactions)->where('payment', 'Dibayar')->count();
    $pendingTransactions = collect($transactions)->where('payment', 'Menunggu pembayaran')->count();
    $completedRevenue = 27145000;
@endphp

@section('content')
    <div class="space-y-8">
        <div class="flex flex-col justify-between gap-5 sm:flex-row sm:items-end">
            <div>
                <p class="text-sm font-semibold text-rose-700">Pesanan pelanggan</p>
                <h1 class="mt-1 text-3xl font-extrabold tracking-tight text-slate-950 sm:text-4xl">Transaksi</h1>
                <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-500">Pantau status pesanan dan pembayaran pelanggan SN Store.</p>
            </div>
            <span class="inline-flex w-fit items-center gap-2 rounded-xl border border-amber-200 bg-amber-50 px-4 py-2.5 text-xs font-semibold text-amber-800">
                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M10 2a.75.75 0 0 1 .75.75v6.5h6.5a.75.75 0 0 1 0 1.5h-6.5v6.5a.75.75 0 0 1-1.5 0v-6.5h-6.5a.75.75 0 0 1 0-1.5h6.5v-6.5A.75.75 0 0 1 10 2Z" clip-rule="evenodd" />
                </svg>
                Data contoh
            </span>
        </div>

        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4" aria-label="Ringkasan transaksi">
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm shadow-slate-900/[0.02]">
                <p class="text-sm font-medium text-slate-500">Total transaksi</p>
                <div class="mt-2 flex items-end justify-between gap-3">
                    <p class="text-3xl font-extrabold tracking-tight text-slate-950">{{ count($transactions) }}</p>
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-rose-50 text-rose-700">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 5.25h16.5v13.5H3.75zM3.75 9h16.5M8.25 14.25h3.5" />
                        </svg>
                    </span>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm shadow-slate-900/[0.02]">
                <p class="text-sm font-medium text-slate-500">Pembayaran berhasil</p>
                <div class="mt-2 flex items-end justify-between gap-3">
                    <p class="text-3xl font-extrabold tracking-tight text-slate-950">{{ $paidTransactions }}</p>
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m5.25 12.75 4.5 4.5 9-10.5" />
                        </svg>
                    </span>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm shadow-slate-900/[0.02]">
                <p class="text-sm font-medium text-slate-500">Menunggu pembayaran</p>
                <div class="mt-2 flex items-end justify-between gap-3">
                    <p class="text-3xl font-extrabold tracking-tight text-slate-950">{{ $pendingTransactions }}</p>
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-50 text-amber-700">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.75V12l3.25 2M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                    </span>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm shadow-slate-900/[0.02]">
                <p class="text-sm font-medium text-slate-500">Nilai transaksi lunas</p>
                <div class="mt-2 flex items-end justify-between gap-3">
                    <p class="text-2xl font-extrabold tracking-tight text-slate-950">Rp {{ number_format($completedRevenue, 0, ',', '.') }}</p>
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-sky-50 text-sky-700">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3.75v16.5m4.5-12.75c0-1.66-2.01-3-4.5-3s-4.5 1.34-4.5 3 2.01 3 4.5 3 4.5 1.34 4.5 3-2.01 3-4.5 3-4.5-1.34-4.5-3" />
                        </svg>
                    </span>
                </div>
            </div>
        </section>

        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm shadow-slate-900/[0.02]" aria-labelledby="transactions-table-title">
            <div class="flex flex-col gap-3 border-b border-slate-100 px-5 py-5 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                <div>
                    <h2 id="transactions-table-title" class="text-base font-bold text-slate-900">Daftar transaksi</h2>
                    <p class="mt-1 text-sm text-slate-500">Transaksi terbaru dari pelanggan.</p>
                </div>
                <span class="inline-flex w-fit items-center rounded-full bg-slate-100 px-3 py-1.5 text-xs font-semibold text-slate-600">
                    {{ count($transactions) }} transaksi
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[1120px] text-left">
                    <thead class="bg-slate-50">
                        <tr class="text-[11px] font-bold uppercase tracking-wider text-slate-500">
                            <th scope="col" class="px-6 py-3.5">Transaksi</th>
                            <th scope="col" class="px-6 py-3.5">Pelanggan</th>
                            <th scope="col" class="px-6 py-3.5">Produk</th>
                            <th scope="col" class="px-6 py-3.5">Tanggal</th>
                            <th scope="col" class="px-6 py-3.5">Total</th>
                            <th scope="col" class="px-6 py-3.5">Pembayaran</th>
                            <th scope="col" class="px-6 py-3.5">Pesanan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($transactions as $transaction)
                            <tr class="transition hover:bg-slate-50/70">
                                <th scope="row" class="whitespace-nowrap px-6 py-4">
                                    <span class="block text-sm font-bold text-slate-800">{{ $transaction['code'] }}</span>
                                    <span class="mt-1 block text-xs font-normal text-slate-500">{{ $transaction['quantity'] }} item</span>
                                </th>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2.5">
                                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-rose-50 text-xs font-bold text-rose-700">{{ $transaction['initials'] }}</span>
                                        <span class="whitespace-nowrap text-sm font-medium text-slate-700">{{ $transaction['customer'] }}</span>
                                    </div>
                                </td>
                                <td class="max-w-56 px-6 py-4">
                                    <span class="block truncate text-sm text-slate-700">{{ $transaction['items'] }}</span>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-500">{{ $transaction['date'] }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm font-semibold text-slate-800">{{ $transaction['total'] }}</td>
                                <td class="px-6 py-4">
                                    @if ($transaction['payment'] === 'Dibayar')
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700">
                                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                            Dibayar
                                        </span>
                                    @elseif ($transaction['payment'] === 'Menunggu pembayaran')
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-700">
                                            <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>
                                            Menunggu
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-600">
                                            <span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span>
                                            Dibatalkan
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if ($transaction['order'] === 'Selesai')
                                        <span class="inline-flex rounded-full bg-sky-50 px-2.5 py-1 text-xs font-semibold text-sky-700">Selesai</span>
                                    @elseif ($transaction['order'] === 'Dikirim')
                                        <span class="inline-flex rounded-full bg-violet-50 px-2.5 py-1 text-xs font-semibold text-violet-700">Dikirim</span>
                                    @elseif ($transaction['order'] === 'Diproses')
                                        <span class="inline-flex rounded-full bg-indigo-50 px-2.5 py-1 text-xs font-semibold text-indigo-700">Diproses</span>
                                    @elseif ($transaction['order'] === 'Menunggu')
                                        <span class="inline-flex rounded-full bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-700">Menunggu</span>
                                    @else
                                        <span class="inline-flex rounded-full bg-rose-50 px-2.5 py-1 text-xs font-semibold text-rose-700">Dibatalkan</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="flex flex-col gap-2 border-t border-slate-100 bg-slate-50/60 px-5 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                <p class="text-xs leading-5 text-slate-500">Data transaksi ini hanya contoh dan belum terhubung ke basis data.</p>
                <p class="text-xs font-medium text-slate-500">Menampilkan {{ count($transactions) }} transaksi</p>
            </div>
        </section>
    </div>
@endsection
