<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            ['category_id' => 1, 'name' => 'منتج أ', 'sale_price' => 15.00],
            ['category_id' => 1, 'name' => 'منتج ب', 'sale_price' => 18.00],
            ['category_id' => 1, 'name' => 'منتج ج', 'sale_price' => 20.00],
            ['category_id' => 1, 'name' => 'منتج د', 'sale_price' => 18.00],
            ['category_id' => 1, 'name' => 'منتج ه', 'sale_price' => 25.00],
            ['category_id' => 1, 'name' => 'منتج و', 'sale_price' => 22.00],
            ['category_id' => 2, 'name' => 'مستلزم أ', 'sale_price' => 45.00],
            ['category_id' => 2, 'name' => 'مستلزم ب', 'sale_price' => 40.00],
            ['category_id' => 2, 'name' => 'مستلزم ج', 'sale_price' => 50.00],
            ['category_id' => 2, 'name' => 'مستلزم د', 'sale_price' => 15.00],
            ['category_id' => 2, 'name' => 'مستلزم ه', 'sale_price' => 12.00],
            ['category_id' => 2, 'name' => 'مستلزم و', 'sale_price' => 10.00],
            ['category_id' => 3, 'name' => 'خام أ', 'sale_price' => 5.00],
            ['category_id' => 3, 'name' => 'خام ب', 'sale_price' => 12.00],
            ['category_id' => 3, 'name' => 'خام ج', 'sale_price' => 5.00],
            ['category_id' => 3, 'name' => 'خام د', 'sale_price' => 6.00],
            ['category_id' => 3, 'name' => 'خام ه', 'sale_price' => 8.00],
            ['category_id' => 3, 'name' => 'خام و', 'sale_price' => 25.00],
            ['category_id' => 4, 'name' => 'خدمة أ', 'sale_price' => 20.00],
            ['category_id' => 4, 'name' => 'خدمة ب', 'sale_price' => 22.00],
            ['category_id' => 4, 'name' => 'خدمة ج', 'sale_price' => 18.00],
            ['category_id' => 4, 'name' => 'خدمة د', 'sale_price' => 15.00],
            ['category_id' => 4, 'name' => 'خدمة ه', 'sale_price' => 12.00],
        ];

        $now = now();
        $records = [];

        foreach ($products as $product) {
            $records[] = [
                'tenant_id' => 1,
                'category_id' => $product['category_id'],
                'name' => $product['name'],
                'purchase_price' => round($product['sale_price'] * 0.6, 2),
                'sale_price' => $product['sale_price'],
                'stock_quantity' => rand(30, 100),
                'unit' => 'piece',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        DB::table('products')->insert($records);
    }
}
