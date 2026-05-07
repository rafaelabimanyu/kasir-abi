<table>
    <tr>
        <td colspan="5">{{ \App\Models\Setting::get('store_name', 'Kasir Abi') }}</td>
    </tr>
    <tr>
        <td colspan="5">Laporan Penjualan</td>
    </tr>
    <tr>
        <td colspan="5">Periode: {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</td>
    </tr>
    <tr>
        <td colspan="5"></td>
    </tr>
    <tr>
        <td colspan="2">Total Pendapatan</td>
        <td colspan="3">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</td>
    </tr>
    <tr>
        <td colspan="2">Total Transaksi</td>
        <td colspan="3">{{ number_format($totalTransactions) }}</td>
    </tr>
    <tr>
        <td colspan="5"></td>
    </tr>
    <tr>
        <th>Tanggal</th>
        <th>Invoice</th>
        <th>Total</th>
        <th>Item</th>
        <th>Payment</th>
    </tr>
    @forelse($transactions as $trx)
        <tr>
            <td>{{ $trx->tanggal->format('Y-m-d') }}</td>
            <td>TRX-{{ str_pad($trx->id, 4, '0', STR_PAD_LEFT) }}</td>
            <td>{{ $trx->total }}</td>
            <td>{{ $trx->items->sum('qty') }}</td>
            <td>{{ strtoupper($trx->payment_method) }}</td>
        </tr>
    @empty
        <tr>
            <td colspan="5">Tidak ada data transaksi</td>
        </tr>
    @endforelse
</table>
