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
            Route::resource('products', ProductController::class);
            Route::get('orders/history', [OrderController::class, 'orderHistory']);
            Route::resource('orders', OrderController::class);
            Route::get('orders/{order_id}/confirm', [OrderController::class, 'confirmOrder']);
            Route::get('orders/{order_id}/cancel', [OrderController::class, 'cancelOrder']);
            Route::get('orders/{order_id}/status', [OrderController::class, 'updateStatus']);
    });

});
