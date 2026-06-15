<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Cetak Harian - {{ $stats['tanggal'] }}</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #9d005e;
            padding-bottom: 10px;
        }
        .header h1 {
            color: #9d005e;
            margin: 0;
            font-size: 24px;
        }
        .stats-table {
            width: 100%;
            margin-bottom: 30px;
            border-collapse: collapse;
        }
        .stats-table td {
            padding: 15px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
        }
        .stat-label {
            color: #64748b;
            font-size: 10px;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        .stat-value {
            font-size: 18px;
            font-weight: bold;
        }
        .main-table {
            width: 100%;
            border-collapse: collapse;
        }
        .payment-table {
            width: 100%;
            margin-bottom: 30px;
            border-collapse: collapse;
        }
        .payment-table th {
            background: #f1f5f9;
            text-align: left;
            padding: 8px;
            border: 1px solid #e2e8f0;
            font-size: 10px;
            text-transform: uppercase;
        }
        .payment-table td {
            padding: 8px;
            border: 1px solid #e2e8f0;
        }
        .main-table th {
            background: #f1f5f9;
            text-align: left;
            padding: 10px;
            border: 1px solid #e2e8f0;
            font-size: 10px;
            text-transform: uppercase;
        }
        .main-table td {
            padding: 10px;
            border: 1px solid #e2e8f0;
            vertical-align: top;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .footer {
            margin-top: 50px;
            text-align: right;
            font-size: 10px;
            color: #64748b;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Artpedia Printshop</h1>
        <p>Laporan Cetak Harian - {{ $stats['tanggal'] }}</p>
    </div>

    <table class="stats-table">
        <tr>
            <td>
                <div class="stat-label">Total Pesanan</div>
                <div class="stat-value">{{ $stats['total_pesanan'] }}</div>
            </td>
            <td>
                <div class="stat-label">Total Pelanggan</div>
                <div class="stat-value">{{ $stats['total_pelanggan'] }}</div>
            </td>
            <td>
                <div class="stat-label">Total Pendapatan</div>
                <div class="stat-value">Rp {{ number_format($stats['total_pendapatan'], 0, ',', '.') }}</div>
            </td>
        </tr>
    </table>

    <table class="payment-table">
        <thead>
            <tr>
                <th>Metode Pembayaran</th>
                <th class="text-center">Total Pesanan</th>
                <th class="text-right">Total Pendapatan</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Cash</td>
                <td class="text-center">{{ $stats['total_pesanan_cash'] }}</td>
                <td class="text-right">Rp {{ number_format($stats['total_pendapatan_cash'], 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Cashless</td>
                <td class="text-center">{{ $stats['total_pesanan_cashless'] }}</td>
                <td class="text-right">Rp {{ number_format($stats['total_pendapatan_cashless'], 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <table class="main-table">
        <thead>
            <tr>
                <th>Waktu Selesai</th>
                <th>No. Pesanan</th>
                <th>Pelanggan</th>
                <th>Produk & Spesifikasi</th>
                <th class="text-center">Jumlah</th>
                <th>Metode Pembayaran</th>
                <th class="text-right">Total</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pesanans as $pesanan)
            <tr>
                <td>{{ optional($pesanan->selesai_produksi_at)->format('H:i') ?? '-' }}</td>
                <td>{{ $pesanan->nomor_pesanan }}</td>
                <td>{{ optional($pesanan->user)->name ?? 'Pelanggan Umum' }}</td>
                <td>
                    {{ optional($pesanan->produk)->nama ?? '-' }}<br>
                    <small style="color: #64748b">
                        {{ optional($pesanan->ukuranKertas)->nama ?? '-' }} | {{ optional($pesanan->jenisKertas)->nama ?? '-' }}
                    </small>
                </td>
                <td class="text-center">{{ $pesanan->jumlah }}</td>
                <td>{{ $pesanan->metode_pembayaran_label }}</td>
                <td class="text-right">Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}</td>
                <td>{{ $pesanan->status_label }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center">Tidak ada pesanan selesai untuk tanggal ini.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada: {{ now()->format('d M Y H:i:s') }} oleh {{ auth()->user()->name }}
    </div>
</body>
</html>
