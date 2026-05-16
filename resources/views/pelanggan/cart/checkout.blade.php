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
            <p>Lanjutkan pembayaran untuk memproses semua item di keranjang</p>
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
                                    @php
                                        $finishing = $item['finishing'] ?? null;
                                        $hasFinishing = !empty($finishing) && strtolower(trim($finishing)) !== 'tidak pakai';
                                        $cutting = $item['opsi_potong'] ?? null;
                                        $hasCutting = in_array($cutting, ['Kiss Cut', 'Die Cut'], true);
                                        $hasSpecs = $hasFinishing || $hasCutting;
                                    @endphp
                                    <tr>
                                        <td>{{ $item['produk_nama'] }}</td>
                                        <td>
                                            @if($hasFinishing)
                                                <span class="checkout-meta text-muted d-block">Finishing: {{ $finishing }}</span>
                                            @endif
                                            @if($hasCutting)
                                                <span class="checkout-meta text-muted d-block">Potong: {{ $cutting }}</span>
                                            @endif
                                            @unless($hasSpecs)
                                                <span class="checkout-meta text-muted d-block">-</span>
                                            @endunless
                                        </td>
                                        <td>{{ $item['jumlah'] }} {{ $item['unit_label'] ?? 'lembar' }}</td>
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

                    <div class="payment-box mb-4">
                        <div class="checkout-title mb-2 text-primary">
                            <i class="bi bi-shield-check me-2"></i>Metode Pembayaran
                        </div>
                        <p class="checkout-meta text-muted mb-0">
                            Metode pembayaran dapat digunakan: QRIS, Transfer Bank (Virtual Account), dan E-Wallet.
                        </p>
                    </div>

                    <form action="{{ route('pelanggan.checkout.process') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-success w-100 py-3 fw-bold">
                            <i class="bi bi-cart-check-fill me-2"></i> Konfirmasi Pemesanan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
