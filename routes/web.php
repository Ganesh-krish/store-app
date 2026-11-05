<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CustomerController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/billing', function () {
    return view('billing');
});

Route::get('/order-history', function () {
    return view('order-history-select');
});

Route::get('/customers/{customer}/orders', [CustomerController::class,'orderHistoryPage']);
Route::get('/customers/{customer}/orders/{order}', [CustomerController::class,'orderDetailsPage']);
