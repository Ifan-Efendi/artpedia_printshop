@extends('layouts.app')

@section('title', 'Menunggu Pembayaran')

@section('content')
    <div class="page-header d-flex justify-content-between align-items-start">
        <div>
            <h1><i class="bi bi-receipt me-2"></i>Pesanan Menunggu Pembayaran</h1>
            <p>Kelola pesanan yang menunggu pembayaran dan antrian produksi.</p>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('kasir.pesanan.index') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Menunggu Pembayaran
                        </option>
                        <option value="dalam_antrian" {{ request('status') == 'dalam_antrian' ? 'selected' : '' }}>Dalam
                            Antrian</option>
                        <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                        <option value="dibatalkan" {{ request('status') == 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                        <option value="" {{ request('status') === '' ? 'selected' : '' }}>Semua Status</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Cari No. Pesanan</label>
                    <input type="text" name="search" class="form-control" placeholder="ART-..."
                        value="{{ request('search') }}">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">Cari</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0" id="kasirPesananRealtime" data-url="{{ route('kasir.pesanan.realtime', request()->query()) }}">
            @include('kasir.pesanan._list', ['pesanans' => $pesanans])
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const container = document.getElementById('kasirPesananRealtime');
        if (!container) return;

        let latestHtml = container.innerHTML;

        async function refreshKasirPesanan() {
            if (document.hidden) return;

            try {
                const response = await fetch(container.dataset.url, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    cache: 'no-store'
                });

                if (!response.ok) return;

                const html = await response.text();
                if (html.trim() && html !== latestHtml) {
                    container.innerHTML = html;
                    latestHtml = html;
                }
            } catch (error) {
                // Keep the current table if the background refresh fails.
            }
        }

        setInterval(refreshKasirPesanan, 5000);
    });
</script>
@endpush
