<?php

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\Setting;
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

    $rawSettings = Setting::allKeyed();
    $qrImage = $rawSettings['payment_qr_image'] ?? 'images/siddhartha-bank-qr.png';
    $qrUrl = str_starts_with($qrImage, 'http')
        ? $qrImage
        : (str_starts_with($qrImage, 'images/') ? asset($qrImage) : asset('storage/'.$qrImage));

    $initialSettings = array_merge([
        'stall_name' => 'घासा • Ghasa Products',
        'display_badge_text' => 'Authentic Nepali Achar & Pani Puri Stall',
        'display_hero_title' => 'स्वादिलो घरको अचार र क्रिस्पी पानी पुरी!',
        'display_hero_desc' => 'Taste our signature Akabare, Gundruk, Buff & Chicken Pickles, or enjoy freshly served hygienic Pani Puri with tingling jaljeera and sweet lapsi chutney.',
        'upi_bank_name' => 'Siddhartha Bank',
        'account_holder' => 'Ghasa Products Nepal',
        'contact_phone' => '+977-9800000000',
        'currency_symbol' => 'रू',
        'show_display_header' => '1',
        'show_hero_banner' => '1',
        'show_qr_pill' => '1',
        'show_carousel_dots' => '1',
        'show_display_footer' => '1',
        'show_product_description' => '0',
    ], $rawSettings, [
        'payment_qr_image_url' => $qrUrl,
    ]);

    return view('customer-display', compact('categories', 'allProducts', 'initialSettings'));
})->name('customer.display');

Route::get('/orders/{order}/receipt', function (Order $order) {
    $order->load('items.product');

    return view('receipts.thermal', compact('order'));
})->name('orders.receipt');
