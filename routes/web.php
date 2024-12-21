<?php

use Barn2App\Http\Controllers\AuthController;
use Barn2App\Http\Controllers\DashboardController;
use Barn2App\Http\Controllers\PlansController;
use Barn2App\Http\Controllers\ProductsController;
use Barn2App\Http\Controllers\SampleController;
use Barn2App\Http\Controllers\SubscriptionController;
use Barn2App\Http\Middleware\ShopifyVerify;
use Illuminate\Support\Facades\Route;

// Authentication routes
Route::group(['prefix' => 'authenticate'], function () {
    Route::get('/', [AuthController::class, 'authenticate'])->name('authenticate');
    Route::get('/token', [AuthController::class, 'token'])->name('authenticate.token');
});

// App routes
Route::middleware([ShopifyVerify::class])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('home');

    Route::get('/products', [ProductsController::class, 'index'])->name('products');
    Route::get('/products/get', [ProductsController::class, 'get'])->name('products.get');
    Route::post('/products/create', [ProductsController::class, 'create'])->name('products.create');

    Route::get('/sample', [SampleController::class, 'index'])->name('sample');

    Route::get('/plans', [PlansController::class, 'index'])->name('plans');
    Route::get('/plans/content', [PlansController::class, 'content'])->name('plans.content');
    // Create a subscription charge and redirect to Shopify's charge approval page
    Route::post('/plans/subscription', [PlansController::class, 'create'])->name('plans.create');

    // After merchant approves the charge, Shopify redirects to this route
    Route::get('/subscription/create', [SubscriptionController::class, 'subscription'])->name('subscription.create');
});
