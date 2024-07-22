<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;


class CategoryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $categories = [
            ['name' => 'Running'],
            ['name' => 'Basketball'],
            ['name' => 'Soccer'],
            ['name' => 'Tennis'],
            ['name' => 'Casual'],
            ['name' => 'Skateboarding'],
            ['name' => 'Training'],
            ['name' => 'Hiking'],
            ['name' => 'Golf'],
            ['name' => 'Baseball'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
