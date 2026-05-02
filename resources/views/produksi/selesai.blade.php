@extends('layouts.app')

@section('title', 'Pesanan Selesai')

@section('content')
    <div class="page-header">
        <h1><i class="bi bi-check2-all me-2"></i>Riwayat Produksi</h1>
        <p>Daftar pesanan yang telah selesai dikerjakan.</p>
    </div>

    <div class="card">
        <div class="card-body p-0">
            @if($pesanans->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>No. Pesanan</th>
                                <th>Produk</th>
                                <th>Pelanggan</th>
                                <th>Selesai Pada</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pesanans as $pesanan)
                                <tr>
                                    <td><code>{{ $pesanan->nomor_pesanan }}</code></td>
                                    <td>{{ $pesanan->produk->nama }} ({{ $pesanan->jumlah }} lbr)</td>
                                    <td>{{ $pesanan->user->name }}</td>
                                    <td>{{ $pesanan->selesai_produksi_at->format('d M Y, H:i') }}</td>
                                    <td>
                                        <span class="badge badge-{{ $pesanan->status }}">
                                            {{ $pesanan->status_label }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('produksi.show', $pesanan->id) }}"
                                            class="btn btn-sm btn-outline-primary">Detail</a>
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
                    <i class="bi bi-clock-history"></i>
                    <h5>Belum ada riwayat pengerjaan</h5>
                </div>
            @endif
        </div>
    </div>
@endsection