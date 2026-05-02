@extends('layouts.app')

@section('title', 'Verifikasi Pesanan ' . $pesanan->nomor_pesanan)

@push('styles')
<style>
    .kasir-detail-card {
        border: 1px solid #f1c3dd;
        border-radius: 8px;
        box-shadow: none;
    }

    .kasir-detail-card .card-header {
        background: #fde7f3;
        border-bottom: 1px solid #f3d3e7;
    }

    .ks-detail-title {
        font-size: 1.05rem;
        font-weight: 700;
        color: #7a0049;
    }

    .ks-label {
        font-size: 0.82rem;
        color: #8b5c7a;
        margin-bottom: 0.1rem;
        display: block;
    }

    .ks-value {
        font-size: 0.95rem;
        color: #5f0038;
        font-weight: 600;
    }

    .ks-meta {
        font-size: 0.88rem;
        color: #8b5c7a;
    }

    .ks-order-code {
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
        font-size: 0.92rem;
        color: #7a0049;
        font-weight: 700;
    }

    .ks-qty {
        font-size: 1.1rem;
        font-weight: 700;
        color: #7a0049;
        line-height: 1;
    }

    .ks-info-box {
        border: 1px solid #f1c3dd;
        border-radius: 8px;
        background: #fff;
        padding: 0.55rem 0.65rem;
        height: 100%;
    }

    .ks-extra-box {
        margin-top: 0.4rem;
        padding: 0.65rem;
        border: 1px solid #f3d3e7;
        border-radius: 8px;
        background: #fdf2f8;
    }

    .ks-extra-item {
        border: 1px solid #f1c3dd;
        border-radius: 8px;
        background: #fff;
        padding: 0.55rem;
        height: 100%;
    }

    .ks-chip {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        border: 1px solid #f1c3dd;
        border-radius: 8px;
        background: #fff;
        color: #7a0049;
        font-size: 0.88rem;
        font-weight: 600;
        padding: 0.3rem 0.6rem;
        width: 100%;
    }

    .timeline {
        position: relative;
        padding-left: 30px;
    }

    .timeline::before {
        content: '';
        position: absolute;
        left: 8px;
        top: 5px;
        bottom: 5px;
        width: 2px;
        background: #e2e8f0;
    }

    .timeline-item {
        position: relative;
        padding-bottom: 1.5rem;
    }

    .timeline-item:last-child {
        padding-bottom: 0;
    }

    .timeline-marker {
        position: absolute;
        left: -26px;
        width: 18px;
        height: 18px;
        border-radius: 50%;
        border: 3px solid white;
        box-shadow: 0 0 0 2px #e2e8f0;
    }

    .timeline-item.completed .timeline-marker {
        box-shadow: 0 0 0 2px #10b981;
    }

    .timeline-item.rejected .timeline-marker {
        box-shadow: 0 0 0 2px #ef4444;
    }
</style>
@endpush

@section('content')
    <div class="page-header">
        <h1 class="h3 fw-bold fw-bold mb-0" style="color: #451a03;">Verifikasi Pembayaran</h1>
    </div>

    <div class="row g-4">
        <!-- Image/Bukti -->
        <div class="col-lg-6">
            <div class="card mb-3 shadow-sm border-0">
                <div class="card-header bg-success text-white py-2">
                    <h5 class="mb-0 small fw-bold"><i class="bi bi-image me-2"></i>Bukti Pembayaran</h5>
                </div>
                <div class="card-body p-2">
                    @if($pesanan->bukti_pembayaran == 'Pesanan Langsung')
                        <div class="p-4 text-center">
                            <i class="bi bi-cash-stack display-4 text-success mb-3"></i>
                            <h5 class="fw-bold">Pembayaran langsung di tempat</h5>
                            <p class="text-muted mb-0">Pesanan ini dibuat dan dibayar di kasir.</p>
                        </div>
                            @else
                            <div class="p-2 text-center">
                            <img src="{{ route('kasir.pesanan.bukti_pembayaran', $pesanan->id) }}" class="img-fluid rounded shadow-sm mb-3"
                                alt="Bukti Bayar" style="max-height: 250px; cursor: pointer;" data-bs-toggle="modal" data-bs-target="#buktiModal">
                            <div class="d-grid">
                                <button type="button" class="btn btn-outline-success btn-sm" data-bs-toggle="modal" data-bs-target="#buktiModal">
                                    <i class="bi bi-fullscreen me-2"></i> Lihat Bukti Transfer
                                </button>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="card shadow-sm border-0 mb-3">
                <div class="card-header bg-light py-2">
                    <h5 class="mb-0 small fw-bold"><i class="bi bi-file-earmark-code me-2" style="color: #9d005e;"></i>File Desain</h5>
                </div>
                <div class="card-body py-2">
                    <div class="d-flex align-items-center justify-content-between">
                        <span class="text-truncate me-2 small">
                            {{ $pesanan->file_desain === 'NANTI_DIKIRIM' ? 'Foto produk menyusul' : basename($pesanan->file_desain) }}
                        </span>
                        @if($pesanan->file_desain === 'NANTI_DIKIRIM')
                            <span class="badge bg-warning text-dark">Menyusul</span>
                        @else
                            <a href="{{ route('kasir.pesanan.file_desain', $pesanan->id) }}" target="_blank"
                                class="btn btn-warning btn-sm px-2 py-1 text-dark">
                                <i class="bi bi-download"></i> Unduh
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-header bg-light py-2">
                    <h5 class="mb-0 small fw-bold"><i class="bi bi-clock-history me-2" style="color: #9d005e;"></i>Tracking Status</h5>
                </div>
                <div class="card-body py-3">
                    <div class="timeline">
                        <div class="timeline-item {{ $pesanan->created_at ? 'completed' : '' }}">
                            <div class="timeline-marker bg-success"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Pesanan Dibuat</h6>
                                <small class="text-muted">{{ $pesanan->created_at->format('d M Y, H:i') }}</small>
                            </div>
                        </div>

                        <div class="timeline-item {{ $pesanan->dikonfirmasi_at ? 'completed' : ($pesanan->status == 'ditolak' ? 'rejected' : '') }}">
                            <div class="timeline-marker {{ $pesanan->dikonfirmasi_at ? 'bg-success' : ($pesanan->status == 'ditolak' ? 'bg-danger' : 'bg-secondary') }}"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">
                                    @if($pesanan->status == 'ditolak')
                                        Pembayaran Ditolak
                                    @else
                                        Pembayaran Divalidasi
                                    @endif
                                </h6>
                                @if($pesanan->status == 'ditolak')
                                    @if($pesanan->dikonfirmasi_at)
                                        <small class="text-muted">{{ $pesanan->dikonfirmasi_at->format('d M Y, H:i') }}</small>
                                    @endif
                                    @if($pesanan->kasir)
                                        <br><small class="text-muted">Oleh: {{ $pesanan->kasir->name }}</small>
                                    @endif
                                    <br><small class="text-danger">{{ $pesanan->alasan_penolakan ?: 'Pesanan ditolak oleh kasir.' }}</small>
                                @elseif($pesanan->dikonfirmasi_at)
                                    <small class="text-muted">{{ $pesanan->dikonfirmasi_at->format('d M Y, H:i') }}</small>
                                    @if($pesanan->kasir)
                                        <br><small class="text-muted">Oleh: {{ $pesanan->kasir->name }}</small>
                                    @endif
                                @else
                                    <small class="text-muted">Menunggu verifikasi kasir</small>
                                @endif
                            </div>
                        </div>

                        <div class="timeline-item {{ $pesanan->mulai_produksi_at ? 'completed' : '' }}">
                            <div class="timeline-marker {{ $pesanan->mulai_produksi_at ? 'bg-success' : 'bg-secondary' }}"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Dalam Produksi</h6>
                                @if($pesanan->mulai_produksi_at)
                                    <small class="text-muted">{{ $pesanan->mulai_produksi_at->format('d M Y, H:i') }}</small>
                                    @if($pesanan->operator)
                                        <br><small class="text-muted">Oleh: {{ $pesanan->operator->name }}</small>
                                    @endif
                                @else
                                    <small class="text-muted">-</small>
                                @endif
                            </div>
                        </div>

                        <div class="timeline-item {{ $pesanan->selesai_produksi_at ? 'completed' : '' }}">
                            <div class="timeline-marker {{ $pesanan->selesai_produksi_at ? 'bg-success' : 'bg-secondary' }}"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Produksi Selesai</h6>
                                @if($pesanan->selesai_produksi_at)
                                    <small class="text-muted">{{ $pesanan->selesai_produksi_at->format('d M Y, H:i') }}</small>
                                @else
                                    <small class="text-muted">-</small>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Details and Actions -->
        <div class="col-lg-6">
            <div class="card mb-3 kasir-detail-card">
                <div class="card-header py-2">
                    <h5 class="mb-0 ks-detail-title">Detail Pesanan</h5>
                </div>
                <div class="card-body py-2">
                    <div class="row g-2">
                        <div class="col-6">
                            <div class="ks-info-box">
                                <label class="ks-label">No. Pesanan</label>
                                <div class="ks-order-code">{{ $pesanan->nomor_pesanan }}</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="ks-info-box">
                                <label class="ks-label">Tanggal</label>
                                <div class="ks-value">{{ $pesanan->created_at->format('d M Y, H:i') }}</div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="ks-info-box">
                                <label class="ks-label">Pelanggan</label>
                                <div class="ks-value">{{ $pesanan->user->name }}</div>
                                <div class="ks-meta">{{ $pesanan->user->telepon }}</div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="ks-info-box">
                                <label class="ks-label">Produk & Spesifikasi</label>
                                <div class="ks-value">{{ $pesanan->produk->nama ?? '-' }}</div>
                                <div class="ks-meta mb-2">{{ $pesanan->ukuranKertas->nama ?? '-' }} | {{ $pesanan->jenisKertas->nama ?? '-' }}</div>
                                <span class="ks-qty">{{ $pesanan->jumlah }}</span>
                                <span class="ks-meta ms-1">{{ str_contains($pesanan->produk->slug, 'kartu-nama') ? 'pcs' : 'lembar' }}</span>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="ks-extra-box row g-3">
                                <div class="col-md-6">
                                    <div class="ks-extra-item">
                                        <label class="ks-label">Finishing</label>
                                        <span class="ks-chip"><i class="bi bi-magic"></i>{{ $pesanan->finishing ?? '-' }}</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="ks-extra-item">
                                        <label class="ks-label">Opsi Potong</label>
                                        <span class="ks-chip"><i class="bi bi-scissors"></i>{{ $pesanan->opsi_potong ?? '-' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="ks-info-box">
                                <label class="ks-label">Status Pembayaran</label>
                                <div class="ks-value">Lunas</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="ks-info-box">
                                <label class="ks-label">Total Pembayaran</label>
                                <div class="ks-value">{{ $pesanan->total_harga_format }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if($pesanan->status == 'pending')
                <!-- Action Form -->
                <div class="card border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
                    <div class="card-body p-4">
                        <h6 class="fw-bold mb-4 text-center" style="color: #9d005e;"><i class="bi bi-shield-check me-2"></i>Tindakan Kasir</h6>

                        <div class="row g-3">
                            <!-- Accept Button -->
                            <div class="col-md-6">
                                <button type="button" class="btn btn-success w-100 py-2 fw-bold" data-bs-toggle="modal" data-bs-target="#terimaModal">
                                    <i class="bi bi-check-circle me-1"></i> Terima Pesanan
                                </button>
                            </div>

                            <!-- Reject Button -->
                            <div class="col-md-6">
                                <button type="button" class="btn btn-danger w-100 py-2 fw-bold" data-bs-toggle="modal" data-bs-target="#tolakModal">
                                    <i class="bi bi-x-circle me-1"></i> Tolak Pesanan
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Terima Pesanan -->
                <div class="modal fade" id="terimaModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content border-0 shadow-lg" style="border-radius: 14px; overflow: hidden;">
                            <div class="modal-body p-4 text-center">
                                <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle"
                                    style="width: 58px; height: 58px; background: rgba(22, 163, 74, 0.12); color: #15803d;">
                                    <i class="bi bi-check2-circle" style="font-size: 1.9rem;"></i>
                                </div>
                                <h5 class="fw-bold mb-2">Terima Pesanan?</h5>
                                <p class="text-muted mb-0">
                                    Pesanan <strong>{{ $pesanan->nomor_pesanan }}</strong> akan divalidasi dan masuk ke antrian produksi.
                                </p>
                            </div>
                            <div class="modal-footer border-0 bg-light px-4 pb-4 pt-3">
                                <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Batal</button>
                                <form action="{{ route('kasir.pesanan.validasi', $pesanan->id) }}" method="POST" class="m-0">
                                    @csrf
                                    <button type="submit" class="btn btn-success px-4 fw-bold">
                                        <i class="bi bi-check-circle me-1"></i> Ya, Terima
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Tolak Pesanan -->
                <div class="modal fade" id="tolakModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content border-0 shadow-lg" style="border-radius: 14px; overflow: hidden;">
                            <div class="modal-body p-4 text-center">
                                <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle"
                                    style="width: 58px; height: 58px; background: rgba(220, 38, 38, 0.12); color: #b91c1c;">
                                    <i class="bi bi-x-circle" style="font-size: 1.9rem;"></i>
                                </div>
                                <h5 class="fw-bold mb-2">Tolak Pesanan?</h5>
                                <p class="text-muted mb-0">
                                    Pesanan <strong>{{ $pesanan->nomor_pesanan }}</strong> akan ditolak dan tidak masuk ke antrian produksi.
                                </p>
                            </div>
                            <div class="modal-footer border-0 bg-light px-4 pb-4 pt-3">
                                <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Batal</button>
                                <form action="{{ route('kasir.pesanan.tolak', $pesanan->id) }}" method="POST" class="m-0">
                                    @csrf
                                    <button type="submit" class="btn btn-danger px-4 fw-bold">
                                        <i class="bi bi-x-circle me-1"></i> Ya, Tolak
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                @if($pesanan->status == 'dalam_antrian')
                    <!-- Action Form for Dalam Antrian -->
                    <div class="card border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
                        <div class="card-body p-4">
                            <h6 class="fw-bold mb-4 text-center" style="color: #9d005e;"><i class="bi bi-shield-check me-2"></i>Tindakan Kasir</h6>

                            <div class="row g-3">
                                <!-- Cancel Button -->
                                <div class="col-12">
                                    <button type="button" class="btn btn-danger w-100 py-2 fw-bold" data-bs-toggle="modal" data-bs-target="#batalkanModal">
                                        <i class="bi bi-x-circle me-1"></i> Batalkan Pesanan
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Batalkan Pesanan -->
                    <div class="modal fade" id="batalkanModal" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content border-0 shadow-lg" style="border-radius: 14px; overflow: hidden;">
                                <div class="modal-body p-4 text-center">
                                    <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle"
                                        style="width: 58px; height: 58px; background: rgba(220, 38, 38, 0.12); color: #b91c1c;">
                                        <i class="bi bi-x-circle" style="font-size: 1.9rem;"></i>
                                    </div>
                                    <h5 class="fw-bold mb-2">Batalkan Pesanan?</h5>
                                    <p class="text-muted mb-0">
                                        Pesanan <strong>{{ $pesanan->nomor_pesanan }}</strong> akan dibatalkan dan tidak dilanjutkan ke proses produksi.
                                    </p>
                                </div>
                                <div class="modal-footer border-0 bg-light px-4 pb-4 pt-3">
                                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Kembali</button>
                                    <form action="{{ route('kasir.pesanan.batalkan', $pesanan->id) }}" method="POST" class="m-0">
                                        @csrf
                                        <button type="submit" class="btn btn-danger px-4 fw-bold">
                                            <i class="bi bi-x-circle me-1"></i> Ya, Batalkan
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="alert alert-{{ $pesanan->status_color }} py-2 mt-2">
                        <span class="small fw-bold">Status: {{ $pesanan->status_label }}</span>
                        @if($pesanan->dikonfirmasi_at)
                            <div class="small opacity-75">Diverifikasi {{ $pesanan->dikonfirmasi_at->format('d/m/y H:i') }} oleh {{ $pesanan->kasir->name }}</div>
                        @endif
                    </div>
                @endif
            @endif
        </div>
    </div>

    @if($pesanan->bukti_pembayaran != 'Pesanan Langsung')
    <!-- Modal Bukti Transfer -->
    <div class="modal fade" id="buktiModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
                <div class="modal-header bg-success text-white py-2">
                    <h5 class="modal-title fw-bold small"><i class="bi bi-image me-2"></i>Bukti Transfer - {{ $pesanan->nomor_pesanan }}</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0 text-center bg-dark d-flex align-items-center justify-content-center" style="min-height: 400px;">
                    <img src="{{ route('kasir.pesanan.bukti_pembayaran', $pesanan->id) }}" class="img-fluid" alt="Bukti Transfer Full">
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
                    <a href="{{ route('kasir.pesanan.bukti_pembayaran', $pesanan->id) }}" target="_blank" class="btn btn-success btn-sm px-3">
                        <i class="bi bi-box-arrow-up-right me-1"></i> Buka di Tab Baru
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endif
@endsection
