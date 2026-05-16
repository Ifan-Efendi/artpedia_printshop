@extends('layouts.app')

@section('title', 'Dashboard Produksi')

@section('content')
    <div class="page-header">
        <h1>Dashboard Produksi</h1>
        <p>Selamat datang, Tim Produksi Artpedia!</p>
    </div>

    <div id="produksiDashboardRealtime" data-url="{{ route('produksi.dashboard.realtime') }}">
        @include('produksi._dashboard_content', [
            'dalamAntrianCount' => $dalamAntrianCount,
            'sedangDiprosesCount' => $sedangDiprosesCount,
            'selesaiHariIni' => $selesaiHariIni,
            'sedangDiproses' => $sedangDiproses,
            'antrianBerikutnya' => $antrianBerikutnya,
        ])
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const container = document.getElementById('produksiDashboardRealtime');
        if (!container) return;

        let latestHtml = container.innerHTML;

        async function refreshProduksiDashboard() {
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
                // Keep the current dashboard state if the background refresh fails.
            }
        }

        setInterval(refreshProduksiDashboard, 5000);
    });
</script>
@endpush
