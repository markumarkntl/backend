<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            ['name' => 'Nasi Goreng', 'sku' => 'MKN-001', 'price' => 15000, 'stock' => 100, 'category' => 'Makanan'],
            ['name' => 'Mie Ayam', 'sku' => 'MKN-002', 'price' => 12000, 'stock' => 100, 'category' => 'Makanan'],
            ['name' => 'Ayam Bakar', 'sku' => 'MKN-003', 'price' => 20000, 'stock' => 50, 'category' => 'Makanan'],
            ['name' => 'Es Teh Manis', 'sku' => 'MNM-001', 'price' => 5000, 'stock' => 200, 'category' => 'Minuman'],
            ['name' => 'Es Jeruk', 'sku' => 'MNM-002', 'price' => 6000, 'stock' => 200, 'category' => 'Minuman'],
            ['name' => 'Air Mineral', 'sku' => 'MNM-003', 'price' => 3000, 'stock' => 300, 'category' => 'Minuman'],
            ['name' => 'Kerupuk', 'sku' => 'SNK-001', 'price' => 2000, 'stock' => 500, 'category' => 'Snack'],
            ['name' => 'Tahu Goreng', 'sku' => 'SNK-002', 'price' => 3000, 'stock' => 200, 'category' => 'Snack'],
        ];

        foreach ($products as $product) {
            DB::table('products')->insert([
                'name'       => $product['name'],
                'sku'        => $product['sku'],
                'price'      => $product['price'],
                'stock'      => $product['stock'],
                'category'   => $product['category'],
                'is_active'  => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}