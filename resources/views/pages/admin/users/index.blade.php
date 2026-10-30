@extends('layouts.admin')

@section('title', 'User')
@section('breadcrumb', 'User')

@section('content')
    <div class="space-y-8">
        <div class="flex flex-col justify-between gap-5 sm:flex-row sm:items-end">
            <div>
                <p class="text-sm font-semibold text-rose-700">Manajemen akun</p>
                <h1 class="mt-1 text-3xl font-extrabold tracking-tight text-slate-950 sm:text-4xl">User</h1>
                <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-500">Lihat informasi akun pengguna SN Store.</p>
            </div>
            <span class="inline-flex w-fit items-center gap-2 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-2.5 text-xs font-semibold text-emerald-800">
                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="m7.5 13.5-3-3 1.06-1.06L7.5 11.38l6.94-6.94L15.5 5.5l-8 8ZM10 2a8 8 0 1 0 0 16 8 8 0 0 0 0-16Z" clip-rule="evenodd" />
                </svg>
                Data tersimpan
            </span>
        </div>

        <section class="grid gap-4 sm:grid-cols-3" aria-label="Ringkasan user">
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm shadow-slate-900/[0.02]">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-sm font-medium text-slate-500">Total user</p>
                        <p class="mt-2 text-3xl font-extrabold tracking-tight text-slate-950">{{ $totalUsers }}</p>
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
                        <p class="text-sm font-medium text-slate-500">Menunggu verifikasi</p>
                        <p class="mt-2 text-3xl font-extrabold tracking-tight text-slate-950">{{ $pendingUsers }}</p>
                    </div>
                    <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-amber-50 text-amber-700">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8.25v4.5m0 3h.008v.008H12v-.008ZM21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                    </span>
                </div>
            </div>
        </section>

        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm shadow-slate-900/[0.02]" aria-labelledby="users-table-title">
            <div class="flex flex-col gap-3 border-b border-slate-100 px-5 py-5 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                <div>
                    <h2 id="users-table-title" class="text-base font-bold text-slate-900">Daftar user</h2>
                    <p class="mt-1 text-sm text-slate-500">Informasi akun pengguna yang tersimpan di database.</p>
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
                <table class="w-full min-w-[800px] text-left">
                    <thead class="bg-slate-50">
                        <tr class="text-[11px] font-bold uppercase tracking-wider text-slate-500">
                            <th scope="col" class="px-6 py-3.5">Pengguna</th>
                            <th scope="col" class="px-6 py-3.5">Role</th>
                            <th scope="col" class="px-6 py-3.5">Bergabung</th>
                            <th scope="col" class="px-6 py-3.5">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($users as $user)
                            <tr class="transition hover:bg-slate-50/70">
                                <th scope="row" class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-violet-100 text-xs font-bold text-violet-700">
                                            {{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($user->name, 0, 1)) }}
                                        </span>
                                        <span>
                                            <span class="block text-sm font-semibold text-slate-800">{{ $user->name }}</span>
                                            <span class="mt-0.5 block text-xs font-normal text-slate-500">{{ $user->email }}</span>
                                        </span>
                                    </div>
                                </th>
                                <td class="px-6 py-4">
                                    <div class="flex flex-wrap gap-1">
                                        @forelse ($user->roles as $role)
                                            <span class="inline-flex rounded-lg bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-600">
                                                {{ match ($role->name) {
                                                    'admin' => 'Admin',
                                                    'customer' => 'Pelanggan',
                                                    default => \Illuminate\Support\Str::headline($role->name),
                                                } }}
                                            </span>
                                        @empty
                                            <span class="text-xs text-slate-500">Belum ada role</span>
                                        @endforelse
                                    </div>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-600">{{ $user->created_at?->format('d/m/Y') ?? '-' }}</td>
                                <td class="px-6 py-4">
                                    @if ($user->email_verified_at)
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
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-sm text-slate-500">Belum ada user yang terdaftar.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="flex flex-col gap-2 border-t border-slate-100 bg-slate-50/60 px-5 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                <p class="text-xs leading-5 text-slate-500">Data user ditampilkan dari database.</p>
                <p class="text-xs font-medium text-slate-500">Menampilkan {{ $users->count() }} dari {{ $totalUsers }} user</p>
            </div>
        </section>
    </div>
@endsection
