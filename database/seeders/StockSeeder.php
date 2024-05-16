<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Stock;

class StockSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $stocks = [
            [
                'category_id' => 3, 
                'product_id' => 1, 
                'stock_quantity_virtual' => 15,
                'stock_quantity_real' => 15,
                'stock_available' => true,
            ],
            [
                'category_id' => 2, 
                'product_id' => 2, 
                'stock_quantity_virtual' => 3,
                'stock_quantity_real' => 5,
                'stock_available' => true,
            ],
            [
                'category_id' => 3,
                'product_id' => 3,
                'stock_quantity_virtual' => 9,
                'stock_quantity_real' => 15,
                'stock_available' => true,
            ],
            [
                'category_id' => 3, 
                'product_id' => 4, 
                'stock_quantity_virtual' => 8,
                'stock_quantity_real' => 10,
                'stock_available' => true,
            ],
        ];

        foreach ($stocks as $stock) {
            Stock::create($stock);
        }
    }
}
