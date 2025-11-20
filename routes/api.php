<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['throttle:api-limiter'])->prefix('v1')->group( function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/refresh', [AuthController::class, 'refreshToken']);
    Route::post('/register', [AuthController::class, 'register']);

    Route::middleware(['auth:api'])->group(function(){
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [UserController::class, 'getUser']);

        Route::middleware('roles:admin,vendor,customer')->group(function () {
            Route::get('orders/history', [OrderController::class, 'orderHistory']);
            Route::post('orders', [OrderController::class, 'store']);
        });

        Route::middleware('roles:admin,vendor')->group(function (){
            Route::get('products/search', [ProductController::class, 'search']);
            Route::resource('products', ProductController::class);
            Route::get('orders', [OrderController::class, 'index']);
            Route::put('orders/{order_id}', [OrderController::class, 'update']);
            Route::get('orders/confirm/{order_id}', [OrderController::class, 'confirmOrder']);
            Route::get('orders/cancel/{order_id}', [OrderController::class, 'cancelOrder']);
            Route::get('orders/status/{order_id}', [OrderController::class, 'updateStatus']);
        });
    });
});
