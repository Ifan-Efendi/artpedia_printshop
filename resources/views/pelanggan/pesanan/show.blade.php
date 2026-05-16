@extends('layouts.app')

@section('title', 'Detail Pesanan ' . $pesanan->nomor_pesanan)

@push('styles')
    <style>
        :root {
            --text-main: #7a0049;
            --text-sub: #8b5c7a;
            --text-strong: #5f0038;
            --font-label: 0.82rem;
            --font-value: 0.95rem;
            --font-meta: 0.88rem;
        }

        .card {
            border: 1px solid #f1c3dd;
            border-radius: 8px;
            box-shadow: none;
        }

        .card-header {
            background: #fde7f3;
            border-bottom: 1px solid #f3d3e7;
        }

        .detail-title {
            font-size: 1.6rem;
            font-weight: 700;
            color: var(--text-main);
        }

        .detail-card-title {
            font-size: 1.05rem;
            font-weight: 700;
            color: var(--text-main);
        }

        .detail-label {
            font-size: var(--font-label);
            color: var(--text-sub);
            margin-bottom: 0.1rem;
            display: block;
        }

        .detail-value {
            font-size: var(--font-value);
            color: var(--text-strong);
            font-weight: 600;
        }

        .detail-meta {
            font-size: var(--font-meta);
            color: var(--text-sub);
        }

        .order-code {
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
            font-size: 0.92rem;
            color: var(--text-main);
            font-weight: 700;
        }

        .qty-number {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--text-main);
            line-height: 1;
        }

        .info-row {
            margin-bottom: 0.15rem;
        }

        .info-row:last-of-type {
            padding-bottom: 0;
        }

        .info-box {
            border: 1px solid #f1c3dd;
            border-radius: 8px;
            background: #fff;
            padding: 0.55rem 0.65rem;
            height: 100%;
        }

        .extra-box {
            margin-top: 0.4rem;
            padding: 0.65rem;
            border: 1px solid #f3d3e7;
            border-radius: 8px;
            background: #fdf2f8;
        }

        .extra-chip {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            border: 1px solid #f1c3dd;
            border-radius: 8px;
            background: #fff;
            color: var(--text-main);
            font-size: var(--font-meta);
            font-weight: 600;
            padding: 0.3rem 0.6rem;
        }

        .extra-item {
            border: 1px solid #f1c3dd;
            border-radius: 8px;
            background: #fff;
            padding: 0.55rem;
            height: 100%;
        }

        .extra-item .detail-label {
            margin-bottom: 0.35rem;
        }

        .extra-item .extra-chip {
            width: 100%;
            min-height: 34px;
            justify-content: flex-start;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .extra-chip i {
            font-size: 0.88rem;
            color: var(--text-main) !important;
            width: 14px;
            min-width: 14px;
            text-align: center;
        }

        .card-body {
            padding: 0.9rem !important;
        }

        .row.g-3 {
            --bs-gutter-y: 0.6rem;
        }

        .summary-total {
            font-size: 1.6rem;
            font-weight: 700;
            color: var(--text-main);
            line-height: 1.1;
        }

        .timeline-content h6 {
            font-size: 0.98rem;
            color: var(--text-strong);
        }

        .timeline-content small {
            font-size: var(--font-meta);
            color: var(--text-sub);
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

        .order-items-table thead th {
            background: #f8fafc;
            color: var(--text-sub);
            font-size: 0.82rem;
            font-weight: 700;
            border-bottom-color: #f3d3e7;
        }

        .order-items-table td {
            vertical-align: top;
            border-color: #f3d3e7;
        }

        .item-name {
            font-size: 0.98rem;
            font-weight: 700;
            color: var(--text-strong);
        }

        .item-subtext {
            font-size: 0.84rem;
            color: var(--text-sub);
            line-height: 1.5;
        }

        .item-spec-line {
            display: block;
            font-size: 0.86rem;
            color: var(--text-sub);
            line-height: 1.5;
        }

        .item-total {
            font-size: 0.96rem;
            font-weight: 700;
            color: var(--text-main);
        }
    </style>
@endpush

@section('content')
    @php
        $pesananItems = $pesananItems ?? collect([$pesanan]);
        $groupTotal = $groupTotal ?? (int) ($pesanan->transaksi->total_harga ?? $pesananItems->sum('total_harga'));
        $itemCount = $pesananItems->count();
    @endphp

    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="detail-title mb-0"><i class="bi bi-receipt me-2" style="color: #9d005e;"></i>Detail Pesanan</h1>
            @if($pesanan->status == 'pending')
                <button type="button" class="btn btn-danger" style="background-color: #dc3545; border-color: #dc3545;"
                    data-bs-toggle="modal" data-bs-target="#batalPesananModal">
                    <i class="bi bi-trash me-2"></i>Batalkan Pesanan
                </button>
            @endif
        </div>
    </div>

    @if($pesanan->status == 'pending')
        <div class="modal fade" id="batalPesananModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 14px; overflow: hidden;">
                    <div class="modal-body p-4 text-center">
                        <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle"
                            style="width: 58px; height: 58px; background: rgba(220, 38, 38, 0.12); color: #b91c1c;">
                            <i class="bi bi-x-circle" style="font-size: 1.9rem;"></i>
                        </div>
                        <h5 class="fw-bold mb-2">Batalkan Pesanan?</h5>
                        <p class="text-muted mb-0">
                            Apakah Anda yakin ingin membatalkan pesanan ini? Pesanan tidak akan masuk ke antrian.
                        </p>
                    </div>
                    <div class="modal-footer border-0 bg-light px-4 pb-4 pt-3">
                        <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Kembali</button>
                        <form action="{{ route('pelanggan.pesanan.destroy', $pesanan->id) }}" method="POST" class="m-0">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger px-4 fw-bold">
                                <i class="bi bi-x-circle me-1"></i> Ya, Batalkan
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="row g-4">
        <!-- Left: Order Details -->
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 detail-card-title">Informasi Pesanan</h5>
                    <span class="badge badge-{{ $pesanan->status }} fs-6">
                        {{ $pesanan->status == 'pending' ? 'Menunggu Pembayaran' : $pesanan->status_label }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6 info-row">
                            <div class="info-box">
                                <label class="detail-label">Nomor Pesanan</label>
                                <div class="order-code">{{ $pesanan->nomor_pesanan }}</div>
                            </div>
                        </div>
                        <div class="col-md-6 info-row">
                            <div class="info-box">
                                <label class="detail-label">Tanggal Pesanan</label>
                                <div class="detail-value">{{ $pesanan->created_at->format('d M Y, H:i') }}</div>
                            </div>
                        </div>
                        <div class="col-md-6 info-row">
                            <div class="info-box">
                                <label class="detail-label">Jumlah Produk</label>
                                <div class="detail-value">{{ $itemCount }} item</div>
                            </div>
                        </div>
                        <div class="col-md-6 info-row">
                            <div class="info-box">
                                <label class="detail-label">Status Pembayaran</label>
                                <div class="detail-value">
                                    {{ ($pesanan->transaksi->pembayaran_status ?? $pesanan->pembayaran_status) === 'paid' ? 'Pembayaran Berhasil' : 'Menunggu Pembayaran' }}
                                </div>
                            </div>
                        </div>
                        <div class="col-12 info-row">
                            <div class="info-box p-0 overflow-hidden">
                                <div class="table-responsive">
                                    <table class="table table-sm align-middle mb-0 order-items-table">
                                        <thead>
                                            <tr>
                                                <th>Produk</th>
                                                <th>Spesifikasi</th>
                                                <th>Jumlah</th>
                                                <th>Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($pesananItems as $item)
                                                @php
                                                    $itemFinishing = $item->finishing ?? null;
                                                    $itemHasFinishing = !empty($itemFinishing) && strtolower(trim($itemFinishing)) !== 'tidak pakai';
                                                    $itemCutting = $item->opsi_potong ?? null;
                                                    $itemHasCutting = in_array($itemCutting, ['Kiss Cut', 'Die Cut'], true);
                                                @endphp
                                                <tr>
                                                    <td>
                                                        <div class="item-name">{{ $item->produk->nama ?? '-' }}</div>
                                                        <span class="item-subtext">{{ $item->produk->kategori->nama ?? '-' }}</span>
                                                        @if($item->catatan)
                                                            <span class="item-spec-line mt-1">Catatan: {{ $item->catatan }}</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <span class="item-spec-line">Ukuran: {{ $item->ukuranKertas->nama ?? '-' }}{{ !empty($item->ukuranKertas->dimensi) ? ' (' . $item->ukuranKertas->dimensi . ')' : '' }}</span>
                                                        <span class="item-spec-line">Bahan: {{ $item->jenisKertas->nama ?? '-' }}</span>
                                                        @if($itemHasFinishing)
                                                            <span class="item-spec-line">Finishing: {{ $itemFinishing }}</span>
                                                        @endif
                                                        @if($itemHasCutting)
                                                            <span class="item-spec-line">Potong: {{ $itemCutting }}</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <span class="item-spec-line">{{ $item->jumlah }} {{ $item->produk->unit_label ?? 'lembar' }}</span>
                                                    </td>
                                                    <td>
                                                        <span class="item-total">Rp {{ number_format($item->total_harga, 0, ',', '.') }}</span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Status Timeline -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Tracking Status</h5>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <!-- Pesanan Dibuat -->
                        <div class="timeline-item {{ $pesanan->created_at ? 'completed' : '' }}">
                            <div class="timeline-marker bg-success"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Pesanan Dibuat</h6>
                                <small class="text-muted">{{ $pesanan->created_at->format('d M Y, H:i') }}</small>
                            </div>
                        </div>

                        <!-- Pembayaran Divalidasi -->
                        <div
                            class="timeline-item {{ $pesanan->dikonfirmasi_at ? 'completed' : ($pesanan->status == 'ditolak' ? 'rejected' : '') }}">
                            <div
                                class="timeline-marker {{ $pesanan->dikonfirmasi_at ? 'bg-success' : ($pesanan->status == 'ditolak' ? 'bg-danger' : 'bg-secondary') }}">
                            </div>
                            <div class="timeline-content">
                                <h6 class="mb-1">
                                    @if($pesanan->status == 'ditolak')
                                        Pembayaran Ditolak
                                    @elseif(($pesanan->transaksi->pembayaran_status ?? $pesanan->pembayaran_status) == 'paid')
                                        Pembayaran Berhasil
                                    @else
                                        Menunggu Pembayaran
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
                                    <small class="text-muted">
                                        {{ ($pesanan->transaksi->pembayaran_status ?? $pesanan->pembayaran_status) == 'paid' ? 'Pembayaran berhasil' : 'Menunggu pembayaran' }}
                                    </small>
                                @endif
                            </div>
                        </div>

                        <!-- Dalam Antrian -->
                        @php
                            $isInQueueOrBeyond = in_array($pesanan->status, ['dalam_antrian', 'diproses', 'selesai'], true);
                        @endphp
                        <div class="timeline-item {{ $isInQueueOrBeyond ? 'completed' : '' }}">
                            <div class="timeline-marker {{ $isInQueueOrBeyond ? 'bg-success' : 'bg-secondary' }}">
                            </div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Dalam Antrian</h6>
                                @if($isInQueueOrBeyond)
                                    <small class="text-muted">
                                        {{ optional($pesanan->dikonfirmasi_at ?? $pesanan->mulai_produksi_at)->format('d M Y, H:i') ?? '-' }}
                                    </small>
                                    @if($pesanan->operator)
                                        <br><small class="text-muted">Oleh: {{ $pesanan->operator->name }}</small>
                                    @endif
                                @else
                                    <small class="text-muted">-</small>
                                @endif
                            </div>
                        </div>

                        <!-- Selesai -->
                        <div class="timeline-item {{ $pesanan->selesai_produksi_at ? 'completed' : '' }}">
                            <div
                                class="timeline-marker {{ $pesanan->selesai_produksi_at ? 'bg-success' : 'bg-secondary' }}">
                            </div>
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

        <!-- Right: Summary -->
        <div class="col-lg-4">
            <div class="card mb-4 overflow-hidden">
                <div class="card-header py-3">
                    <h5 class="mb-0 detail-card-title"><i class="bi bi-receipt me-2"></i>Ringkasan Pembayaran</h5>
                </div>
                <div class="card-body p-4 text-center">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-2 small">
                            <span class="text-muted">Jumlah Produk:</span>
                            <span class="fw-bold">{{ $itemCount }} item</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2 small">
                            <span class="text-muted">Nomor Pesanan:</span>
                            <span class="fw-bold">{{ $pesanan->nomor_pesanan }}</span>
                        </div>
                    </div>
                    <hr class="my-3">
                    <div class="d-flex justify-content-between align-items-center mb-0">
                        <span class="fw-bold text-dark">Total:</span>
                        <span class="summary-total mb-0">Rp {{ number_format($groupTotal, 0, ',', '.') }}</span>
                    </div>

                    @php
                        $snapToken = $pesanan->transaksi->snap_token ?? $pesanan->snap_token;
                        $isPaid = ($pesanan->transaksi->pembayaran_status ?? $pesanan->pembayaran_status) == 'paid';
                    @endphp

                    @if(!$isPaid && $snapToken)
                        <div class="mt-4">
                            <button id="pay-button" class="btn btn-success w-100 py-2 fw-bold fs-5" data-token="{{ $snapToken }}">
                                <i class="bi bi-credit-card-2-back me-2"></i>Bayar Sekarang
                            </button>
                        </div>
                    @elseif(!$isPaid && !$snapToken)
                        <div class="mt-4">
                            <div class="alert alert-warning small">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                Sistem belum bisa membuat kode pembayaran. Mohon pastikan API Key Midtrans sudah benar.
                            </div>
                        </div>
                    @elseif($isPaid)
                        <div class="mt-4">
                            <span class="badge bg-success w-100 py-3 fs-6">
                                <i class="bi bi-check-circle-fill me-2"></i>Pembayaran Berhasil
                            </span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- File -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-file-earmark me-2"></i>File Pesanan</h5>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex flex-column gap-3">
                        @foreach($pesananItems as $item)
                            <div>
                                <label class="text-muted small fw-bold mb-2 d-block">{{ $item->produk->nama ?? 'File Desain' }}</label>
                                <div class="d-flex align-items-center">
                                    <a href="{{ route('pelanggan.pesanan.file_desain', $item->id) }}" target="_blank"
                                        class="btn btn-sm btn-outline-primary px-3">
                                        <i class="bi bi-eye-fill me-1"></i> Lihat File
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@if(!$isPaid && $snapToken)
    @push('scripts')
        <script src="{{ config('midtrans.is_production') ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}" data-client-key="{{ config('midtrans.client_key') }}"></script>
        <script type="text/javascript">
            const payButton = document.getElementById('pay-button');
            const paymentStatusUrl = "{{ route('pelanggan.pesanan.payment_status', $pesanan->id) }}";
            let paymentPollingHandle = null;
            let currentMidtransOrderId = null;

            async function checkPaymentStatus() {
                try {
                    const url = currentMidtransOrderId
                        ? `${paymentStatusUrl}?order_id=${encodeURIComponent(currentMidtransOrderId)}`
                        : paymentStatusUrl;

                    const response = await fetch(url, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' },
                        cache: 'no-store'
                    });

                    if (!response.ok) return false;

                    const data = await response.json();
                    if (data.is_paid) {
                        window.location.href = data.redirect_url;
                        return true;
                    }
                } catch (error) {
                    console.error('Gagal memeriksa status pembayaran', error);
                }

                return false;
            }

            function startPaymentPolling() {
                if (paymentPollingHandle) return;

                paymentPollingHandle = setInterval(async function () {
                    const paid = await checkPaymentStatus();
                    if (paid) {
                        clearInterval(paymentPollingHandle);
                    }
                }, 4000);
            }

            payButton.addEventListener('click', function () {
                window.snap.pay('{{ $snapToken }}', {
                    onSuccess: function (result) {
                        currentMidtransOrderId = result.order_id || currentMidtransOrderId;
                        checkPaymentStatus();
                        startPaymentPolling();
                    },
                    onPending: function (result) {
                        currentMidtransOrderId = result.order_id || currentMidtransOrderId;
                        startPaymentPolling();
                    },
                    onError: function () {
                        alert("Pembayaran gagal!");
                    },
                    onClose: function () {
                        startPaymentPolling();
                    }
                });
            });

            startPaymentPolling();
        </script>
    @endpush
@endif
