<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\PesananController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\KasirController;
use App\Http\Controllers\ProdukCmsController;
use App\Http\Controllers\KategoriProdukController;
use App\Http\Controllers\ProduksiController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\AccountSettingsController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Sistem Informasi Pemesanan Percetakan - Artpedia Printshop
|
*/

// ============ PUBLIC ROUTES ============
Route::get('/', [LandingController::class, 'index'])->name('landing');
Route::get('/katalog', [LandingController::class, 'katalog'])->name('katalog');
Route::get('/katalog/{slug}', [LandingController::class, 'show'])->name('katalog.show');

// Auth Routes
Auth::routes();

// Home - Redirect based on role
Route::get('/home', [HomeController::class, 'index'])->name('home');

// Account Settings
Route::middleware('auth')->group(function () {
    Route::get('/akun/pengaturan', [AccountSettingsController::class, 'edit'])->name('account.settings');
    Route::put('/akun/pengaturan/profil', [AccountSettingsController::class, 'updateProfile'])->name('account.profile.update');
    Route::put('/akun/pengaturan/password', [AccountSettingsController::class, 'updatePassword'])->name('account.password.update');
});

// ============ PELANGGAN ROUTES ============
Route::middleware(['auth', 'role:pelanggan'])->prefix('pelanggan')->name('pelanggan.')->group(function () {
    Route::get('/dashboard', [HomeController::class, 'pelangganDashboard'])->name('dashboard');

    // Pesanan
    Route::get('/pesanan', [PesananController::class, 'index'])->name('pesanan.index');
    Route::get('/pesanan/create', [PesananController::class, 'create'])->name('pesanan.create');
    Route::post('/pesanan', [PesananController::class, 'store'])->name('pesanan.store');
    Route::get('/pesanan/{id}', [PesananController::class, 'show'])->name('pesanan.show');
    Route::get('/pesanan/{id}/file-desain', [PesananController::class, 'viewFileDesain'])->name('pesanan.file_desain');
    Route::get('/pesanan/{id}/bukti-pembayaran', [PesananController::class, 'viewBuktiPembayaran'])->name('pesanan.bukti_pembayaran');
    Route::delete('/pesanan/{id}', [PesananController::class, 'destroy'])->name('pesanan.destroy');

    // AJAX: Hitung harga
    Route::post('/hitung-harga', [PesananController::class, 'hitungHarga'])->name('hitung-harga');

    // Keranjang
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::delete('/cart/{id}', [CartController::class, 'remove'])->name('cart.remove');
    Route::get('/checkout', [CartController::class, 'checkout'])->name('checkout');
    Route::post('/checkout', [CartController::class, 'processCheckout'])->name('checkout.process');
});

// ============ KASIR ROUTES ============
Route::middleware(['auth', 'role:kasir'])->prefix('kasir')->name('kasir.')->group(function () {
    Route::get('/dashboard', [KasirController::class, 'dashboard'])->name('dashboard');

    // Pesanan
    Route::get('/pesanan', [KasirController::class, 'index'])->name('pesanan.index');
    Route::get('/pesanan/realtime/list', [KasirController::class, 'realtimePesanan'])->name('pesanan.realtime');
    Route::get('/pesanan/buat', [KasirController::class, 'create'])->name('pesanan.create');
    Route::post('/pesanan/buat/item', [KasirController::class, 'addItem'])->name('pesanan.item.add');
    Route::delete('/pesanan/buat/item/{id}', [KasirController::class, 'removeItem'])->name('pesanan.item.remove');
    Route::delete('/pesanan/buat/item', [KasirController::class, 'clearItems'])->name('pesanan.item.clear');
    Route::get('/cart', [KasirController::class, 'cart'])->name('cart.index');
    Route::get('/checkout', [KasirController::class, 'checkout'])->name('checkout');
    Route::post('/pesanan/buat', [KasirController::class, 'store'])->name('pesanan.store');
    Route::get('/pesanan/{id}', [KasirController::class, 'show'])->name('pesanan.show');
    Route::get('/pesanan/{id}/file-desain', [KasirController::class, 'viewFileDesain'])->name('pesanan.file_desain');
    Route::get('/pesanan/{id}/bukti-pembayaran', [KasirController::class, 'viewBuktiPembayaran'])->name('pesanan.bukti_pembayaran');
    Route::post('/pesanan/{id}/validasi', [KasirController::class, 'validasi'])->name('pesanan.validasi');
    Route::post('/pesanan/{id}/tolak', [KasirController::class, 'tolak'])->name('pesanan.tolak');
    Route::post('/pesanan/{id}/batalkan', [KasirController::class, 'batalkan'])->name('pesanan.batalkan');
    Route::get('/antrian', [KasirController::class, 'antrian'])->name('antrian');

    // CMS Produk
    Route::get('/produk', [ProdukCmsController::class, 'index'])->name('produk.index');
    Route::get('/produk/tambah', [ProdukCmsController::class, 'create'])->name('produk.create');
    Route::post('/produk', [ProdukCmsController::class, 'store'])->name('produk.store');
    Route::get('/produk/{id}/edit', [ProdukCmsController::class, 'edit'])->name('produk.edit');
    Route::put('/produk/{id}', [ProdukCmsController::class, 'update'])->name('produk.update');
    Route::post('/produk/{id}/toggle', [ProdukCmsController::class, 'toggleAktif'])->name('produk.toggle');

    // CMS Kategori Produk
    Route::get('/kategori', [KategoriProdukController::class, 'index'])->name('kategori.index');
    Route::get('/kategori/tambah', [KategoriProdukController::class, 'create'])->name('kategori.create');
    Route::post('/kategori', [KategoriProdukController::class, 'store'])->name('kategori.store');
    Route::get('/kategori/{id}/edit', [KategoriProdukController::class, 'edit'])->name('kategori.edit');
    Route::put('/kategori/{id}', [KategoriProdukController::class, 'update'])->name('kategori.update');
    Route::post('/kategori/{id}/toggle', [KategoriProdukController::class, 'toggleAktif'])->name('kategori.toggle');

    // Laporan
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/export-pdf', [LaporanController::class, 'exportPdf'])->name('laporan.export');
    // Riwayat Pesanan (Kasir)
    Route::get('/riwayat', [App\Http\Controllers\KasirController::class, 'riwayat'])->name('riwayat');
});

// ============ OPERATOR PRODUKSI ROUTES ============
Route::middleware(['auth', 'role:operator_produksi'])->prefix('produksi')->name('produksi.')->group(function () {
    Route::get('/dashboard', [ProduksiController::class, 'dashboard'])->name('dashboard');

    // Antrian (SJF)
    Route::get('/antrian', [ProduksiController::class, 'antrian'])->name('antrian');
    Route::get('/antrian/realtime/list', [ProduksiController::class, 'realtimeAntrian'])->name('antrian.realtime');
    Route::get('/pesanan/{id}', [ProduksiController::class, 'show'])->name('show');
    Route::get('/pesanan/{id}/download', [ProduksiController::class, 'download'])->name('download');

    // Update Status
    Route::post('/pesanan/{id}/mulai', [ProduksiController::class, 'mulai'])->name('mulai');
    Route::post('/pesanan/{id}/selesai', [ProduksiController::class, 'selesai'])->name('selesai');

    // Riwayat
    Route::get('/riwayat', [ProduksiController::class, 'selesaiList'])->name('riwayat');
});
