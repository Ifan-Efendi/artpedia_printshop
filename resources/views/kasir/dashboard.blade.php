@extends('layouts.app')

@section('title', 'Dashboard Kasir')

@section('content')
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h1>Dashboard Kasir</h1>
            <p>Pantau dan verifikasi pembayaran pesanan masuk.</p>
        </div>
        <a href="{{ route('kasir.pesanan.create') }}" class="btn btn-primary shadow">
            <i class="bi bi-plus-lg me-2"></i> Buat Pesanan Baru
        </a>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                    <i class="bi bi-shield-exclamation"></i>
                </div>
                <div class="stat-value text-warning">{{ $pendingCount }}</div>
                <div class="stat-label">Menunggu Verifikasi</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-icon bg-success bg-opacity-10 text-success">
                    <i class="bi bi-calendar-check"></i>
                </div>
                <div class="stat-value text-success">{{ $todayValidated }}</div>
                <div class="stat-label">Total Pesanan Diterima</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-icon bg-danger bg-opacity-10 text-danger">
                    <i class="bi bi-x-circle"></i>
                </div>
                <div class="stat-value text-danger">{{ $totalValidated }}</div>
                <div class="stat-label">Total Dibatalkan</div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Pesanan Baru Masuk</h5>
            <a href="{{ route('kasir.pesanan.index') }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
        </div>
        <div class="card-body p-0">
            @if($recentPesanan->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>No. Pesanan</th>
                                <th>Pelanggan</th>
                                <th>Produk</th>
                                <th>Total</th>
                                <th>Tanggal</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentPesanan as $pesanan)
                                <tr>
                                    <td><code>{{ $pesanan->nomor_pesanan }}</code></td>
                                    <td>{{ $pesanan->user->name ?? '-' }}</td>
                                    <td>{{ $pesanan->produk->nama ?? '-' }}</td>
                                    <td class="fw-bold text-primary">{{ $pesanan->total_harga_format }}</td>
                                    <td>{{ $pesanan->created_at->format('d M Y, H:i') }}</td>
                                    <td>
                                        <a href="{{ route('kasir.pesanan.show', $pesanan->id) }}" class="btn btn-sm btn-success">
                                            <i class="bi bi-shield-check me-1"></i> Verifikasi
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="empty-state py-5">
                    <i class="bi bi-check2-circle text-success"></i>
                    <h5>Semua pesanan sudah diverifikasi!</h5>
                    <p class="text-muted">Tidak ada pesanan pending saat ini.</p>
                </div>
            @endif
        </div>
    </div>
@endsection
