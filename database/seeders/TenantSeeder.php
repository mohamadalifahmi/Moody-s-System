<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TenantSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('tenants')->insert([
            'name' => 'Moody\'s Management',
            'slug' => 'althwq',
            'business_type' => 'general',
            'email' => 'info@moodyslb.com',
            'phone' => '03-123456',
            'currency' => 'LBP',
            'timezone' => 'Asia/Beirut',
            'is_active' => true,
            'settings' => json_encode(['exchange_rate' => 89500]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
