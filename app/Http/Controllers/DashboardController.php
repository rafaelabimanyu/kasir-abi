<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Shift;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->input('filter', 'today');
        
        $startDate = match($filter) {
            'today' => Carbon::today(),
            '7days' => Carbon::today()->subDays(6),
            '30days' => Carbon::today()->subDays(29),
            default => Carbon::today()
        };
        $endDate = Carbon::today()->endOfDay();

        // Stat cards
        $todayRevenue     = Transaction::whereBetween('tanggal', [$startDate, $endDate])->sum('total');
        $todayTransactions = Transaction::whereBetween('tanggal', [$startDate, $endDate])->count();
        $todayItemsSold   = TransactionItem::whereHas('transaction', fn($q) => $q->whereBetween('tanggal', [$startDate, $endDate]))->sum('qty');
        $todayAvgTransaction = $todayTransactions > 0 ? round($todayRevenue / $todayTransactions) : 0;
        
        $todayCashRevenue = Transaction::whereBetween('tanggal', [$startDate, $endDate])->where('payment_method', 'cash')->sum('total');
        $todayNonCashRevenue = Transaction::whereBetween('tanggal', [$startDate, $endDate])->where('payment_method', '!=', 'cash')->sum('total');

        // Grafik 7 hari terakhir (tetap statis untuk 7 hari atau menyesuaikan, sementara biarkan 7 hari terakhir agar chart tidak berubah)
        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $revenue = Transaction::whereDate('tanggal', $date->toDateString())->sum('total');
            $count   = Transaction::whereDate('tanggal', $date->toDateString())->count();
            $chartData[] = [
                'date'    => $date->translatedFormat('d M'),
                'day'     => $date->translatedFormat('D'),
                'revenue' => $revenue,
                'count'   => $count,
            ];
        }

        // Produk terlaris bulan ini (atau sesuai filter)
        $topProducts = TransactionItem::select('product_id', DB::raw('SUM(qty) as total_qty'), DB::raw('SUM(qty * harga) as total_revenue'))
            ->whereHas('transaction', fn($q) => $q->whereBetween('tanggal', [$startDate, $endDate]))
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->with('product')
            ->get();

        // Transaksi terbaru
        $recentTransactions = Transaction::with('items.product', 'user')
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->latest()
            ->limit(5)
            ->get();
        // Cek shift aktif
        $activeShift = Shift::where('user_id', Auth::id())->whereNull('ended_at')->first();

        if ($request->ajax()) {
            return view('pages.dashboard._content', compact(
                'todayRevenue', 'todayTransactions', 'todayItemsSold', 'todayAvgTransaction',
                'todayCashRevenue', 'todayNonCashRevenue', 'chartData', 'topProducts', 'recentTransactions', 'filter'
            ))->render();
        }

        return view('pages.dashboard', compact(
            'todayRevenue', 'todayTransactions', 'todayItemsSold', 'todayAvgTransaction',
            'todayCashRevenue', 'todayNonCashRevenue', 'chartData', 'topProducts', 'recentTransactions', 'filter', 'activeShift'
        ));
    }
}
