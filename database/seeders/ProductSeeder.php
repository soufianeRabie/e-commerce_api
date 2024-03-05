<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Product::factory()->create([
            "title"=> "PC Gamer 20",
    "description"=> "External GPU for laptops. Boost your laptop's gaming performance with ease.",
    "image"=> "images/image1.jpg",
    "rating"=> "4.2",
    "price"=> 199.99,
    "oldPrice"=> 249.99,
    "isSold"=> true
        ]);
    }
}

