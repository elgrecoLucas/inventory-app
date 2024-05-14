<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Ropa',
                'slug' => 'ropa'
            ],
            [
                'name' => 'Electrodomésticos',
                'slug' => 'electrodomesticos'
            ],
            [
                'name' => 'Hogar y cocina',
                'slug' => 'hogar-cocina'
            ],
            [
                'name' => 'Deportes y Fitness',
                'slug' => 'deportes-fitness'
            ],
            [
                'name' => 'Belleza y Cuidado Personal',
                'slug' => 'belleza-cuidado-personal'
            ],
            [
                'name' => 'Camping, Caza y Pesca',
                'slug' => 'camping-caza-pesca'
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
