<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
        ]);

        // Settings default — hanya insert jika belum ada
        DB::table('settings')->insertOrIgnore([
            ['key' => 'store_name',     'value' => 'Toko POS',       'created_at' => now(), 'updated_at' => now()],
            ['key' => 'store_address',  'value' => 'Jl. Contoh No.1','created_at' => now(), 'updated_at' => now()],
            ['key' => 'store_phone',    'value' => '08123456789',    'created_at' => now(), 'updated_at' => now()],
            ['key' => 'tax_enabled',    'value' => 'false',          'created_at' => now(), 'updated_at' => now()],
            ['key' => 'tax_percentage', 'value' => '11',             'created_at' => now(), 'updated_at' => now()],
            ['key' => 'receipt_footer', 'value' => 'Terima kasih!',  'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}