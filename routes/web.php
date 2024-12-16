<?php

use Barn2App\Http\Controllers\AuthController;
use Barn2App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index'])->middleware('shopify.verify')->name('home');
Route::group(['prefix' => 'authenticate'], function () {
    Route::get('/', [AuthController::class, 'authenticate'])->name('authenticate');
    Route::get('/token', [AuthController::class, 'token'])->name('authenticate.token');
});
