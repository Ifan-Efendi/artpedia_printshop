@extends('layouts.app')

@section('title', 'Buat Pesanan')

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
        text-transform: none;
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

    .cart-price,
    .cart-qty,
    .cart-total {
        font-size: 0.98rem;
        color: #1f2937;
        vertical-align: middle;
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

    .cart-table tfoot .grand-total {
        color: #1f2937;
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

    .cart-footer .btn {
        font-size: 0.92rem;
        font-weight: 600;
    }
</style>
@endpush

@section('content')
    <div class="page-header">
        <div>
            <h1><i class="bi bi-bag-check me-2"></i>Keranjang Pesanan</h1>
            <p>Review item pesanan Anda sebelum checkout</p>
        </div>
    </div>

    @error('checkout')
        <div class="alert alert-danger" role="alert">
            <strong>{{ $message }}</strong>
        </div>
    @enderror

    <div class="card cart-shell">
        <div class="card-body p-0">
            @if(count($cart) > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 cart-table">
                        <thead>
                            <tr>
                                <th>Products</th>
                                <th class="text-center">Price</th>
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
                                    <td class="text-center cart-price">Rp. {{ number_format($item['harga_satuan'], 0, ',', '.') }}</td>
                                    <td class="text-center cart-qty">
                                        <span class="cart-qty-value">{{ $item['jumlah'] }}</span>
                                    </td>
                                    <td class="text-center cart-total">Rp. {{ number_format($item['total_harga'], 0, ',', '.') }}</td>
                                    <td class="text-center">
                                        <form action="{{ route('pelanggan.cart.remove', $item['id']) }}" method="POST" onsubmit="return confirm('Hapus item ini dari keranjang?')">
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
                                <td class="text-center grand-total">Rp. {{ number_format($total, 0, ',', '.') }}</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="cart-footer">
                    <div class="d-flex gap-2 flex-wrap">
                        <a href="{{ route('pelanggan.pesanan.create') }}" class="btn btn-outline-primary px-4">
                            <i class="bi bi-chevron-left me-1"></i> Tambah Pesanan
                        </a>
                        <form action="{{ route('pelanggan.cart.clear') }}" method="POST" onsubmit="return confirm('Kosongkan semua item keranjang?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-outline-danger px-4" type="submit">
                                <i class="bi bi-trash3 me-1"></i> Kosongkan
                            </button>
                        </form>
                    </div>
                    <div>
                        <form action="{{ route('pelanggan.checkout.process') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-success px-4">
                                Checkout <i class="bi bi-chevron-right ms-1"></i>
                            </button>
                        </form>
                    </div>
                </div>
            @else
                <div class="empty-state py-5">
                    <i class="bi bi-basket"></i>
                    <h5>Keranjang masih kosong</h5>
                    <p class="text-muted">Pilih produk dari katalog lalu sesuaikan spesifikasinya.</p>
                    <a href="{{ route('katalog') }}" class="btn btn-primary">
                        <i class="bi bi-grid me-2"></i> Lihat Katalog
                    </a>
                </div>
            @endif
        </div>
    </div>
@endsection
