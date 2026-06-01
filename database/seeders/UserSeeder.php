<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'tenant_id' => 1,
                'name' => 'مدير النظام',
                'email' => 'admin@althwq.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tenant_id' => 1,
                'name' => 'المبيعات',
                'email' => 'cashier@althwq.com',
                'password' => Hash::make('password'),
                'role' => 'sales',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tenant_id' => 1,
                'name' => 'العمليات',
                'email' => 'kitchen@althwq.com',
                'password' => Hash::make('password'),
                'role' => 'operations',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tenant_id' => 1,
                'name' => 'المخزون',
                'email' => 'stock@althwq.com',
                'password' => Hash::make('password'),
                'role' => 'inventory',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
