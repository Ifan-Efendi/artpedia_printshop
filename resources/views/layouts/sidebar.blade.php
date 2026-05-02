@php
    $role = auth()->user()->role ?? 'pelanggan';
@endphp

<aside class="sidebar" id="sidebar">
    <div class="d-lg-none d-flex justify-content-end pt-3 px-3 pb-0">
        <button class="btn btn-sm text-white rounded-circle shadow-sm" 
                style="background-color: var(--primary-color); width: 32px; height: 32px; padding: 0; display: flex; align-items: center; justify-content: center;" 
                onclick="toggleSidebar()">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>
    @if($role === 'pelanggan')
        <nav class="nav flex-column">
            <a class="nav-link {{ request()->routeIs('pelanggan.dashboard') ? 'active' : '' }}"
                href="{{ route('pelanggan.dashboard') }}">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
            <a class="nav-link {{ request()->routeIs('pelanggan.pesanan.create') || request()->routeIs('pelanggan.cart.*') || request()->routeIs('pelanggan.checkout') || request()->routeIs('pelanggan.checkout.process') ? 'active' : '' }}"
                href="{{ route('pelanggan.pesanan.create') }}">
                <i class="bi bi-plus-circle"></i> Buat Pesanan
            </a>
            <a class="nav-link {{ request()->routeIs('pelanggan.pesanan.index') || request()->routeIs('pelanggan.pesanan.show') ? 'active' : '' }}"
                href="{{ route('pelanggan.pesanan.index') }}">
                <i class="bi bi-list-ul"></i> Pesanan Saya
            </a>
        </nav>
        <nav class="nav flex-column">
            <a class="nav-link" href="{{ route('katalog') }}">
                <i class="bi bi-grid"></i> Lihat Katalog
            </a>
        </nav>
    @elseif($role === 'kasir')
        <nav class="nav flex-column">
            <a class="nav-link {{ request()->routeIs('kasir.dashboard') ? 'active' : '' }}"
                href="{{ route('kasir.dashboard') }}">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
            <a class="nav-link {{ request()->routeIs('kasir.pesanan.create') ? 'active' : '' }}"
                href="{{ route('kasir.pesanan.create') }}">
                <i class="bi bi-plus-square"></i> Buat Pesanan
            </a>
            <a class="nav-link {{ request()->routeIs('kasir.produk.*') || request()->routeIs('kasir.kategori.*') ? 'active' : '' }}"
                href="{{ route('kasir.produk.index') }}">
                <i class="bi bi-box-seam"></i> Kelola Produk
            </a>
            <a class="nav-link {{ request()->routeIs('kasir.pesanan.index') || (request()->routeIs('kasir.pesanan.show') && !request()->routeIs('kasir.pesanan.create')) ? 'active' : '' }}"
                href="{{ route('kasir.pesanan.index') }}">
                <i class="bi bi-receipt"></i> Verifikasi Pesanan
            </a>
            <a class="nav-link {{ request()->routeIs('kasir.antrian') ? 'active' : '' }}"
                href="{{ route('kasir.antrian') }}">
                <i class="bi bi-list-ol"></i> Lihat Antrian
            </a>
            <a class="nav-link {{ request()->routeIs('kasir.riwayat') ? 'active' : '' }}"
                href="{{ route('kasir.riwayat') }}">
                <i class="bi bi-check2-all"></i> Riwayat Pesanan
            </a>
            <a class="nav-link {{ request()->routeIs('kasir.laporan.*') ? 'active' : '' }}"
                href="{{ route('kasir.laporan.index') }}">
                <i class="bi bi-file-earmark-bar-graph"></i> Laporan Harian
            </a>
        </nav>
    @elseif($role === 'operator_produksi')
        <nav class="nav flex-column">
            <a class="nav-link {{ request()->routeIs('produksi.dashboard') ? 'active' : '' }}"
                href="{{ route('produksi.dashboard') }}">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
            <a class="nav-link {{ request()->routeIs('produksi.antrian') ? 'active' : '' }}"
                href="{{ route('produksi.antrian') }}">
                <i class="bi bi-list-ol"></i> Antrian Produksi
            </a>
            <a class="nav-link {{ request()->routeIs('produksi.riwayat') ? 'active' : '' }}"
                href="{{ route('produksi.riwayat') }}">
                <i class="bi bi-check2-all"></i> Riwayat Produksi
            </a>
        </nav>
    @endif
</aside>
