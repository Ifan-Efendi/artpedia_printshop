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

    .qty-box {
        min-width: 44px;
        height: 34px;
        border: 1px solid #9ca3af;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        background: #fff;
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
</style>
@endpush

@section('content')
    <div class="page-header">
        <div>
            <h1><i class="bi bi-cart3 me-2"></i>Keranjang Kasir</h1>
            <p>Review item pesanan langsung sebelum diproses ke antrian produksi.</p>
        </div>
    </div>

    <div class="card cart-shell">
        <div class="card-body p-0">
            @if(count($cart) > 0)
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
                                <tr>
                                    <td>
                                        <div class="cart-product-wrap">
                                            <div class="cart-thumb">
                                                <i class="bi bi-file-earmark-text"></i>
                                            </div>
                                            <div>
                                                <strong class="d-block mb-1 cart-product-title">{{ $item['produk_nama'] }}</strong>
                                                <span class="cart-meta d-block">{{ $item['ukuran_nama'] }} | {{ $item['jenis_nama'] }}</span>
                                                <span class="cart-meta d-block">Finishing: {{ $item['finishing'] ?? 'Tidak Pakai' }}</span>
                                                <span class="cart-meta d-block">Potong: {{ $item['opsi_potong'] ?? 'Potong Kotak' }}</span>
                                                <span class="cart-meta d-block">
                                                    File desain: {{ !empty($item['foto_produk_nanti']) ? 'Menyusul' : 'Sudah diunggah' }}
                                                </span>
                                                @if(!empty($item['catatan']))
                                                    <span class="cart-meta d-block">Catatan: {{ $item['catatan'] }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">Rp {{ number_format($item['harga_satuan'], 0, ',', '.') }}</td>
                                    <td class="text-center">
                                        <span class="qty-box">{{ $item['jumlah'] }}</span>
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
                            <i class="bi bi-chevron-left me-1"></i> Tambah Item
                        </a>
                        <form action="{{ route('kasir.pesanan.item.clear') }}" method="POST" onsubmit="return confirm('Kosongkan semua item keranjang kasir?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-outline-danger px-4" type="submit">
                                <i class="bi bi-trash3 me-1"></i> Kosongkan
                            </button>
                        </form>
                    </div>
                    <a href="{{ route('kasir.checkout') }}" class="btn btn-success px-4">
                        Checkout <i class="bi bi-chevron-right ms-1"></i>
                    </a>
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
        </div>
    </div>
@endsection
