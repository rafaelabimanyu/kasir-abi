<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Transaksi #{{ str_pad($transaction->id, 4, '0', STR_PAD_LEFT) }}</title>
    <style>
        /* CSS Khusus Printer Thermal 58mm/80mm */
        @import url('https://fonts.googleapis.com/css2?family=Courier+Prime:ital,wght@0,400;0,700;1,400;1,700&display=swap');
        
        body {
            margin: 0;
            padding: 0;
            background-color: #fff;
            color: #000;
            font-family: 'Courier Prime', 'Courier New', Courier, monospace;
            font-size: 12px;
            line-height: 1.4;
        }

        .receipt-container {
            width: 58mm; /* Sesuaikan dengan printer, bisa 80mm */
            max-width: 100%;
            margin: 0 auto;
            padding: 10px;
        }

        @media print {
            body { font-size: 11px; }
            .receipt-container { width: 100%; padding: 0; }
            .no-print { display: none !important; }
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .font-bold { font-weight: bold; }
        
        .header { margin-bottom: 15px; }
        .store-name { font-size: 16px; font-weight: bold; margin-bottom: 2px; }
        .store-address { font-size: 11px; margin-bottom: 2px; }
        .store-contact { font-size: 11px; }
        
        .divider {
            border-top: 1px dashed #000;
            margin: 8px 0;
        }

        .meta-info {
            display: flex;
            justify-content: space-between;
            font-size: 11px;
            margin-bottom: 3px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
        }

        th { font-weight: bold; text-align: left; border-bottom: 1px dashed #000; padding-bottom: 4px; }
        td { padding: 4px 0; vertical-align: top; }
        
        .item-row td { padding-top: 2px; padding-bottom: 2px; }
        .item-name { display: block; margin-bottom: 2px; }
        
        .totals {
            margin-top: 10px;
        }
        
        .totals-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2px;
        }

        .totals-row.grand-total {
            font-weight: bold;
            font-size: 13px;
            margin-top: 4px;
            margin-bottom: 4px;
        }

        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 11px;
        }
        
        .watermark {
            margin-top: 10px;
            font-size: 9px;
            text-align: center;
        }

        .btn-print {
            display: block;
            width: 100%;
            padding: 10px;
            background-color: #4f46e5;
            color: white;
            border: none;
            border-radius: 6px;
            font-family: inherit;
            font-weight: bold;
            cursor: pointer;
            margin-top: 20px;
            text-align: center;
            text-decoration: none;
        }
        
        .btn-back {
            display: block;
            width: 100%;
            padding: 10px;
            background-color: #e2e8f0;
            color: #475569;
            border: none;
            border-radius: 6px;
            font-family: inherit;
            font-weight: bold;
            cursor: pointer;
            margin-top: 10px;
            text-align: center;
            text-decoration: none;
        }
    </style>
</head>
<body onload="window.print()">

<div class="receipt-container">
    <div class="header text-center">
        <div class="store-name">{{ \App\Models\Setting::get('store_name', 'Kasir Abi') }}</div>
        <div class="store-address">{{ \App\Models\Setting::get('store_address', 'Jl. Contoh Alamat No. 123') }}</div>
        <div class="store-contact">{{ \App\Models\Setting::get('store_phone', '081234567890') }}</div>
        <div style="font-size: 10px; margin-top: 5px; text-transform: capitalize;">
            {{ \Carbon\Carbon::parse($transaction->created_at)->locale('id')->isoFormat('dddd, D MMMM Y - HH:mm') }}
        </div>
    </div>

    <div class="divider"></div>

    <div class="meta-info">
        <span>No: TRX-{{ str_pad($transaction->id, 4, '0', STR_PAD_LEFT) }}</span>
        <span>Metode: {{ ucfirst($transaction->payment_method) }}</span>
    </div>
    <div class="meta-info">
        <span>Kasir: {{ $transaction->user->name ?? 'System' }}</span>
        <span>Status: {{ ucfirst($transaction->status) }}</span>
    </div>

    <div class="divider"></div>

    <table>
        @foreach($transaction->items as $item)
        <tr class="item-row">
            <td colspan="3">
                <span class="item-name">{{ $item->product->nama ?? 'Produk Dihapus' }}</span>
            </td>
        </tr>
        <tr class="item-row">
            <td style="width: 15%">{{ $item->qty }}x</td>
            <td style="width: 35%">{{ number_format($item->harga, 0, ',', '.') }}</td>
            <td style="width: 50%" class="text-right">{{ number_format($item->qty * $item->harga, 0, ',', '.') }}</td>
        </tr>
        @endforeach
    </table>

    <div class="divider"></div>

    @php
        $subtotal = $transaction->items->sum(fn($i) => $i->qty * $i->harga);
        $taxEnabled = \App\Models\Setting::get('tax_enabled', '0') == '1';
        $taxPercentage = (float) \App\Models\Setting::get('tax_percentage', '11');
        $taxAmount = $taxEnabled ? $subtotal * ($taxPercentage / 100) : 0;
    @endphp

    <div class="totals">
        <div class="totals-row">
            <span>Subtotal</span>
            <span>{{ number_format($subtotal, 0, ',', '.') }}</span>
        </div>
        
        @if($taxEnabled)
        <div class="totals-row">
            <span>Pajak ({{ $taxPercentage }}%)</span>
            <span>{{ number_format($taxAmount, 0, ',', '.') }}</span>
        </div>
        @endif

        <div class="totals-row grand-total">
            <span>TOTAL</span>
            <span>Rp {{ number_format($transaction->total, 0, ',', '.') }}</span>
        </div>
        
        <div class="totals-row">
            <span>Bayar ({{ ucfirst($transaction->payment_method) }})</span>
            <span>{{ number_format($transaction->bayar, 0, ',', '.') }}</span>
        </div>
        <div class="totals-row">
            <span>Kembali</span>
            <span>{{ number_format($transaction->kembalian, 0, ',', '.') }}</span>
        </div>
    </div>

    <div class="divider"></div>

    <div class="footer">
        <div>Terima kasih atas kunjungan Anda!</div>
        <div>Barang yang sudah dibeli tidak dapat ditukar/dikembalikan</div>
    </div>
    
    <div class="watermark">
        Powered by Kasir Abi
    </div>

    <!-- Tombol hanya terlihat di browser, disembunyikan saat di-print -->
    <div class="no-print">
        <button onclick="window.print()" class="btn-print">Cetak Ulang</button>
        <button onclick="window.close()" class="btn-back">Tutup</button>
    </div>
</div>

</body>
</html>
