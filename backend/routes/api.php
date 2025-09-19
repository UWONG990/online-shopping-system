<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// API Routes
Route::prefix('v1')->group(function () {
    // Authentication routes
    Route::post('/register', [App\Http\Controllers\AuthController::class, 'register']);
    Route::post('/login', [App\Http\Controllers\AuthController::class, 'login']);
    
    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [App\Http\Controllers\AuthController::class, 'logout']);
        Route::get('/profile', [App\Http\Controllers\AuthController::class, 'profile']);
        
        // Shop routes
        Route::apiResource('shops', App\Http\Controllers\ShopController::class);
        Route::post('/shops/{shop}/request-approval', [App\Http\Controllers\ShopController::class, 'requestApproval']);
        
        // Product routes
        Route::apiResource('products', App\Http\Controllers\ProductController::class);
        
        // Order routes
        Route::apiResource('orders', App\Http\Controllers\OrderController::class);
        Route::post('/orders/{order}/pay', [App\Http\Controllers\OrderController::class, 'processPayment']);
        
        // Admin routes
        Route::middleware('role:admin')->group(function () {
            Route::get('/admin/shops/pending', [App\Http\Controllers\Admin\ShopController::class, 'pending']);
            Route::post('/admin/shops/{shop}/approve', [App\Http\Controllers\Admin\ShopController::class, 'approve']);
            Route::post('/admin/shops/{shop}/reject', [App\Http\Controllers\Admin\ShopController::class, 'reject']);
        });
    });
});