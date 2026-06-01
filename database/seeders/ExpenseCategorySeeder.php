<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExpenseCategorySeeder extends Seeder
{
    public function run(): void
    {
        DB::table('expense_categories')->insert([
            ['tenant_id' => 1, 'name' => 'إيجار', 'description' => 'إيجار المحل', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['tenant_id' => 1, 'name' => 'فواتير خدمات', 'description' => 'كهرباء، ماء، غاز', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['tenant_id' => 1, 'name' => 'رواتب', 'description' => 'رواتب الموظفين', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['tenant_id' => 1, 'name' => 'صيانة', 'description' => 'صيانة المعدات', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['tenant_id' => 1, 'name' => 'تسويق', 'description' => 'إعلانات وتسويق', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
