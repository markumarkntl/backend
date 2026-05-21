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
                'name'       => 'Admin RUdi',
                'nip'        => '12345678',
                'password'   => Hash::make('password'),
                'role'       => 'admin',
                'is_active'  => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name'       => 'Kasir',
                'nip'        => '2001',
                'password'   => Hash::make('2001'),
                'role'       => 'kasir',
                'is_active'  => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name'       => 'Kasir Tempek',
                'nip'        => '20002',
                'password'   => Hash::make('20002'),
                'role'       => 'kasir',
                'is_active'  => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}