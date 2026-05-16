<?php

namespace App\Http\Controllers;

use App\Models\Pesanan;
use App\Models\Produk;
use App\Models\KategoriProduk;
use App\Models\UkuranKertas;
use App\Models\JenisKertas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Services\MidtransService;
use App\Services\PricingService;

class PesananController extends Controller
{
    protected $midtransService;
    protected $pricingService;

    public function __construct(MidtransService $midtransService, PricingService $pricingService)
    {
        $this->midtransService = $midtransService;
        $this->pricingService = $pricingService;
    }
    /**
     * Display list of customer's orders
     */
    public function index()
    {
        $pesanans = Pesanan::where('user_id', auth()->id())
            ->with(['produk', 'ukuranKertas', 'jenisKertas'])
            ->latest()
            ->paginate(10);

        return view('pelanggan.pesanan.index', compact('pesanans'));
    }

    /**
     * Show form for creating new order
     */
    public function create(Request $request)
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

        // Pre-select product if coming from katalog
        $selectedProduk = null;
        if ($request->filled('produk')) {
            $selectedProduk = Produk::where('slug', $request->produk)->aktif()->first();
        }

        return view('pelanggan.pesanan.create', compact('produks', 'produkOptions', 'kategoris', 'selectedProduk'));
    }

    /**
     * Store new order
     */
    public function store(Request $request)
    {
        $request->validate([
            'produk_id' => 'required|exists:produk,id',
            'ukuran_kertas_id' => 'nullable|exists:ukuran_kertas,id',
            'jenis_kertas_id' => 'nullable|exists:jenis_kertas,id',
            'jumlah' => 'required|integer|min:1',
            'file_desain' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'bukti_pembayaran' => 'nullable|file|mimes:jpg,jpeg,png|max:5120',
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

        // Upload files
        $fileDesain = $request->file('file_desain')->store('desain', 'public');
        $buktiPembayaran = $request->hasFile('bukti_pembayaran') 
            ? $request->file('bukti_pembayaran')->store('bukti_bayar', 'public')
            : 'Midtrans';

        // Prepare Finishing string
        $finishing = $this->pricingService->finishingLabel($finishingInput);

        // Create order
        $pesanan = Pesanan::create([
            'nomor_pesanan' => Pesanan::generateNomorPesanan(),
            'user_id' => auth()->id(),
            'produk_id' => $produk->id,
            'ukuran_kertas_id' => $ukuran->id,
            'jenis_kertas_id' => $jenis->id,
            'jumlah' => $request->jumlah,
            'file_desain' => $fileDesain,
            'catatan' => $request->catatan,
            'harga_satuan' => $hargaSatuan,
            'total_harga' => $totalHarga,
            'bukti_pembayaran' => $buktiPembayaran,
            'estimasi_waktu' => $estimasiWaktu,
            'finishing' => $finishing,
            'opsi_potong' => $opsiPotong,
            'status' => 'pending',
            'pembayaran_status' => 'pending',
        ]);

        // Generate Midtrans Snap Token
        try {
            $snapToken = $this->midtransService->getSnapToken($pesanan);
            $pesanan->update(['snap_token' => $snapToken]);
        } catch (\Exception $e) {
            // Log error or handle failure
        }

        return redirect()->route('pelanggan.pesanan.show', $pesanan->id)
            ->with('success', 'Pesanan berhasil dibuat! Silakan lakukan pembayaran.');
    }

    /**
     * Display order detail
     */
    public function show($id)
    {
        $pesanan = Pesanan::where('user_id', auth()->id())
            ->with(['produk', 'ukuranKertas', 'jenisKertas', 'kasir', 'operator', 'transaksi'])
            ->find($id);

        if (!$pesanan) {
            return redirect()->route('pelanggan.pesanan.index')
                ->with('error', 'Pesanan tidak ditemukan atau sudah dibatalkan.');
        }

        $paymentModel = $pesanan->transaksi ?: $pesanan;

        if ($paymentModel->pembayaran_status === 'pending') {
            try {
                $snapToken = $this->midtransService->getSnapToken($paymentModel);
                $paymentModel->update(['snap_token' => $snapToken]);

                $paymentModel->setAttribute('snap_token', $snapToken);
            } catch (\Exception $e) {
                session()->flash('error', 'Pesanan sudah tersimpan, tetapi kode pembayaran Midtrans belum bisa dibuat: ' . $e->getMessage());
            }
        }

        $pesananItems = Pesanan::where('user_id', auth()->id())
            ->where('nomor_pesanan', $pesanan->nomor_pesanan)
            ->with(['produk.kategori', 'ukuranKertas', 'jenisKertas'])
            ->orderBy('id')
            ->get();

        $groupTotal = (int) ($pesanan->transaksi->total_harga ?? $pesananItems->sum('total_harga'));

        return view('pelanggan.pesanan.show', compact('pesanan', 'pesananItems', 'groupTotal'));
    }

    public function paymentStatus($id)
    {
        $pesanan = Pesanan::where('user_id', auth()->id())
            ->with('transaksi')
            ->findOrFail($id);

        $orderId = $this->resolveOrderId($requestOrderId = request()->query('order_id'));
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
        $redirectUrl = $paymentStatus === 'paid'
            ? route('pelanggan.pesanan.show', ['id' => $pesanan->id, 'show_feedback' => 1])
            : route('pelanggan.pesanan.show', $pesanan->id);

        return response()->json([
            'payment_status' => $paymentStatus,
            'status' => $pesanan->status,
            'is_paid' => $paymentStatus === 'paid',
            'redirect_url' => $redirectUrl,
        ]);
    }

    private function resolveOrderId(?string $orderId): ?string
    {
        return is_string($orderId) && trim($orderId) !== '' ? trim($orderId) : null;
    }

    /**
     * Stream design file for customer preview
     */
    public function viewFileDesain($id)
    {
        $pesanan = Pesanan::where('user_id', auth()->id())->findOrFail($id);
        $path = $pesanan->file_desain;

        if (!$path || !Storage::disk('public')->exists($path)) {
            return redirect()->back()->with('error', 'File desain tidak ditemukan.');
        }

        return response()->file(storage_path('app/public/' . $path));
    }

    /**
     * Stream payment proof file for customer preview
     */
    public function viewBuktiPembayaran($id)
    {
        $pesanan = Pesanan::where('user_id', auth()->id())->findOrFail($id);
        $path = $pesanan->bukti_pembayaran;

        if (!$path || $path === 'Pesanan Langsung') {
            return redirect()->back()->with('error', 'Pesanan ini tidak memiliki bukti transfer.');
        }

        if (!Storage::disk('public')->exists($path)) {
            return redirect()->back()->with('error', 'Bukti pembayaran tidak ditemukan.');
        }

        return response()->file(storage_path('app/public/' . $path));
    }

    /**
     * Calculate price via AJAX
     */
    public function hitungHarga(Request $request)
    {
        $produk = Produk::find($request->produk_id);
        
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
     * Cancel/Delete order (for customer)
     */
    public function destroy($id)
    {
        $pesanan = Pesanan::where('user_id', auth()->id())->findOrFail($id);

        $pesananItems = Pesanan::where('user_id', auth()->id())
            ->where('nomor_pesanan', $pesanan->nomor_pesanan)
            ->get();

        if ($pesananItems->contains(fn ($item) => $item->status !== 'pending')) {
            return redirect()->back()->with('error', 'Hanya pesanan berstatus "Menunggu Validasi" yang dapat dibatalkan.');
        }

        $fileDesainPaths = $pesananItems->pluck('file_desain')->filter()->unique();
        foreach ($fileDesainPaths as $path) {
            Storage::disk('public')->delete($path);
        }

        $buktiPaths = $pesananItems->pluck('bukti_pembayaran')
            ->filter(fn ($path) => !empty($path) && !in_array($path, ['Pesanan Langsung', 'Midtrans'], true))
            ->unique();
        foreach ($buktiPaths as $path) {
            Storage::disk('public')->delete($path);
        }

        Pesanan::where('user_id', auth()->id())
            ->where('nomor_pesanan', $pesanan->nomor_pesanan)
            ->delete();

        return redirect()->route('pelanggan.pesanan.index')
            ->with('success', 'Pesanan ' . $pesanan->nomor_pesanan . ' berhasil dibatalkan.');
    }
}
