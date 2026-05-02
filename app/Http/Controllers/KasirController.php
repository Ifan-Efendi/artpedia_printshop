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
use Illuminate\Support\Str;

class KasirController extends Controller
{
    private string $walkInCartKey = 'kasir_walkin_cart';

    /**
     * Display kasir dashboard
     */
    public function dashboard()
    {
        $pendingCount = Pesanan::pending()->count();
        $todayValidated = Pesanan::whereIn('status', ['dalam_antrian', 'diproses', 'selesai'])->count();
        $totalValidated = Pesanan::where('status', 'dibatalkan')->count();

        $recentPesanan = Pesanan::pending()
            ->with(['user', 'produk'])
            ->latest()
            ->take(5)
            ->get();

        return view('kasir.dashboard', compact('pendingCount', 'todayValidated', 'totalValidated', 'recentPesanan'));
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
            $query->pending();
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
        $pesanan = Pesanan::with(['user', 'produk', 'ukuranKertas', 'jenisKertas'])
            ->findOrFail($id);

        return view('kasir.pesanan.show', compact('pesanan'));
    }

    /**
     * Stream design file for cashier preview
     */
    public function viewFileDesain($id)
    {
        $pesanan = Pesanan::findOrFail($id);
        $path = $pesanan->file_desain;

        if ($path === 'NANTI_DIKIRIM') {
            return redirect()->back()->with('error', 'Foto produk belum diunggah. Pelanggan akan mengirim menyusul.');
        }

        if (!$path || !Storage::disk('public')->exists($path)) {
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

        if (!$path || $path === 'Pesanan Langsung') {
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
        $pesanan = Pesanan::findOrFail($id);
        if (!in_array($pesanan->status, ['pending', 'dalam_antrian'])) {
            return redirect()->back()->with('error', 'Hanya pesanan pending atau dalam antrian yang dapat dibatalkan.');
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
        $cart = session()->get($this->walkInCartKey, []);
        if (empty($cart)) {
            return redirect()->route('kasir.pesanan.create')->with('error', 'Keranjang pesanan langsung masih kosong.');
        }

        $total = collect($cart)->sum('total_harga');

        return view('kasir.cart.checkout', compact('cart', 'total'));
    }

    /**
     * Add item to walk-in cart
     */
    public function addItem(Request $request)
    {
        $request->validate([
            'produk_id' => 'required|exists:produk,id',
            'jumlah' => 'required|integer|min:1',
            'file_desain' => 'required_without:foto_produk_nanti|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'foto_produk_nanti' => 'nullable|boolean',
            'catatan' => 'nullable|string|max:1000',
            'finishing' => 'nullable',
            'opsi_potong' => 'nullable|string',
        ]);

        $produk = Produk::findOrFail($request->produk_id);
        
        // Use provided IDs or default to the first ones if missing (since they are not in the form)
        $ukuranId = $request->input('ukuran_kertas_id') ?? UkuranKertas::first()->id;
        $jenisId = $request->input('jenis_kertas_id') ?? JenisKertas::first()->id;

        $ukuran = UkuranKertas::findOrFail($ukuranId);
        $jenis = JenisKertas::findOrFail($jenisId);

        $finishingInput = $this->normalizeFinishing($request->input('finishing'));
        $finishing = !empty($finishingInput) ? implode(', ', $finishingInput) : 'Tidak Pakai';

        $hargaSatuan = $this->calculateCustomPrice(
            $produk->kategori->slug,
            $ukuran->nama,
            $jenis->nama,
            $finishingInput,
            $request->opsi_potong,
            $request->jumlah
        );

        $totalHarga = $hargaSatuan * $request->jumlah;
        $estimasiWaktu = ceil($produk->estimasi_waktu_per_unit * $request->jumlah);
        $fotoProdukNanti = $request->boolean('foto_produk_nanti');
        $fileDesainTemp = $request->hasFile('file_desain')
            ? $request->file('file_desain')->store('desain_temp_walkin', 'public')
            : null;
        $itemId = uniqid('wlk_', true);

        $walkInCart = session()->get($this->walkInCartKey, []);
        $walkInCart[$itemId] = [
            'id' => $itemId,
            'produk_id' => $produk->id,
            'produk_nama' => $produk->nama,
            'ukuran_kertas_id' => $ukuran->id,
            'ukuran_nama' => $ukuran->nama,
            'jenis_kertas_id' => $jenis->id,
            'jenis_nama' => $jenis->nama,
            'jumlah' => $request->jumlah,
            'file_desain_temp' => $fileDesainTemp,
            'foto_produk_nanti' => $fotoProdukNanti,
            'catatan' => $request->catatan,
            'harga_satuan' => $hargaSatuan,
            'total_harga' => $totalHarga,
            'estimasi_waktu' => $estimasiWaktu,
            'finishing' => $finishing,
            'opsi_potong' => $request->opsi_potong ?? 'Potong Kotak',
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

    /**
     * Store walk-in order
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_pelanggan' => 'required|string|max:255',
            'email_pelanggan' => 'nullable|email',
            'telepon_pelanggan' => 'required|string|max:15',
        ]);

        $walkInCart = session()->get($this->walkInCartKey, []);
        if (empty($walkInCart)) {
            return redirect()->route('kasir.pesanan.create')->with('error', 'Keranjang pesanan langsung masih kosong.');
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
            $email = $request->email_pelanggan ?: $request->telepon_pelanggan . '@artpedia.com';
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

        foreach ($walkInCart as $item) {
            if (!empty($item['file_desain_temp'])) {
                $finalPath = str_replace('desain_temp_walkin/', 'desain/', $item['file_desain_temp']);
                Storage::disk('public')->move($item['file_desain_temp'], $finalPath);
            } else {
                $finalPath = 'NANTI_DIKIRIM';
            }

            Pesanan::create([
                'nomor_pesanan' => Pesanan::generateNomorPesanan(),
                'user_id' => $user->id,
                'produk_id' => $item['produk_id'],
                'ukuran_kertas_id' => $item['ukuran_kertas_id'],
                'jenis_kertas_id' => $item['jenis_kertas_id'],
                'jumlah' => $item['jumlah'],
                'file_desain' => $finalPath,
                'catatan' => $item['catatan'],
                'harga_satuan' => $item['harga_satuan'],
                'total_harga' => $item['total_harga'],
                'bukti_pembayaran' => 'Pesanan Langsung',
                'estimasi_waktu' => $item['estimasi_waktu'],
                'finishing' => $item['finishing'] ?? 'Tidak Pakai',
                'opsi_potong' => $item['opsi_potong'] ?? 'Potong Kotak',
                'status' => 'dalam_antrian',
                'dikonfirmasi_oleh' => auth()->id(),
                'dikonfirmasi_at' => now(),
            ]);
        }

        session()->forget($this->walkInCartKey);

        return redirect()->route('kasir.pesanan.index')
            ->with('success', count($walkInCart) . ' item pesanan walk-in berhasil dibuat dan masuk antrian.');
    }

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

        if ($finishing && is_array($finishing)) {
            foreach ($finishing as $f) {
                if ($f === 'Glossy' || $f === 'Doff') $price += 4000;
            }
        }
        return $price;
    }

    private function normalizeFinishing($finishing)
    {
        if (is_array($finishing)) {
            return array_values(array_filter($finishing));
        }

        if (is_string($finishing) && trim($finishing) !== '') {
            return [trim($finishing)];
        }

        return [];
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
