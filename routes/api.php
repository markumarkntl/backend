<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\TransactionController;
use Illuminate\Support\Facades\Route;

// ── Public Routes ─────────────────────────────────────────────────────────────
Route::post('/login', [AuthController::class, 'login']);

// ── Protected Routes ──────────────────────────────────────────────────────────
Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me',      [AuthController::class, 'me']);

    // Categories (read-only, diambil dari data produk)
    Route::get('/categories', [CategoryController::class, 'index']);

    // Products
    Route::get('/products',         [ProductController::class, 'index']);
    Route::get('/products/{id}',    [ProductController::class, 'show']);
    Route::post('/products',        [ProductController::class, 'store']);
    Route::post('/products/{id}',   [ProductController::class, 'update']);   // ?_method=PUT dari mobile
    Route::put('/products/{id}',    [ProductController::class, 'update']);   // fallback PUT normal
    Route::delete('/products/{id}', [ProductController::class, 'destroy']);

    // Transactions
    Route::post('/transactions',         [TransactionController::class, 'store']);
    Route::get('/transactions/history',  [TransactionController::class, 'history']);
});