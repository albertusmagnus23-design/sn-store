<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProductController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register'])
    ->middleware('throttle:5,1')
    ->name('api.register');
Route::post('/login', [AuthController::class, 'login'])
    ->middleware('throttle:5,1')
    ->name('api.login');
Route::get('/user', [AuthController::class, 'user'])
    ->middleware('auth:sanctum')
    ->name('api.user');

Route::get('/categories', CategoryController::class)->name('api.categories.index');
Route::get('/products', ProductController::class)->name('api.products.index');
Route::get('/products/{productId}', [ProductController::class, 'show'])
    ->whereNumber('productId')
    ->name('api.products.show');
