@extends('layouts.app')

@section('title', 'Kelola Produk')

@section('content')
<div class="page-header mb-4 d-flex justify-content-between align-items-center">
    <div>
        <h1 class="fw-bold" style="color: #9d005e;"><i class="bi bi-layers-half me-2"></i>Kelola Produk</h1>
        <p class="text-muted mb-0">Kelola kategori dan produk</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('kasir.kategori.create') }}" class="btn btn-magenta fw-semibold">
            <i class="bi bi-folder-plus me-1"></i>Tambah Kategori
        </a>
    </div>
</div>

<div class="accordion" id="accordionKategori">
    @forelse($kategoris as $index => $kategori)
        <div class="accordion-item mb-2 border rounded-2 overflow-hidden shadow-sm">
            <h2 class="accordion-header" id="heading{{ $kategori->id }}">
                <button class="accordion-button {{ $index === 0 ? '' : 'collapsed' }} py-2 px-3" type="button" 
                        data-bs-toggle="collapse" data-bs-target="#collapse{{ $kategori->id }}" 
                        aria-expanded="{{ $index === 0 ? 'true' : 'false' }}" aria-controls="collapse{{ $kategori->id }}">
                    <div class="d-flex align-items-center justify-content-between w-100 me-2">
                        <div class="d-flex align-items-center gap-2">
                            <div class="kategori-icon-circle-sm">
                                <i class="bi bi-printer"></i>
                            </div>
                            <div>
                                <div class="fw-bold text-dark" style="font-size: 0.95rem;">{{ $kategori->nama }}</div>
                                <small class="text-muted" style="font-size: 0.75rem;">{{ $kategori->produk_count }} produk</small>
                            </div>
                        </div>
                        <div class="d-flex gap-2 align-items-center">
                            <a href="{{ route('kasir.kategori.edit', $kategori->id) }}" class="btn btn-sm btn-outline-primary" title="Edit Kategori">
                                <i class="bi bi-pencil-square"></i>
                            </a>
                        </div>
                    </div>
                </button>
            </h2>
            <div id="collapse{{ $kategori->id }}" class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}" 
                 aria-labelledby="heading{{ $kategori->id }}" data-bs-parent="#accordionKategori">
                <div class="accordion-body bg-light bg-opacity-50 p-0">
                    <div class="p-2 px-3 border-bottom d-flex justify-content-between align-items-center bg-white">
                        <h6 class="mb-0 fw-bold text-muted text-uppercase small tracking-wider" style="font-size: 0.7rem;">Daftar Produk > {{ $kategori->nama }}</h6>
                        <a href="{{ route('kasir.produk.create', ['kategori_id' => $kategori->id]) }}" class="btn btn-sm btn-magenta py-1 px-2" style="font-size: 0.8rem;">
                            <i class="bi bi-plus-lg me-1"></i>Tambah Produk
                        </a>
                    </div>
                    
                    @if($kategori->produk->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0 bg-white">
                                <thead class="table-light">
                                    <tr style="font-size: 0.85rem;">
                                        <th class="ps-3 py-2" style="width: 70px;">Gambar</th>
                                        <th class="py-2">Nama Produk</th>
                                        <th class="py-2">Harga</th>
                                        <th class="text-center py-2">Min. Order</th>
                                        <th class="text-end pe-3 py-2" style="width: 100px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($kategori->produk as $produk)
                                        <tr style="font-size: 0.9rem;">
                                            <td class="ps-3 py-2 align-middle">
                                                @if($produk->gambar)
                                                    <img src="{{ asset('storage/' . $produk->gambar) }}" alt="{{ $produk->nama }}" 
                                                         class="rounded shadow-sm" style="width: 36px; height: 36px; object-fit: cover;">
                                                @else
                                                    <div class="rounded bg-light d-flex align-items-center justify-content-center shadow-sm"
                                                         style="width: 36px; height: 36px;">
                                                        <i class="bi bi-image text-muted" style="font-size: 0.8rem;"></i>
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="py-2 align-middle">
                                                <div class="fw-semibold text-dark">{{ $produk->nama }}</div>
                                                @if($produk->is_finishing || $produk->is_cutting)
                                                    <div class="d-flex gap-1 mt-1">
                                                        @if($produk->is_finishing)
                                                            <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25" style="font-size: 0.65rem;">Finishing</span>
                                                        @endif
                                                        @if($produk->is_cutting)
                                                            <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25" style="font-size: 0.65rem;">Cutting</span>
                                                        @endif
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="py-2 align-middle fw-bold text-magenta">
                                                {{ $produk->harga_format }}
                                            </td>
                                            <td class="py-2 align-middle text-center">
                                                <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 py-1 px-2" style="font-size: 0.75rem;">
                                                    {{ $produk->min_order }} {{ $produk->unit_label }}
                                                </span>
                                            </td>
                                            <td class="text-end pe-3 py-2 align-middle">
                                                <div class="d-flex justify-content-end gap-1">
                                                    <a href="{{ route('kasir.produk.edit', $produk->id) }}" 
                                                       class="btn btn-xs btn-outline-primary p-1" title="Edit Produk" style="line-height: 1;">
                                                        <i class="bi bi-pencil-square" style="font-size: 0.85rem;"></i>
                                                    </a>
                                                    <form action="{{ route('kasir.produk.toggle', $produk->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-xs {{ $produk->aktif ? 'btn-outline-magenta' : 'btn-outline-success' }} p-1" 
                                                                title="{{ $produk->aktif ? 'Nonaktifkan' : 'Aktifkan' }}" style="line-height: 1;">
                                                            <i class="bi {{ $produk->aktif ? 'bi-toggle-on' : 'bi-toggle-off' }}" style="font-size: 0.85rem;"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="py-5 text-center text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-2 opacity-25"></i>
                            <p class="mb-0">Belum ada produk di kategori ini.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @empty
        <div class="card p-5 text-center border-dashed">
            <i class="bi bi-folder2-open fs-1 text-muted mb-3"></i>
            <h4>Belum ada kategori</h4>
            <p class="text-muted">Silakan tambah kategori produk pertama Anda untuk mulai mengelola produk.</p>
            <div class="mt-3">
                <a href="{{ route('kasir.kategori.create') }}" class="btn btn-magenta">
                    <i class="bi bi-plus-lg me-1"></i>Tambah Kategori
                </a>
            </div>
        </div>
    @endforelse
</div>

@push('styles')
<style>
    .accordion-button:not(.collapsed) {
        background-color: #fdf2f8;
        color: #9d005e;
        box-shadow: inset 0 -1px 0 rgba(0,0,0,.125);
    }
    
    .accordion-button:focus {
        border-color: #9d005e;
        box-shadow: 0 0 0 0.25rem rgba(157, 0, 94, 0.25);
    }

    .kategori-icon-circle-sm {
        width: 32px;
        height: 32px;
        border-radius: 6px;
        background: white;
        color: #9d005e;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    }

    .btn-magenta {
        background-color: #9d005e;
        border-color: #9d005e;
        color: white;
        transition: all 0.3s ease;
    }

    .btn-magenta:hover {
        background-color: #7d004b;
        border-color: #7d004b;
        color: white;
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(157, 0, 94, 0.2);
    }

    .btn-outline-magenta {
        color: #9d005e;
        border-color: #9d005e;
    }

    .btn-outline-magenta:hover {
        background-color: #9d005e;
        color: white;
        border-color: #9d005e;
    }

    .text-magenta {
        color: #9d005e !important;
    }

    .tracking-wider {
        letter-spacing: 0.05em;
    }

    .border-dashed {
        border-style: dashed !important;
    }
</style>
@endpush

@endsection
