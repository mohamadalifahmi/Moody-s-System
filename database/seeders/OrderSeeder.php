<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $orderNumber = 'ORD-20260530-';

        $orders = [
            [
                'order_number' => $orderNumber . '0001',
                'user_id' => 1,
                'status' => 'completed',
                'payment_status' => 'paid',
                'items' => [
                    ['product_id' => 1, 'name' => 'حمص', 'quantity' => 2, 'unit_price' => 15.00],
                    ['product_id' => 4, 'name' => 'شيش طاووق', 'quantity' => 1, 'unit_price' => 45.00],
                    ['product_id' => 7, 'name' => 'كولا', 'quantity' => 3, 'unit_price' => 5.00],
                ],
            ],
            [
                'order_number' => $orderNumber . '0002',
                'user_id' => 2,
                'status' => 'completed',
                'payment_status' => 'paid',
                'items' => [
                    ['product_id' => 2, 'name' => 'متبل', 'quantity' => 1, 'unit_price' => 18.00],
                    ['product_id' => 5, 'name' => 'كفتة', 'quantity' => 2, 'unit_price' => 40.00],
                ],
            ],
            [
                'order_number' => $orderNumber . '0003',
                'user_id' => 1,
                'status' => 'pending',
                'payment_status' => 'unpaid',
                'items' => [
                    ['product_id' => 6, 'name' => 'مقلوبة', 'quantity' => 1, 'unit_price' => 50.00],
                    ['product_id' => 10, 'name' => 'كنافة', 'quantity' => 2, 'unit_price' => 20.00],
                    ['product_id' => 8, 'name' => 'عصير برتقال طازج', 'quantity' => 1, 'unit_price' => 12.00],
                ],
            ],
            [
                'order_number' => $orderNumber . '0004',
                'user_id' => 2,
                'status' => 'completed',
                'payment_status' => 'paid',
                'items' => [
                    ['product_id' => 3, 'name' => 'ورق عنب', 'quantity' => 2, 'unit_price' => 25.00],
                    ['product_id' => 9, 'name' => 'شاي', 'quantity' => 2, 'unit_price' => 5.00],
                ],
            ],
            [
                'order_number' => $orderNumber . '0005',
                'user_id' => 1,
                'status' => 'pending',
                'payment_status' => 'unpaid',
                'items' => [
                    ['product_id' => 11, 'name' => 'أم علي', 'quantity' => 1, 'unit_price' => 22.00],
                    ['product_id' => 12, 'name' => 'بسبوسة', 'quantity' => 3, 'unit_price' => 18.00],
                ],
            ],
        ];

        $now = now();

        foreach ($orders as $order) {
            $subtotal = collect($order['items'])->sum(fn($item) => $item['quantity'] * $item['unit_price']);
            $total = $subtotal;

            $orderId = DB::table('orders')->insertGetId([
                'tenant_id' => 1,
                'user_id' => $order['user_id'],
                'order_number' => $order['order_number'],
                'subtotal' => $subtotal,
                'tax' => 0,
                'discount' => 0,
                'total' => $total,
                'status' => $order['status'],
                'payment_status' => $order['payment_status'],
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            $items = [];
            foreach ($order['items'] as $item) {
                $items[] = [
                    'order_id' => $orderId,
                    'product_id' => $item['product_id'],
                    'name' => $item['name'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total' => $item['quantity'] * $item['unit_price'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            DB::table('order_items')->insert($items);
        }
    }
}
