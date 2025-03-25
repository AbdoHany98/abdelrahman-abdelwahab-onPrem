<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\Api\OrderController;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::post('/orders', [OrderController::class, 'create']);
    Route::put('/orders/{order}', [OrderController::class, 'update']);
    Route::get('/orders/{order}', [OrderController::class, 'show']);
    Route::post('/orders/{order}/approve', [OrderController::class, 'processApproval']);
    Route::get('/orders/history', [OrderController::class, 'orderHistory']);
});