<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Brand;
use App\Models\Category;

class ProductTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $products = [
            [
                'name' => 'Air Max 90',
                'brand_id' => Brand::where('name', 'Nike')->first()->id,
                'category_id' => Category::where('name', 'Running')->first()->id,
                'qty' => 50,
                'price' => 1200000, // Harga dalam Rupiah
                'description' => 'Classic running shoes with a modern twist.',
            ],
            [
                'name' => 'UltraBoost 21',
                'brand_id' => Brand::where('name', 'Adidas')->first()->id,
                'category_id' => Category::where('name', 'Running')->first()->id,
                'qty' => 40,
                'price' => 1800000,
                'description' => 'High-performance running shoes with exceptional comfort.',
            ],
            [
                'name' => 'Suede Classic',
                'brand_id' => Brand::where('name', 'Puma')->first()->id,
                'category_id' => Category::where('name', 'Casual')->first()->id,
                'qty' => 70,
                'price' => 6500000,
                'description' => 'Timeless casual shoes with a classic design.',
            ],
            [
                'name' => 'Club C 85',
                'brand_id' => Brand::where('name', 'Reebok')->first()->id,
                'category_id' => Category::where('name', 'Casual')->first()->id,
                'qty' => 60,
                'price' => 7500000,
                'description' => 'Retro-inspired casual shoes with a minimalist style.',
            ],
            [
                'name' => 'Curry 8',
                'brand_id' => Brand::where('name', 'Under Armour')->first()->id,
                'category_id' => Category::where('name', 'Basketball')->first()->id,
                'qty' => 30,
                'price' => 14000000,
                'description' => 'High-performance basketball shoes designed for agility.',
            ],
            [
                'name' => 'Fresh Foam 1080',
                'brand_id' => Brand::where('name', 'New Balance')->first()->id,
                'category_id' => Category::where('name', 'Running')->first()->id,
                'qty' => 45,
                'price' => 15000000,
                'description' => 'Comfortable running shoes with superior cushioning.',
            ],
            [
                'name' => 'Gel-Kayano 27',
                'brand_id' => Brand::where('name', 'Asics')->first()->id,
                'category_id' => Category::where('name', 'Running')->first()->id,
                'qty' => 35,
                'price' => 16000000,
                'description' => 'Stability running shoes for long-distance runners.',
            ],
            [
                'name' => 'Chuck Taylor All Star',
                'brand_id' => Brand::where('name', 'Converse')->first()->id,
                'category_id' => Category::where('name', 'Casual')->first()->id,
                'qty' => 80,
                'price' => 5500000,
                'description' => 'Iconic casual shoes with a timeless design.',
            ],
            [
                'name' => 'Old Skool',
                'brand_id' => Brand::where('name', 'Vans')->first()->id,
                'category_id' => Category::where('name', 'Skateboarding')->first()->id,
                'qty' => 65,
                'price' => 6000000,
                'description' => 'Durable skateboarding shoes with a classic look.',
            ],
            [
                'name' => 'Disruptor II',
                'brand_id' => Brand::where('name', 'Fila')->first()->id,
                'category_id' => Category::where('name', 'Casual')->first()->id,
                'qty' => 50,
                'price' => 7000000,
                'description' => 'Chunky casual shoes with a bold design.',
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
