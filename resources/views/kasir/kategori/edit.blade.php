@extends('layouts.app')

@section('title', 'Edit Kategori - ' . $kategori->nama)

@section('content')
    <div class="page-header">
        <div class="d-flex align-items-center gap-3 mb-1">
            <a href="{{ route('kasir.kategori.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <h1 class="fw-bold" style="color: #9d005e;">Edit Kategori</h1>
                <p class="text-muted">Perbarui data kategori <strong>{{ $kategori->nama }}</strong>.</p>
            </div>
        </div>
    </div>

    <form action="{{ route('kasir.kategori.update', $kategori->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row g-3">
            <div class="col-lg-8">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white py-2 border-bottom">
                        <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-pencil-square me-2"></i>Informasi Kategori</h6>
                    </div>
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-md-12">
                                <label for="nama" class="form-label fw-semibold small">Nama Kategori <span class="text-danger">*</span></label>
                                <input type="text" name="nama" id="nama"
                                       class="form-control @error('nama') is-invalid @enderror"
                                       value="{{ old('nama', $kategori->nama) }}" required>
                                @error('nama')
                                    <div class="invalid-feedback small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card shadow-sm border-0">
                    <div class="card-body d-grid gap-2 p-3">
                        <button type="submit" class="btn btn-magenta fw-bold">
                            <i class="bi bi-check-lg me-1"></i>Simpan Perubahan
                        </button>
                        <a href="{{ route('kasir.kategori.index') }}" class="btn btn-sm btn-outline-secondary">
                            Batal
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('styles')
<style>
    .btn-magenta {
        background-color: #9d005e;
        border-color: #9d005e;
        color: white;
    }
    .btn-magenta:hover {
        background-color: #7a0049;
        border-color: #7a0049;
        color: white;
    }
    .form-control:focus {
        border-color: #9d005e;
        box-shadow: 0 0 0 0.25rem rgba(157, 0, 94, 0.1);
    }
</style>
@endpush
