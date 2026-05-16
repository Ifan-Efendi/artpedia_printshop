@extends('layouts.app')

@section('title', 'Edit Produk - ' . $produk->nama)

@section('content')
    <div class="page-header">
        <div class="d-flex align-items-center gap-3 mb-1">
            <a href="{{ route('kasir.produk.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <h1>Edit Produk</h1>
                <p>Perbarui data produk <strong>{{ $produk->nama }}</strong>.</p>
            </div>
        </div>
    </div>

    <form action="{{ route('kasir.produk.update', $produk->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row g-4">
            {{-- Form Inti --}}
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-pencil-square me-2"></i>Informasi Produk</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            {{-- Nama --}}
                            <div class="col-12">
                                <label for="nama" class="form-label">Nama Produk <span class="text-danger">*</span></label>
                                <input type="text" name="nama" id="nama"
                                       class="form-control @error('nama') is-invalid @enderror"
                                       value="{{ old('nama', $produk->nama) }}" required>
                                @error('nama')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Kategori --}}
                            <div class="col-md-6">
                                <label for="kategori_id" class="form-label">Kategori <span class="text-danger">*</span></label>
                                <select name="kategori_id" id="kategori_id"
                                        class="form-select @error('kategori_id') is-invalid @enderror" required>
                                    <option value="">-- Pilih Kategori --</option>
                                    @foreach($kategoris as $kat)
                                        <option value="{{ $kat->id }}" {{ old('kategori_id', $produk->kategori_id) == $kat->id ? 'selected' : '' }}>
                                            {{ $kat->nama }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('kategori_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Harga Satuan --}}
                            <div class="col-md-6">
                                <label for="harga_satuan" class="form-label">Harga Satuan (Rp) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" name="harga_satuan" id="harga_satuan"
                                           class="form-control @error('harga_satuan') is-invalid @enderror"
                                           value="{{ old('harga_satuan', $produk->harga_satuan) }}" min="0" step="100" required>
                                    @error('harga_satuan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Min Order --}}
                            <div class="col-md-6">
                                <label for="min_order" class="form-label">Minimal Order <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" name="min_order" id="min_order"
                                           class="form-control @error('min_order') is-invalid @enderror"
                                           value="{{ old('min_order', $produk->min_order) }}" min="1" required>
                                    <span class="input-group-text">unit</span>
                                    @error('min_order')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Status Produk --}}
                            <div class="col-md-4">
                                <label class="form-label">Status Produk</label>
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" name="aktif" id="aktif" value="1"
                                           {{ old('aktif', $produk->aktif) ? 'checked' : '' }}
                                           style="width: 2.5em; height: 1.25em; cursor: pointer;">
                                    <label class="form-check-label ms-2" for="aktif" id="aktifLabel">
                                        @if(old('aktif', $produk->aktif))
                                            <span class="badge bg-success bg-opacity-10 text-success">Aktif</span>
                                        @else
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary">Nonaktif</span>
                                        @endif
                                    </label>
                                </div>
                            </div>

                            {{-- Butuh Finishing --}}
                            <div class="col-md-4">
                                <label class="form-label">Opsi Finishing</label>
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" name="is_finishing" id="is_finishing" value="1"
                                           {{ old('is_finishing', $produk->is_finishing) ? 'checked' : '' }}
                                           style="width: 2.5em; height: 1.25em; cursor: pointer;">
                                    <label class="form-check-label ms-2" for="is_finishing" id="finishingLabel">
                                        @if(old('is_finishing', $produk->is_finishing))
                                            <span class="badge bg-success bg-opacity-10 text-success">Aktif</span>
                                        @else
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary">Nonaktif</span>
                                        @endif
                                    </label>
                                </div>
                            </div>

                            {{-- Butuh Cutting --}}
                            <div class="col-md-4">
                                <label class="form-label">Opsi Cutting</label>
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" name="is_cutting" id="is_cutting" value="1"
                                           {{ old('is_cutting', $produk->is_cutting) ? 'checked' : '' }}
                                           style="width: 2.5em; height: 1.25em; cursor: pointer;">
                                    <label class="form-check-label ms-2" for="is_cutting" id="cuttingLabel">
                                        @if(old('is_cutting', $produk->is_cutting))
                                            <span class="badge bg-success bg-opacity-10 text-success">Aktif</span>
                                        @else
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary">Nonaktif</span>
                                        @endif
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mt-4">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="estimasi_waktu_per_unit" class="form-label">Estimasi Waktu Pengerjaan / Lembar</label>
                                <div class="input-group">
                                    <input type="number" name="estimasi_waktu_per_unit" id="estimasi_waktu_per_unit"
                                           class="form-control @error('estimasi_waktu_per_unit') is-invalid @enderror"
                                           value="{{ old('estimasi_waktu_per_unit', $produk->estimasi_waktu_per_unit) }}" min="1">
                                    <span class="input-group-text">menit</span>
                                    @error('estimasi_waktu_per_unit')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-12">
                                <label for="deskripsi" class="form-label">Deskripsi</label>
                                <textarea name="deskripsi" id="deskripsi" rows="4"
                                          class="form-control @error('deskripsi') is-invalid @enderror"
                                          placeholder="Deskripsi detail produk (opsional)...">{{ old('deskripsi', $produk->deskripsi) }}</textarea>
                                @error('deskripsi')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Sidebar: Gambar & Submit --}}
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-image me-2"></i>Gambar Produk</h5>
                    </div>
                    <div class="card-body">
                        {{-- Preview --}}
                        <div class="text-center mb-3">
                            <div id="imagePreviewWrapper"
                                 class="rounded-3 d-flex align-items-center justify-content-center mx-auto image-preview-wrapper {{ $produk->gambar ? 'has-image' : 'no-image' }}"
                                 style="width: 100%; aspect-ratio: 1; overflow: hidden; transition: all 0.3s ease;">
                                <div id="imagePlaceholder" class="text-center text-muted {{ $produk->gambar ? 'd-none' : '' }}">
                                    <i class="bi bi-cloud-arrow-up" style="font-size: 2.5rem;"></i>
                                    <p class="mb-0 mt-1 small">Upload gambar</p>
                                    <p class="mb-0 small text-muted">JPG, PNG (Maks. 2MB)</p>
                                </div>
                                <img id="imagePreview"
                                     src="{{ $produk->gambar ? asset('storage/' . $produk->gambar) : '' }}"
                                     alt="Preview"
                                     class="{{ $produk->gambar ? '' : 'd-none' }}"
                                     style="width: 100%; height: 100%; object-fit: cover;">
                            </div>
                        </div>
                        <input type="file" name="gambar" id="gambar"
                               class="form-control @error('gambar') is-invalid @enderror"
                               accept="image/jpeg,image/png">
                        @if($produk->gambar)
                            <small class="text-muted mt-1 d-block">
                                <i class="bi bi-info-circle me-1"></i>Kosongkan jika tidak ingin mengganti gambar.
                            </small>
                        @endif
                        @error('gambar')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Submit --}}
                <div class="card mt-4">
                    <div class="card-body d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bi bi-check-lg me-2"></i>Simpan Perubahan
                        </button>
                        <a href="{{ route('kasir.produk.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-lg me-2"></i>Batal
                        </a>
                    </div>
                </div>

                {{-- Info --}}
                <div class="card mt-4">
                    <div class="card-body">
                        <h6 class="text-muted mb-3"><i class="bi bi-clock-history me-2"></i>Informasi</h6>
                        <div class="d-flex justify-content-between mb-2">
                            <small class="text-muted">Dibuat</small>
                            <small class="fw-semibold">{{ $produk->created_at->format('d M Y, H:i') }}</small>
                        </div>
                        <div class="d-flex justify-content-between">
                            <small class="text-muted">Terakhir diubah</small>
                            <small class="fw-semibold">{{ $produk->updated_at->format('d M Y, H:i') }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('scripts')
<script>
    // Image preview
    document.getElementById('gambar').addEventListener('change', function(e) {
        const file = e.target.files[0];
        const preview = document.getElementById('imagePreview');
        const placeholder = document.getElementById('imagePlaceholder');
        const wrapper = document.getElementById('imagePreviewWrapper');

        if (file) {
            if (file.size > 2 * 1024 * 1024) {
                alert('Ukuran gambar maksimal 2MB!');
                this.value = '';
                return;
            }

            const reader = new FileReader();
            reader.onload = function(ev) {
                preview.src = ev.target.result;
                preview.classList.remove('d-none');
                placeholder.classList.add('d-none');
                wrapper.classList.add('has-image');
                wrapper.classList.remove('no-image');
            };
            reader.readAsDataURL(file);
        }
    });

    // Toggle labels
    function setupToggle(id, labelId) {
        document.getElementById(id).addEventListener('change', function() {
            const label = document.getElementById(labelId);
            if (this.checked) {
                label.innerHTML = '<span class="badge bg-success bg-opacity-10 text-success">Aktif</span>';
            } else {
                label.innerHTML = '<span class="badge bg-secondary bg-opacity-10 text-secondary">Nonaktif</span>';
            }
        });
    }

    setupToggle('aktif', 'aktifLabel');
    setupToggle('is_finishing', 'finishingLabel');
    setupToggle('is_cutting', 'cuttingLabel');
</script>
@endpush

@push('styles')
<style>
    .image-preview-wrapper.has-image {
        background: #f1f5f9;
        border: 2px solid var(--primary-color);
    }
    
    .image-preview-wrapper.no-image {
        background: #f1f5f9;
        border: 2px dashed #cbd5e1;
    }
</style>
@endpush
