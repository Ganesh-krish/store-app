<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\LowStockProductController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CustomerController;


Route::post('/orders', [OrderController::class, 'store']);
Route::get('/customers/{email}/orders', [CustomerController::class,'orders']);
Route::get('/products/low-stock', [LowStockProductController::class, 'index']);
Route::get('/products', [ProductController::class, 'index']);
Route::get('/customers', [CustomerController::class, 'index']);
Route::post('/customers', [CustomerController::class, 'store']);
