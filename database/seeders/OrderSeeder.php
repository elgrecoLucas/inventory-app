<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Order;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $orders = [
            [
                'user_id' => 1,
                'total_amount' => 319.94,
                'status' => 'processing', 
                'shipping_method' => 'home delivery'
            ],
            [
                'user_id' => 1,
                'total_amount' => 239.96,
                'status' => 'processing', 
                'shipping_method' => 'home delivery'
            ],
        ];

        foreach ($orders as $order) {
            Order::create($order);
        }
    }
}
