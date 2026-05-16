<?php

namespace App\Http\Controllers;

use App\Models\Pesanan;
use App\Models\Produk;
use App\Models\KategoriProduk;
use App\Models\UkuranKertas;
use App\Models\JenisKertas;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Services\MidtransService;
use App\Services\PricingService;

class KasirController extends Controller
{
    private string $walkInCartKey = 'kasir_walkin_cart';
    protected MidtransService $midtransService;
    protected PricingService $pricingService;

    public function __construct(MidtransService $midtransService, PricingService $pricingService)
    {
        $this->midtransService = $midtransService;
        $this->pricingService = $pricingService;
    }

    /**
     * Display kasir dashboard
     */
    public function dashboard()
    {
        $pesananMasukCount = Pesanan::where('status', 'pending')
            ->where('pembayaran_status', 'pending')
            ->count();
        $pesananAntrianCount = Pesanan::where('status', 'dalam_antrian')->count();
        $pesananSelesaiCount = Pesanan::where('status', 'selesai')->count();

        $recentPesanan = Pesanan::where('status', 'pending')
            ->where('pembayaran_status', 'pending')
            ->with(['user', 'produk'])
            ->latest()
            ->take(5)
            ->get();

        return view('kasir.dashboard', compact('pesananMasukCount', 'pesananAntrianCount', 'pesananSelesaiCount', 'recentPesanan'));
    }

    /**
     * Display list of pending orders
     */
    public function index(Request $request)
    {
        $pesanans = $this->getPesananList($request);

        return view('kasir.pesanan.index', compact('pesanans'));
    }

    public function realtimePesanan(Request $request)
    {
        $pesanans = $this->getPesananList($request);

        return view('kasir.pesanan._list', compact('pesanans'));
    }

    private function getPesananList(Request $request)
    {
        $query = Pesanan::with(['user', 'produk', 'ukuranKertas', 'jenisKertas']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            $query->where('status', 'pending')
                ->where('pembayaran_status', 'pending');
        }

        if ($request->filled('search')) {
            $query->where('nomor_pesanan', 'like', '%' . $request->search . '%');
        }

        return $query->latest()->paginate(15)->appends(request()->query());
    }

    /**
     * Display order detail for validation
     */
    public function show($id)
    {
        $pesanan = Pesanan::with(['user', 'produk', 'ukuranKertas', 'jenisKertas', 'transaksi'])
            ->findOrFail($id);

        $paymentModel = $pesanan->transaksi ?: $pesanan;

        if (($paymentModel->pembayaran_status ?? null) === 'pending') {
            try {
                $snapToken = $this->midtransService->getSnapToken($paymentModel);
                $paymentModel->update(['snap_token' => $snapToken]);
                $paymentModel->setAttribute('snap_token', $snapToken);
            } catch (\Exception $e) {
                session()->flash('error', 'Pesanan tersimpan, tetapi kode pembayaran belum bisa diperbarui: ' . $e->getMessage());
            }
        }

        $pesananItems = Pesanan::where('nomor_pesanan', $pesanan->nomor_pesanan)
            ->with(['produk.kategori', 'ukuranKertas', 'jenisKertas'])
            ->orderBy('id')
            ->get();

        $groupTotal = (int) ($pesanan->transaksi->total_harga ?? $pesananItems->sum('total_harga'));

        return view('kasir.pesanan.show', compact('pesanan', 'pesananItems', 'groupTotal'));
    }

    public function paymentStatus($id)
    {
        $pesanan = Pesanan::with('transaksi')->findOrFail($id);

        $orderId = $this->resolveOrderId(request()->query('order_id'));
        if ($orderId) {
            try {
                $this->midtransService->syncTransactionStatus($orderId);
                $pesanan->refresh();
                $pesanan->load('transaksi');
            } catch (\Exception $e) {
                // Keep current local status if Midtrans status check fails.
            }
        }

        $paymentStatus = $pesanan->transaksi->pembayaran_status ?? $pesanan->pembayaran_status;

        return response()->json([
            'payment_status' => $paymentStatus,
            'status' => $pesanan->status,
            'is_paid' => $paymentStatus === 'paid',
            'redirect_url' => $paymentStatus === 'paid'
                ? route('kasir.pesanan.show', ['id' => $pesanan->id, 'show_feedback' => 1])
                : route('kasir.pesanan.show', $pesanan->id),
        ]);
    }

    private function resolveOrderId(?string $orderId): ?string
    {
        return is_string($orderId) && trim($orderId) !== '' ? trim($orderId) : null;
    }

    /**
     * Stream design file for cashier preview
     */
    public function viewFileDesain($id)
    {
        $pesanan = Pesanan::findOrFail($id);
        $path = $pesanan->file_desain;

        if (!$path || $path === 'NANTI_DIKIRIM') {
            return redirect()->back()->with('error', 'File desain tidak tersedia untuk pesanan ini.');
        }

        if (!Storage::disk('public')->exists($path)) {
            return redirect()->back()->with('error', 'File desain tidak ditemukan.');
        }

        $fullPath = storage_path('app/public/' . $path);
        $ext = pathinfo($fullPath, PATHINFO_EXTENSION);
        $filename = $pesanan->nomor_pesanan . '_desain.' . $ext;

        return response()->download($fullPath, $filename);
    }

    /**
     * Stream payment proof file for cashier preview
     */
    public function viewBuktiPembayaran($id)
    {
        $pesanan = Pesanan::findOrFail($id);
        $path = $pesanan->bukti_pembayaran;

        if (!$path || in_array($path, ['Pesanan Langsung', 'Midtrans Kasir'])) {
            return redirect()->back()->with('error', 'Pesanan ini tidak memiliki bukti transfer.');
        }

        if (!Storage::disk('public')->exists($path)) {
            return redirect()->back()->with('error', 'Bukti pembayaran tidak ditemukan.');
        }

        return response()->file(storage_path('app/public/' . $path));
    }

    /**
     * Validate payment and add to queue
     */
    public function validasi(Request $request, $id)
    {
        $pesanan = Pesanan::pending()->findOrFail($id);

        $pesanan->update([
            'status' => 'dalam_antrian',
            'dikonfirmasi_oleh' => auth()->id(),
            'dikonfirmasi_at' => now(),
        ]);

        return redirect()->route('kasir.pesanan.index')
            ->with('success', 'Pesanan ' . $pesanan->nomor_pesanan . ' berhasil divalidasi dan masuk antrian produksi.');
    }

    /**
     * Reject payment
     */
    public function tolak(Request $request, $id)
    {
        $request->validate([
            'alasan_penolakan' => 'nullable|string|max:500',
        ]);

        $alasanPenolakan = trim((string) $request->input('alasan_penolakan', ''));
        if ($alasanPenolakan === '') {
            $alasanPenolakan = null;
        }

        $pesanan = Pesanan::pending()->findOrFail($id);

        $pesanan->update([
            'status' => 'ditolak',
            'alasan_penolakan' => $alasanPenolakan,
            'dikonfirmasi_oleh' => auth()->id(),
            'dikonfirmasi_at' => now(),
        ]);

        return redirect()->route('kasir.pesanan.index')
            ->with('success', 'Pesanan ' . $pesanan->nomor_pesanan . ' ditolak.');
    }

    public function batalkan(Request $request, $id)
    {
        $pesanan = Pesanan::with('transaksi')->findOrFail($id);
        $paymentStatus = $pesanan->transaksi->pembayaran_status ?? $pesanan->pembayaran_status;

        if (!in_array($pesanan->status, ['pending', 'dalam_antrian']) || $paymentStatus === 'paid') {
            return redirect()->back()->with('error', 'Hanya pesanan yang belum dibayar yang dapat dibatalkan.');
        }

        $pesanan->update([
            'status' => 'dibatalkan',
            'dikonfirmasi_oleh' => auth()->id(),
            'dikonfirmasi_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Pesanan ' . $pesanan->nomor_pesanan . ' telah dibatalkan.');
    }

    /**
     * Display history of completed orders for kasir
     */
    public function riwayat()
    {
        $pesanans = Pesanan::where('status', 'selesai')
            ->with(['produk', 'user', 'operator'])
            ->latest('selesai_produksi_at')
            ->paginate(20);

        return view('kasir.riwayat', compact('pesanans'));
    }

    /**
     * Show form for walk-in order
     */
    public function create()
    {
        $produks = Produk::aktif()->with('kategori')->get();
        $produkOptions = $produks->map(function ($produk) {
            return [
                'id' => $produk->id,
                'nama' => $produk->nama,
                'slug' => $produk->slug,
                'harga_satuan' => $produk->harga_satuan,
                'kategori_id' => $produk->kategori_id,
                'min_order' => $produk->min_order,
                'is_finishing' => (bool) $produk->is_finishing,
                'is_cutting' => (bool) $produk->is_cutting,
                'unit_label' => $produk->unit_label,
            ];
        })->values();
        $kategoris = KategoriProduk::aktif()->orderBy('nama')->get();
        $walkInCart = session()->get($this->walkInCartKey, []);
        $walkInTotal = collect($walkInCart)->sum('total_harga');

        return view('kasir.pesanan.create', compact('produks', 'produkOptions', 'kategoris', 'walkInCart', 'walkInTotal'));
    }

    public function cart()
    {
        $cart = session()->get($this->walkInCartKey, []);
        $total = collect($cart)->sum('total_harga');

        return view('kasir.cart.index', compact('cart', 'total'));
    }

    public function checkout()
    {
        return redirect()->route('kasir.cart.index');
    }

    /**
     * Add item to walk-in cart
     */
    public function addItem(Request $request)
    {
        $request->validate([
            'produk_id' => 'required|exists:produk,id',
            'jumlah' => 'required|integer|min:1',
            'file_desain' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'catatan' => 'nullable|string|max:1000',
            'finishing' => 'nullable',
            'opsi_potong' => 'nullable|string',
        ]);

        $produk = Produk::findOrFail($request->produk_id);
        $minOrder = max((int) $produk->min_order, 1);

        if ((int) $request->jumlah < $minOrder) {
            return redirect()->back()
                ->withErrors(['jumlah' => 'Minimal order untuk produk ini adalah ' . $minOrder . ' ' . $produk->unit_label . '.'])
                ->withInput();
        }

        [$ukuran, $jenis] = $this->pricingService->resolveSpecs(
            $produk,
            $request->ukuran_kertas_id,
            $request->jenis_kertas_id
        );

        $finishingInput = $produk->is_finishing
            ? $this->pricingService->normalizeFinishing($request->input('finishing'))
            : [];
        $finishing = $this->pricingService->finishingLabel($finishingInput);
        $opsiPotong = $produk->is_cutting && $request->filled('opsi_potong')
            ? $request->opsi_potong
            : null;

        $hargaSatuan = $this->pricingService->calculate(
            $produk,
            $ukuran->nama,
            $jenis->nama,
            $finishingInput,
            $opsiPotong,
            $request->jumlah
        );

        $totalHarga = $hargaSatuan * $request->jumlah;
        $estimasiWaktu = ceil($produk->estimasi_waktu_per_unit * $request->jumlah);
        $fileDesainTemp = $request->file('file_desain')->store('desain_temp_walkin', 'public');
        $itemId = uniqid('wlk_', true);

        $walkInCart = session()->get($this->walkInCartKey, []);
        $walkInCart[$itemId] = [
            'id' => $itemId,
            'produk_id' => $produk->id,
            'produk_nama' => $produk->nama,
            'gambar' => $produk->gambar,
            'ukuran_kertas_id' => $ukuran->id,
            'ukuran_nama' => $ukuran->nama,
            'jenis_kertas_id' => $jenis->id,
            'jenis_nama' => $jenis->nama,
            'jumlah' => $request->jumlah,
            'unit_label' => $produk->unit_label,
            'file_desain_temp' => $fileDesainTemp,
            'catatan' => $request->catatan,
            'harga_satuan' => $hargaSatuan,
            'total_harga' => $totalHarga,
            'estimasi_waktu' => $estimasiWaktu,
            'finishing' => $finishing,
            'opsi_potong' => $opsiPotong,
        ];

        session()->put($this->walkInCartKey, $walkInCart);

        return redirect()->route('kasir.cart.index')->with('success', 'Item berhasil ditambahkan ke keranjang kasir.');
    }

    /**
     * Remove item from walk-in cart
     */
    public function removeItem($id)
    {
        $walkInCart = session()->get($this->walkInCartKey, []);

        if (isset($walkInCart[$id])) {
            if (!empty($walkInCart[$id]['file_desain_temp'])) {
                Storage::disk('public')->delete($walkInCart[$id]['file_desain_temp']);
            }
            unset($walkInCart[$id]);
            session()->put($this->walkInCartKey, $walkInCart);
        }

        return back()->with('success', 'Item berhasil dihapus dari daftar walk-in.');
    }

    /**
     * Clear all walk-in items
     */
    public function clearItems()
    {
        $walkInCart = session()->get($this->walkInCartKey, []);
        foreach ($walkInCart as $item) {
            if (!empty($item['file_desain_temp'])) {
                Storage::disk('public')->delete($item['file_desain_temp']);
            }
        }

        session()->forget($this->walkInCartKey);

        return back()->with('success', 'Daftar item walk-in berhasil dikosongkan.');
    }

    public function hitungHarga(Request $request)
    {
        $produk = Produk::with('kategori')->find($request->produk_id);

        [$ukuran, $jenis] = $produk
            ? $this->pricingService->resolveSpecs($produk, $request->ukuran_kertas_id, $request->jenis_kertas_id)
            : [null, null];

        if (!$produk || !$ukuran || !$jenis) {
            return response()->json(['error' => 'Data tidak lengkap'], 422);
        }

        $jumlah = max((int) $request->input('jumlah', 1), 1);
        $minOrder = max((int) $produk->min_order, 1);

        if ($jumlah < $minOrder) {
            return response()->json([
                'error' => 'Minimal order untuk produk ini adalah ' . $minOrder . ' ' . $produk->unit_label . '.',
            ], 422);
        }

        $finishingInput = $produk->is_finishing
            ? $this->pricingService->normalizeFinishing($request->input('finishing'))
            : [];
        $opsiPotong = $produk->is_cutting && $request->filled('opsi_potong')
            ? $request->opsi_potong
            : null;

        $hargaSatuan = $this->pricingService->calculate(
            $produk,
            $ukuran->nama,
            $jenis->nama,
            $finishingInput,
            $opsiPotong,
            $jumlah
        );
        $totalHarga = $hargaSatuan * $jumlah;
        $estimasiWaktu = ceil($produk->estimasi_waktu_per_unit * $jumlah);

        return response()->json([
            'harga_satuan' => $hargaSatuan,
            'harga_satuan_format' => 'Rp ' . number_format($hargaSatuan, 0, ',', '.'),
            'total_harga' => $totalHarga,
            'total_harga_format' => 'Rp ' . number_format($totalHarga, 0, ',', '.'),
            'estimasi_waktu' => $estimasiWaktu,
        ]);
    }

    /**
     * Store walk-in order
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_pelanggan' => 'required|string|max:255',
            'email_pelanggan' => 'nullable|email',
            'telepon_pelanggan' => 'required|string|max:15',
            'metode_pembayaran' => 'required|in:cash,midtrans',
        ]);

        $walkInCart = session()->get($this->walkInCartKey, []);
        if (empty($walkInCart)) {
            return redirect()->route('kasir.pesanan.create')->with('error', 'Keranjang pesanan langsung masih kosong.');
        }

        foreach ($walkInCart as $item) {
            if (empty($item['file_desain_temp'])) {
                return redirect()->route('kasir.pesanan.create')
                    ->with('error', 'Setiap pesanan kasir wajib menyertakan file desain sebelum dilanjutkan.');
            }
        }

        // Find or create customer
        $user = null;
        if ($request->filled('email_pelanggan')) {
            $user = User::where('email', $request->email_pelanggan)->first();
        }

        if (!$user) {
            $user = User::where('telepon', $request->telepon_pelanggan)->first();
        }

        if (!$user) {
            $email = $request->email_pelanggan ?: ('walkin+' . preg_replace('/\D+/', '', (string) $request->telepon_pelanggan) . '@artpedia.local');
            if (User::where('email', $email)->exists()) {
                $user = User::where('email', $email)->first();
            } else {
                $user = User::create([
                    'name' => $request->nama_pelanggan,
                    'email' => $email,
                    'telepon' => $request->telepon_pelanggan,
                    'password' => Hash::make(Str::random(12)),
                    'role' => 'pelanggan',
                ]);
            }
        }

        $totalHarga = collect($walkInCart)->sum('total_harga');
        $isCash = $request->metode_pembayaran === 'cash';

        [$transaksi, $firstPesananId] = DB::transaction(function () use ($user, $totalHarga, $isCash, $walkInCart) {
            $transaksi = \App\Models\Transaksi::create([
                'nomor_transaksi' => \App\Models\Transaksi::generateNomorTransaksi(),
                'user_id' => $user->id,
                'total_harga' => $totalHarga,
                'bukti_pembayaran' => $isCash ? 'Pesanan Langsung' : 'Midtrans Kasir',
                'status' => $isCash ? 'valid' : 'pending',
                'pembayaran_status' => $isCash ? 'paid' : 'pending',
            ]);

            $firstPesananId = null;
            $nomorPesananGroup = Pesanan::generateNomorPesanan();

            foreach ($walkInCart as $item) {
                $finalPath = str_replace('desain_temp_walkin/', 'desain/', $item['file_desain_temp']);
                Storage::disk('public')->move($item['file_desain_temp'], $finalPath);

                $pesanan = Pesanan::create([
                    'nomor_pesanan' => $nomorPesananGroup,
                    'user_id' => $user->id,
                    'transaksi_id' => $transaksi->id,
                    'produk_id' => $item['produk_id'],
                    'ukuran_kertas_id' => $item['ukuran_kertas_id'],
                    'jenis_kertas_id' => $item['jenis_kertas_id'],
                    'jumlah' => $item['jumlah'],
                    'file_desain' => $finalPath,
                    'catatan' => $item['catatan'],
                    'harga_satuan' => $item['harga_satuan'],
                    'total_harga' => $item['total_harga'],
                    'bukti_pembayaran' => $isCash ? 'Pesanan Langsung' : 'Midtrans Kasir',
                    'estimasi_waktu' => $item['estimasi_waktu'],
                    'finishing' => $item['finishing'] ?? 'Tidak Pakai',
                    'opsi_potong' => !empty($item['opsi_potong']) ? $item['opsi_potong'] : null,
                    'status' => $isCash ? 'dalam_antrian' : 'pending',
                    'pembayaran_status' => $isCash ? 'paid' : 'pending',
                    'dikonfirmasi_oleh' => auth()->id(),
                    'dikonfirmasi_at' => $isCash ? now() : null,
                ]);

                $firstPesananId ??= $pesanan->id;
            }

            return [$transaksi, $firstPesananId];
        });

        if (!$isCash) {
            try {
                $snapToken = $this->midtransService->getSnapToken($transaksi);
                $transaksi->update(['snap_token' => $snapToken]);
            } catch (\Exception $e) {
                session()->flash('error', 'Pesanan tersimpan, tetapi token pembayaran Midtrans belum bisa dibuat: ' . $e->getMessage());
            }
        }

        session()->forget($this->walkInCartKey);

        if ($isCash) {
            return redirect()->route('kasir.pesanan.show', ['id' => $firstPesananId, 'show_created' => 1])
                ->with('success', count($walkInCart) . ' item pesanan cash berhasil dibuat dan masuk antrian.');
        }

        return redirect()->route('kasir.pesanan.show', $firstPesananId)
            ->with('success', count($walkInCart) . ' item pesanan berhasil dibuat. Silakan lanjutkan pembayaran.');
    }

    /**
     * Display production queue for cashier monitoring
     */
    public function antrian()
    {
        $antrian = Pesanan::dalamAntrian()
            ->with(['produk', 'ukuranKertas', 'jenisKertas', 'user'])
            ->paginate(20);

        $sedangDiproses = Pesanan::diproses()
            ->with(['produk', 'user', 'operator'])
            ->get();

        return view('kasir.antrian', compact('antrian', 'sedangDiproses'));
    }
}
