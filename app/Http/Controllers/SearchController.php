<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;

class SearchController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function globalSearch(Request $request)
    {
        $keyword = $request->input('q');

        if (!$keyword || strlen($keyword) < 2) {
            return response()->json([]);
        }

        $results = collect();

        $results = $results->merge($this->searchProducts($keyword));
        $results = $results->merge($this->searchTransactions($keyword));

        /** @var \App\Models\User|null $user */
        $user = auth()->user();
        if ($user && $user->isAdmin()) {
            $results = $results->merge($this->searchUsers($keyword));
        }

        return response()->json($results);
    }

    private function searchProducts(string $keyword)
    {
        return Product::where('nama', 'like', "%{$keyword}%")
            ->orWhere('deskripsi', 'like', "%{$keyword}%")
            ->take(5)
            ->get(['id', 'nama', 'harga'])
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'type' => 'product',
                    'title' => $item->nama,
                    'subtitle' => 'Rp ' . number_format($item->harga, 0, ',', '.'),
                    'url' => route('produk.index', ['search' => $item->nama]),
                    'icon' => 'package'
                ];
            });
    }

    private function searchTransactions(string $keyword)
    {
        return Transaction::where('id', 'like', "%{$keyword}%")
            ->take(5)
            ->get(['id', 'total', 'status'])
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'type' => 'transaction',
                    'title' => 'TRX-' . str_pad($item->id, 4, '0', STR_PAD_LEFT),
                    'subtitle' => 'Total: Rp ' . number_format($item->total, 0, ',', '.') . ' (' . ucfirst($item->status) . ')',
                    'url' => route('transaksi.history', ['search' => $item->id]),
                    'icon' => 'receipt'
                ];
            });
    }

    private function searchUsers(string $keyword)
    {
        return User::where('name', 'like', "%{$keyword}%")
            ->orWhere('email', 'like', "%{$keyword}%")
            ->take(5)
            ->get(['id', 'name', 'role'])
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'type' => 'user',
                    'title' => $item->name,
                    'subtitle' => ucfirst($item->role),
                    'url' => route('users.index', ['search' => $item->name]),
                    'icon' => 'user'
                ];
            });
    }
}
