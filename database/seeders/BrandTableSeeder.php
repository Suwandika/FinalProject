<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Brand;


class BrandTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $brands = [
            ['name' => 'Nike'],
            ['name' => 'Adidas'],
            ['name' => 'Puma'],
            ['name' => 'Reebok'],
            ['name' => 'Under Armour'],
            ['name' => 'New Balance'],
            ['name' => 'Asics'],
            ['name' => 'Converse'],
            ['name' => 'Vans'],
            ['name' => 'Fila'],
        ];

        foreach ($brands as $brand) {
            Brand::create($brand);
        }
    }
}
