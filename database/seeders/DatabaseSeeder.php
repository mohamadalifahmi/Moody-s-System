<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        if (DB::table('tenants')->count() > 0) {
            return;
        }

        $this->call([
            TenantSeeder::class,
            UserSeeder::class,
            ExpenseCategorySeeder::class,
            ProductCategorySeeder::class,
            ProductSeeder::class,
            SupplierSeeder::class,
            OrderSeeder::class,
        ]);
    }
}
