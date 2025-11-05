<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ShopSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $shops = [
            [
                'name' => 'Pizza Palace',
                'category_id' => 1,
                'description' => 'The best pizza in town.',
                'image' => 'shop_images/pizza_palace.jpg',
                'address' => '123 Main St, Cityville',
                'phone' => '123-456-7890',
            ],
            [
                'name' => 'Coffee Corner',
                'category_id' => 2,
                'description' => 'Cozy place for coffee lovers.',
                'image' => 'shop_images/coffee_corner.jpg',
                'address' => '456 Coffee Rd, Townsville',
                'phone' => '987-654-3210',
            ],
            [
                'name' => 'Fitness Hub',
                'category_id' => 4,
                'description' => 'Your neighborhood gym.',
                'image' => 'shop_images/fitness_hub.jpg',
                'address' => '789 Workout Ave, Fit City',
                'phone' => '555-123-4567',
            ],
            [
                'name' => 'Style Salon',
                'category_id' => 5,
                'description' => 'Trendy hairstyles and beauty services.',
                'image' => '',
                'address' => '321 Beauty St, Glamour Town',
                'phone' => '444-555-6666',
            ]
        ];

        foreach ($shops as $shop) {
            \App\Models\Shop::create($shop);
        }
    }
}
