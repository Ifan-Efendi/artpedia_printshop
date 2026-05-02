@extends('layouts.app')

@section('title', 'Checkout Pesanan')

@push('styles')
<style>
    .checkout-meta {
        font-size: 0.92rem;
        line-height: 1.5;
    }

    .checkout-title {
        font-size: 1rem;
        font-weight: 700;
        color: #1f2937;
    }

    .checkout-total {
        font-size: 1.85rem;
        font-weight: 700;
        color: #9d005e;
        line-height: 1.25;
    }

    .payment-box {
        border: 1px solid #f1c3dd;
        border-radius: 10px;
        padding: 0.85rem;
        background: #fdf2f8;
    }

    .bank-line {
        font-size: 0.92rem;
        color: #7a4b68;
        margin-bottom: 0.25rem;
    }

    .qris-thumb-btn {
        border: 1px solid #f1c3dd;
        border-radius: 8px;
        background: #fff;
        padding: 0.5rem;
        width: 100%;
        text-align: center;
    }

    .qris-thumb {
        max-width: 180px;
        width: 100%;
        height: auto;
        border-radius: 6px;
    }

    .qris-hint {
        font-size: 0.84rem;
        color: #7a4b68;
    }
</style>
@endpush

@section('content')
    <div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h1><i class="bi bi-credit-card me-2"></i>Checkout Pesanan</h1>
            <p>Upload bukti pembayaran untuk memproses semua item di keranjang</p>
        </div>
        <a href="{{ route('pelanggan.cart.index') }}" class="btn btn-outline-primary">
            <i class="bi bi-arrow-left me-2"></i> Kembali ke Keranjang
        </a>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-white">
                    <h6 class="mb-0 checkout-title">Ringkasan Item</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Produk</th>
                                    <th>Spesifikasi</th>
                                    <th>Jumlah</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($cart as $item)
                                    <tr>
                                        <td>{{ $item['produk_nama'] }}</td>
                                        <td>
                                            <span class="checkout-meta text-muted d-block">{{ $item['ukuran_nama'] }} | {{ $item['jenis_nama'] }}</span>
                                            <span class="checkout-meta text-muted d-block">Finishing: {{ $item['finishing'] ?? 'Tidak Pakai' }}</span>
                                            <span class="checkout-meta text-muted d-block">Potong: {{ $item['opsi_potong'] ?? 'Potong Kotak' }}</span>
                                        </td>
                                        <td>{{ $item['jumlah'] }}</td>
                                        <td class="fw-bold">Rp {{ number_format($item['total_harga'], 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header bg-white">
                    <h6 class="mb-0 checkout-title">Pembayaran</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="checkout-meta text-muted">Total yang harus dibayar</div>
                        <div class="checkout-total">Rp {{ number_format($total, 0, ',', '.') }}</div>
                    </div>

                    <div class="payment-box mb-3">
                        <div class="checkout-title mb-2">Transfer Bank</div>
                        <div class="bank-line">Bank: <strong>BANK SYARIAH INDONESIA</strong></div>
                        <div class="bank-line">No. Rekening: <strong>6768000090</strong></div>
                        <div class="bank-line mb-3">a.n <strong>PT BKS ARTPEDIA PRINT</strong></div>

                        <div class="checkout-title mb-2">Pembayaran via QRIS</div>
                        <button type="button" class="qris-thumb-btn" data-bs-toggle="modal" data-bs-target="#qrisModal">
                            <img src="{{ asset('images/qris-artpedia.jpg') }}" alt="QRIS Artpedia" class="qris-thumb">
                        </button>
                        <div class="qris-hint mt-2">Klik gambar untuk memperbesar QRIS</div>
                    </div>

                    <form action="{{ route('pelanggan.checkout.process') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="bukti_pembayaran" class="form-label fw-bold">Upload Bukti Pembayaran</label>
                            <input type="file" name="bukti_pembayaran" id="bukti_pembayaran" class="form-control @error('bukti_pembayaran') is-invalid @enderror" accept=".jpg,.jpeg,.png" required>
                            @error('bukti_pembayaran')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="checkout-meta text-muted mt-1">Format JPG/PNG, maksimal 5MB</div>
                        </div>
                        <button type="submit" class="btn btn-success w-100">
                            <i class="bi bi-check2-circle me-1"></i> Konfirmasi Checkout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="qrisModal" tabindex="-1" aria-labelledby="qrisModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="qrisModalLabel">QRIS Pembayaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <img src="{{ asset('images/qris-artpedia.jpg') }}" alt="QRIS Artpedia" class="img-fluid rounded">
                </div>
            </div>
        </div>
    </div>
@endsection
