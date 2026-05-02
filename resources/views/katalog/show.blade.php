@extends('layouts.app')

@section('title', $produk->nama)

@section('content')
    <div class="container py-4">
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('landing') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('katalog') }}">Katalog</a></li>
                <li class="breadcrumb-item active">{{ $produk->nama }}</li>
            </ol>
        </nav>

        <div class="row g-4">
            <!-- Product Image -->
            <div class="col-lg-5">
                <div class="card">
                    @if($produk->gambar)
                        <img src="{{ asset('storage/' . $produk->gambar) }}" class="card-img-top" alt="{{ $produk->nama }}"
                            style="object-fit: cover;">
                    @else
                        <div class="bg-light d-flex align-items-center justify-content-center" style="height: 350px;">
                            <i class="bi bi-image text-muted" style="font-size: 5rem;"></i>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Product Info -->
            <div class="col-lg-7">
                <div class="card h-100">
                    <div class="card-body">
                        <span class="badge bg-primary bg-opacity-10 text-primary mb-3">{{ $produk->kategori->nama }}</span>
                        <h2 class="fw-bold mb-3">{{ $produk->nama }}</h2>

                        <div class="mb-4">
                            <span class="fs-3 fw-bold text-primary">{{ $produk->harga_format }}</span>
                            <span class="text-muted">/lembar</span>
                        </div>

                        <div class="mb-4">
                            <h6 class="fw-bold">Deskripsi</h6>
                            <p class="text-muted">{{ $produk->deskripsi ?: 'Tidak ada deskripsi' }}</p>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-6">
                                <div class="bg-light rounded p-3">
                                    <i class="bi bi-stack text-primary me-2"></i>
                                    <small class="text-muted">Min. Order</small>
                                    <div class="fw-bold">{{ $produk->min_order }} lembar</div>
                                </div>
                            </div>
                        </div>

                        @auth
                            @if(auth()->user()->isPelanggan())
                                <a href="{{ route('pelanggan.pesanan.create', ['produk' => $produk->slug]) }}"
                                    class="btn btn-primary btn-lg w-100">
                                    <i class="bi bi-sliders me-2"></i> Pilih & Sesuaikan
                                </a>
                            @endif
                        @else
                            <a href="{{ route('login') }}" class="btn btn-primary btn-lg w-100">
                                <i class="bi bi-box-arrow-in-right me-2"></i> Login untuk Memesan
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </div>

        <!-- Related Products -->
        @if($produkTerkait->count() > 0)
            <div class="mt-5">
                <h4 class="fw-bold mb-4">Produk Terkait</h4>
                <div class="row g-4">
                    @foreach($produkTerkait as $related)
                        <div class="col-6 col-lg-3">
                            <div class="card h-100">
                                @if($related->gambar)
                                    <img src="{{ asset('storage/' . $related->gambar) }}" class="card-img-top"
                                        alt="{{ $related->nama }}" style="height: 150px; object-fit: cover;">
                                @else
                                    <div class="bg-light d-flex align-items-center justify-content-center" style="height: 150px;">
                                        <i class="bi bi-image text-muted" style="font-size: 2rem;"></i>
                                    </div>
                                @endif
                                <div class="card-body">
                                    <h6 class="fw-bold">{{ $related->nama }}</h6>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-primary fw-bold">{{ $related->harga_format }}</span>
                                        <a href="{{ route('katalog.show', $related->slug) }}"
                                            class="btn btn-sm btn-outline-primary">Detail</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
@endsection
