<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate   = $request->input('end_date', now()->toDateString());

        // Summary
        $totalRevenue     = Transaction::whereBetween('tanggal', [$startDate, $endDate])->sum('total');
        $totalTransactions = Transaction::whereBetween('tanggal', [$startDate, $endDate])->count();
        $totalItemsSold   = TransactionItem::whereHas('transaction', fn($q) => $q->whereBetween('tanggal', [$startDate, $endDate]))->sum('qty');
        $avgTransaction   = $totalTransactions > 0 ? round($totalRevenue / $totalTransactions) : 0;

        // Daily chart
        $dailyChart = Transaction::select(
                DB::raw("tanggal as date"),
                DB::raw("SUM(total) as revenue"),
                DB::raw("COUNT(*) as count")
            )
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->groupBy('tanggal')
            ->orderBy('tanggal')
            ->get();

        // Transactions list
        $transactions = Transaction::with('items.product')
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('pages.laporan', compact(
            'startDate', 'endDate',
            'totalRevenue', 'totalTransactions', 'totalItemsSold', 'avgTransaction',
            'dailyChart', 'transactions'
        ));
    }

    public function exportPdf(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate   = $request->input('end_date', now()->toDateString());

        // Summary
        $totalRevenue     = Transaction::whereBetween('tanggal', [$startDate, $endDate])->sum('total');
        $totalTransactions = Transaction::whereBetween('tanggal', [$startDate, $endDate])->count();

        // Transactions list
        $transactions = Transaction::whereBetween('tanggal', [$startDate, $endDate])
            ->orderBy('tanggal', 'asc')
            ->get();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pages.laporan-pdf', compact(
            'startDate', 'endDate',
            'totalRevenue', 'totalTransactions', 'transactions'
        ));

        return $pdf->download('laporan-penjualan-' . $startDate . '-to-' . $endDate . '.pdf');
    }

    public function exportExcel(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate   = $request->input('end_date', now()->toDateString());

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\LaporanExport($startDate, $endDate),
            'laporan-penjualan-' . $startDate . '-to-' . $endDate . '.xlsx'
        );
    }
}
