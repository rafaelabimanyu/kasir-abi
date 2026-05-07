<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Database\Seeder;

class TransactionSeeder extends Seeder
{
    public function run(): void
    {
        $products = Product::all();
        if ($products->isEmpty()) return;

        // Generate 7 hari terakhir data transaksi
        for ($day = 6; $day >= 0; $day--) {
            $date = now()->subDays($day)->toDateString();
            $txCount = rand(5, 15);

            for ($t = 0; $t < $txCount; $t++) {
                $itemCount = rand(1, 4);
                $selectedProducts = $products->random($itemCount);
                $total = 0;
                $items = [];

                foreach ($selectedProducts as $product) {
                    $qty = rand(1, 3);
                    $total += $product->harga * $qty;
                    $items[] = [
                        'product_id' => $product->id,
                        'qty'        => $qty,
                        'harga'      => $product->harga,
                    ];
                }

                $bayar = (int) (ceil($total / 5000) * 5000);

                $transaction = Transaction::create([
                    'total'     => $total,
                    'bayar'     => $bayar,
                    'kembalian' => $bayar - $total,
                    'tanggal'   => $date,
                    'user_id'   => null,
                ]);

                foreach ($items as $item) {
                    $transaction->items()->create($item);
                }
            }
        }
    }
}
