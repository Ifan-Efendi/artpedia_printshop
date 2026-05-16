@extends('layouts.app')

@section('title', 'Dashboard')

@push('styles')
<style>
    @media (max-width: 768px) {
        /* Stats grid: centered and balanced */
        .stats-row .col-6, .stats-row .col-12 {
            padding: 0.25rem !important;
        }
        .stat-card {
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 110px;
            padding: 0.75rem !important;
        }

        /* Horizontal Scroll for Recent Orders */
        .mobile-scroll-container {
            display: flex;
            overflow-x: auto;
            gap: 1rem;
            padding: 0.5rem 0.25rem 1rem;
            scroll-snap-type: x mandatory;
            -webkit-overflow-scrolling: touch;
        }
        .mobile-scroll-container::-webkit-scrollbar {
            display: none; /* Sembunyikan scrollbar agar bersih */
        }
        
        .recent-order-card {
            flex: 0 0 85%; /* Lebar kartu 85% layar supaya kelihatan ada kartu selanjutnya */
            scroll-snap-align: start;
            background: white;
            border-radius: 16px;
            border: 1px solid #f1f5f9;
            padding: 1.25rem;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }

        .order-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            border-bottom: 1px dashed #e2e8f0;
            padding-bottom: 0.75rem;
        }

        .order-card-body p {
            margin-bottom: 0.5rem;
            font-size: 0.85rem;
            display: flex;
            justify-content: space-between;
        }

        /* Hide Original Table on Mobile */
        .desktop-table {
            display: none !important;
        }
    }

    @media (min-width: 769px) {
        .mobile-scroll-container {
            display: none !important;
        }
    }
</style>
@endpush

@section('content')
    <div class="page-header">
        <h1>Dashboard</h1>
        <p>Selamat datang, {{ auth()->user()->name }}!</p>
    </div>

    <!-- Stats -->
    <div class="row g-2 g-md-4 mb-4 stats-row">
        <div class="col-6 col-md-4">
            <div class="stat-card h-100">
                <div class="stat-icon" style="background-color: rgba(157, 0, 94, 0.1); color: #9d005e;">
                    <i class="bi bi-cart-check"></i>
                </div>
                <div class="stat-value text-truncate">{{ $totalPesanan }}</div>
                <div class="stat-label">Total Pesanan</div>
            </div>
        </div>
        <div class="col-6 col-md-4">
            <div class="stat-card h-100">
                <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                    <i class="bi bi-hourglass-split"></i>
                </div>
                <div class="stat-value text-truncate">{{ $pesananAktif }}</div>
                <div class="stat-label">Pesanan Aktif</div>
            </div>
        </div>
        <div class="col-12 col-md-4 mt-2 mt-md-0">
            <div class="stat-card h-100">
                <div class="stat-icon bg-success bg-opacity-10 text-success">
                    <i class="bi bi-check-circle"></i>
                </div>
                <div class="stat-value text-truncate">{{ $pesananSelesai }}</div>
                <div class="stat-label text-truncate">Pesanan Selesai</div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-6">
            <div class="card h-100 quick-action-card">
                <div class="card-body text-center py-4">
                    <i class="bi bi-plus-circle text-primary" style="font-size: 3rem;"></i>
                    <h5 class="mt-3 mb-2">Buat Pesanan</h5>
                    <p class="text-muted d-none d-md-block">Pesan produk cetak dengan mudah</p>
                    <a href="{{ route('pelanggan.pesanan.create') }}" class="btn btn-primary w-100">
                        <i class="bi bi-plus-circle me-1"></i> Pesan
                    </a>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-6">
            <div class="card h-100 quick-action-card">
                <div class="card-body text-center py-4">
                    <i class="bi bi-list-ul" style="font-size: 3rem; color: #9d005e;"></i>
                    <h5 class="mt-3 mb-2">Riwayat</h5>
                    <p class="text-muted d-none d-md-block">Pantau status pesanan Anda</p>
                    <a href="{{ route('pelanggan.pesanan.index') }}" class="btn btn-outline-primary shadow-sm w-100">
                        <i class="bi bi-eye me-1"></i> Lihat
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Orders -->
    <div class="card border-0 bg-transparent shadow-none">
        <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center px-0">
            <h5 class="mb-0 fw-bold"><i class="bi bi-clock-history me-2 text-primary"></i>Pesanan Terbaru</h5>
            <a href="{{ route('pelanggan.pesanan.index') }}" class="btn btn-sm text-primary fw-bold">Lihat Semua</a>
        </div>
        
        @if($recentPesanan->count() > 0)
            <!-- Mobile Horizontal Scroll View -->
            <div class="mobile-scroll-container">
                @foreach($recentPesanan as $pesanan)
                    <div class="recent-order-card">
                        <div class="order-card-header">
                            <code class="text-primary fw-bold">{{ $pesanan->nomor_pesanan }}</code>
                            <span class="badge badge-{{ $pesanan->status }} small">
                                {{ $pesanan->status_label }}
                            </span>
                        </div>
                        <div class="order-card-body">
                            <p><span>Produk:</span> <strong class="text-dark">{{ $pesanan->produk->nama ?? '-' }}</strong></p>
                            <p><span>Jumlah:</span> <strong>{{ $pesanan->jumlah }} {{ $pesanan->produk->unit_label ?? 'lembar' }}</strong></p>
                            <p><span>Tanggal:</span> <span class="text-muted small">{{ $pesanan->created_at->format('d M Y') }}</span></p>
                            <a href="{{ route('pelanggan.pesanan.show', $pesanan->id) }}" class="btn btn-primary btn-sm w-100 mt-2">
                                Detail Pesanan
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Desktop Table View -->
            <div class="card desktop-table">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>No. Pesanan</th>
                                    <th>Produk</th>
                                    <th>Jumlah</th>
                                    <th>Status</th>
                                    <th>Tanggal</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentPesanan as $pesanan)
                                    <tr>
                                        <td><code>{{ $pesanan->nomor_pesanan }}</code></td>
                                        <td>{{ $pesanan->produk->nama ?? '-' }}</td>
                                        <td>{{ $pesanan->jumlah }} {{ $pesanan->produk->unit_label ?? 'lembar' }}</td>
                                        <td>
                                            <span class="badge badge-{{ $pesanan->status }}">
                                                {{ $pesanan->status_label }}
                                            </span>
                                        </td>
                                        <td>{{ $pesanan->created_at->format('d M Y') }}</td>
                                        <td>
                                            <a href="{{ route('pelanggan.pesanan.show', $pesanan->id) }}"
                                                class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @else
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body">
                    <div class="empty-state py-5">
                        <i class="bi bi-inbox"></i>
                        <h5>Belum ada pesanan</h5>
                        <p class="text-muted">Mulai dengan membuat pesanan pertama Anda!</p>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
