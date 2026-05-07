<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Penjualan</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 1px solid #ddd; padding-bottom: 10px; }
        .store-name { font-size: 20px; font-weight: bold; margin-bottom: 5px; }
        .report-title { font-size: 16px; margin-bottom: 5px; }
        .period { color: #666; margin-bottom: 10px; }
        .summary { margin-bottom: 20px; }
        .summary table { width: 50%; }
        .summary td { padding: 4px 0; }
        .summary td.label { font-weight: bold; width: 150px; }
        .table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .table th, .table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        .table th { background-color: #f8f9fa; font-weight: bold; }
        .text-right { text-align: right !important; }
        .text-center { text-align: center !important; }
        .footer { margin-top: 30px; text-align: center; font-size: 10px; color: #666; }
    </style>
</head>
<body>

    <div class="header">
        <div class="store-name">{{ \App\Models\Setting::get('store_name', 'Kasir Abi') }}</div>
        <div class="report-title">LAPORAN PENJUALAN</div>
        <div class="period">Periode: {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} s/d {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</div>
    </div>

    <div class="summary">
        <table>
            <tr>
                <td class="label">Total Pendapatan</td>
                <td>: Rp {{ number_format($totalRevenue, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="label">Total Transaksi</td>
                <td>: {{ number_format($totalTransactions) }}</td>
            </tr>
        </table>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Kode Transaksi</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $index => $trx)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $trx->tanggal->format('d M Y') }}</td>
                    <td>#TRX-{{ str_pad($trx->id, 4, '0', STR_PAD_LEFT) }}</td>
                    <td class="text-right">Rp {{ number_format($trx->total, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center">Tidak ada data transaksi pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada: {{ now()->format('d M Y H:i') }}
    </div>

</body>
</html>
