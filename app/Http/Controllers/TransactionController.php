<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Shift;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    /**
     * Tampilkan halaman POS dengan produk dari database.
     */
    public function posIndex()
    {
        $products = Product::with('category')->where('stok', '>', 0)->get();
        $categories = $products->pluck('category.nama')->unique()->values();

        // Cek shift aktif
        $user = Auth::user();

        if (!$user) {
            return redirect('/login');
        }

        $activeShift = Shift::where('user_id', $user->id)->whereNull('ended_at')->first();

        // Cek stok hampir habis
        $lowStockProducts = Product::where('stok', '<=', 5)->where('stok', '>', 0)->get();

        return view('pages.pos', compact('products', 'categories', 'activeShift', 'lowStockProducts'));
    }

    /**
     * Simpan transaksi dari POS (JSON API).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'items'              => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.qty'        => 'required|integer|min:1',
            'items.*.harga'      => 'required|integer|min:0',
            'bayar'              => 'required|integer|min:0',
            'payment_method'     => 'required|in:cash,transfer,qris,e-wallet',
        ]);

        // Cek shift aktif
        $user = Auth::user();
        $activeShift = Shift::where('user_id', $user->id)->whereNull('ended_at')->first();
        if (!$activeShift) {
            return response()->json([
                'success' => false,
                'message' => 'Anda harus memulai shift terlebih dahulu.',
            ], 422);
        }

        try {
            $transaction = DB::transaction(function () use ($validated, $user, $activeShift) {
                $subtotal = 0;

                // Validasi stok & hitung subtotal
                foreach ($validated['items'] as $item) {
                    $product = Product::findOrFail($item['product_id']);
                    if ($product->stok < $item['qty']) {
                        throw new \Exception("Stok {$product->nama} tidak cukup (sisa: {$product->stok})");
                    }
                    $subtotal += $item['harga'] * $item['qty'];
                }

                $discountGlobal = (float) \App\Models\Setting::get('discount_global', '0');
                $discountAmount = $subtotal * ($discountGlobal / 100);
                $subtotalAfterDiscount = $subtotal - $discountAmount;

                $taxEnabled = \App\Models\Setting::get('tax_enabled', '0') == '1';
                $taxPercentage = (float) \App\Models\Setting::get('tax_percentage', '11');
                $taxAmount = $taxEnabled ? $subtotalAfterDiscount * ($taxPercentage / 100) : 0;

                $total = $subtotalAfterDiscount + $taxAmount;

                // Untuk non-cash, bayar = total (exact), kembalian = 0
                if ($validated['payment_method'] !== 'cash') {
                    $bayar = $total;
                    $kembalian = 0;
                } else {
                    $bayar = $validated['bayar'];
                    $kembalian = $bayar - $total;
                }

                if ($validated['payment_method'] === 'cash' && $bayar < $total) {
                    throw new \Exception('Pembayaran kurang dari total');
                }

                // Buat transaksi
                $transaction = Transaction::create([
                    'total'          => $total,
                    'bayar'          => $bayar,
                    'kembalian'      => $kembalian,
                    'payment_method' => $validated['payment_method'],
                    'tanggal'        => now()->toDateString(),
                    'user_id'        => $user->id,
                    'shift_id'       => $activeShift->id,
                ]);

                // Buat items & kurangi stok
                foreach ($validated['items'] as $item) {
                    $transaction->items()->create([
                        'product_id' => $item['product_id'],
                        'qty'        => $item['qty'],
                        'harga'      => $item['harga'],
                    ]);
                    Product::where('id', $item['product_id'])->decrement('stok', $item['qty']);
                }

                return $transaction;
            });

            return response()->json([
                'success'        => true,
                'message'        => 'Transaksi berhasil!',
                'transaction_id' => $transaction->id,
                'kembalian'      => $transaction->kembalian,
                'payment_method' => $transaction->payment_method,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Tampilkan riwayat transaksi.
     */
    public function history(Request $request)
    {
        $query = Transaction::with(['user', 'shift'])->latest();

        // Kasir hanya melihat transaksinya sendiri
        if (!Auth::user()->isAdmin()) {
            $query->where('user_id', Auth::user()->id);
        }

        // Filter
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('tanggal', [$request->start_date, $request->end_date]);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $query->where('id', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }
        
        // Admin Kasir filter
        if (Auth::user()->isAdmin() && $request->filled('kasir_id')) {
            $query->where('user_id', $request->kasir_id);
        }

        $transactions = $query->paginate(15)->withQueryString();
        
        $kasirs = collect();
        if (Auth::user()->isAdmin()) {
            $kasirs = \App\Models\User::where('role', 'kasir')->get();
        }

        if ($request->ajax()) {
            return view('pages.transaksi._table', compact('transactions'))->render();
        }

        return view('pages.transaksi.index', compact('transactions', 'kasirs'));
    }

    /**
     * Tampilkan detail transaksi untuk dicetak atau dilihat.
     */
    public function show(Transaction $transaction)
    {
        // Kasir tidak boleh melihat transaksi orang lain
        if (!Auth::user()->isAdmin() && $transaction->user_id !== Auth::user()->id) {
            abort(403, 'Unauthorized access.');
        }

        $transaction->load(['items.product', 'user', 'voidBy']);
        
        return response()->json([
            'success' => true,
            'transaction' => $transaction,
            'items' => $transaction->items,
            'user' => $transaction->user->name ?? 'System',
        ]);
    }

    /**
     * Tampilkan struk untuk dicetak.
     */
    public function print(Transaction $transaction)
    {
        // Kasir tidak boleh nge-print transaksi orang lain
        if (!Auth::user()->isAdmin() && $transaction->user_id !== Auth::user()->id) {
            abort(403, 'Unauthorized access.');
        }

        $transaction->load(['items.product', 'user']);
        
        return view('pages.transaksi.receipt', compact('transaction'));
    }

    /**
     * Void transaksi (Hanya Admin).
     */
    public function voidTransaction(Request $request, Transaction $transaction)
    {
        if (!Auth::user()->isAdmin()) {
            return back()->with('error', 'Unauthorized action.');
        }

        if ($transaction->status === 'void') {
            return back()->with('error', 'Transaksi sudah di-void.');
        }

        $validated = $request->validate([
            'void_reason' => 'required|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            // Restore stok
            foreach ($transaction->items as $item) {
                if ($item->product_id) {
                    Product::where('id', $item->product_id)->increment('stok', $item->qty);
                }
            }

            // Update status transaksi
            $transaction->update([
                'status' => 'void',
                'void_by' => Auth::user()->id,
                'void_reason' => $validated['void_reason'],
                'void_at' => now(),
            ]);

            DB::commit();
            return back()->with('success', 'Transaksi berhasil di-void.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal melakukan void: ' . $e->getMessage());
        }
    }
}
