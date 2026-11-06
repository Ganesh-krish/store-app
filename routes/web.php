<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/billing', function () {
    return view('billing');
});

Route::get('/order-history', function () {
    return view('order-history-select');
});
