@extends('layouts.app')

@section('title', 'Daftar Kategori')

@section('content')
    <div class="card kategori-list-card">
        <div class="card-header d-flex justify-content-between align-items-center gap-3">
            <h5 class="mb-0 d-flex align-items-center fw-bold" style="color: #9d005e;">
                <i class="bi bi-tags me-2"></i>Daftar Kategori
            </h5>
            <a href="{{ route('kasir.kategori.create') }}" class="btn btn-magenta fw-semibold">
                <i class="bi bi-plus-lg me-1"></i>Tambah Kategori
            </a>
        </div>

        <div class="card-body border-bottom">
            <form method="GET" action="{{ route('kasir.kategori.index') }}" class="row g-2">
                <div class="col-lg-6">
                    <input type="text"
                           name="search"
                           class="form-control kategori-filter-input"
                           placeholder="Cari nama kategori..."
                           value="{{ request('search') }}">
                </div>
                <div class="col-lg-2 d-grid">
                    <button type="submit" class="btn btn-outline-primary kategori-filter-button">Filter</button>
                </div>
            </form>
        </div>

        <div class="card-body p-0">
            @if($kategoris->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0 kategori-table">
                        <thead>
                            <tr>
                                <th style="width: 33.33%;">Kategori</th>
                                <th style="width: 33.33%;" class="text-center">Jumlah Produk</th>
                                <th style="width: 33.33%;" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($kategoris as $kategori)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="kategori-icon">
                                                <i class="bi bi-folder2-open"></i>
                                            </div>
                                            <div>
                                                <div class="d-flex align-items-center flex-wrap gap-2">
                                                    <span class="fw-semibold text-dark">{{ $kategori->nama }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="fw-semibold">{{ $kategori->produk_count }}</span>
                                        <span class="text-muted">produk</span>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-1">
                                            <a href="{{ route('kasir.kategori.edit', $kategori->id) }}"
                                               class="btn btn-sm btn-outline-primary kategori-action-btn"
                                               title="Edit Kategori">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($kategoris->hasPages())
                    <div class="card-footer d-flex justify-content-center py-3">
                        {{ $kategoris->links() }}
                    </div>
                @endif
            @else
                <div class="empty-state py-5">
                    <i class="bi bi-tags"></i>
                    <h5>Belum ada kategori</h5>
                    <p class="text-muted">Mulai tambahkan kategori produk.</p>
                    <a href="{{ route('kasir.kategori.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-lg me-2"></i>Tambah Kategori Pertama
                    </a>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
<script>
</script>
@endpush

@push('styles')
<style>
    .kategori-list-card {
        border-radius: 12px;
        overflow: hidden;
    }

    .kategori-list-card .card-header {
        min-height: 56px;
        background: #ffffff;
    }

    .kategori-filter-input {
        min-height: 46px;
        font-size: 0.95rem;
    }

    .kategori-filter-button {
        min-height: 46px;
        font-weight: 500;
    }

    .kategori-table thead th {
        color: #334155;
        padding: 1rem;
    }

    .kategori-table tbody td {
        padding: 1.05rem;
    }

    .kategori-icon {
        width: 44px;
        height: 44px;
        border-radius: 8px;
        background: #f1f5f9;
        color: #64748b;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 auto;
        font-size: 1.25rem;
    }

    .kategori-action-btn {
        width: 32px;
        height: 32px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0;
        border-radius: 6px;
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

    @media (max-width: 576px) {
        .kategori-list-card .card-header {
            align-items: flex-start !important;
            flex-direction: column;
        }

        .kategori-list-card .card-header .btn {
            width: 100%;
        }

        .kategori-table th:nth-child(2),
        .kategori-table td:nth-child(2) {
            min-width: 150px;
        }
    }
</style>
@endpush
