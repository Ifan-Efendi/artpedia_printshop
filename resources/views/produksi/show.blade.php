@extends('layouts.app')

@section('title', 'Detail Produksi ' . $pesanan->nomor_pesanan)

@push('styles')
<style>
    .prod-detail-card {
        border: 0;
        border-radius: 14px;
        overflow: hidden;
        box-shadow: 0 10px 24px rgba(15, 23, 42, 0.08);
    }

    .prod-detail-card .card-header {
        padding: 0.9rem 1.2rem;
    }

    .prod-detail-card .card-header h5 {
        font-size: 1.05rem;
        font-weight: 700;
    }

    .prod-detail-card .card-body {
        padding: 1.25rem;
    }

    .prod-detail-label {
        display: block;
        margin-bottom: 0.35rem;
        font-size: 0.84rem;
        font-weight: 500;
        color: #6b7280;
    }

    .prod-detail-value {
        font-size: 1rem;
        font-weight: 600;
        line-height: 1.45;
        color: #1f2937;
    }

    .prod-detail-order-id {
        font-size: 1rem;
        font-weight: 700;
        color: #c21875;
        line-height: 1.35;
    }

    .prod-detail-product {
        font-size: 1.45rem;
        font-weight: 700;
        line-height: 1.25;
        color: #9d005e;
        margin-bottom: 0;
    }

    .prod-detail-status-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0.45rem 0.9rem;
        border-radius: 10px;
        font-size: 0.95rem;
        font-weight: 700;
        background: #dcfce7;
        color: #15803d;
    }

    .prod-spec-box {
        height: 100%;
        padding: 0.95rem 1rem;
        border-radius: 12px;
        background: #f8fafc;
        border: 1px solid #e5e7eb;
    }

    .prod-spec-box .prod-detail-value {
        font-size: 0.98rem;
    }

    .prod-divider {
        margin: 0.25rem 0 0.75rem;
        border-color: #e5e7eb;
    }

    .prod-summary-row {
        align-items: start;
        margin-bottom: 0.35rem;
    }

    .prod-product-block {
        margin-top: 0.15rem;
    }
</style>
@endpush

@section('content')
    @php
        $finishing = $pesanan->finishing ?? null;
        $hasFinishing = !empty($finishing) && strtolower(trim($finishing)) !== 'tidak pakai';
        $cutting = $pesanan->opsi_potong ?? null;
        $hasCutting = in_array($cutting, ['Kiss Cut', 'Die Cut'], true);
        $unitLabel = $pesanan->produk->unit_label ?? 'lembar';
    @endphp

    <div class="page-header">
        <h1>Detail Produksi</h1>
    </div>

    <div class="row g-4">
        <!-- Production Details -->
        <div class="col-lg-7">
            <div class="card mb-4 prod-detail-card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Detail Pesanan</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3 mb-4 prod-summary-row">
                        <div class="col-md-7 col-6">
                            <label class="prod-detail-label">No. Pesanan</label>
                            <div class="prod-detail-order-id">{{ $pesanan->nomor_pesanan }}</div>
                        </div>
                        <div class="col-md-5 col-6 text-end">
                            <label class="prod-detail-label">Status</label>
                            <div><span class="prod-detail-status-badge">{{ $pesanan->status_label }}</span>
                            </div>
                        </div>
                        <div class="col-12">
                            <hr class="prod-divider">
                        </div>
                        <div class="col-12 prod-product-block">
                            <label class="prod-detail-label">Produk</label>
                            <h4 class="prod-detail-product">{{ $pesanan->produk->nama ?? '-' }}</h4>
                        </div>
                        <div class="col-md-4">
                            <div class="prod-spec-box">
                                <label class="prod-detail-label">Ukuran</label>
                                <div class="prod-detail-value">{{ $pesanan->ukuranKertas->nama ?? '-' }}
                                    @if(!empty($pesanan->ukuranKertas->dimensi))
                                        <br><span class="text-muted fw-normal">{{ $pesanan->ukuranKertas->dimensi }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="prod-spec-box">
                                <label class="prod-detail-label">Jenis Kertas</label>
                                <div class="prod-detail-value">{{ $pesanan->jenisKertas->nama ?? '-' }}</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="prod-spec-box">
                                <label class="prod-detail-label">Jumlah</label>
                                <div class="prod-detail-value">{{ $pesanan->jumlah }} {{ $unitLabel }}</div>
                            </div>
                        </div>
                        @if($hasFinishing || $hasCutting)
                            <div class="col-12">
                                <div class="row g-3">
                                    @if($hasFinishing)
                                        <div class="col-md-6">
                                            <div class="prod-spec-box">
                                                <label class="prod-detail-label">Finishing</label>
                                                <div class="prod-detail-value">{{ $finishing }}</div>
                                            </div>
                                        </div>
                                    @endif
                                    @if($hasCutting)
                                        <div class="col-md-6">
                                            <div class="prod-spec-box">
                                                <label class="prod-detail-label">Opsi Potong</label>
                                                <div class="prod-detail-value">{{ $cutting }}</div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>

                    @if($pesanan->catatan)
                        <div class="bg-warning bg-opacity-10 p-3 rounded border border-warning mb-4">
                            <h6 class="fw-bold text-warning-emphasis"><i class="bi bi-exclamation-triangle-fill"></i> Catatan
                                Pembeli:</h6>
                            <p class="mb-0">{{ $pesanan->catatan }}</p>
                        </div>
                    @endif

                    <div class="d-grid gap-2">
                        @if($pesanan->file_desain && $pesanan->file_desain !== 'NANTI_DIKIRIM')
                            <a href="{{ route('produksi.download', $pesanan->id) }}" class="btn btn-primary btn-lg text-white">
                                <i class="bi bi-download me-2"></i> Download File Desain
                            </a>
                        @else
                            <button type="button" class="btn btn-outline-secondary btn-lg" disabled>
                                <i class="bi bi-exclamation-circle me-2"></i> File Desain Tidak Tersedia
                            </button>
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
                        <i class="bi bi-check2-all me-2"></i> Tandai Selesai
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
                    <h5 class="mb-0">Status Pembayaran</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-start gap-3">
                        <div class="bg-success text-white rounded-circle"
                            style="width: 24px; height: 24px; flex-shrink: 0; display: flex; align-items: center; justify-content: center; font-size: 12px;">
                            <i class="bi bi-check"></i>
                        </div>
                        <div>
                            <h6 class="small fw-bold mb-1">Pembayaran Berhasil</h6>
                            <small
                                class="text-muted">{{ $pesanan->dikonfirmasi_at ? $pesanan->dikonfirmasi_at->format('d M Y, H:i') : '-' }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
