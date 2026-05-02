<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Produk;
use App\Models\UkuranKertas;
use App\Models\JenisKertas;
use App\Models\Transaksi;
use App\Models\Pesanan;
use Illuminate\Support\Facades\Storage;

class CartController extends Controller
{
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
            'catatan' => 'nullable|string|max:1000',
            'finishing' => 'nullable',
            'opsi_potong' => 'nullable|string',
        ]);

        $produk = Produk::findOrFail($request->produk_id);
        $ukuran = UkuranKertas::findOrFail($request->ukuran_kertas_id);
        $jenis = JenisKertas::findOrFail($request->jenis_kertas_id);

        $finishingInput = $this->normalizeFinishing($request->input('finishing'));
        $finishingLabel = !empty($finishingInput) ? implode(', ', $finishingInput) : 'Tidak Pakai';

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

        $filePath = $request->file('file_desain')->store('desain_temp', 'public');

        $cart = session()->get('cart', []);
        $cartId = uniqid();

        $cart[$cartId] = [
            'id' => $cartId,
            'produk_id' => $produk->id,
            'produk_nama' => $produk->nama,
            'ukuran_id' => $ukuran->id,
            'ukuran_nama' => $ukuran->nama,
            'jenis_id' => $jenis->id,
            'jenis_nama' => $jenis->nama,
            'jumlah' => $request->jumlah,
            'catatan' => $request->catatan,
            'harga_satuan' => $hargaSatuan,
            'total_harga' => $totalHarga,
            'estimasi_waktu' => $estimasiWaktu,
            'file_desain' => $filePath,
            'finishing' => $finishingLabel,
            'opsi_potong' => $request->opsi_potong ?? 'Potong Kotak',
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

    public function checkout()
    {
        $cart = session()->get('cart', []);
        if (empty($cart))
            return redirect()->route('katalog')->with('error', 'Keranjang kosong!');

        $total = 0;
        foreach ($cart as $item)
            $total += $item['total_harga'];

        return view('pelanggan.cart.checkout', compact('cart', 'total'));
    }

    public function processCheckout(Request $request)
    {
        $request->validate([
            'bukti_pembayaran' => 'required|image|max:5120',
        ]);

        $cart = session()->get('cart', []);
        if (empty($cart))
            return redirect()->route('katalog')->with('error', 'Keranjang kosong!');

        $totalHarga = 0;
        foreach ($cart as $item)
            $totalHarga += $item['total_harga'];

        $buktiPath = $request->file('bukti_pembayaran')->store('bukti_bayar', 'public');

        $transaksi = Transaksi::create([
            'nomor_transaksi' => Transaksi::generateNomorTransaksi(),
            'user_id' => auth()->id(),
            'total_harga' => $totalHarga,
            'bukti_pembayaran' => $buktiPath,
            'status' => 'pending',
        ]);

        foreach ($cart as $item) {
            // Move file from temp to final
            $finalPath = str_replace('desain_temp/', 'desain/', $item['file_desain']);
            Storage::disk('public')->move($item['file_desain'], $finalPath);

            Pesanan::create([
                'nomor_pesanan' => Pesanan::generateNomorPesanan(),
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
                'opsi_potong' => $item['opsi_potong'] ?? 'Potong Kotak',
                'status' => 'pending',
            ]);
        }

        session()->forget('cart');

        return redirect()->route('home')
            ->with('success', 'Checkout berhasil! Mohon menunggu validasi kasir.')
            ->with('feedback_prompt', true);
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

    private function calculateCustomPrice($slug, $ukuran, $bahan, $finishing, $potong, $jumlah = 1)
    {
        $price = 0;

        if ($slug === 'poster') {
            $price = ($ukuran === 'A3' || $ukuran === 'Custom') ? 7000 : 5000;
        } elseif ($slug === 'sticker') {
            if (str_contains($bahan, 'Vinyl')) {
                if ($ukuran === 'A3' || $ukuran === 'Custom') $price = 11000;
                elseif ($ukuran === 'A4') $price = 8000;
                elseif ($ukuran === 'A5') $price = 6000;
                elseif ($ukuran === 'A6') $price = 4000;
                else $price = 8000;
            } else {
                if ($ukuran === 'A3' || $ukuran === 'Custom') $price = 8000;
                elseif ($ukuran === 'A4') $price = 6000;
                elseif ($ukuran === 'A5') $price = 4000;
                elseif ($ukuran === 'A6') $price = 2000;
                else $price = 6000;
            }
            if ($potong === 'Kiss Cut') $price += 4000;
            elseif ($potong === 'Die Cut') $price += 8000;
        } elseif ($slug === 'kartu-nama') {
            $price = 480;
            if ($finishing && is_array($finishing)) {
                foreach ($finishing as $f) {
                    if ($f === 'Glossy' || $f === 'Doff') {
                        $finishingCost = ceil($jumlah / 25) * 4000;
                        $price += ($finishingCost / $jumlah);
                    }
                }
            }
            return $price;
        } elseif ($slug === 'kartu-ucapan') {
            $price = 7000;
        } elseif ($slug === 'brosur') {
            if ($ukuran === 'A3' || $ukuran === 'Custom') $price = 5000;
            elseif ($ukuran === 'A4') $price = 4000;
            elseif ($ukuran === 'A5') $price = 3000;
            elseif ($ukuran === 'A6') $price = 2000;
            else $price = 5000;
        }

        if ($finishing && is_array($finishing)) {
            foreach ($finishing as $f) {
                if ($f === 'Glossy' || $f === 'Doff') $price += 4000;
            }
        }

        return $price;
    }
}
