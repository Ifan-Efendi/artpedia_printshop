@extends('layouts.app')

@section('title', 'Artpedia Printshop')

@push('styles')
    <link href="https://fonts.googleapis.com/css2?family=Kaushan+Script&display=swap" rel="stylesheet">
@endpush

@section('content')
    <style>
        .hero-section {
            background: linear-gradient(135deg, #9d005e 0%, #7a0049 50%, #4a002c 100%);
            padding: 5rem 0;
            margin-bottom: 2rem;
            color: white;
            position: relative;
            overflow: hidden;
            border-bottom: 5px solid var(--secondary-color);
        }

        .brand-container {
            display: inline-block;
            margin-bottom: 2rem;
        }

        .hero-row {
            min-height: 455px;
        }

        .hero-copy {
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .hero-title {
            font-family: 'Montserrat', sans-serif;
            font-size: 3.5rem;
            font-weight: 700;
            color: #ffffff;
            line-height: 1.1;
            margin-bottom: 0.4rem;
            letter-spacing: 0;
            display: flex;
            align-items: baseline;
            flex-wrap: wrap;
            gap: 15px;
        }

        .brand-sub {
            font-weight: 700;
            color: rgba(255, 255, 255, 0.95);
        }

        .hero-tagline {
            display: inline-block;
            position: relative;
            font-family: 'Kaushan Script', cursive;
            font-weight: 400;
            color: #ffbf12;
            font-size: 2rem;
            line-height: 1.25;
            margin: 0 0 1.35rem;
            text-shadow: 0 3px 12px rgba(74, 0, 44, 0.35);
            max-width: 100%;
            transform: skewX(-6deg);
            transform-origin: left center;
        }

        @media (max-width: 991px) {
            .hero-title {
                font-size: 2.5rem;
                justify-content: center;
                text-align: center;
            }
        }

        .hero-subtitle {
            font-size: 1.15rem;
            opacity: 0.9;
            line-height: 1.6;
        }

        .hero-service-list {
            max-width: 560px;
            margin-left: 0;
            margin-right: 0;
        }

        .hero-service-list > [class*="col-"] {
            padding-left: 0;
        }

        .hero-contact-info {
            display: flex;
            flex-direction: column;
            gap: 0.45rem;
            margin-bottom: 1rem;
            color: rgba(255, 255, 255, 0.92);
            font-size: 1.15rem;
            line-height: 1.6;
        }

        .hero-contact-info a {
            color: #ffffff;
            text-decoration: none;
            font-weight: inherit;
        }

        .hero-contact-info a:hover {
            color: #ffbf12;
        }

        .hero-contact-info i {
            color: #ffbf12;
            margin-right: 0.5rem;
        }

        .hero-visual {
            min-height: 430px;
        }

        .hero-actions {
            margin-top: 1rem;
        }

        @media (max-width: 991px) {
            .hero-section {
                padding: 3rem 0;
                text-align: left !important; /* Paksa rata kiri */
            }
            .hero-row {
                min-height: auto;
            }
            .hero-copy {
                justify-content: flex-start;
            }
            .hero-title {
                font-family: 'Montserrat', sans-serif !important;
                font-size: 2.2rem !important; /* Ukuran lebih kecil */
                font-weight: 800 !important;
                line-height: 1.2 !important;
                letter-spacing: 0 !important;
                display: flex !important; /* Kembali ke flex agar sejajar ke samping */
                justify-content: flex-start !important; /* Paksa rata kiri untuk kontainer flex */
                flex-wrap: wrap;
                gap: 10px;
                text-align: left !important;
                margin-bottom: 0.35rem !important;
            }
            .brand-sub {
                display: inline-block !important;
                text-align: left !important;
                font-size: 2.2rem !important; /* Ukuran sama dengan Artpedia */
                margin-top: 0 !important;
            }
            .hero-tagline {
                font-size: 1.45rem;
                line-height: 1.35;
                margin-bottom: 1.15rem;
                text-align: left;
            }
            .hero-subtitle {
                font-size: 1.1rem !important;
                text-align: left !important;
                margin-left: 0 !important;
            }
            .hero-service-list {
                max-width: 100%;
            }
            .hero-contact-info {
                font-size: 1.1rem !important;
                line-height: 1.6;
            }
            .hero-visual {
                min-height: 320px;
            }
            .hero-actions {
                margin-top: 0.75rem;
            }
            .hero-features-row {
                justify-content: center;
                gap: 1.5rem !important;
                flex-wrap: wrap;
            }
            .hero-feature-item {
                flex-direction: column;
                text-align: center;
                gap: 0.5rem !important;
            }
            .hero-feature-item .me-3 {
                margin-right: 0 !important;
            }
        }

        .feature-card {
            border: none;
            border-radius: 20px;
            padding: 2.25rem 1.5rem !important;
            transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
            background: #ffffff;
            box-shadow: 0 4px 20px rgba(0,0,0,0.04);
            text-align: center !important;
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 12px 30px rgba(0,0,0,0.08);
        }

        .feature-icon {
            width: 72px;
            height: 72px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.2rem !important;
            font-size: 1.25rem;
            padding: 6px;
        }

        .feature-icon i, .feature-icon-inner {
            font-size: 1.45rem;
            line-height: 1;
        }

        .feature-card h5 {
            margin-top: 0.75rem !important;
            margin-bottom: 0.5rem !important;
        }

        .cta-section {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            border-radius: 30px;
            padding: 5rem 2rem;
            color: white;
            text-align: center;
            margin-top: 4rem;
            position: relative;
            overflow: hidden;
        }

        @media (max-width: 768px) {
            .cta-section {
                padding: 3rem 1.5rem;
                border-radius: 20px;
            }
        }

        @keyframes shimmer-glow {
            0% { filter: drop-shadow(0 0 15px rgba(255,255,255,0.1)); transform: scale(1); }
            50% { filter: drop-shadow(0 0 25px rgba(255,255,255,0.3)); transform: scale(1.02); }
            100% { filter: drop-shadow(0 0 15px rgba(255,255,255,0.1)); transform: scale(1); }
        }

        @keyframes orbit-cwc {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        @keyframes orbit-ccw {
            from { transform: rotate(360deg); }
            to { transform: rotate(0deg); }
        }

        @keyframes float-particle {
            0%, 100% { transform: translate(0, 0); opacity: 0; }
            50% { opacity: 1; }
            25% { transform: translate(10px, -15px); }
            75% { transform: translate(-10px, 15px); }
        }

        .ring-outer {
            position: absolute;
            width: min(450px, 90vw);
            height: min(450px, 90vw);
            border-radius: 50%;
            background: conic-gradient(from 0deg, transparent 0%, rgba(255,255,255,0.1) 20%, transparent 40%);
            animation: orbit-cwc 15s linear infinite;
        }

        .ring-inner {
            position: absolute;
            width: min(350px, 70vw);
            height: min(350px, 70vw);
            border-radius: 50%;
            border: 1px solid rgba(255,255,255,0.05);
            border-top: 2px solid rgba(255,255,255,0.3);
            border-left: 2px solid rgba(255,255,255,0.3);
            animation: orbit-ccw 10s linear infinite;
        }

        .printer-icon {
            width: min(100%, 430px);
            height: auto;
            opacity: 0.98;
            animation: shimmer-glow 4s ease-in-out infinite;
            filter: drop-shadow(0 15px 30px rgba(0, 0, 0, 0.2));
        }

        .particle {
            position: absolute;
            background: white;
            border-radius: 50%;
            animation: float-particle 4s ease-in-out infinite;
        }

        /* Contact Section */
        .contact-card {
            border-radius: 20px;
            border: none;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
            height: 100%;
        }

        .contact-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
        }

        .contact-icon {
            width: 60px;
            height: 60px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .products-heading {
            gap: 1rem;
        }

        .product-card {
            border-radius: 12px;
            overflow: hidden;
            letter-spacing: 0;
            word-spacing: 0;
        }

        .product-card .card-img-top {
            height: 180px;
            object-fit: cover;
        }

        .product-card .card-title,
        .product-card .card-text,
        .product-card .btn,
        .product-card .text-primary {
            letter-spacing: 0;
            word-spacing: 0;
        }

        @media (max-width: 575px) {
            .products-heading {
                align-items: flex-start !important;
                flex-direction: column;
            }
        }

    </style>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center hero-row">
                <div class="col-lg-7 hero-copy">
                    <h1 class="hero-title">
                        <span>Artpedia</span>
                        <span class="brand-sub">Printshop</span>
                    </h1>
                    <p class="hero-tagline">Harga Terjangkau, Hasil Berkualitas</p>
                    <div class="hero-subtitle mb-3">
                        <p class="mb-0">Mau cetak apa hari ini? Kirim desain atau ceritakan kebutuhanmu dan kami siap membantu.</p>
                    </div>
                    <div class="row g-2 mb-4 hero-service-list hero-subtitle">
                        <div class="col-6 col-md-5">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-square-fill me-2" style="font-size: 0.35rem; color: #ffc107;"></i> Stiker
                            </div>
                        </div>
                        <div class="col-6 col-md-5">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-square-fill me-2" style="font-size: 0.35rem; color: #ffc107;"></i> Poster
                            </div>
                        </div>
                        <div class="col-6 col-md-5">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-square-fill me-2" style="font-size: 0.35rem; color: #ffc107;"></i> Kartu Nama
                            </div>
                        </div>
                        <div class="col-6 col-md-5">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-square-fill me-2" style="font-size: 0.35rem; color: #ffc107;"></i> Kartu Ucapan
                            </div>
                        </div>
                        <div class="col-6 col-md-5">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-square-fill me-2" style="font-size: 0.35rem; color: #ffc107;"></i> Sertifikat
                            </div>
                        </div>
                        <div class="col-6 col-md-5">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-square-fill me-2" style="font-size: 0.35rem; color: #ffc107;"></i> Dll
                            </div>
                        </div>
                    </div>
                    <div class="hero-contact-info">
                        <div>
                            <i class="bi bi-geo-alt-fill"></i>
                            <span>Jl. Raya Bojongsoang No.190, Lengkong, Kec. Bojongsoang, Kabupaten Bandung, Jawa Barat 40288</span>
                        </div>
                        <div>
                            <i class="bi bi-whatsapp"></i>
                            <a href="https://wa.me/6282130003595" target="_blank" rel="noopener">0821-3000-3595</a>
                        </div>
                    </div>
                    <div class="d-flex gap-3 flex-wrap justify-content-start hero-actions">
                        <a href="{{ route('katalog') }}" class="btn btn-light px-4 py-2 shadow-sm border-0">
                            <i class="bi bi-grid me-2"></i> Lihat Katalog
                        </a>
                        @guest
                            <a href="{{ route('register') }}" class="btn btn-warning px-4 py-2 text-white">
                                <i class="bi bi-person-plus me-2"></i> Daftar Sekarang
                            </a>
                        @else
                            @if(auth()->user()->role === 'operator_produksi')
                                <a href="{{ route('produksi.antrian') }}" class="btn btn-warning px-4 py-2 shadow border-0 text-white">
                                    <i class="bi bi-list-ol me-2"></i> Antrian Produksi
                                </a>
                            @elseif(auth()->user()->role === 'kasir')
                                <a href="{{ route('kasir.pesanan.index') }}" class="btn btn-warning px-4 py-2 shadow border-0 text-white">
                                    <i class="bi bi-list-check me-2"></i> Daftar Pesanan
                                </a>
                            @else
                                <a href="{{ route('katalog') }}" class="btn btn-warning px-4 py-2 shadow border-0 text-white">
                                    <i class="bi bi-cart-plus me-2"></i> Pesan sekarang
                                </a>
                            @endif
                        @endguest
                    </div>
                </div>
                <div class="col-lg-5 text-center mt-5 mt-lg-0 position-relative d-flex align-items-center justify-content-center hero-visual">
                    <!-- Planetary Rings -->
                    <div class="ring-outer d-none d-md-block"></div>
                    <div class="ring-inner d-none d-md-block"></div>

                    <!-- Particles -->
                    <div class="particle" style="width: 4px; height: 4px; top: 20%; left: 20%; animation-delay: 0s;"></div>
                    <div class="particle" style="width: 6px; height: 6px; top: 70%; right: 20%; animation-delay: 1s;"></div>
                    <div class="particle" style="width: 3px; height: 3px; top: 40%; right: 10%; animation-delay: 2s;"></div>

                    <div class="position-relative" style="z-index: 1;">
                        <img src="{{ asset('images/welcome.png') }}" alt="Selamat datang di Artpedia" class="printer-icon">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features -->
    <section class="container py-4">
        <div class="text-center mb-4 pb-lg-2">
            <h2 class="fw-bold section-title">Mengapa Memilih Kami?</h2>
            <p class="text-muted">Layanan percetakan digital terbaik untuk kebutuhan Anda</p>
        </div>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="feature-card card h-100 text-center">
                    <div class="feature-icon bg-primary bg-opacity-10 text-primary">
                        <i class="bi bi-lightning-charge-fill feature-icon-inner"></i>
                    </div>
                    <h5 class="fw-bold mt-3">Proses Cepat</h5>
                    <p class="text-muted mb-0">Proses pengerjaan cepat dengan sistem antrian prioritas yang cerdas</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card card h-100 text-center">
                    <div class="feature-icon bg-success bg-opacity-10 text-success">
                        <i class="bi bi-patch-check-fill feature-icon-inner"></i>
                    </div>
                    <h5 class="fw-bold mt-3">Kualitas Terjamin</h5>
                    <p class="text-muted mb-0">Menggunakan mesin cetak berkualitas tinggi dengan hasil yang tajam dan presisi</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card card h-100 text-center">
                    <div class="feature-icon bg-warning bg-opacity-10 text-warning">
                        <i class="bi bi-geo-alt-fill feature-icon-inner"></i>
                    </div>
                    <h5 class="fw-bold mt-3">Tracking Real-time</h5>
                    <p class="text-muted mb-0">Pantau status pesanan Anda secara real-time dari mana saja</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Categories -->
    @if(isset($kategoris) && $kategoris->count() > 0)
        <section class="container py-4">
            <div class="text-center mb-4">
                <h2 class="fw-bold section-title">Kategori Produk</h2>
                <p class="text-muted">Berbagai jenis layanan cetak yang kami sediakan</p>
            </div>

            <div class="row g-3">
                @foreach($kategoris as $kategori)
                    <div class="col-6 col-md-4 col-lg-2">
                        <a href="{{ route('katalog', ['kategori' => $kategori->slug]) }}" class="text-decoration-none">
                            <div class="card text-center p-3 h-100">
                                <i class="bi bi-printer fs-1 text-primary mb-2"></i>
                                <h6 class="mb-1">{{ $kategori->nama }}</h6>
                                <small class="text-muted">{{ $kategori->produk_aktif_count ?? 0 }} produk</small>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    <!-- Latest Products -->
    @if(isset($produkTerbaru) && $produkTerbaru->count() > 0)
        <section class="container products-section py-4">
            <div class="products-heading d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold mb-1 section-title">Produk Terbaru</h2>
                    <p class="text-muted mb-0">Lihat layanan cetak terbaru kami</p>
                </div>
                <a href="{{ route('katalog') }}" class="btn btn-outline-primary">
                    Lihat Semua <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>

            <div class="row g-4">
                @foreach($produkTerbaru as $produk)
                    <div class="col-sm-6 col-lg-4">
                        <a href="{{ route('katalog.show', $produk->slug) }}" class="text-decoration-none text-dark d-block h-100">
                            <div class="card product-card h-100 shadow-sm border-0 position-relative">
                                <img src="{{ $produk->gambar_url }}" class="card-img-top" alt="{{ $produk->nama }}">
                                <div class="card-body d-flex flex-column p-3">
                                    <h6 class="card-title fw-bold mb-2">{{ $produk->nama }}</h6>
                                    <p class="card-text text-muted flex-grow-1 mb-2" style="font-size: 0.95rem; line-height: 1.45;">{{ Str::limit($produk->deskripsi, 60) }}</p>
                                    <div class="d-flex justify-content-between align-items-center mt-2">
                                        <span class="fw-bold text-primary">{{ $produk->harga_format }}</span>
                                        <span class="btn btn-primary py-1 px-3" style="font-size: 0.9rem;">
                                            <i class="bi bi-eye"></i> Detail
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    <!-- CTA -->
    <section class="container mb-4">
        <div class="cta-section">
            <h2 class="fw-bold mb-3 section-title text-white">Siap Untuk Memesan?</h2>
            <p class="mb-4 opacity-75">Daftar sekarang dan nikmati kemudahan pemesanan cetak online!</p>
            @guest
                <a href="{{ route('register') }}" class="btn btn-primary btn-lg px-5">
                    <i class="bi bi-rocket-takeoff me-2"></i> Mulai Sekarang
                </a>
            @else
                @if(auth()->user()->role === 'operator_produksi')
                    <a href="{{ route('produksi.antrian') }}" class="btn btn-primary btn-lg px-5">
                        <i class="bi bi-list-ol me-2"></i> Lihat Antrian Produksi
                    </a>
                @elseif(auth()->user()->role === 'kasir')
                    <a href="{{ route('kasir.pesanan.index') }}" class="btn btn-primary btn-lg px-5">
                        <i class="bi bi-list-check me-2"></i> Lihat Daftar Pesanan
                    </a>
                @else
                    <a href="{{ route('katalog') }}" class="btn btn-primary btn-lg px-5">
                        <i class="bi bi-grid me-2"></i> Pilih Produk
                    </a>
                @endif
            @endguest
        </div>
    </section>

    <!-- Contact Section -->
    <section class="container py-4">
        <div class="text-center mb-4">
            <h2 class="fw-bold section-title">Hubungi Kami</h2>
            <p class="text-muted">Punya pertanyaan? Kami siap melayani Anda</p>
        </div>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="contact-card card p-4">
                    <div class="contact-icon bg-primary bg-opacity-10 text-primary">
                        <i class="bi bi-geo-alt-fill"></i>
                    </div>
                    <h5 class="fw-bold">Alamat</h5>
                    <p class="text-muted mb-0">Jl. Raya Bojongsoang No.190, Lengkong, <br>Kec. Bojongsoang, Kabupaten Bandung, Jawa Barat 40288</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="contact-card card p-4">
                    <div class="contact-icon bg-success bg-opacity-10 text-success">
                        <i class="bi bi-whatsapp"></i>
                    </div>
                    <h5 class="fw-bold">Kontak</h5>
                    <p class="text-muted mb-0">Hubungi kami via WhatsApp untuk respon lebih cepat:<br>
                        <a href="https://wa.me/6282130003595" target="_blank" rel="noopener" class="text-decoration-none fw-bold text-success">
                            0821-3000-3595
                        </a>
                    </p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="contact-card card p-4">
                    <div class="contact-icon bg-info bg-opacity-10 text-info">
                        <i class="bi bi-clock-fill"></i>
                    </div>
                    <h5 class="fw-bold">Jam Operasional</h5>
                    <p class="text-muted mb-0">Senin - Sabtu: 08.00 - 17.00<br>Minggu: Tutup</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="container text-center py-4 text-muted border-top">
        <p class="mb-2">
            <i class="bi bi-geo-alt me-1"></i>
            Jl. Raya Bojongsoang No.190, Lengkong, Kec. Bojongsoang, Kabupaten Bandung, Jawa Barat 40288
        </p>
        <p class="mb-2">
            <i class="bi bi-whatsapp me-1"></i>
            <a href="https://wa.me/6282130003595" target="_blank" rel="noopener" class="text-decoration-none text-success fw-semibold">
                0821-3000-3595
            </a>
        </p>
        <p class="mb-0">&copy; {{ date('Y') }} Artpedia Printshop. All rights reserved.</p>
    </footer>
@endsection
