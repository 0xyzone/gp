<?php

use Illuminate\Support\Facades\Route;

Route::get('/display', function () {
    return view('customer-display');
})->name('customer.display');
