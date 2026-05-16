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

        .info-row {
            margin-bottom: 0.15rem;
        }

        .info-box {
            border: 1px solid #f1c3dd;
            border-radius: 8px;
            background: #fff;
            padding: 0.55rem 0.65rem;
            height: 100%;
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
        $paymentStatus = $pesanan->transaksi->pembayaran_status ?? $pesanan->pembayaran_status;
        $isPaid = $paymentStatus === 'paid';
        $isCashless = $pesanan->bukti_pembayaran === 'Midtrans Kasir';
        $paymentStatusLabel = $isPaid ? 'Pembayaran Berhasil' : 'Menunggu Pembayaran';
        $canCancel = in_array($pesanan->status, ['pending', 'dalam_antrian'], true) && !$isPaid;
        $isInQueueOrBeyond = in_array($pesanan->status, ['dalam_antrian', 'diproses', 'selesai'], true);
        $snapToken = $pesanan->transaksi->snap_token ?? $pesanan->snap_token;
        $rawEmail = $pesanan->user->email ?? null;
        $phoneDigits = preg_replace('/\D+/', '', (string) ($pesanan->user->telepon ?? ''));
        $emailDigits = preg_replace('/\D+/', '', (string) $rawEmail);
        $isPlaceholderEmail = blank($rawEmail)
            || str_starts_with((string) $rawEmail, 'walkin+')
            || (($phoneDigits !== '') && $emailDigits === $phoneDigits && str_ends_with((string) $rawEmail, '@artpedia.com'));
        $displayEmail = $isPlaceholderEmail ? '-' : $rawEmail;
    @endphp

    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="detail-title mb-0"><i class="bi bi-receipt me-2" style="color: #9d005e;"></i>Detail Pesanan</h1>
            @if($canCancel)
                <button type="button" class="btn btn-danger" style="background-color: #dc3545; border-color: #dc3545;"
                    data-bs-toggle="modal" data-bs-target="#batalPesananModal">
                    <i class="bi bi-trash me-2"></i>Batalkan Pesanan
                </button>
            @endif
        </div>
    </div>

    @if($canCancel)
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
                            Pesanan <strong>{{ $pesanan->nomor_pesanan }}</strong> akan dibatalkan.
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
    @endif

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 detail-card-title">Informasi Pesanan</h5>
                    <span class="badge badge-{{ $pesanan->status }} fs-6">
                        {{ $pesanan->status === 'pending' ? 'Menunggu Pembayaran' : $pesanan->status_label }}
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
                                <div class="detail-value">{{ $paymentStatusLabel }}</div>
                            </div>
                        </div>
                        <div class="col-12 info-row">
                            <div class="info-box">
                                <label class="detail-label">Data Pemesan</label>
                                <div class="detail-value">{{ $pesanan->user->name ?? '-' }}</div>
                                <div class="detail-meta">{{ $pesanan->user->telepon ?? '-' }}</div>
                                <div class="detail-meta">{{ $displayEmail }}</div>
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

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Tracking Status</h5>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item {{ $pesanan->created_at ? 'completed' : '' }}">
                            <div class="timeline-marker bg-success"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Pesanan Dibuat</h6>
                                <small class="text-muted">{{ $pesanan->created_at->format('d M Y, H:i') }}</small>
                            </div>
                        </div>

                        <div class="timeline-item {{ $pesanan->dikonfirmasi_at ? 'completed' : ($pesanan->status === 'ditolak' ? 'rejected' : '') }}">
                            <div class="timeline-marker {{ $pesanan->dikonfirmasi_at ? 'bg-success' : ($pesanan->status === 'ditolak' ? 'bg-danger' : 'bg-secondary') }}"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">
                                    @if($pesanan->status === 'ditolak')
                                        Pembayaran Ditolak
                                    @elseif($isPaid)
                                        Pembayaran Berhasil
                                    @else
                                        Menunggu Pembayaran
                                    @endif
                                </h6>
                                @if($pesanan->status === 'ditolak')
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
                                    <small class="text-muted">{{ $isPaid ? 'Pembayaran berhasil' : 'Menunggu pembayaran' }}</small>
                                @endif
                            </div>
                        </div>

                        <div class="timeline-item {{ $isInQueueOrBeyond ? 'completed' : '' }}">
                            <div class="timeline-marker {{ $isInQueueOrBeyond ? 'bg-success' : 'bg-secondary' }}"></div>
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

                    @if(!$isPaid && $isCashless && $snapToken)
                        <div class="mt-4">
                            <button id="kasir-pay-button" class="btn btn-success w-100 py-2 fw-bold fs-5" data-token="{{ $snapToken }}">
                                <i class="bi bi-credit-card-2-back me-2"></i>Bayar Sekarang
                            </button>
                        </div>
                    @elseif(!$isPaid && !$isCashless)
                        <div class="mt-4">
                            <span class="badge bg-warning text-dark w-100 py-3 fs-6">
                                <i class="bi bi-hourglass-split me-2"></i>{{ $paymentStatusLabel }}
                            </span>
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
                                    <a href="{{ route('kasir.pesanan.file_desain', $item->id) }}" target="_blank"
                                        class="btn btn-sm btn-outline-primary px-3">
                                        <i class="bi bi-eye-fill me-1"></i> Lihat File
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            @if($canCancel)
                <div class="mt-4">
                    <button type="button" class="btn btn-danger w-100 py-2 fw-bold" data-bs-toggle="modal" data-bs-target="#batalPesananModal">
                        <i class="bi bi-trash me-2"></i>Batalkan Pesanan
                    </button>
                </div>
            @endif

            @if(!$isPaid && $isCashless && $snapToken)
                <div class="alert alert-warning py-2 mt-3">
                    <span class="small fw-bold">Status: Menunggu Pembayaran</span>
                    <div class="small opacity-75">Klik tombol Bayar Sekarang pada bagian ringkasan pembayaran untuk melanjutkan.</div>
                </div>
            @elseif($isPaid)
                <div class="alert alert-success py-2 mt-3">
                    <span class="small fw-bold">Status: Pembayaran Berhasil</span>
                    <div class="small opacity-75">Pesanan siap diteruskan ke antrian produksi.</div>
                </div>
            @else
                <div class="alert alert-warning py-2 mt-3">
                    <span class="small fw-bold">Status: {{ $paymentStatusLabel }}</span>
                    <div class="small opacity-75">Pesanan ini masih menunggu pembayaran pelanggan.</div>
                </div>
            @endif
        </div>
    </div>
@endsection

@if((request()->boolean('show_feedback') && $isPaid) || request()->boolean('show_created'))
    <div class="modal fade" id="kasirPaymentSuccessModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 14px; overflow: hidden;">
                <div class="position-absolute top-0 end-0 p-3" style="z-index: 2;">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 text-center">
                    <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle"
                        style="width: 58px; height: 58px; background: rgba(22, 163, 74, 0.12); color: #15803d;">
                        <i class="bi bi-check-circle" style="font-size: 1.9rem;"></i>
                    </div>
                    <h5 class="fw-bold mb-2">{{ request()->boolean('show_created') ? 'Pesanan Berhasil Dibuat' : 'Pembayaran Berhasil' }}</h5>
                    <p class="text-muted mb-0">
                        @if(request()->boolean('show_created'))
                            Pesanan <strong>{{ $pesanan->nomor_pesanan }}</strong> berhasil dibuat dan siap diproses.
                        @else
                            Pembayaran pesanan <strong>{{ $pesanan->nomor_pesanan }}</strong> berhasil diproses.
                        @endif
                    </p>
                </div>
                <div class="modal-footer border-0 bg-light px-4 pb-4 pt-3 d-flex justify-content-center gap-2 flex-wrap">
                    <a href="{{ route('kasir.dashboard') }}"
                        class="btn px-4 text-white fw-bold d-inline-flex align-items-center justify-content-center"
                        style="background-color: #9d005e; border-color: #9d005e; min-width: 190px;">Dashboard</a>
                    <a href="{{ route('kasir.antrian') }}"
                        class="btn btn-success px-4 fw-bold d-inline-flex align-items-center justify-content-center"
                        style="min-width: 190px;">Lihat Antrian</a>
                </div>
            </div>
        </div>
    </div>
@endif

@if(!$isPaid && $isCashless && $snapToken)
    @push('scripts')
        <script src="{{ config('midtrans.is_production') ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}" data-client-key="{{ config('midtrans.client_key') }}"></script>
        <script type="text/javascript">
            const kasirPayButton = document.getElementById('kasir-pay-button');
            const kasirPaymentStatusUrl = "{{ route('kasir.pesanan.payment_status', $pesanan->id) }}";
            let kasirPaymentPollingHandle = null;
            let kasirCurrentMidtransOrderId = null;

            async function checkKasirPaymentStatus() {
                try {
                    const url = kasirCurrentMidtransOrderId
                        ? `${kasirPaymentStatusUrl}?order_id=${encodeURIComponent(kasirCurrentMidtransOrderId)}`
                        : kasirPaymentStatusUrl;

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
                    console.error('Gagal memeriksa status pembayaran kasir', error);
                }

                return false;
            }

            function startKasirPaymentPolling() {
                if (kasirPaymentPollingHandle) return;

                kasirPaymentPollingHandle = setInterval(async function () {
                    const paid = await checkKasirPaymentStatus();
                    if (paid) {
                        clearInterval(kasirPaymentPollingHandle);
                    }
                }, 4000);
            }

            if (kasirPayButton) {
                kasirPayButton.addEventListener('click', function () {
                    window.snap.pay(kasirPayButton.dataset.token, {
                        onSuccess: function (result) {
                            kasirCurrentMidtransOrderId = result.order_id || kasirCurrentMidtransOrderId;
                            checkKasirPaymentStatus();
                            startKasirPaymentPolling();
                        },
                        onPending: function (result) {
                            kasirCurrentMidtransOrderId = result.order_id || kasirCurrentMidtransOrderId;
                            startKasirPaymentPolling();
                        },
                        onError: function () {
                            alert('Pembayaran gagal!');
                        },
                        onClose: function () {
                            startKasirPaymentPolling();
                        }
                    });
                });

                startKasirPaymentPolling();
            }
        </script>
    @endpush
@endif

@if((request()->boolean('show_feedback') && $isPaid) || request()->boolean('show_created'))
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const modalEl = document.getElementById('kasirPaymentSuccessModal');
                if (!modalEl || typeof bootstrap === 'undefined') return;

                const modal = new bootstrap.Modal(modalEl);
                modal.show();

                const currentUrl = new URL(window.location.href);
                if (currentUrl.searchParams.has('show_feedback')) {
                    currentUrl.searchParams.delete('show_feedback');
                }
                if (currentUrl.searchParams.has('show_created')) {
                    currentUrl.searchParams.delete('show_created');
                }
                const nextUrl = currentUrl.pathname
                    + (currentUrl.search ? currentUrl.search : '')
                    + currentUrl.hash;
                window.history.replaceState({}, document.title, nextUrl);
            });
        </script>
    @endpush
@endif
