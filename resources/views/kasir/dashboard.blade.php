@extends('layouts.app')

@section('title', 'Dashboard Kasir')

@section('content')
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h1>Dashboard Kasir</h1>
            <p>Pantau pesanan masuk, antrian produksi, dan pesanan selesai.</p>
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
                <div class="stat-value text-warning">{{ $pesananMasukCount }}</div>
                <div class="stat-label">Menunggu Pembayaran</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-icon bg-success bg-opacity-10 text-success">
                    <i class="bi bi-calendar-check"></i>
                </div>
                <div class="stat-value text-success">{{ $pesananAntrianCount }}</div>
                <div class="stat-label">Pesanan Dalam Antrian</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                    <i class="bi bi-check2-circle"></i>
                </div>
                <div class="stat-value text-primary">{{ $pesananSelesaiCount }}</div>
                <div class="stat-label">Pesanan Selesai</div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Pesanan Menunggu Pembayaran</h5>
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
                                        <div class="d-flex gap-1 flex-wrap">
                                            <a href="{{ route('kasir.pesanan.show', $pesanan->id) }}" class="btn btn-sm text-white" style="background: #c00073; border-color: #c00073;">
                                                <i class="bi bi-eye me-1"></i> Detail
                                            </a>
                                            @if($pesanan->status === 'pending' && $pesanan->pembayaran_status === 'pending')
                                                <form action="{{ route('kasir.pesanan.batalkan', $pesanan->id) }}" method="POST" onsubmit="return confirm('Batalkan pesanan {{ $pesanan->nomor_pesanan }}?')">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                        <i class="bi bi-x-circle me-1"></i> Batal
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="empty-state py-5">
                    <i class="bi bi-check2-circle text-success"></i>
                    <h5>Tidak ada pesanan menunggu pembayaran</h5>
                    <p class="text-muted">Pesanan yang sudah dibayar akan masuk ke antrian produksi.</p>
                </div>
            @endif
        </div>
    </div>
@endsection
