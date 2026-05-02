@extends('layouts.app')

@section('title', 'Pesanan Saya')

@push('styles')
<style>
    .pesanan-table th,
    .pesanan-table td {
        font-size: 0.95rem;
        color: #111827;
    }

    .pesanan-table .pesanan-subtext {
        font-size: 0.85rem;
        color: #8b5c7a;
    }

    .pesanan-table .pesanan-primary {
        font-weight: 600;
        color: #111827;
    }

    .pesanan-table .pesanan-number {
        font-size: 0.88rem;
        font-weight: 600;
        letter-spacing: 0.01em;
    }
</style>
@endpush

@section('content')
    <div class="page-header d-flex justify-content-between align-items-start">
        <div>
            <h1><i class="bi bi-list-ul me-2"></i>Pesanan Saya</h1>
            <p>Daftar semua pesanan Anda</p>
        </div>
        <a href="{{ route('pelanggan.pesanan.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-2"></i> Buat Pesanan
        </a>
    </div>

    <div class="card">
        <div class="card-body p-0">
            @if($pesanans->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0 pesanan-table">
                        <thead>
                            <tr>
                                <th>No. Pesanan</th>
                                <th>Produk</th>
                                <th>Jumlah</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Tanggal</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pesanans as $pesanan)
                                <tr>
                                    <td><span class="pesanan-primary pesanan-number">{{ $pesanan->nomor_pesanan }}</span></td>
                                    <td>
                                        <div class="pesanan-primary">{{ $pesanan->produk->nama ?? '-' }}</div>
                                        <div class="pesanan-subtext">
                                            {{ $pesanan->ukuranKertas->nama ?? '' }} | {{ $pesanan->jenisKertas->nama ?? '' }}
                                        </div>
                                    </td>
                                    <td>{{ $pesanan->jumlah }} lembar</td>
                                    <td><span class="pesanan-primary">{{ $pesanan->total_harga_format }}</span></td>
                                    <td>
                                        <span class="badge badge-{{ $pesanan->status }}">
                                            {{ $pesanan->status_label }}
                                        </span>
                                    </td>
                                    <td>{{ $pesanan->created_at->format('d M Y H:i') }}</td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('pelanggan.pesanan.show', $pesanan->id) }}"
                                                class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-eye"></i> Detail
                                            </a>
                                            @if($pesanan->status == 'pending')
                                                <button type="button" class="btn btn-sm btn-outline-danger"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#batalPesananModal"
                                                    data-action="{{ route('pelanggan.pesanan.destroy', $pesanan->id) }}">
                                                    <i class="bi bi-trash"></i> Batal
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="p-3">
                    {{ $pesanans->links() }}
                </div>
            @else
                <div class="empty-state py-5">
                    <i class="bi bi-inbox"></i>
                    <h5>Belum ada pesanan</h5>
                    <p class="text-muted">Mulai dengan membuat pesanan pertama Anda!</p>
                    <a href="{{ route('pelanggan.pesanan.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-2"></i> Buat Pesanan
                    </a>
                </div>
            @endif
        </div>
    </div>

    <div class="modal fade" id="batalPesananModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 14px; overflow: hidden;">
                <div class="modal-body p-4 text-center">
                    <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle"
                        style="width: 58px; height: 58px; background: rgba(220, 38, 38, 0.12); color: #b91c1c;">
                        <i class="bi bi-x-circle" style="font-size: 1.9rem;"></i>
                    </div>
                    <h5 class="fw-bold mb-2">Batalkan Pesanan?</h5>
                    <p class="text-muted mb-0">
                        Apakah Anda yakin ingin membatalkan pesanan ini? Pesanan tidak akan masuk ke antrian.
                    </p>
                </div>
                <div class="modal-footer border-0 bg-light px-4 pb-4 pt-3">
                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Kembali</button>
                    <form id="batalPesananForm" action="#" method="POST" class="m-0">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger px-4 fw-bold">
                            <i class="bi bi-x-circle me-1"></i> Ya, Batalkan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modal = document.getElementById('batalPesananModal');
        const form = document.getElementById('batalPesananForm');

        if (!modal || !form) return;

        modal.addEventListener('show.bs.modal', function (event) {
            const trigger = event.relatedTarget;
            const action = trigger ? trigger.getAttribute('data-action') : null;

            if (action) {
                form.setAttribute('action', action);
            }
        });
    });
</script>
@endpush
