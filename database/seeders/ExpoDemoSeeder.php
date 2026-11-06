<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ExpoDemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Beverages' => [
                ['name' => 'Signature Cold Brew', 'code' => 'BEV-01', 'price' => 180.00],
                ['name' => 'Iced Matcha Latte', 'code' => 'BEV-02', 'price' => 220.00],
                ['name' => 'Fresh Lemon Mint Iced Tea', 'code' => 'BEV-03', 'price' => 150.00],
                ['name' => 'Sparkling Berry Cooler', 'code' => 'BEV-04', 'price' => 200.00],
            ],
            'Snacks & Pastries' => [
                ['name' => 'Artisanal Butter Croissant', 'code' => 'SNK-01', 'price' => 140.00],
                ['name' => 'Dark Chocolate Brownie', 'code' => 'SNK-02', 'price' => 160.00],
                ['name' => 'Crispy Truffle Fries', 'code' => 'SNK-03', 'price' => 250.00],
                ['name' => 'Caramel Glazed Donut', 'code' => 'SNK-04', 'price' => 120.00],
            ],
            'Expo Merchandise' => [
                ['name' => 'Limited Edition Tote Bag', 'code' => 'MERCH-01', 'price' => 450.00],
                ['name' => 'Stainless Steel Coffee Flask', 'code' => 'MERCH-02', 'price' => 850.00],
                ['name' => 'Stall Souvenir Pin Pack', 'code' => 'MERCH-03', 'price' => 200.00],
                ['name' => 'Cotton Brand T-Shirt', 'code' => 'MERCH-04', 'price' => 750.00],
            ],
            'Combo Packs' => [
                ['name' => 'Expo Special: Drink + Brownie', 'code' => 'CMB-01', 'price' => 300.00],
                ['name' => 'Mega Chill: 2 Drinks + Fries', 'code' => 'CMB-02', 'price' => 550.00],
                ['name' => 'VIP Pack: Drink + Merch Flask', 'code' => 'CMB-03', 'price' => 950.00],
            ],
        ];

        $sortOrder = 1;
        foreach ($categories as $catName => $products) {
            $category = Category::firstOrCreate(
                ['slug' => Str::slug($catName)],
                [
                    'name' => $catName,
                    'sort_order' => $sortOrder++,
                    'is_active' => true,
                ]
            );

            $pSort = 1;
            foreach ($products as $p) {
                Product::updateOrCreate(
                    ['code' => $p['code']],
                    [
                        'category_id' => $category->id,
                        'name' => $p['name'],
                        'price' => $p['price'],
                        'description' => 'Freshly served at the Expo stall.',
                        'is_active' => true,
                        'sort_order' => $pSort++,
                    ]
                );
            }
        }
    }
}
