<?php

use Barn2App\Http\Controllers\AuthController;
use Barn2App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

// Authentication routes
Route::group(['prefix' => 'authenticate'], function () {
    Route::get('/', [AuthController::class, 'authenticate'])->name('authenticate');
    Route::get('/token', [AuthController::class, 'token'])->name('authenticate.token');
});

// App routes
Route::get('/', [DashboardController::class, 'index'])->middleware('shopify.verify')->name('home');
Route::get('/products', [DashboardController::class, 'products'])->name('products');
Route::get('/sample', [DashboardController::class, 'sample'])->name('sample');
