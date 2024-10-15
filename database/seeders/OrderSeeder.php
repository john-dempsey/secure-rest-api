<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\Product;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = Product::all();
        Order::factory()->count(10)->create()->each(function ($order) use ($products) {
            $numberOfProducts = rand(1, 5);
            $orderProducts = $products->random($numberOfProducts);
            foreach($orderProducts as $product) {
                $discount = rand(0, 100);
                $order->products()->attach($product->id, [
                    'quantity' => rand(1, 5),
                    'price' => ($discount > 75) ? number_format($product->price * 0.75, 2) : $product->price
                ]);
            };
        });
    }
}
