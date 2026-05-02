@extends('layouts.app')

@section('title', 'Checkout Kasir')

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

    .customer-box {
        border: 1px solid #f1c3dd;
        border-radius: 10px;
        padding: 1rem;
        background: #fdf2f8;
    }
</style>
@endpush

@section('content')
    <div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h1><i class="bi bi-cash-coin me-2"></i>Checkout Kasir</h1>
            <p>Lengkapi data pelanggan untuk memproses pesanan langsung.</p>
        </div>
        <a href="{{ route('kasir.cart.index') }}" class="btn btn-outline-primary">
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
                                            <span class="checkout-meta text-muted d-block">File desain: {{ !empty($item['foto_produk_nanti']) ? 'Menyusul' : 'Sudah diunggah' }}</span>
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
                    <h6 class="mb-0 checkout-title">Data Pelanggan</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="checkout-meta text-muted">Total pesanan langsung</div>
                        <div class="checkout-total">Rp {{ number_format($total, 0, ',', '.') }}</div>
                    </div>

                    <form action="{{ route('kasir.pesanan.store') }}" method="POST">
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

                        <button type="submit" class="btn btn-success w-100">
                            <i class="bi bi-check2-circle me-1"></i> Simpan & Proses Antrian
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
