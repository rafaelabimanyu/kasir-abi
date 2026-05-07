<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShiftController extends Controller
{
    /**
     * Mulai shift baru (JSON API).
     */
    public function start(Request $request)
    {
        $user = Auth::user();

        // Cek apakah sudah ada shift aktif
        $activeShift = Shift::where('user_id', $user->id)->whereNull('ended_at')->first();
        if ($activeShift) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah memiliki shift aktif.',
                'shift'   => $this->formatShift($activeShift),
            ], 422);
        }

        $validated = $request->validate([
            'opening_cash' => 'required|integer|min:0',
        ]);

        $shift = Shift::create([
            'user_id'      => $user->id,
            'started_at'   => now(),
            'opening_cash' => $validated['opening_cash'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Shift berhasil dimulai.',
            'shift'   => $this->formatShift($shift),
        ]);
    }

    /**
     * Tutup shift aktif (JSON API).
     */
    public function close(Request $request)
    {
        $user = Auth::user();
        $shift = Shift::where('user_id', $user->id)->whereNull('ended_at')->first();

        if (!$shift) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada shift aktif.',
            ], 422);
        }

        $validated = $request->validate([
            'closing_cash' => 'required|integer|min:0',
            'notes'        => 'nullable|string|max:500',
        ]);

        $shift->update([
            'ended_at'     => now(),
            'closing_cash' => $validated['closing_cash'],
            'notes'        => $validated['notes'] ?? null,
        ]);

        $shift->refresh();

        // Rekap shift
        $totalRevenue     = $shift->transactions()->sum('total');
        $totalTransactions = $shift->transactions()->count();
        $cashRevenue      = $shift->transactions()->where('payment_method', 'cash')->sum('total');
        $transferRevenue  = $shift->transactions()->where('payment_method', 'transfer')->sum('total');
        $qrisRevenue      = $shift->transactions()->where('payment_method', 'qris')->sum('total');
        $totalExpenses    = $shift->expenses()->sum('amount');
        $expectedCash     = $shift->opening_cash + $cashRevenue - $totalExpenses;
        $difference       = $validated['closing_cash'] - $expectedCash;

        return response()->json([
            'success' => true,
            'message' => 'Shift berhasil ditutup.',
            'recap'   => [
                'shift_id'       => $shift->id,
                'started_at'     => $shift->started_at->format('H:i'),
                'ended_at'       => $shift->ended_at->format('H:i'),
                'duration'       => $shift->started_at->diffForHumans($shift->ended_at, true),
                'opening_cash'   => $shift->opening_cash,
                'closing_cash'   => $validated['closing_cash'],
                'total_revenue'  => $totalRevenue,
                'total_trx'      => $totalTransactions,
                'cash_revenue'   => $cashRevenue,
                'transfer_revenue' => $transferRevenue,
                'qris_revenue'   => $qrisRevenue,
                'total_expenses' => $totalExpenses,
                'expected_cash'  => $expectedCash,
                'difference'     => $difference,
            ],
        ]);
    }

    /**
     * Ambil shift aktif user (JSON API).
     */
    public function active()
    {
        $user = Auth::user();
        $shift = Shift::where('user_id', $user->id)->whereNull('ended_at')->first();

        if (!$shift) {
            return response()->json(['shift' => null]);
        }

        return response()->json([
            'shift' => $this->formatShift($shift),
        ]);
    }

    /**
     * Format shift data untuk JSON response.
     */
    private function formatShift(Shift $shift): array
    {
        return [
            'id'           => $shift->id,
            'started_at'   => $shift->started_at->format('H:i'),
            'started_full' => $shift->started_at->format('d M Y H:i'),
            'opening_cash' => $shift->opening_cash,
            'total_revenue' => $shift->total_revenue,
            'total_trx'    => $shift->total_transactions,
            'duration'     => $shift->started_at->diffForHumans(now(), true),
        ];
    }
    /**
     * Tambahkan pengeluaran shift (JSON API).
     */
    public function addExpense(Request $request)
    {
        $user = Auth::user();
        $shift = Shift::where('user_id', $user->id)->whereNull('ended_at')->first();

        if (!$shift) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada shift aktif.',
            ], 422);
        }

        $validated = $request->validate([
            'amount'      => 'required|integer|min:1',
            'description' => 'required|string|max:255',
        ]);

        $shift->expenses()->create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Pengeluaran berhasil dicatat.',
        ]);
    }

    /**
     * Tampilkan riwayat shift khusus untuk kasir.
     */
    public function myShifts()
    {
        $shifts = Shift::where('user_id', Auth::user()->id)
            ->orderBy('started_at', 'desc')
            ->paginate(15);

        return view('pages.shift.index', compact('shifts'));
    }
}
