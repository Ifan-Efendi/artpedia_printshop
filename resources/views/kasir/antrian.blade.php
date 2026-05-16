@extends('layouts.app')

@section('title', 'Lihat Antrian Produksi')

@section('content')
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h1><i class="bi bi-list-ol me-2"></i>Lihat Antrian Produksi</h1>
            <p class="text-muted">Pantau proses dan urutan pengerjaan pesanan di bagian produksi secara real-time.</p>
        </div>
    </div>

    @if($sedangDiproses->count() > 0)
        <div class="card mb-4 border-primary">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Sedang Dikerjakan</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>No. Pesanan</th>
                                <th>Pelanggan</th>
                                <th>Produk</th>
                                <th>Operator</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sedangDiproses as $proses)
                                <tr>
                                    <td><code>{{ $proses->nomor_pesanan }}</code></td>
                                    <td>{{ $proses->user->name ?? '-' }}</td>
                                    <td>{{ $proses->produk->nama }}</td>
                                    <td>
                                        <span class="badge bg-info text-dark">
                                            <i class="bi bi-person me-1"></i> {{ $proses->operator->name }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">Sedang Dikerjakan</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0 fw-bold">Antrian Proses Pesanan</h5>
        </div>
        <div class="card-body p-0">
            @if($antrian->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="text-center">#</th>
                                <th>No. Pesanan</th>
                                <th>Pelanggan</th>
                                <th>Produk & Spek</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($antrian as $key => $antri)
                                <tr class="{{ $key == 0 && request('page', 1) == 1 ? 'table-warning' : '' }}">
                                    <td class="text-center">
                                        @if($key == 0 && request('page', 1) == 1)
                                            <span class="badge bg-warning text-dark">NEXT</span>
                                        @else
                                            {{ $antrian->firstItem() + $key }}
                                        @endif
                                    </td>
                                    <td><code>{{ $antri->nomor_pesanan }}</code></td>
                                    <td>{{ $antri->user->name ?? '-' }}</td>
                                    <td>
                                        <strong>{{ $antri->produk->nama }}</strong><br>
                                        <small class="text-muted">{{ $antri->ukuranKertas->nama }} |
                                            {{ $antri->jenisKertas->nama }}</small>
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $antri->status_color }}" style="color: #000;">
                                            {{ $antri->status == 'diproses' ? 'Dalam Antrian Cetak' : $antri->status_label }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('kasir.pesanan.show', $antri->id) }}"
                                                class="btn btn-sm text-white" style="background: #c00073; border-color: #c00073;">
                                                <i class="bi bi-eye me-1"></i>Detail
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="p-3">
                    {{ $antrian->links() }}
                </div>
            @else
                <div class="empty-state py-5">
                    <i class="bi bi-clipboard-check text-success"></i>
                    <h5 class="mt-3">Antrian kosong!</h5>
                    <p class="text-muted">Tidak ada pesanan yang sedang menunggu di produksi.</p>
                </div>
            @endif
        </div>
    </div>
@endsection
