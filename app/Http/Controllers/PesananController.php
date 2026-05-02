<?php

namespace App\Http\Controllers;

use App\Models\Pesanan;
use App\Models\Produk;
use App\Models\KategoriProduk;
use App\Models\UkuranKertas;
use App\Models\JenisKertas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PesananController extends Controller
{
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
        // Get first available paper size and type as default
        $defaultUkuran = UkuranKertas::first()->id ?? null;
        $defaultJenis = JenisKertas::first()->id ?? null;

        // Merge defaults if not present in request
        $request->merge([
            'ukuran_kertas_id' => $request->ukuran_kertas_id ?? $defaultUkuran,
            'jenis_kertas_id' => $request->jenis_kertas_id ?? $defaultJenis,
        ]);

        $request->validate([
            'produk_id' => 'required|exists:produk,id',
            'ukuran_kertas_id' => 'required|exists:ukuran_kertas,id',
            'jenis_kertas_id' => 'required|exists:jenis_kertas,id',
            'jumlah' => 'required|integer|min:1',
            'file_desain' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'bukti_pembayaran' => 'required|file|mimes:jpg,jpeg,png|max:5120',
            'catatan' => 'nullable|string|max:1000',
            'finishing' => 'nullable|array',
            'opsi_potong' => 'nullable|string',
        ]);

        $produk = Produk::findOrFail($request->produk_id);
        $ukuran = UkuranKertas::findOrFail($request->ukuran_kertas_id);
        $jenis = JenisKertas::findOrFail($request->jenis_kertas_id);

        // Pricing Engine Sync with Section 8 rules
        $hargaSatuan = $this->calculateCustomPrice(
            $produk->kategori->slug, // Use Category Slug for logic
            $ukuran->nama,
            $jenis->nama,
            $request->finishing,
            $request->opsi_potong,
            $request->jumlah
        );

        $totalHarga = $hargaSatuan * $request->jumlah;
        $estimasiWaktu = ceil($produk->estimasi_waktu_per_unit * $request->jumlah);

        // Upload files
        $fileDesain = $request->file('file_desain')->store('desain', 'public');
        $buktiPembayaran = $request->file('bukti_pembayaran')->store('bukti_bayar', 'public');

        // Prepare Finishing string
        $finishing = $request->has('finishing') ? implode(', ', $request->finishing) : 'Tidak Pakai';

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
            'opsi_potong' => $request->opsi_potong ?? 'Potong Kotak',
            'status' => 'pending',
        ]);

        return redirect()->route('home')
            ->with('success', 'Pesanan berhasil dibuat! Nomor pesanan: ' . $pesanan->nomor_pesanan)
            ->with('feedback_prompt', true);
    }

    /**
     * Logic for custom pricing engine
     */
    private function calculateCustomPrice($slug, $ukuran, $bahan, $finishing, $potong, $jumlah = 1)
    {
        $price = 0;

        if ($slug === 'poster') {
            // Simplified size-based pricing
            $price = ($ukuran === 'A3' || $ukuran === 'Custom') ? 7000 : 5000;
        } elseif ($slug === 'sticker') {
            // Size-based pricing for both vinyl and chromo
            if (str_contains($bahan, 'Vinyl')) {
                // Vinyl with size-based pricing
                if ($ukuran === 'A3' || $ukuran === 'Custom') $price = 11000;
                elseif ($ukuran === 'A4') $price = 8000;
                elseif ($ukuran === 'A5') $price = 6000;
                elseif ($ukuran === 'A6') $price = 4000;
                else $price = 8000; // default
            } else {
                // Chromo with size-based pricing
                if ($ukuran === 'A3' || $ukuran === 'Custom') $price = 8000;
                elseif ($ukuran === 'A4') $price = 6000;
                elseif ($ukuran === 'A5') $price = 4000;
                elseif ($ukuran === 'A6') $price = 2000;
                else $price = 6000; // default
            }
            // Add cutting options
            if ($potong === 'Kiss Cut') $price += 4000;
            elseif ($potong === 'Die Cut') $price += 8000;
        } elseif ($slug === 'kartu-nama') {
            $price = 480; // Per pcs
            // For kartu nama, finishing is calculated differently
            if ($finishing && is_array($finishing)) {
                foreach ($finishing as $f) {
                    if ($f === 'Glossy' || $f === 'Doff') {
                        // Rp 4.000 per 25 pcs
                        $finishingCost = ceil($jumlah / 25) * 4000;
                        $price += ($finishingCost / $jumlah); // Distribute per pcs
                    }
                }
            }
            return $price; // Return early for kartu-nama
        } elseif ($slug === 'kartu-ucapan') {
            $price = 7000;
        } elseif ($slug === 'brosur') {
            // Size-based pricing for brosur
            if ($ukuran === 'A3' || $ukuran === 'Custom') $price = 5000;
            elseif ($ukuran === 'A4') $price = 4000;
            elseif ($ukuran === 'A5') $price = 3000;
            elseif ($ukuran === 'A6') $price = 2000;
            else $price = 5000; // default for unknown/custom
        }

        // Add Finishing (for non-kartu-nama products)
        if ($finishing && is_array($finishing)) {
            foreach ($finishing as $f) {
                if ($f === 'Glossy' || $f === 'Doff') $price += 4000;
            }
        }

        return $price;
    }

    /**
     * Display order detail
     */
    public function show($id)
    {
        $pesanan = Pesanan::where('user_id', auth()->id())
            ->with(['produk', 'ukuranKertas', 'jenisKertas', 'kasir', 'operator'])
            ->find($id);

        if (!$pesanan) {
            return redirect()->route('pelanggan.pesanan.index')
                ->with('error', 'Pesanan tidak ditemukan atau sudah dibatalkan.');
        }

        return view('pelanggan.pesanan.show', compact('pesanan'));
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
        
        // Get first available paper size and type as default
        $defaultUkuranId = UkuranKertas::first()->id ?? null;
        $defaultJenisId = JenisKertas::first()->id ?? null;

        $ukuranId = $request->ukuran_kertas_id ?? $defaultUkuranId;
        $jenisId = $request->jenis_kertas_id ?? $defaultJenisId;

        $ukuran = UkuranKertas::find($ukuranId);
        $jenis = JenisKertas::find($jenisId);

        if (!$produk || !$ukuran || !$jenis) {
            return response()->json(['error' => 'Data tidak lengkap'], 422);
        }

        $hargaSatuan = $this->calculateCustomPrice(
            $produk->kategori->slug, // Use Category Slug for logic
            $ukuran->nama,
            $jenis->nama,
            $request->finishing,
            $request->opsi_potong
        );

        $totalHarga = $hargaSatuan * $request->jumlah;
        $estimasiWaktu = ceil($produk->estimasi_waktu_per_unit * $request->jumlah);

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

        if ($pesanan->status !== 'pending') {
            return redirect()->back()->with('error', 'Hanya pesanan berstatus "Menunggu Validasi" yang dapat dibatalkan.');
        }

        // Delete associated files
        if ($pesanan->file_desain) {
            Storage::disk('public')->delete($pesanan->file_desain);
        }
        if ($pesanan->bukti_pembayaran && $pesanan->bukti_pembayaran !== 'Pesanan Langsung') {
            Storage::disk('public')->delete($pesanan->bukti_pembayaran);
        }

        $pesanan->delete();

        return redirect()->route('pelanggan.pesanan.index')
            ->with('success', 'Pesanan ' . $pesanan->nomor_pesanan . ' berhasil dibatalkan.');
    }
}
