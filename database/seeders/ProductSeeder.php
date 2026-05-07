<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // Buat kategori
        $minuman = Category::create(['nama' => 'Minuman']);
        $makanan = Category::create(['nama' => 'Makanan']);
        $snack   = Category::create(['nama' => 'Snack']);

        // Produk Minuman
        $minumanProducts = [
            ['nama' => 'Es Kopi Susu',    'harga' => 20000, 'stok' => 124],
            ['nama' => 'Americano',        'harga' => 18000, 'stok' => 98],
            ['nama' => 'Cappuccino',       'harga' => 22000, 'stok' => 76],
            ['nama' => 'Caffe Latte',      'harga' => 24000, 'stok' => 54],
            ['nama' => 'Teh Manis Dingin', 'harga' => 8000,  'stok' => 200],
            ['nama' => 'Jus Alpukat',      'harga' => 20000, 'stok' => 3],
            ['nama' => 'Jus Jeruk',        'harga' => 15000, 'stok' => 45],
            ['nama' => 'Matcha Latte',     'harga' => 25000, 'stok' => 12],
        ];

        foreach ($minumanProducts as $p) {
            $minuman->products()->create($p);
        }

        // Produk Makanan
        $makananProducts = [
            ['nama' => 'Nasi Goreng Special', 'harga' => 30000, 'stok' => 45],
            ['nama' => 'Mie Ayam Bakso',      'harga' => 25000, 'stok' => 32],
            ['nama' => 'Indomie Goreng',       'harga' => 15000, 'stok' => 80],
            ['nama' => 'Nasi Ayam Geprek',     'harga' => 28000, 'stok' => 4],
        ];

        foreach ($makananProducts as $p) {
            $makanan->products()->create($p);
        }

        // Produk Snack
        $snackProducts = [
            ['nama' => 'Roti Bakar Coklat', 'harga' => 15000, 'stok' => 0],
            ['nama' => 'French Fries',       'harga' => 18000, 'stok' => 67],
            ['nama' => 'Pisang Goreng',      'harga' => 12000, 'stok' => 2],
            ['nama' => 'Kentang Wedges',     'harga' => 20000, 'stok' => 35],
        ];

        foreach ($snackProducts as $p) {
            $snack->products()->create($p);
        }
    }
}
