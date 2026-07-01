@extends('layouts.app')

@section('title', 'Keranjang Kasir')

@push('styles')
<style>
    .cart-shell {
        border: 1px solid #f1c3dd;
        border-radius: 8px;
        background: #fff;
    }

    .cart-table thead th {
        background: #fde7f3;
        color: #7a0049;
        font-size: 0.9rem;
        font-weight: 700;
        letter-spacing: 0;
    }

    .cart-table td,
    .cart-table th {
        border-color: #f3d3e7 !important;
    }

    .cart-product-wrap {
        display: flex;
        gap: 1rem;
        align-items: flex-start;
    }

    .cart-thumb {
        width: 78px;
        height: 78px;
        border-radius: 4px;
        background: #fdf2f8;
        border: 1px solid #f3d3e7;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #9d005e;
        font-size: 1.25rem;
        flex-shrink: 0;
        overflow: hidden;
    }

    .cart-thumb img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .cart-meta {
        font-size: 0.92rem;
        line-height: 1.5;
        color: #7a4b68;
    }

    .cart-product-title {
        font-size: 1rem;
        font-weight: 600;
        color: #1f2937;
    }

    .cart-total {
        color: #dc2626;
        font-weight: 700;
    }

    .cart-qty-value {
        font-size: 1rem;
        font-weight: 600;
        color: #1f2937;
    }

    .btn-remove-circle {
        width: 34px;
        height: 34px;
        border-radius: 999px;
        border: 0;
        background: linear-gradient(180deg, #ef4444, #b91c1c);
        color: #fff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .btn-remove-circle:hover {
        background: linear-gradient(180deg, #dc2626, #991b1b);
        color: #fff;
    }

    .cart-table tfoot td {
        background: #fdf2f8;
        font-size: 1.1rem;
        font-weight: 700;
        color: #7a0049;
    }

    .cart-footer {
        border-top: 1px solid #f3d3e7;
        background: #fdf2f8;
        padding: 1rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 0.75rem;
        flex-wrap: wrap;
    }

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

    .customer-box {
        border: 1px solid #f1c3dd;
        border-radius: 10px;
        padding: 1rem;
        background: #fdf2f8;
    }
</style>
@endpush

@section('content')
    <div class="page-header">
        <div>
            <h1><i class="bi bi-cart3 me-2"></i>Keranjang Kasir</h1>
            <p>Review item pesanan langsung sebelum diproses ke antrian produksi.</p>
        </div>
    </div>

    @error('checkout')
        <div class="alert alert-danger" role="alert">
            <strong>{{ $message }}</strong>
        </div>
    @enderror

    @if(count($cart) > 0)
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card cart-shell">
                    <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 cart-table">
                        <thead>
                            <tr>
                                <th>Produk</th>
                                <th class="text-center">Harga</th>
                                <th class="text-center">Qty</th>
                                <th class="text-center">Total</th>
                                <th class="text-center">Hapus</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($cart as $item)
                                @php
                                    $finishing = $item['finishing'] ?? null;
                                    $hasFinishing = !empty($finishing) && strtolower(trim($finishing)) !== 'tidak pakai';
                                    $cutting = $item['opsi_potong'] ?? null;
                                    $hasCutting = in_array($cutting, ['Kiss Cut', 'Die Cut'], true);
                                @endphp
                                <tr>
                                    <td>
                                        <div class="cart-product-wrap">
                                            <div class="cart-thumb">
                                                @if(!empty($item['gambar']))
                                                    <img src="{{ asset('storage/' . $item['gambar']) }}" alt="{{ $item['produk_nama'] }}">
                                                @else
                                                    <i class="bi bi-file-earmark-text"></i>
                                                @endif
                                            </div>
                                            <div>
                                                <strong class="d-block mb-1 cart-product-title">{{ $item['produk_nama'] }}</strong>
                                                @if($hasFinishing)
                                                    <span class="cart-meta d-block">Finishing: {{ $finishing }}</span>
                                                @endif
                                                @if($hasCutting)
                                                    <span class="cart-meta d-block">Potong: {{ $cutting }}</span>
                                                @endif
                                                @if(!empty($item['catatan']))
                                                    <span class="cart-meta d-block">Catatan: {{ $item['catatan'] }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">Rp {{ number_format($item['harga_satuan'], 0, ',', '.') }}</td>
                                    <td class="text-center">
                                        <span class="cart-qty-value">{{ $item['jumlah'] }}</span>
                                    </td>
                                    <td class="text-center cart-total">Rp {{ number_format($item['total_harga'], 0, ',', '.') }}</td>
                                    <td class="text-center">
                                        <form action="{{ route('kasir.pesanan.item.remove', $item['id']) }}" method="POST" onsubmit="return confirm('Hapus item ini dari keranjang?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-remove-circle" title="Hapus">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" class="text-end">Total</td>
                                <td class="text-center text-dark">Rp {{ number_format($total, 0, ',', '.') }}</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="cart-footer">
                    <div class="d-flex gap-2 flex-wrap">
                        <a href="{{ route('kasir.pesanan.create') }}" class="btn btn-outline-primary px-4">
                            <i class="bi bi-chevron-left me-1"></i> Tambah Pesanan
                        </a>
                        <form action="{{ route('kasir.pesanan.item.clear') }}" method="POST" onsubmit="return confirm('Kosongkan semua item keranjang kasir?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-outline-danger px-4" type="submit">
                                <i class="bi bi-trash3 me-1"></i> Kosongkan
                            </button>
                        </form>
                    </div>
                </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header bg-white">
                        <h6 class="mb-0 checkout-title">Data Pelanggan</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="checkout-meta text-muted">Total pesanan langsung</div>
                            <div class="checkout-total">Rp {{ number_format($total, 0, ',', '.') }}</div>
                        </div>

                        <form action="{{ route('kasir.pesanan.store') }}" method="POST" id="checkoutKasirForm">
                            @csrf
                            <div class="customer-box mb-3">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Nama Pelanggan</label>
                                    <input type="text" name="nama_pelanggan" class="form-control @error('nama_pelanggan') is-invalid @enderror" required placeholder="Nama lengkap pelanggan" value="{{ old('nama_pelanggan') }}">
                                    @error('nama_pelanggan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Nomor Telepon / WA</label>
                                    <input type="text" name="telepon_pelanggan" class="form-control @error('telepon_pelanggan') is-invalid @enderror" required placeholder="08xxxxxxxx" value="{{ old('telepon_pelanggan') }}">
                                    @error('telepon_pelanggan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-0">
                                    <label class="form-label fw-bold">Email <span class="fw-normal text-muted">(Opsional)</span></label>
                                    <input type="email" name="email_pelanggan" class="form-control @error('email_pelanggan') is-invalid @enderror" placeholder="email@example.com" value="{{ old('email_pelanggan') }}">
                                    @error('email_pelanggan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="customer-box mb-3">
                                <label class="form-label fw-bold d-block mb-2">Metode Pembayaran</label>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="metode_pembayaran" id="metodeCash" value="cash" {{ old('metode_pembayaran', 'cash') === 'cash' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="metodeCash">
                                        Cash
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="metode_pembayaran" id="metodeMidtrans" value="midtrans" {{ old('metode_pembayaran') === 'midtrans' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="metodeMidtrans">
                                        Cashless
                                    </label>
                                </div>
                                @error('metode_pembayaran')
                                    <div class="text-danger small mt-2">{{ $message }}</div>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-success w-100">
                                <i class="bi bi-check2-circle me-1"></i> Checkout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @else
                <div class="empty-state py-5">
                    <i class="bi bi-basket"></i>
                    <h5>Keranjang kasir masih kosong</h5>
                    <p class="text-muted">Tambahkan item pesanan langsung terlebih dahulu.</p>
                    <a href="{{ route('kasir.pesanan.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-2"></i> Buat Pesanan
                    </a>
                </div>
    @endif
@endsection
