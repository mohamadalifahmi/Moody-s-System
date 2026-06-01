<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductCategorySeeder extends Seeder
{
    public function run(): void
    {
        DB::table('product_categories')->insert([
            ['tenant_id' => 1, 'name' => 'منتجات', 'description' => 'المنتجات والخدمات', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['tenant_id' => 1, 'name' => 'مستلزمات', 'description' => 'مستلزمات التشغيل', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['tenant_id' => 1, 'name' => 'مواد خام', 'description' => 'المواد الخام والإمدادات', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['tenant_id' => 1, 'name' => 'خدمات', 'description' => 'الخدمات المقدمة', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
