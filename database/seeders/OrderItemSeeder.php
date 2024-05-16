<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\OrderItem;

class OrderItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $items = [
            [
                'order_id' => 1,
                'stock_id' => 3,
                'product_id' => 3,
                'category_id' => 3, 
                'quantity' => 4,
                'unit_amount' => 59.99,
                'total_amount' => 239.96,
            ],
            [
                'order_id' => 1,
                'stock_id' => 2,
                'product_id' => 2,
                'category_id' => 2, 
                'quantity' => 2,
                'unit_amount' => 39.99,
                'total_amount' => 79.98,
            ],
            [
                'order_id' => 2,
                'stock_id' => 3,
                'product_id' => 3,
                'category_id' => 3, 
                'quantity' => 2,
                'unit_amount' => 59.99,
                'total_amount' => 119.98,
            ],
            [
                'order_id' => 2,
                'stock_id' => 4,
                'product_id' => 4,
                'category_id' => 3, 
                'quantity' => 2,
                'unit_amount' => 59.99,
                'total_amount' => 119.98,
            ],
        ];

        foreach ($items as $item) {
            OrderItem::create($item);
        }
    }
}
