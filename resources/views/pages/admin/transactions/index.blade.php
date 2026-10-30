@extends('layouts.admin')

@section('title', 'Transaksi')
@section('breadcrumb', 'Transaksi')

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
                Data pesanan terbaru
            </span>
        </div>

        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4" aria-label="Ringkasan transaksi">
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm shadow-slate-900/[0.02]">
                <p class="text-sm font-medium text-slate-500">Total transaksi</p>
                <div class="mt-2 flex items-end justify-between gap-3">
                    <p class="text-3xl font-extrabold tracking-tight text-slate-950">{{ $totalTransactions }}</p>
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
                    {{ $transactions->total() }} transaksi
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
                        @forelse ($transactions as $transaction)
                            <tr class="transition hover:bg-slate-50/70">
                                <th scope="row" class="whitespace-nowrap px-6 py-4">
                                    <span class="block text-sm font-bold text-slate-800">{{ $transaction->order_number }}</span>
                                    <span class="mt-1 block text-xs font-normal text-slate-500">{{ $transaction->items->sum('quantity') }} item</span>
                                </th>
                                <td class="px-6 py-4">
                                    <span class="whitespace-nowrap text-sm font-medium text-slate-700">{{ $transaction->customer_name }}</span>
                                </td>
                                <td class="max-w-56 px-6 py-4">
                                    <span class="block truncate text-sm text-slate-700">{{ $transaction->items->pluck('product_name')->join(', ') }}</span>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-500">{{ $transaction->created_at->format('d M Y, H:i') }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm font-semibold text-slate-800">Rp {{ number_format((float) $transaction->total, 0, ',', '.') }}</td>
                                <td class="px-6 py-4">
                                    @if ($transaction->status === 'paid')
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700">
                                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                            Dibayar
                                        </span>
                                    @elseif ($transaction->status === 'pending')
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-700">
                                            <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>
                                            Menunggu
                                        </span>
                                    @elseif ($transaction->status === 'failed')
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-rose-50 px-2.5 py-1 text-xs font-semibold text-rose-700">
                                            <span class="h-1.5 w-1.5 rounded-full bg-rose-500"></span>
                                            Gagal
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-600">
                                            <span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span>
                                            Dibatalkan
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if ($transaction->status === 'paid')
                                        <span class="inline-flex rounded-full bg-indigo-50 px-2.5 py-1 text-xs font-semibold text-indigo-700">Diproses</span>
                                    @elseif ($transaction->status === 'pending')
                                        <span class="inline-flex rounded-full bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-700">Menunggu</span>
                                    @elseif ($transaction->status === 'failed')
                                        <span class="inline-flex rounded-full bg-rose-50 px-2.5 py-1 text-xs font-semibold text-rose-700">Gagal</span>
                                    @else
                                        <span class="inline-flex rounded-full bg-rose-50 px-2.5 py-1 text-xs font-semibold text-rose-700">Dibatalkan</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-sm text-slate-500">Belum ada transaksi.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="flex flex-col gap-2 border-t border-slate-100 bg-slate-50/60 px-5 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                <p class="text-xs leading-5 text-slate-500">Daftar pesanan tersimpan di basis data.</p>
                <div class="text-xs font-medium text-slate-500">{{ $transactions->links() }}</div>
            </div>
        </section>
    </div>
@endsection
