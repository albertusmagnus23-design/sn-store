<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('pages.public.home');
})->name('home');

Route::get('/dashboard', function () {
    return view('pages.admin.dashboard.index');
})->middleware(['auth', 'admin', 'verified'])->name('dashboard');

Route::middleware(['auth', 'admin', 'verified'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('categories', CategoryController::class)->except(['show']);

    Route::get('/products', function () {
        return view('pages.admin.products.index');
    })->name('products.index');

    Route::get('/transactions', function () {
        return view('pages.admin.transactions.index');
    })->name('transactions.index');

    Route::get('/users', function () {
        return view('pages.admin.users.index');
    })->name('users.index');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
