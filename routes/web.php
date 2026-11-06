<?php

use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

Route::get('/display', function () {
    $categories = collect();
    $allProducts = collect();

    if (Schema::hasTable('categories')) {
        $categories = Category::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->with(['products' => function ($q) {
                $q->where('is_active', true)
                    ->orderBy('sort_order')
                    ->orderBy('name');
            }])
            ->get();
    }

    if (Schema::hasTable('products')) {
        $allProducts = Product::query()
            ->where('is_active', true)
            ->with('category')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    return view('customer-display', compact('categories', 'allProducts'));
})->name('customer.display');
