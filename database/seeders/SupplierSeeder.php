<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('suppliers')->insert([
            ['tenant_id' => 1, 'name' => 'شركة التميمي للمواد الغذائية', 'phone' => '0555000222', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['tenant_id' => 1, 'name' => 'مؤسسة الخضروات الطازجة', 'phone' => '0555000333', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['tenant_id' => 1, 'name' => 'شركة اللحوم الممتازة', 'phone' => '0555000444', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
