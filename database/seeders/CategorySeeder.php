<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Restaurants', 'icon' => 'restaurant.png'],
            ['name' => 'Cafes', 'icon' => 'cafe.png'],
            ['name' => 'Shops', 'icon' => 'shop.png'],
            ['name' => 'Gyms', 'icon' => 'gym.png'],
            ['name' => 'Salons'],
        ];

        foreach ($categories as $category) {
            \App\Models\Category::create($category);
        }
    }
}
