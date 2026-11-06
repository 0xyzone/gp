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
            'Homemade Achar & Pickles' => [
                [
                    'name' => 'Akabare Khursani Achar',
                    'code' => 'GP-ACH-01',
                    'price' => 350.00,
                    'description' => 'Fiery authentic round red chili achar infused with mustard oil and traditional spices.',
                ],
                [
                    'name' => 'Gundruk Bhatmas Achar',
                    'code' => 'GP-ACH-02',
                    'price' => 280.00,
                    'description' => 'Fermented leafy greens mixed with roasted soybeans and Himalayan herbs.',
                ],
                [
                    'name' => 'Lapsi Achar (Sweet & Sour)',
                    'code' => 'GP-ACH-03',
                    'price' => 250.00,
                    'description' => 'Traditional Nepali hog plum pickle with the perfect sweet, tangy and spicy balance.',
                ],
                [
                    'name' => 'Mula Ra Kakro Ko Achar',
                    'code' => 'GP-ACH-04',
                    'price' => 220.00,
                    'description' => 'Crunchy sun-dried radish & sesame seed pickle with aromatic timur pepper.',
                ],
                [
                    'name' => 'Buff Meat Achar',
                    'code' => 'GP-ACH-05',
                    'price' => 450.00,
                    'description' => 'Rich, savory slow-cooked shredded buffalo meat pickle seasoned with mountain spices.',
                ],
                [
                    'name' => 'Chicken Timur Achar',
                    'code' => 'GP-ACH-06',
                    'price' => 420.00,
                    'description' => 'Tender boneless chicken bites preserved with tingling Szechuan timur and roasted spices.',
                ],
            ],
            'Expo Special Pani Puri Stall' => [
                [
                    'name' => 'Classic Crispy Pani Puri (Plate of 8)',
                    'code' => 'GP-PP-01',
                    'price' => 100.00,
                    'description' => 'Crispy puris stuffed with spiced aloo chana mash and tangy mint-coriander spicy water.',
                ],
                [
                    'name' => 'Khatta Meetha Pani Puri (Plate of 8)',
                    'code' => 'GP-PP-02',
                    'price' => 120.00,
                    'description' => 'Puris filled with sweet lapsi chutney & spicy jaljeera water blend.',
                ],
                [
                    'name' => 'Dahi Puri Chat (6 Pieces)',
                    'code' => 'GP-PP-03',
                    'price' => 150.00,
                    'description' => 'Loaded puris topped with chilled creamy yogurt, sev, pomegranate and sweet-sour chutneys.',
                ],
                [
                    'name' => 'Akabare Spicy Pani Puri Challenge',
                    'code' => 'GP-PP-04',
                    'price' => 140.00,
                    'description' => 'Extreme hot pani puri seasoned with our signature Ghasa Akabare chili extract.',
                ],
            ],
            'Gift Packs & Combos' => [
                [
                    'name' => 'Trio Achar Gift Hamper (3 Jars)',
                    'code' => 'GP-CMB-01',
                    'price' => 850.00,
                    'description' => 'Beautiful wooden gift box containing Akabare, Lapsi, and Gundruk achar jars.',
                ],
                [
                    'name' => 'Stall Combo: Plate of Pani Puri + Achar Jar',
                    'code' => 'GP-CMB-02',
                    'price' => 420.00,
                    'description' => 'Enjoy 1 plate of fresh pani puri at our stall and take home 1 jar of signature achar.',
                ],
                [
                    'name' => 'Family Achar Feast Box (5 Jars)',
                    'code' => 'GP-CMB-03',
                    'price' => 1450.00,
                    'description' => 'Complete assortment of all 5 bestseller Ghasa pickles for family feasts.',
                ],
            ],
        ];

        // Clean out legacy irrelevant demo items
        Product::whereNotIn('code', [
            'GP-ACH-01', 'GP-ACH-02', 'GP-ACH-03', 'GP-ACH-04', 'GP-ACH-05', 'GP-ACH-06',
            'GP-PP-01', 'GP-PP-02', 'GP-PP-03', 'GP-PP-04',
            'GP-CMB-01', 'GP-CMB-02', 'GP-CMB-03',
        ])->delete();

        Category::whereNotIn('name', array_keys($categories))->delete();

        $sortOrder = 1;
        foreach ($categories as $catName => $products) {
            $category = Category::updateOrCreate(
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
                        'description' => $p['description'],
                        'is_active' => true,
                        'sort_order' => $pSort++,
                    ]
                );
            }
        }
    }
}
