<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\SettingController;
use App\Http\Controllers\Api\ReportController;
use Illuminate\Support\Facades\Route;

// ── Public Routes ─────────────────────────────────────────────────────────────
Route::post('/login', [AuthController::class, 'login']);

// ── Protected Routes ──────────────────────────────────────────────────────────
Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // Categories
    Route::get('/categories', [CategoryController::class, 'index']);

    // Products
    Route::get('/products',         [ProductController::class, 'index']);
    Route::get('/products/{id}',    [ProductController::class, 'show']);
    Route::post('/products',        [ProductController::class, 'store']);
    Route::post('/products/{id}',   [ProductController::class, 'update']);
    Route::put('/products/{id}',    [ProductController::class, 'update']);
    Route::delete('/products/{id}', [ProductController::class, 'destroy']);

    // Transactions
    Route::post('/transactions',        [TransactionController::class, 'store']);
    Route::get('/transactions/history', [TransactionController::class, 'history']);

    // ── Admin only ──────────────────────────────────────────────────────────
    // Users (manajemen kasir)
    Route::get('/users',          [UserController::class, 'index']);
    Route::post('/users',         [UserController::class, 'store']);
    Route::put('/users/{id}',     [UserController::class, 'update']);
    Route::delete('/users/{id}',  [UserController::class, 'destroy']);

    // Settings
    Route::get('/settings',  [SettingController::class, 'index']);
    Route::post('/settings', [SettingController::class, 'update']);

    // Reports
    Route::get('/reports/summary', [ReportController::class, 'summary']);
    Route::get('/reports/chart',   [ReportController::class, 'chart']);
});