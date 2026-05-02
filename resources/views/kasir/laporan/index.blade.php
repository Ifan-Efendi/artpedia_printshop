@extends('layouts.app')

@section('title', 'Laporan Cetak Harian')

@section('content')
<div class="page-header">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h1>Laporan Cetak Harian</h1>
            <p>Rekapitulasi pesanan dan pendapatan harian.</p>
        </div>
        <div class="col-md-4 text-md-end mt-3 mt-md-0">
            <a href="{{ route('kasir.laporan.export', ['tanggal' => $date]) }}" class="btn btn-outline-danger">
                <i class="bi bi-file-earmark-pdf me-2"></i> Export PDF
            </a>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('kasir.laporan.index') }}" method="GET" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label">Pilih Tanggal</label>
                <input type="date" name="tanggal" class="form-control" value="{{ $date }}">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-filter"></i> Filter
                </button>
            </div>
        </form>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                <i class="bi bi-receipt"></i>
            </div>
            <div class="stat-value">{{ number_format($stats['total_pesanan']) }}</div>
            <div class="stat-label">Total Pesanan</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-icon bg-info bg-opacity-10 text-info">
                <i class="bi bi-people"></i>
            </div>
            <div class="stat-value">{{ number_format($stats['total_pelanggan']) }}</div>
            <div class="stat-label">Total Pelanggan</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-icon bg-success bg-opacity-10 text-success">
                <i class="bi bi-cash-stack"></i>
            </div>
            <div class="stat-value">Rp {{ number_format($stats['total_pendapatan'], 0, ',', '.') }}</div>
            <div class="stat-label">Total Pendapatan</div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Rincian Pesanan ({{ $stats['tanggal'] }})</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Waktu Validasi</th>
                        <th>No. Pesanan</th>
                        <th>Pelanggan</th>
                        <th>Produk & Spesifikasi</th>
                        <th class="text-center">Jumlah</th>
                        <th class="text-end">Total Harga</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pesanans as $pesanan)
                    <tr>
                        <td>{{ optional($pesanan->dikonfirmasi_at ?? $pesanan->created_at)->format('H:i') ?? '-' }}</td>
                        <td><code>{{ $pesanan->nomor_pesanan }}</code></td>
                        <td>
                            <div class="fw-bold">{{ optional($pesanan->user)->name ?? 'Pelanggan Umum' }}</div>
                            <div class="small text-muted">{{ optional($pesanan->user)->telepon ?? '-' }}</div>
                        </td>
                        <td>
                            <div class="fw-bold">{{ optional($pesanan->produk)->nama ?? '-' }}</div>
                            <div class="small text-muted">
                                {{ optional($pesanan->ukuranKertas)->nama ?? '-' }} | {{ optional($pesanan->jenisKertas)->nama ?? '-' }}
                            </div>
                        </td>
                        <td class="text-center">{{ $pesanan->jumlah }}</td>
                        <td class="text-end fw-bold">Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}</td>
                        <td>
                            <span class="badge badge-{{ $pesanan->status_color }}" style="color: #000;">
                                {{ $pesanan->status_label }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <i class="bi bi-inbox fs-1 text-muted d-block mb-3"></i>
                            <span class="text-muted">Tidak ada data pesanan untuk tanggal ini.</span>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
