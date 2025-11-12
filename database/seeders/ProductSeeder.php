<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            ['id' => 1, 'name' => 'Starter Plan', 'price' => 9900, 'currency' => 'MYR'],
            ['id' => 2, 'name' => 'Pro Plan',     'price' => 19900, 'currency' => 'MYR'],
        ];

        foreach ($products as $product) {
            Product::updateOrCreate(['id' => $product['id']], $product);
        }
    }
}
