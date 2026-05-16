@extends('layouts.app')

@section('title', 'Antrian Produksi (SJF)')

@push('styles')
<style>
    .queue-action-btn {
        min-width: 92px;
    }
</style>
@endpush

@section('content')
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h1><i class="bi bi-list-ol me-2"></i>Antrian Produksi</h1>
            <p>Urutan pengerjaan antrian produksi</p>
        </div>
    </div>

    <div class="alert alert-info border-info d-flex align-items-center mb-4 shadow-sm">
        <i class="bi bi-info-circle-fill fs-4 me-3"></i>
        <div>
            <strong>Algoritma SJF digunakan:</strong> urutan pesanan otomatis diurutkan menggunakan algoritma SJF berdasarkan proses pengerjaan paling singkat.
        </div>
    </div>

    <div id="produksiAntrianRealtime" data-url="{{ route('produksi.antrian.realtime', request()->query()) }}">
        @include('produksi._antrian_list', ['antrian' => $antrian, 'sedangDiproses' => $sedangDiproses])
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const container = document.getElementById('produksiAntrianRealtime');
        if (!container) return;

        let latestHtml = container.innerHTML;

        async function refreshProduksiAntrian() {
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
                // Keep the current queue if the background refresh fails.
            }
        }

        setInterval(refreshProduksiAntrian, 5000);
    });
</script>
@endpush
