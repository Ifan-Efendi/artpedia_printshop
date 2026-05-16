@extends('layouts.app')

@section('title', 'Katalog Produk')

@section('content')
    <div class="container py-4">
        <div class="page-header">
            <h1><i class="bi bi-grid me-2"></i>Katalog Produk</h1>
            <p>Pilih produk cetak sesuai kebutuhan Anda</p>
        </div>

        <!-- Filter & Search -->
        <div class="card mb-4">
            <div class="card-body">
                <form action="{{ route('katalog') }}" method="GET" class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Kategori</label>
                        <select name="kategori" class="form-select" onchange="this.form.submit()">
                            <option value="">Semua Kategori</option>
                            @foreach($kategoris as $kat)
                                <option value="{{ $kat->slug }}" {{ request('kategori') == $kat->slug ? 'selected' : '' }}>
                                    {{ $kat->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-8">
                        <label class="form-label">Cari Produk</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0">
                                <i class="bi bi-search text-muted"></i>
                            </span>
                            <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="Ketik nama produk & tekan Enter..." value="{{ request('search') }}">
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Products Grid -->
        @if($produks->count() > 0)
            <div class="row g-4">
                @foreach($produks as $produk)
                    <div class="col-md-6 col-lg-4 col-xl-3">
                        <a href="{{ route('katalog.show', $produk->slug) }}" class="text-decoration-none text-dark d-block h-100">
                        <div class="card h-100 shadow-sm border-0 position-relative">
                            <!-- Category Badge -->
                            <span class="badge bg-primary position-absolute top-0 start-0 m-3 shadow-sm z-2">
                                {{ $produk->kategori->nama }}
                            </span>

                            <img src="{{ $produk->gambar_url }}" class="card-img-top" alt="{{ $produk->nama }}"
                                style="height: 180px; object-fit: cover;">
                            <div class="card-body d-flex flex-column p-3">
                                <h6 class="card-title fw-bold mb-2">{{ $produk->nama }}</h6>
                                <p class="card-text text-muted flex-grow-1 mb-2" style="font-size: 0.95rem; line-height: 1.45;">{{ Str::limit($produk->deskripsi, 60) }}</p>
                                <div class="d-flex justify-content-between align-items-center mt-2">
                                    <span class="fw-bold text-primary">{{ $produk->harga_format }}</span>
                                    <span class="btn btn-primary py-1 px-3" style="font-size: 0.9rem;">
                                        <i class="bi bi-eye"></i> Detail
                                    </span>
                                </div>
                            </div>
                        </div>
                        </a>
                    </div>
                @endforeach
            </div>

            <div class="mt-4">
                {{ $produks->withQueryString()->links() }}
            </div>
        @else
            <div class="empty-state">
                <i class="bi bi-inbox"></i>
                <h5>Tidak ada produk ditemukan</h5>
                <p class="text-muted">Coba ubah filter atau kata kunci pencarian</p>
            </div>
        @endif
    </div>
@endsection
