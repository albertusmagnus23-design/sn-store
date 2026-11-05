@extends('layouts.public')

@section('title', 'Checkout')

@section('content')
    <div class="min-h-screen">
        <header class="border-b border-slate-200 bg-white">
            <nav class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-4 sm:px-6 lg:px-8">
                <a href="{{ route('home') }}" class="text-lg font-extrabold tracking-tight text-slate-950">sn<span class="text-rose-700">store</span></a>
                <a href="{{ route('cart.index') }}" class="text-sm font-semibold text-slate-600 hover:text-rose-700">Kembali ke keranjang</a>
            </nav>
        </header>

        <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 sm:py-12 lg:px-8">
            <div class="mb-8">
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-rose-700">Satu langkah lagi</p>
                <h1 class="mt-2 text-3xl font-extrabold tracking-tight text-slate-950 sm:text-4xl">Checkout</h1>
            </div>

            @if ($cartItems->isEmpty())
                <section class="rounded-2xl border border-dashed border-slate-300 bg-white p-10 text-center">
                    <h2 class="text-lg font-bold text-slate-900">Tidak ada produk untuk checkout</h2>
                    <a href="{{ route('products.index') }}" class="mt-5 inline-flex rounded-full bg-rose-700 px-5 py-3 text-sm font-semibold text-white hover:bg-rose-800">Jelajahi produk</a>
                </section>
            @else
                @if ($errors->any())
                    <div role="alert" class="mb-6 rounded-xl bg-rose-50 px-4 py-3 text-sm text-rose-800">
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <form
                    method="POST"
                    action="{{ route('checkout.store') }}"
                    class="grid gap-8 lg:grid-cols-[minmax(0,1fr)_380px]"
                    data-checkout-shipping
                    data-shipping-url="{{ route('checkout.shipping-options') }}"
                    data-subtotal="{{ $cartTotal }}"
                >
                    @csrf
                    <input type="hidden" name="shipping_quote_id" value="">
                    <input type="hidden" name="shipping_option" value="">
                    <section
                        class="rounded-2xl border border-slate-200 bg-white p-5 sm:p-7"
                        data-checkout-regions
                        data-cities-url="{{ route('checkout.regions.cities', ['provinceCode' => '__CODE__']) }}"
                        data-districts-url="{{ route('checkout.regions.districts', ['cityCode' => '__CODE__']) }}"
                        data-villages-url="{{ route('checkout.regions.villages', ['districtCode' => '__CODE__']) }}"
                        data-selected-city="{{ old('city_code') }}"
                        data-selected-district="{{ old('district_code') }}"
                        data-selected-village="{{ old('village_code') }}"
                    >
                        <h2 class="text-lg font-bold text-slate-900">Informasi penerima</h2>
                        <p class="mt-1 text-sm text-slate-500">Data terisi dari profil dan dapat disesuaikan untuk pesanan ini.</p>
                        <div class="mt-6 grid gap-5">
                            <div>
                                <label for="name" class="mb-1.5 block text-sm font-semibold text-slate-700">Nama lengkap</label>
                                <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required autocomplete="name" class="w-full rounded-xl border-slate-300 focus:border-rose-400 focus:ring-rose-200">
                                @error('name') <p class="mt-1 text-sm text-rose-700">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="phone" class="mb-1.5 block text-sm font-semibold text-slate-700">Nomor HP</label>
                                <input id="phone" name="phone" type="tel" value="{{ old('phone', $user->phone) }}" required autocomplete="tel" class="w-full rounded-xl border-slate-300 focus:border-rose-400 focus:ring-rose-200">
                                @error('phone') <p class="mt-1 text-sm text-rose-700">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="email" class="mb-1.5 block text-sm font-semibold text-slate-700">Email</label>
                                <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required autocomplete="email" class="w-full rounded-xl border-slate-300 focus:border-rose-400 focus:ring-rose-200">
                                @error('email') <p class="mt-1 text-sm text-rose-700">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="street_address" class="mb-1.5 block text-sm font-semibold text-slate-700">Alamat jalan, nomor, RT/RW</label>
                                <textarea id="street_address" name="street_address" rows="3" required autocomplete="street-address" class="w-full rounded-xl border-slate-300 focus:border-rose-400 focus:ring-rose-200">{{ old('street_address', $user->address) }}</textarea>
                                @error('street_address') <p class="mt-1 text-sm text-rose-700">{{ $message }}</p> @enderror
                            </div>
                            <div class="grid gap-5 sm:grid-cols-2">
                                <div>
                                    <label for="province_code" class="mb-1.5 block text-sm font-semibold text-slate-700">Provinsi</label>
                                    <select id="province_code" name="province_code" required class="w-full rounded-xl border-slate-300 focus:border-rose-400 focus:ring-rose-200">
                                        <option value="">Pilih provinsi</option>
                                        @foreach ($provinces as $province)
                                            <option value="{{ $province->code }}" @selected(old('province_code') === $province->code)>{{ $province->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('province_code') <p class="mt-1 text-sm text-rose-700">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label for="city_code" class="mb-1.5 block text-sm font-semibold text-slate-700">Kabupaten/Kota</label>
                                    <select id="city_code" name="city_code" required disabled class="w-full rounded-xl border-slate-300 focus:border-rose-400 focus:ring-rose-200 disabled:cursor-not-allowed disabled:bg-slate-100 disabled:text-slate-400">
                                        <option value="">Pilih kabupaten/kota</option>
                                    </select>
                                    @error('city_code') <p class="mt-1 text-sm text-rose-700">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label for="district_code" class="mb-1.5 block text-sm font-semibold text-slate-700">Kecamatan</label>
                                    <select id="district_code" name="district_code" required disabled class="w-full rounded-xl border-slate-300 focus:border-rose-400 focus:ring-rose-200 disabled:cursor-not-allowed disabled:bg-slate-100 disabled:text-slate-400">
                                        <option value="">Pilih kecamatan</option>
                                    </select>
                                    @error('district_code') <p class="mt-1 text-sm text-rose-700">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label for="village_code" class="mb-1.5 block text-sm font-semibold text-slate-700">Desa/Kelurahan</label>
                                    <select id="village_code" name="village_code" required disabled class="w-full rounded-xl border-slate-300 focus:border-rose-400 focus:ring-rose-200 disabled:cursor-not-allowed disabled:bg-slate-100 disabled:text-slate-400">
                                        <option value="">Pilih desa/kelurahan</option>
                                    </select>
                                    @error('village_code') <p class="mt-1 text-sm text-rose-700">{{ $message }}</p> @enderror
                                </div>
                            </div>
                            <p id="region-load-error" class="hidden text-sm text-rose-700" role="alert"></p>
                            <div>
                                <label for="postal_code" class="mb-1.5 block text-sm font-semibold text-slate-700">Kode pos</label>
                                <input id="postal_code" name="postal_code" type="text" inputmode="numeric" pattern="[0-9]{5}" maxlength="5" value="{{ old('postal_code') }}" required autocomplete="postal-code" class="w-full rounded-xl border-slate-300 focus:border-rose-400 focus:ring-rose-200">
                                @error('postal_code') <p class="mt-1 text-sm text-rose-700">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </section>

                    <aside class="h-fit rounded-2xl border border-slate-200 bg-white p-5 sm:p-6">
                        <h2 class="text-lg font-bold text-slate-900">Ringkasan pesanan</h2>
                        <ul class="mt-5 space-y-4">
                            @foreach ($cartItems as $cartItem)
                                <li class="flex justify-between gap-4 text-sm">
                                    <span class="text-slate-600">{{ $cartItem->product->name }} <span class="text-slate-400">x{{ $cartItem->quantity }}</span></span>
                                    <span class="shrink-0 font-semibold text-slate-900">Rp {{ number_format((float) $cartItem->product->price * $cartItem->quantity, 0, ',', '.') }}</span>
                                </li>
                            @endforeach
                        </ul>
                        <div class="mt-5 flex justify-between gap-4 border-t border-slate-100 pt-4">
                            <span class="font-semibold text-slate-600">Subtotal produk</span>
                            <span class="font-semibold text-slate-900">Rp {{ number_format($cartTotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="mt-4 border-t border-slate-100 pt-4">
                            <h3 class="font-semibold text-slate-900">Pengiriman</h3>
                            <p class="mt-1 text-xs leading-5 text-slate-500">Asal pengiriman: Kota Makassar. Ongkir dihitung berdasarkan total berat produk.</p>
                            <button type="button" data-shipping-calculate class="mt-3 w-full rounded-xl border border-rose-200 px-4 py-2.5 text-sm font-semibold text-rose-700 transition hover:bg-rose-50">
                                Cek ongkos kirim
                            </button>
                            <p data-shipping-error class="mt-3 hidden rounded-lg bg-rose-50 p-3 text-sm text-rose-800" role="alert"></p>
                            <div data-shipping-options class="mt-3 grid gap-2" aria-live="polite"></div>
                            <div class="mt-4 flex justify-between gap-4">
                                <span class="font-semibold text-slate-600">Ongkos kirim</span>
                                <span data-shipping-fee class="font-semibold text-slate-900">Pilih layanan</span>
                            </div>
                        </div>
                        <div class="mt-4 flex justify-between gap-4 border-t border-slate-100 pt-4">
                            <span class="font-semibold text-slate-600">Total</span>
                            <span data-checkout-total class="font-extrabold text-slate-950">Rp {{ number_format($cartTotal, 0, ',', '.') }}</span>
                        </div>
                        <p class="mt-4 rounded-xl bg-amber-50 p-3 text-xs leading-5 text-amber-900">Setelah pesanan dibuat, kamu akan diarahkan ke halaman pembayaran aman Midtrans. Stok baru dikurangi setelah pembayaran terverifikasi.</p>
                        <button type="submit" class="mt-5 w-full rounded-xl bg-rose-700 px-5 py-3 text-sm font-bold text-white transition hover:bg-rose-800">Buat pesanan</button>
                    </aside>
                </form>
            @endif
        </main>
    </div>
@endsection
