@extends('layouts.app')

@section('title', 'Pesanan Kasir (Pesanan Langsung)')

@push('styles')
<style>
    .order-builder {
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        background: #ffffff;
    }

    .order-builder .card-body {
        padding: 1.25rem;
    }

    .step-head {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        border-bottom: 1px dashed #b8c1cc;
        padding-bottom: 0.5rem;
        margin-bottom: 1rem;
    }

    .step-no {
        width: 40px;
        height: 40px;
        background: #9d005e;
        color: #fff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        font-weight: 700;
        border-radius: 4px;
        line-height: 1;
    }

    .step-title {
        font-size: 1.15rem;
        font-weight: 700;
        color: #27374a;
        line-height: 1.35;
    }

    .order-note-text,
    .order-meta {
        font-size: 0.92rem;
        line-height: 1.5;
    }

    .left-panel .form-label,
    .right-panel .form-label {
        font-weight: 600;
        color: #7a0049;
        font-size: 0.95rem;
    }

    .left-panel .form-control:focus,
    .left-panel .form-select:focus,
    .right-panel .form-control:focus,
    .right-panel .form-select:focus {
        border-color: #9d005e;
        box-shadow: 0 0 0 0.2rem rgba(157, 0, 94, 0.12);
    }

    #produk_id:focus,
    #jenis_kertas_id:focus,
    #ukuran_kertas_id:focus {
        border-color: #9d005e !important;
        box-shadow: 0 0 0 0.2rem rgba(157, 0, 94, 0.12) !important;
    }

    #produk_id.is-valid,
    #jenis_kertas_id.is-valid,
    #ukuran_kertas_id.is-valid,
    #produk_id:focus.is-valid,
    #jenis_kertas_id:focus.is-valid,
    #ukuran_kertas_id:focus.is-valid {
        border-color: #9d005e !important;
        box-shadow: 0 0 0 0.2rem rgba(157, 0, 94, 0.12) !important;
    }

    #produk_id option:checked,
    #jenis_kertas_id option:checked,
    #ukuran_kertas_id option:checked {
        background-color: #f5d5e9;
        color: #7a0049;
    }

    .section-divider {
        border-bottom: 1px solid #f1c3dd;
        margin: 0 0 0.9rem;
        padding-bottom: 0.55rem;
    }

    .section-divider .label {
        color: #7a0049;
        font-weight: 700;
        text-transform: none;
        font-size: 0.95rem;
    }

    .extras-box {
        border: 1px solid #f1c3dd;
        border-radius: 8px;
        background: #fdf2f8;
        padding: 0.95rem;
        margin-top: 1rem;
    }

    .option-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 0.6rem;
    }

    .option-grid.equal-size {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 0.6rem;
    }

    .option-grid.equal-size .position-relative {
        display: block;
    }

    .option-chip {
        display: inline-flex;
        align-items: center;
        border: 1px solid #f1c3dd;
        border-radius: 8px;
        padding: 0.52rem 0.9rem;
        font-size: 0.9rem;
        line-height: 1.25;
        background: #fff;
        color: #7a0049;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .option-chip:hover {
        border-color: #9d005e;
        background: #fde7f3;
    }

    .option-chip-input {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }

    .option-grid.equal-size .option-chip {
        width: 100%;
        justify-content: center;
        min-height: 42px;
    }

    .option-chip-input:checked + .option-chip {
        border-color: #9d005e;
        background: rgba(157, 0, 94, 0.08);
        color: #7a0049;
        font-weight: 600;
    }

    .upload-box {
        border: 1px solid #f1c3dd;
        border-radius: 10px;
        background: #fdf2f8;
        padding: 1rem;
        text-align: center;
    }

    .upload-box i {
        font-size: 2.3rem;
        color: #16a34a;
    }

    .checkout-box {
        border: 1px solid #f1c3dd;
        border-radius: 10px;
        padding: 1rem;
        background: #fdf2f8;
    }

    .checkout-total {
        font-size: 1.2rem;
        font-weight: 600;
        color: #111827;
    }

    .checkout-total #total_price {
        color: #111827 !important;
    }

    .btn-add-cart {
        background: #16a34a;
        border-color: #16a34a;
        color: #fff;
        font-weight: 700;
    }

    .btn-add-cart:hover,
    .btn-add-cart:focus {
        background: #15803d;
        border-color: #15803d;
        color: #fff;
    }

    .alert-info {
        border: 1px solid #f1c3dd;
        background: #fdf2f8;
    }

    .table thead th {
        background: #fde7f3;
        color: #7a0049;
        border-bottom-color: #f1c3dd;
    }

    @media (max-width: 991.98px) {
        .step-title {
            font-size: 1.05rem;
        }
    }
</style>
@endpush

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card shadow-sm order-builder">
                <div class="card-body p-4">
                    <div class="alert alert-info mb-4">
                        <span class="order-note-text mb-0 d-block">Setelah produk ditambahkan, lanjutkan checkout di keranjang pesanan.</span>
                    </div>

                    <form action="{{ route('kasir.pesanan.item.add') }}" method="POST" enctype="multipart/form-data" id="itemForm">
                        @csrf

                        <div class="row g-4">
                            <div class="col-lg-7 left-panel">
                                <div class="step-head">
                                    <span class="step-no">1</span>
                                    <div class="step-title">Pilih Produk & Spesifikasi</div>
                                </div>

                                <p class="text-muted mb-3">Isi detail pesanan, lalu tambahkan ke keranjang.</p>

                                <div class="mb-3">
                                    <label for="kategori_id" class="form-label">Kategori Produk</label>
                                    <select name="kategori_id" id="kategori_id" class="form-select @error('kategori_id') is-invalid @enderror" required>
                                        <option value="" selected disabled>-- Pilih Kategori --</option>
                                        @foreach($kategoris as $kategori)
                                            <option value="{{ $kategori->id }}">{{ $kategori->nama }}</option>
                                        @endforeach
                                    </select>
                                    @error('kategori_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="produk_id" class="form-label">Produk Cetak</label>
                                    <select name="produk_id" id="produk_id" class="form-select @error('produk_id') is-invalid @enderror" required disabled>
                                        <option value="" selected disabled>-- Pilih kategori dulu --</option>
                                    </select>
                                    @error('produk_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div id="options-section" style="display: none;">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="jumlah" class="form-label">Jumlah Order</label>
                                            <input type="number" name="jumlah" id="jumlah" class="form-control" value="1" min="1" required>
                                            <div class="order-meta text-muted mt-1" id="min-order-text">Min. Order: 1</div>
                                        </div>
                                    </div>

                                    <div class="extras-box">
                                        <div class="section-divider">
                                            <span class="label">Tambahan</span>
                                        </div>

                                        <div class="mb-3" id="finishing-group">
                                            <label class="form-label d-block mb-2">Finishing</label>
                                            <div class="option-grid equal-size">
                                                <label class="position-relative">
                                                    <input class="option-chip-input finishing-opt" type="radio" name="finishing" value="" checked>
                                                    <span class="option-chip">Tidak Ada</span>
                                                </label>
                                                <label class="position-relative">
                                                    <input class="option-chip-input finishing-opt" type="radio" name="finishing" value="Glossy" data-harga="4000">
                                                    <span class="option-chip">Laminasi Glossy (+Rp 4.000)</span>
                                                </label>
                                                <label class="position-relative">
                                                    <input class="option-chip-input finishing-opt" type="radio" name="finishing" value="Doff" data-harga="4000">
                                                    <span class="option-chip">Laminasi Doff (+Rp 4.000)</span>
                                                </label>
                                            </div>
                                        </div>

                                        <div class="mb-3" id="cutting-group" style="display: none;">
                                            <label class="form-label d-block mb-2">Opsi Potong</label>
                                            <div class="option-grid equal-size">
                                                <label class="position-relative">
                                                    <input class="option-chip-input cutting-opt" type="radio" name="opsi_potong" value="Kiss Cut" data-harga="4000">
                                                    <span class="option-chip">Kiss Cut (+Rp 4.000)</span>
                                                </label>
                                                <label class="position-relative">
                                                    <input class="option-chip-input cutting-opt" type="radio" name="opsi_potong" value="Die Cut" data-harga="8000">
                                                    <span class="option-chip">Die Cut (+Rp 8.000)</span>
                                                </label>
                                            </div>
                                        </div>

                                        <div class="mb-0">
                                            <label for="catatan" class="form-label">Catatan Tambahan</label>
                                            <textarea name="catatan" id="catatan" class="form-control" rows="3" placeholder="Tambahkan catatan di sini"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-5 right-panel">
                                <div id="options-right" style="display: none;">
                                    <div class="step-head">
                                        <span class="step-no">2</span>
                                        <div class="step-title">Upload File</div>
                                    </div>

                                    <p class="order-meta text-muted mb-3">
                                        Format: PDF, JPG, PNG (Max 10MB)
                                    </p>

                                    <div class="upload-box mb-4">
                                        <i class="bi bi-file-earmark-arrow-up-fill d-block mb-2"></i>
                                        <div class="fw-semibold mb-2 text-dark">Upload File Cetak</div>
                                        <input type="file" name="file_desain" id="file_desain" class="form-control" required accept=".pdf,.jpg,.jpeg,.png">
                                    </div>

                                    <div class="step-head">
                                        <span class="step-no">3</span>
                                        <div class="step-title">Total Harga</div>
                                    </div>

                                    <div class="checkout-box">
                                        <div class="checkout-total mb-3"><span id="total_price">Rp 0</span></div>
                                        <button type="submit" class="btn btn-add-cart w-100 py-2">
                                            <i class="bi bi-cart-plus me-2"></i> Tambah ke Keranjang
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    @if(count($walkInCart) > 0)
                        <div class="extras-box mt-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <div>
                                <div class="step-title mb-1">{{ count($walkInCart) }} item ada di keranjang pesanan</div>
                                <div class="order-meta text-muted">Total sementara: Rp {{ number_format($walkInTotal, 0, ',', '.') }}</div>
                            </div>
                            <a href="{{ route('kasir.cart.index') }}" class="btn btn-success px-4 fw-bold">
                                <i class="bi bi-cart3 me-2"></i> Lihat Keranjang
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const produkOptions = @json($produkOptions);
    const produkMap = Object.fromEntries(produkOptions.map(produk => [String(produk.id), produk]));
    const kategoriSelect = document.getElementById('kategori_id');
    const produkSelect = document.getElementById('produk_id');
    const jumlahInput = document.getElementById('jumlah');
    const minOrderText = document.getElementById('min-order-text');
    const finishingGroup = document.getElementById('finishing-group');
    const cuttingGroup = document.getElementById('cutting-group');

    kategoriSelect.addEventListener('change', function() {
        fillProdukByKategori(this.value);
    });

    document.getElementById('produk_id').addEventListener('change', function() {
        const produk = produkMap[String(this.value)] || null;
        const section = document.getElementById('options-section');
        const rightSection = document.getElementById('options-right');

        if (produk) {
            section.style.display = 'block';
            rightSection.style.display = 'block';
            updateOptions(produk);
            calculateTotal();
        } else {
            section.style.display = 'none';
            rightSection.style.display = 'none';
        }
    });

    function fillProdukByKategori(kategoriId) {
        produkSelect.innerHTML = '<option value="" selected disabled>-- Pilih Produk --</option>';
        document.getElementById('options-section').style.display = 'none';
        document.getElementById('options-right').style.display = 'none';

        if (!kategoriId) {
            produkSelect.disabled = true;
            return;
        }

        const filtered = produkOptions.filter(p => String(p.kategori_id) === String(kategoriId));
        filtered.forEach(produk => {
            const option = new Option(
                `${produk.nama} - Rp ${Number(produk.harga_satuan).toLocaleString('id-ID')}`,
                produk.id
            );
            produkSelect.add(option);
        });

        produkSelect.disabled = filtered.length === 0;
    }

    function updateOptions(produk) {
        const minOrder = Math.max(parseInt(produk.min_order || 1, 10), 1);
        const unitLabel = produk.unit_label || 'lembar';

        jumlahInput.min = minOrder;
        if (parseInt(jumlahInput.value || 0, 10) < minOrder) {
            jumlahInput.value = minOrder;
        }

        if (minOrderText) {
            minOrderText.innerText = `Min. Order: ${minOrder} ${unitLabel}`;
        }

        if (finishingGroup) {
            finishingGroup.style.display = produk.is_finishing ? 'block' : 'none';
            if (!produk.is_finishing) {
                const noneOption = document.querySelector('input[name="finishing"][value=""]');
                if (noneOption) {
                    noneOption.checked = true;
                }
            }
        }

        if (cuttingGroup) {
            cuttingGroup.style.display = produk.is_cutting ? 'block' : 'none';
            if (!produk.is_cutting) {
                document.querySelectorAll('.cutting-opt').forEach(el => el.checked = false);
            }
        }
    }

    async function calculateTotal() {
        const produkId = document.getElementById('produk_id').value;
        if (!produkId) return;

        const produk = produkMap[String(produkId)] || null;
        if (!produk) return;

        const qty = Math.max(parseInt(document.getElementById('jumlah').value || 1, 10), parseInt(produk.min_order || 1, 10));
        if (parseInt(document.getElementById('jumlah').value || 0, 10) !== qty) {
            document.getElementById('jumlah').value = qty;
        }
        const fin = document.querySelector('input[name="finishing"]:checked');
        const cut = document.querySelector('input[name="opsi_potong"]:checked');

        try {
            const response = await fetch("{{ route('kasir.hitung-harga') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': "{{ csrf_token() }}",
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    produk_id: produkId,
                    jumlah: qty,
                    finishing: produk.is_finishing && fin && fin.value ? fin.value : null,
                    opsi_potong: produk.is_cutting && cut && cut.value ? cut.value : null,
                }),
            });

            if (!response.ok) return;

            const data = await response.json();
            document.getElementById('total_price').innerText = data.total_harga_format;
        } catch (error) {
            console.error('Gagal menghitung harga', error);
        }
    }

    document.getElementById('jumlah').addEventListener('input', calculateTotal);
    document.querySelectorAll('input[type=radio]').forEach(el => el.addEventListener('change', calculateTotal));

    fillProdukByKategori(null);
</script>
@endpush
@endsection
