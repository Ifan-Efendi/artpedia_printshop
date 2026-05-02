@extends('layouts.app')

@section('title', 'Dashboard Produksi')

@section('content')
    <div class="page-header">
        <h1>Dashboard Produksi</h1>
        <p>Selamat datang, Tim Produksi Artpedia!</p>
    </div>

    <!-- Stats -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-icon bg-info bg-opacity-10 text-info">
                    <i class="bi bi-list-ol"></i>
                </div>
                <div class="stat-value">{{ $dalamAntrianCount }}</div>
                <div class="stat-label">Dalam Antrian</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                    <i class="bi bi-gear-wide-connected"></i>
                </div>
                <div class="stat-value">{{ $sedangDiprosesCount }}</div>
                <div class="stat-label">Sedang Dikerjakan</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-icon bg-success bg-opacity-10 text-success">
                    <i class="bi bi-check2-circle"></i>
                </div>
                <div class="stat-value">{{ $selesaiHariIni }}</div>
                <div class="stat-label">Pesanan Selesai</div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Processing Now -->
        <div class="col-lg-7">
            <div class="card h-100 border-success border-2">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="bi bi-play-circle me-2"></i>Sedang Diproses</h5>
                </div>
                <div class="card-body">
                    @if($sedangDiproses)
                        <div class="d-flex justify-content-between align-items-start mb-4">
                            <div>
                                <h3 class="fw-bold mb-1">{{ $sedangDiproses->produk->nama }}</h3>
                                <code class="fs-5">{{ $sedangDiproses->nomor_pesanan }}</code>
                            </div>
                            <span class="badge bg-primary px-3 py-2 fs-6">SANGAT PRIORITAS</span>
                        </div>

                        <div class="bg-light rounded p-3 mb-4">
                            <div class="row g-3">
                                <div class="col-6">
                                    <small class="text-muted d-block">Ukuran Kertas</small>
                                    <strong>{{ $sedangDiproses->ukuranKertas->nama }}</strong>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block">Jenis Kertas</small>
                                    <strong>{{ $sedangDiproses->jenisKertas->nama }}</strong>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block">Jumlah</small>
                                    <strong class="fs-5">{{ $sedangDiproses->jumlah }} lembar</strong>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block">Pelanggan</small>
                                    <strong>{{ $sedangDiproses->user->name }}</strong>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            @if($sedangDiproses->file_desain === 'NANTI_DIKIRIM')
                                <button type="button" class="btn btn-outline-secondary btn-lg" disabled>
                                    <i class="bi bi-hourglass-split me-2"></i> Foto Produk Menyusul
                                </button>
                            @else
                                <a href="{{ route('produksi.download', $sedangDiproses->id) }}"
                                    class="btn btn-outline-primary btn-lg">
                                    <i class="bi bi-download me-2"></i> Download File Desain
                                </a>
                            @endif
                            <form action="{{ route('produksi.selesai', $sedangDiproses->id) }}" method="POST" class="mt-2">
                                @csrf
                                <button type="submit" class="btn btn-success btn-lg w-100"
                                    onclick="return confirm('Produksi sudah selesai?')">
                                    <i class="bi bi-check2-all me-2"></i> Tandai Selesai
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="empty-state py-5">
                            <i class="bi bi-pause-circle text-muted"></i>
                            <h5>Belum ada pesanan yang Anda proses</h5>
                            <p class="text-muted">Ambil pesanan tersingkat dari antrian!</p>
                            <a href="{{ route('produksi.antrian') }}" class="btn btn-primary mt-3">
                                Lihat Antrian
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Next Queue -->
        <div class="col-lg-5">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-list-ol me-2"></i>Pesanan Selanjutnya</h5>
                </div>
                <div class="card-body p-0">
                    @if($antrianBerikutnya->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($antrianBerikutnya as $antri)
                                <div class="list-group-item p-3">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="fw-bold text-primary">Antrian</span>
                                        <small class="text-muted">{{ $antri->created_at->diffForHumans() }}</small>
                                    </div>
                                    <h6 class="mb-1 text-truncate">{{ $antri->produk->nama }}</h6>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <code class="small">{{ $antri->nomor_pesanan }}</code>
                                        <a href="{{ route('produksi.show', $antri->id) }}"
                                            class="btn btn-sm btn-link p-0">Detail</a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="p-3 bg-light border-top">
                            <a href="{{ route('produksi.antrian') }}" class="btn btn-sm btn-outline-primary w-100">Buka Antrian
                                Lengkap</a>
                        </div>
                    @else
                        <div class="p-5 text-center text-muted">
                            <i class="bi bi-emoji-smile fs-1 d-block mb-3"></i>
                            Tidak ada antrian produksi
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
