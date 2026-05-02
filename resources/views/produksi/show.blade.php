@extends('layouts.app')

@section('title', 'Detail Produksi ' . $pesanan->nomor_pesanan)

@section('content')
    <div class="page-header">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-2">
                <li class="breadcrumb-item"><a href="{{ route('produksi.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('produksi.antrian') }}">Antrian</a></li>
                <li class="breadcrumb-item active">{{ $pesanan->nomor_pesanan }}</li>
            </ol>
        </nav>
        <h1>Detail Produksi</h1>
    </div>

    <div class="row g-4">
        <!-- Production Details -->
        <div class="col-lg-7">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Detail Pesanan</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3 mb-4">
                        <div class="col-6">
                            <label class="text-muted small">No. Pesanan</label>
                            <div class="h5 fw-bold mb-0"><code>{{ $pesanan->nomor_pesanan }}</code></div>
                        </div>
                        <div class="col-6 text-end">
                            <label class="text-muted small">Status</label>
                            <div><span class="badge badge-{{ $pesanan->status }} fs-6">{{ $pesanan->status_label }}</span>
                            </div>
                        </div>
                        <div class="col-12">
                            <hr>
                        </div>
                        <div class="col-12">
                            <label class="text-muted small">Produk Yang Dicetak</label>
                            <h4 class="fw-bold text-primary">{{ $pesanan->produk->nama ?? '-' }}</h4>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted small">Ukuran Kertas</label>
                            <div class="fw-bold">{{ $pesanan->ukuranKertas->nama ?? '-' }}
                                ({{ $pesanan->ukuranKertas->dimensi ?? '' }})</div>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted small">Jenis Kertas</label>
                            <div class="fw-bold">{{ $pesanan->jenisKertas->nama ?? '-' }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted small">Jumlah Cetak</label>
                            <div class="fw-bold">{{ $pesanan->jumlah }} lembar</div>
                        </div>
                    </div>

                    @if($pesanan->catatan)
                        <div class="bg-warning bg-opacity-10 p-3 rounded border border-warning mb-4">
                            <h6 class="fw-bold text-warning-emphasis"><i class="bi bi-exclamation-triangle-fill"></i> Instruksi
                                Khusus:</h6>
                            <p class="mb-0">{{ $pesanan->catatan }}</p>
                        </div>
                    @endif

                    <div class="d-grid gap-2">
                        @if($pesanan->file_desain === 'NANTI_DIKIRIM')
                            <button type="button" class="btn btn-outline-secondary btn-lg" disabled>
                                <i class="bi bi-hourglass-split me-2"></i> Foto Produk Menyusul
                            </button>
                        @else
                            <a href="{{ route('produksi.download', $pesanan->id) }}" class="btn btn-primary btn-lg text-white">
                                <i class="bi bi-download me-2"></i> Download File Desain
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            @if($pesanan->status == 'dalam_antrian')
                <form action="{{ route('produksi.mulai', $pesanan->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-success btn-lg w-100" {{ $sedangDiprosesCount > 0 ? 'disabled' : '' }}>
                        <i class="bi bi-play-fill me-2"></i> Mulai Kerjakan Pesanan Ini
                    </button>
                    @if($sedangDiprosesCount > 0)
                        <p class="text-danger small text-center mt-2">Selesaikan pekerjaan Anda saat ini sebelum mengambil antrian
                            baru.</p>
                    @endif
                </form>
            @elseif($pesanan->status == 'diproses' && $pesanan->diproses_oleh == auth()->id())
                <form action="{{ route('produksi.selesai', $pesanan->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-success btn-lg w-100"
                        onclick="return confirm('Sudah selesai diproduksi?')">
                        <i class="bi bi-check2-all me-2"></i> Tandai Selesai & Beritahu Pelanggan
                    </button>
                </form>
            @endif
        </div>

        <!-- Info Pelanggan & Lainya -->
        <div class="col-lg-5">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Informasi Pelanggan</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="bg-secondary bg-opacity-10 text-secondary rounded-circle"
                            style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                            <i class="bi bi-person"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-0 text-uppercase">{{ $pesanan->user->name }}</h6>
                            <small class="text-muted">ID Pelanggan: #{{ $pesanan->user->id }}</small>
                        </div>
                    </div>
                    <div class="d-grid gap-2">
                        <a href="https://wa.me/{{ '62' . substr(preg_replace('/[^0-9]/', '', $pesanan->user->telepon), 1) }}" 
                            target="_blank" class="btn btn-sm btn-outline-success">
                            <i class="bi bi-whatsapp me-2"></i> Hubungi Pelanggan
                        </a>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Riwayat Verifikasi</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-start gap-3">
                        <div class="bg-success text-white rounded-circle"
                            style="width: 24px; height: 24px; flex-shrink: 0; display: flex; align-items: center; justify-content: center; font-size: 12px;">
                            <i class="bi bi-check"></i>
                        </div>
                        <div>
                            <h6 class="small fw-bold mb-1">Pembayaran Valid</h6>
                            <p class="small text-muted mb-0">Diverifikasi oleh {{ $pesanan->kasir->name ?? 'Kasir' }}</p>
                            <small
                                class="text-muted">{{ $pesanan->dikonfirmasi_at ? $pesanan->dikonfirmasi_at->format('d M Y, H:i') : '-' }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
