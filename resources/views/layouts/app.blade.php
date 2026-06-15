<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('images/favicon-artpedia.png') }}">

    <title>@yield('title', 'Artpedia Printshop') - Sistem Pemesanan Percetakan</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Montserrat:wght@400;700;800;900&display=swap" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        :root {
            --primary-color: #9d005e;
            --primary-dark: #7a0049;
            --secondary-color: #f59e0b;
            --success-color: #10b981;
            --danger-color: #ef4444;
            --dark-color: #1e293b;
            --light-bg: #fff5fa;
            --card-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        * {
            font-family: 'Inter', sans-serif;
        }

        body {
            background-color: var(--light-bg);
            min-height: 100vh;
            font-size: 0.95rem;
            color: #334155;
            line-height: 1.6;
        }

        /* Navbar */
        .navbar-custom {
            background: linear-gradient(135deg, #9d005e 0%, #7a0049 100%);
            box-shadow: var(--card-shadow);
            padding-top: 8px;
            padding-bottom: 8px;
        }

        .navbar-custom .navbar-brand {
            font-weight: 700;
            font-size: 1.4rem;
            color: white;
        }

        .navbar-brand img {
            height: 36px;
        }



        .navbar-custom .nav-link {
            color: rgba(255, 255, 255, 0.9);
            font-weight: 500;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }

        .navbar-custom .nav-link:hover {
            color: white;
            transform: translateY(-1px);
        }

        .cart-link {
            position: relative;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            color: white;
            background: rgba(255, 255, 255, 0.14);
            transition: all 0.25s ease;
        }

        .cart-link:hover {
            background: rgba(255, 255, 255, 0.24);
            transform: translateY(-1px);
            color: white;
        }

        .cart-count-badge {
            position: absolute;
            top: -6px;
            right: -8px;
            min-width: 18px;
            height: 18px;
            border-radius: 999px;
            background: #f59e0b;
            color: #1e293b;
            font-size: 0.7rem;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0 5px;
            line-height: 1;
            border: 1px solid rgba(255, 255, 255, 0.85);
        }

        /* Sidebar */
        .sidebar {
            background: white;
            height: calc(100vh - 56px);
            overflow-y: auto;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.05);
            position: fixed;
            top: 56px;
            left: 0;
            width: 260px;
            z-index: 100;
            padding-top: 0;
            padding-bottom: 2rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .sidebar.collapsed {
            transform: translateX(-100%);
        }

        .sidebar .nav-link {
            color: #64748b;
            padding: 0.8rem 1.5rem;
            border-radius: 0;
            margin: 0.2rem 0;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 1rem;
            white-space: nowrap;
        }

        .sidebar .nav-link:hover {
            background: linear-gradient(90deg, rgba(157, 0, 94, 0.05) 0%, transparent 100%);
            color: var(--primary-color);
        }

        .sidebar .nav-link.active {
            background: linear-gradient(90deg, rgba(157, 0, 94, 0.1) 0%, transparent 100%);
            color: var(--primary-color);
            border-left: 4px solid var(--primary-color);
            font-weight: 600;
        }

        .sidebar .nav-link i {
            font-size: 1.25rem;
        }

        .main-content {
            margin-left: 260px;
            padding: 1.5rem;
            min-height: calc(100vh - 56px);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .main-content.full-width {
            margin-left: 0;
        }

        /* Cards */
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: var(--card-shadow);
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            background: transparent;
            border-bottom: 1px solid #e2e8f0;
            font-weight: 600;
        }

        /* Stats Card */
        .stat-card {
            background: white;
            border-radius: 16px;
            padding: 1.5rem;
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, rgba(157, 0, 94, 0.1) 0%, transparent 100%);
            border-radius: 50%;
            transform: translate(30%, -30%);
        }

        .stat-card .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }

        .stat-card .stat-value {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--dark-color);
        }

        .stat-card .stat-label {
            font-size: 0.875rem;
            color: #64748b;
        }

        /* Buttons */
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            border: none;
            font-weight: 500;
            padding: 0.6rem 1.5rem;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(157, 0, 94, 0.4);
            background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary-color) 100%);
        }

        .btn-success {
            background: linear-gradient(135deg, var(--success-color) 0%, #059669 100%);
            border: none;
        }

        .btn-warning {
            background: linear-gradient(135deg, var(--secondary-color) 0%, #d97706 100%);
            border: none;
            color: white;
        }

        .btn-warning:hover {
            color: white;
        }

        /* Override Bootstrap Utilities */
        .text-primary { color: var(--primary-color) !important; }
        .bg-primary { background-color: var(--primary-color); }
        .bg-primary-subtle { background-color: rgba(157, 0, 94, 0.1) !important; }
        .border-primary { border-color: var(--primary-color) !important; }
        
        /* Fix Background Opacity with Brand Color */
        .stat-icon.bg-primary {
            background-color: rgba(157, 0, 94, 0.1) !important;
            color: var(--primary-color) !important;
        }

        .btn-outline-primary {
            color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-outline-primary:hover,
        .btn-outline-primary:focus,
        .btn-check:checked + .btn-outline-primary {
            background-color: var(--primary-color) !important;
            border-color: var(--primary-color) !important;
            color: white !important;
            box-shadow: 0 0 0 0.25rem rgba(157, 0, 94, 0.25) !important;
        }

        .btn-primary {
            background-color: var(--primary-color) !important;
            border-color: var(--primary-color) !important;
        }
        .btn-primary:hover, .btn-primary:focus, .btn-primary:active {
            background-color: var(--primary-dark) !important;
            border-color: var(--primary-dark) !important;
            box-shadow: 0 0 0 0.25rem rgba(157, 0, 94, 0.25) !important;
        }

        /* Form Controls */
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(157, 0, 94, 0.25);
        }

        .link-primary { color: var(--primary-color) !important; }
        .page-link { color: var(--primary-color); }
        .page-item.active .page-link {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }


        /* Badge */
        .badge {
            font-weight: 500;
            padding: 0.5em 0.8em;
            border-radius: 6px;
        }

        /* Table */
        .table {
            border-collapse: separate;
            border-spacing: 0;
        }

        .table thead th {
            background: #f1f5f9;
            border: none;
            font-weight: 600;
            color: #475569;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
            padding: 1rem;
        }

        .table tbody td {
            padding: 1rem;
            vertical-align: middle;
            border-bottom: 1px solid #e2e8f0;
        }

        .table tbody tr:hover {
            background-color: #f8fafc;
        }

        /* Form */
        .form-control,
        .form-select {
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            padding: 0.6rem 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(157, 0, 94, 0.1);
        }

        .form-label {
            font-weight: 600;
            color: #475569;
            margin-bottom: 0.4rem;
            font-size: 0.875rem;
        }

        /* Alerts */
        .alert {
            border: none;
            border-radius: 10px;
            padding: 1rem 1.25rem;
        }

        /* Status Badge Colors */
        .badge-pending {
            background: #fef3c7;
            color: #92400e;
        }

        .badge-ditolak {
            background: #fee2e2;
            color: #991b1b;
        }

        .badge-dibatalkan {
            background: #e2e8f0;
            color: #334155;
        }

        .badge-dalam_antrian {
            background: #dbeafe;
            color: #1e40af;
        }

        .badge-diproses {
            background: #e0e7ff;
            color: #3730a3;
        }

        .badge-selesai {
            background: #d1fae5;
            color: #065f46;
        }

        /* Responsive */
        @media (max-width: 992px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                box-shadow: none;
            }

            .sidebar.show {
                transform: translateX(0);
                box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
            }

            .main-content {
                margin-left: 0;
                width: 100%;
            }
            
            /* Overlay */
            .sidebar-overlay {
                position: fixed;
                top: 0;
                left: 0;
                width: 100vw;
                height: 100vh;
                background: rgba(0, 0, 0, 0.5);
                z-index: 99;
                opacity: 0;
                visibility: hidden;
                transition: all 0.3s ease;
                backdrop-filter: blur(2px);
            }

            .sidebar-overlay.show {
                opacity: 1;
                visibility: visible;
            }
        }

        /* Page Header */
        .page-header {
            margin-bottom: 1.5rem;
        }

        .page-header h1 {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--dark-color);
            margin-bottom: 0.25rem;
        }

        .page-header p {
            color: #64748b;
            margin-bottom: 0;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 3rem;
            color: #64748b;
        }

        .empty-state i {
            font-size: 4rem;
            color: #cbd5e1;
            margin-bottom: 1rem;
        }

        .dropdown-menu .dropdown-item:active,
        .dropdown-menu .dropdown-item:focus {
            background-color: #f8fafc;
            color: inherit;
        }

        .dropdown-menu .dropdown-item.text-danger:active,
        .dropdown-menu .dropdown-item.text-danger:focus {
            background-color: #fee2e2;
            color: #dc2626 !important;
        }
        /* Bottom Navigation (Mobile Only) */
        .bottom-nav {
            display: none;
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: white;
            box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.05);
            z-index: 1030;
            padding: 8px 0;
            border-top: 1px solid #f1f5f9;
        }

        .bottom-nav-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: #94a3b8;
            text-decoration: none;
            font-size: 0.7rem;
            font-weight: 500;
            flex: 1;
            transition: all 0.2s ease;
        }

        .bottom-nav-item i {
            font-size: 1.4rem;
            margin-bottom: 2px;
        }

        .bottom-nav-item.active {
            color: var(--primary-color);
        }

        /* Mobile Specific Adjustments */
        @media (max-width: 768px) {
            body {
                font-size: 0.85rem; /* Font dasar lebih kecil */
            }

            h1, .h1 { font-size: 1.2rem !important; }
            h2, .h2 { font-size: 1.1rem !important; }
            h3, .h3 { font-size: 1rem !important; }
            h4, .h4 { font-size: 0.95rem !important; }
            h5, .h5 { font-size: 0.9rem !important; }
            h6, .h6 { font-size: 0.85rem !important; }

            .bottom-nav {
                display: flex;
            }

            .main-content {
                padding-top: 1rem !important;
                padding-bottom: 80px !important; 
                padding-left: 0.75rem;
                padding-right: 0.75rem;
            }

            .navbar-custom .navbar-brand img {
                height: 28px;
            }

            .page-header h1 {
                font-size: 1.15rem;
                letter-spacing: -0.01em;
            }

            .page-header p {
                font-size: 0.8rem;
            }

            /* Buttons smaller on mobile */
            .btn {
                padding: 0.4rem 0.8rem;
                font-size: 0.85rem;
            }
            
            /* Compact Cards on Mobile */
            .card-body {
                padding: 0.75rem;
            }
            
            .stat-card {
                padding: 0.8rem;
            }
            .stat-card .stat-icon {
                width: 35px;
                height: 35px;
                font-size: 1rem;
                margin-bottom: 0.5rem;
            }
            .stat-card .stat-value {
                font-size: 1.2rem;
            }
            .stat-card .stat-label {
                font-size: 0.75rem;
            }

            /* Hide Sidebar toggle on mobile for pelanggan if using bottom nav */
            .btn-link.d-lg-none {
                display: none !important;
            }
        }
    </style>

    @stack('styles')
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-custom fixed-top">
        <div class="container-fluid px-4">
            <div class="d-flex align-items-center">
                @auth
                    <button class="btn btn-link text-white p-0 d-lg-none me-3" onclick="toggleSidebar()">
                        <i class="bi bi-list fs-3"></i>
                    </button>
                @endauth
                <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('landing') }}">
                    <img src="{{ asset('images/logo-white.png') }}" alt="Logo">
                </a>
            </div>

            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <i class="bi bi-list text-white fs-4"></i>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">

                <ul class="navbar-nav ms-auto align-items-center">
                    @guest
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('katalog') }}">
                                <i class="bi bi-grid me-1"></i> Katalog
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">
                                <i class="bi bi-box-arrow-in-right me-1"></i> Login
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-light btn-sm ms-2" href="{{ route('register') }}">
                                Daftar
                            </a>
                        </li>
                    @else
                        @if(Auth::user()->role === 'pelanggan' || Auth::user()->role === 'kasir')
                            @php
                                $cartCount = Auth::user()->role === 'kasir'
                                    ? count(session('kasir_walkin_cart', []))
                                    : count(session('cart', []));
                                $cartRoute = Auth::user()->role === 'kasir'
                                    ? route('kasir.cart.index')
                                    : route('pelanggan.cart.index');
                            @endphp
                            <li class="nav-item me-2">
                                <a href="{{ $cartRoute }}" class="cart-link text-decoration-none" title="Keranjang">
                                    <i class="bi bi-cart3 fs-5"></i>
                                    @if($cartCount > 0)
                                        <span class="cart-count-badge">{{ $cartCount }}</span>
                                    @endif
                                </a>
                            </li>
                        @endif

                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" role="button"
                                data-bs-toggle="dropdown">
                                <div class="bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                    <i class="bi bi-person-fill"></i>
                                </div>
                                {{ Auth::user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">

                                <li>
                                    <a class="dropdown-item" href="{{ route('home') }}">
                                        <i class="bi bi-speedometer2 me-2"></i> Dashboard
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('account.settings') }}">
                                        <i class="bi bi-gear me-2"></i> Pengaturan Akun
                                    </a>
                                </li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <a class="dropdown-item text-danger" href="{{ route('logout') }}"
                                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <i class="bi bi-box-arrow-right me-2"></i> Logout
                                    </a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @endguest

                    @auth
                        @if(!request()->routeIs('landing'))
<!--                        <li class="nav-item ms-2">
                            <button class="btn btn-link text-white p-0 border-0 shadow-none" id="sidebarToggle" title="Toggle Sidebar">
                                <div class="bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; transition: all 0.3s ease;">
                                    <i class="bi bi-layout-sidebar-inset"></i>
                                </div>
                            </button>
                        </li>-->
                        @endif
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <div style="padding-top: 56px;">
        @auth
            @php $isLanding = request()->routeIs('landing'); @endphp
            @if(!$isLanding)
                @include('layouts.sidebar')
            @endif
            <main class="main-content {{ $isLanding ? 'full-width p-0' : '' }}" id="mainContent">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle me-2"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-circle me-2"></i>
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @yield('content')
            </main>
        @else
            <main>
                @yield('content')
            </main>
        @endauth
    </div>

    <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

    @auth
        @php
            $shouldShowFeedbackModal = auth()->user()->role === 'pelanggan'
                && (session('feedback_prompt') || request()->boolean('show_feedback'));
        @endphp
        @if($shouldShowFeedbackModal)
            <div class="modal fade" id="feedbackModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow-lg" style="border-radius: 14px; overflow: hidden;">
                        <div class="position-absolute top-0 end-0 p-3" style="z-index: 2;">
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-4 text-center">
                            <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle"
                                style="width: 58px; height: 58px; background: rgba(22, 163, 74, 0.12); color: #15803d;">
                                <i class="bi bi-check2-circle" style="font-size: 1.9rem;"></i>
                            </div>
                            <h5 class="fw-bold mb-2 text-success">Pembayaran Berhasil</h5>
                            <p class="text-muted mb-3">
                                Pembayaran Anda sudah kami terima. Pesanan akan segera kami proses.
                            </p>
                            <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle"
                                style="width: 46px; height: 46px; background: rgba(157, 0, 94, 0.1); color: #9d005e;">
                                <i class="bi bi-chat-heart" style="font-size: 1.45rem;"></i>
                            </div>
                            <h6 class="fw-bold mb-2">Bantu Kami Menjadi Lebih Baik</h6>
                            <p class="text-muted mb-0">
                                Mohon luangkan waktu sebentar untuk membagikan pengalaman Anda setelah melakukan pemesanan di Artpedia.
                            </p>
                        </div>
                        <div class="modal-footer border-0 bg-light px-4 pb-4 pt-3 d-flex justify-content-center gap-2 flex-wrap">
                            <a href="{{ route('pelanggan.dashboard') }}"
                                class="btn px-4 text-white fw-bold d-inline-flex align-items-center justify-content-center"
                                style="background-color: #9d005e; border-color: #9d005e; min-width: 190px;">
                                Kembali ke Dashboard
                            </a>
                            <a href="https://forms.gle/RWLXe4ncwE2ZkgdF7" target="_blank" rel="noopener"
                                class="btn btn-success px-4 fw-bold d-inline-flex align-items-center justify-content-center"
                                style="min-width: 190px;">
                                Isi Kuesioner
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endauth

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            
            sidebar.classList.toggle('show');
            overlay.classList.toggle('show');
            
            // Prevent scrolling when sidebar is open
            if (sidebar.classList.contains('show')) {
                document.body.style.overflow = 'hidden';
            } else {
                document.body.style.overflow = '';
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            const feedbackModal = document.getElementById('feedbackModal');
            if (feedbackModal) {
                new bootstrap.Modal(feedbackModal).show();

                const currentUrl = new URL(window.location.href);
                if (currentUrl.searchParams.has('show_feedback')) {
                    currentUrl.searchParams.delete('show_feedback');
                    const nextUrl = currentUrl.pathname
                        + (currentUrl.search ? currentUrl.search : '')
                        + currentUrl.hash;
                    window.history.replaceState({}, document.title, nextUrl);
                }
            }
        });
    </script>

    @auth
        @if(auth()->user()->role === 'pelanggan')
            <!-- Bottom Navigation for Mobile -->
            <nav class="bottom-nav d-md-none">
                <a href="{{ route('home') }}" class="bottom-nav-item {{ request()->routeIs('home') || request()->routeIs('pelanggan.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-house-door{{ request()->routeIs('home') || request()->routeIs('pelanggan.dashboard') ? '-fill' : '' }}"></i>
                    <span>Beranda</span>
                </a>
                <a href="{{ route('katalog') }}" class="bottom-nav-item {{ request()->routeIs('katalog') ? 'active' : '' }}">
                    <i class="bi bi-grid{{ request()->routeIs('katalog') ? '-fill' : '' }}"></i>
                    <span>Katalog</span>
                </a>
                <a href="{{ route('pelanggan.pesanan.index') }}" class="bottom-nav-item {{ request()->routeIs('pelanggan.pesanan.*') ? 'active' : '' }}">
                    <i class="bi bi-bag-check{{ request()->routeIs('pelanggan.pesanan.*') ? '-fill' : '' }}"></i>
                    <span>Pesanan</span>
                </a>
                <a href="{{ route('account.settings') }}" class="bottom-nav-item {{ request()->routeIs('account.settings') ? 'active' : '' }}">
                    <i class="bi bi-person{{ request()->routeIs('account.settings') ? '-fill' : '' }}"></i>
                    <span>Profil</span>
                </a>
            </nav>
        @endif
    @endauth

    @stack('scripts')
</body>

</html>
