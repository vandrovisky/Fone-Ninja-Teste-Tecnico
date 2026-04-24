<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SaleController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me',      [AuthController::class, 'me']);

    Route::get('/produtos',  [ProductController::class, 'index']);
    Route::post('/produtos', [ProductController::class, 'store']);

    Route::get('/compras',  [PurchaseController::class, 'index']);
    Route::post('/compras', [PurchaseController::class, 'store']);

    Route::get('/vendas',          [SaleController::class, 'index']);
    Route::post('/vendas',         [SaleController::class, 'store']);
    Route::delete('/vendas/{id}',  [SaleController::class, 'destroy']);
});
