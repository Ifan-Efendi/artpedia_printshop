<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Produk;
use App\Models\UkuranKertas;
use App\Models\JenisKertas;
use App\Models\Transaksi;
use App\Models\Pesanan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Services\MidtransService;
use App\Services\PricingService;

class CartController extends Controller
{
    protected $midtransService;
    protected $pricingService;

    public function __construct(MidtransService $midtransService, PricingService $pricingService)
    {
        $this->midtransService = $midtransService;
        $this->pricingService = $pricingService;
    }
    public function index()
    {
        $cart = session()->get('cart', []);
        $total = 0;
        foreach ($cart as $item) {
            $total += $item['total_harga'];
        }

        return view('pelanggan.cart.index', compact('cart', 'total'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'produk_id' => 'required|exists:produk,id',
            'ukuran_kertas_id' => 'nullable|exists:ukuran_kertas,id',
            'jenis_kertas_id' => 'nullable|exists:jenis_kertas,id',
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
        $finishingLabel = $this->pricingService->finishingLabel($finishingInput);
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

        $filePath = $request->file('file_desain')->store('desain_temp', 'public');

        $cart = session()->get('cart', []);
        $cartId = uniqid();

        $cart[$cartId] = [
            'id' => $cartId,
            'produk_id' => $produk->id,
            'produk_nama' => $produk->nama,
            'gambar' => $produk->gambar,
            'ukuran_id' => $ukuran->id,
            'ukuran_nama' => $ukuran->nama,
            'jenis_id' => $jenis->id,
            'jenis_nama' => $jenis->nama,
            'jumlah' => $request->jumlah,
            'unit_label' => $produk->unit_label,
            'catatan' => $request->catatan,
            'harga_satuan' => $hargaSatuan,
            'total_harga' => $totalHarga,
            'estimasi_waktu' => $estimasiWaktu,
            'file_desain' => $filePath,
            'finishing' => $finishingLabel,
            'opsi_potong' => $opsiPotong,
        ];

        session()->put('cart', $cart);

        return redirect()->route('pelanggan.cart.index')->with('success', 'Produk berhasil ditambah ke keranjang!');
    }

    public function remove($id)
    {
        $cart = session()->get('cart', []);
        if (isset($cart[$id])) {
            Storage::disk('public')->delete($cart[$id]['file_desain']);
            unset($cart[$id]);
            session()->put('cart', $cart);
        }
        return back()->with('success', 'Produk dihapus dari keranjang.');
    }

    public function clear()
    {
        $cart = session()->get('cart', []);

        foreach ($cart as $item) {
            if (!empty($item['file_desain'])) {
                Storage::disk('public')->delete($item['file_desain']);
            }
        }

        session()->forget('cart');

        return back()->with('success', 'Keranjang berhasil dikosongkan.');
    }

    public function checkout()
    {
        return redirect()->route('pelanggan.cart.index');
    }

    private function getMinOrderCheckoutError(array $cart): ?string
    {
        foreach ($cart as $item) {
            $produk = Produk::find($item['produk_id'] ?? null);

            if (!$produk) {
                return 'Produk ' . ($item['produk_nama'] ?? 'di keranjang') . ' sudah tidak tersedia. Silakan hapus item tersebut dari keranjang.';
            }

            $jumlah = (int) ($item['jumlah'] ?? 0);
            $minOrder = max((int) $produk->min_order, 1);

            if ($jumlah < $minOrder) {
                return 'Minimal order untuk ' . $produk->nama . ' adalah ' . $minOrder . ' ' . $produk->unit_label . '.';
            }
        }

        return null;
    }

    public function processCheckout(Request $request)
    {
        $request->validate([
            'bukti_pembayaran' => 'nullable|image|max:5120',
        ]);

        $cart = session()->get('cart', []);
        if (empty($cart))
            return redirect()->route('katalog')->with('error', 'Keranjang kosong!');

        if ($checkoutError = $this->getMinOrderCheckoutError($cart)) {
            return redirect()->route('pelanggan.cart.index')
                ->withErrors(['checkout' => $checkoutError])
                ->withInput();
        }

        $totalHarga = 0;
        foreach ($cart as $item)
            $totalHarga += $item['total_harga'];

        $buktiPath = $request->hasFile('bukti_pembayaran') 
            ? $request->file('bukti_pembayaran')->store('bukti_bayar', 'public')
            : 'Midtrans';

        $paymentTokenReady = false;
        $firstPesananId = null;

        [$transaksi, $firstPesananId] = DB::transaction(function () use ($totalHarga, $buktiPath, $cart, &$firstPesananId) {
            $transaksi = Transaksi::create([
                'nomor_transaksi' => Transaksi::generateNomorTransaksi(),
                'user_id' => auth()->id(),
                'total_harga' => $totalHarga,
                'bukti_pembayaran' => $buktiPath,
                'status' => 'pending',
                'pembayaran_status' => 'pending',
            ]);

            $nomorPesananGroup = Pesanan::generateNomorPesanan();
            $firstPesananId = null;

            foreach ($cart as $item) {
                $finalPath = str_replace('desain_temp/', 'desain/', $item['file_desain']);
                if (Storage::disk('public')->exists($item['file_desain'])) {
                    Storage::disk('public')->move($item['file_desain'], $finalPath);
                }

                $pesanan = Pesanan::create([
                    'nomor_pesanan' => $nomorPesananGroup,
                    'user_id' => auth()->id(),
                    'transaksi_id' => $transaksi->id,
                    'produk_id' => $item['produk_id'],
                    'ukuran_kertas_id' => $item['ukuran_id'],
                    'jenis_kertas_id' => $item['jenis_id'],
                    'jumlah' => $item['jumlah'],
                    'file_desain' => $finalPath,
                    'catatan' => $item['catatan'],
                    'harga_satuan' => $item['harga_satuan'],
                    'total_harga' => $item['total_harga'],
                    'bukti_pembayaran' => $buktiPath,
                    'estimasi_waktu' => $item['estimasi_waktu'],
                    'finishing' => $item['finishing'] ?? 'Tidak Pakai',
                    'opsi_potong' => !empty($item['opsi_potong']) ? $item['opsi_potong'] : null,
                    'status' => 'pending',
                ]);
                $firstPesananId ??= $pesanan->id;
            }

            return [$transaksi, $firstPesananId];
        });

        try {
            if ($totalHarga <= 0) {
                throw new \Exception("Total harga harus lebih dari 0 untuk menggunakan Midtrans.");
            }
            $snapToken = $this->midtransService->getSnapToken($transaksi);
            $transaksi->update(['snap_token' => $snapToken]);
            $paymentTokenReady = true;
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal membuat token pembayaran: ' . $e->getMessage());
        }

        session()->forget('cart');

        $message = $paymentTokenReady
            ? 'Checkout berhasil! Silakan klik tombol Bayar Sekarang di bawah ini.'
            : 'Checkout berhasil dan pesanan sudah tersimpan. Token pembayaran Midtrans belum bisa dibuat.';

        return redirect()->route('pelanggan.pesanan.show', $firstPesananId)
            ->with('success', $message);
    }

}
