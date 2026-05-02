<?php

namespace App\Http\Controllers;

use App\Models\Pesanan;
use Illuminate\Http\Request;

class ProduksiController extends Controller
{
    /**
     * Display operator dashboard
     */
    public function dashboard()
    {
        $dalamAntrianCount = Pesanan::where('status', 'dalam_antrian')->count();
        $sedangDiprosesCount = Pesanan::where('status', 'diproses')
            ->where('diproses_oleh', auth()->id())
            ->count();
        $selesaiHariIni = Pesanan::where('diproses_oleh', auth()->id())
            ->whereDate('selesai_produksi_at', today())
            ->count();

        // Get current processing order
        $sedangDiproses = Pesanan::where('status', 'diproses')
            ->where('diproses_oleh', auth()->id())
            ->with(['produk', 'ukuranKertas', 'jenisKertas', 'user'])
            ->first();

        // Get next in queue (SJF)
        $antrianBerikutnya = Pesanan::dalamAntrian()
            ->with(['produk', 'user'])
            ->take(5)
            ->get();

        return view('produksi.dashboard', compact(
            'dalamAntrianCount',
            'sedangDiprosesCount',
            'selesaiHariIni',
            'sedangDiproses',
            'antrianBerikutnya'
        ));
    }

    /**
     * Display production queue (SJF order)
     */
    public function antrian()
    {
        [$antrian, $sedangDiproses] = $this->getAntrianData();

        return view('produksi.antrian', compact('antrian', 'sedangDiproses'));
    }

    public function realtimeAntrian()
    {
        [$antrian, $sedangDiproses] = $this->getAntrianData();

        return view('produksi._antrian_list', compact('antrian', 'sedangDiproses'));
    }

    private function getAntrianData()
    {
        $antrian = Pesanan::dalamAntrian()
            ->with(['produk', 'ukuranKertas', 'jenisKertas', 'user', 'kasir'])
            ->paginate(20)
            ->withQueryString();

        $sedangDiproses = Pesanan::diproses()
            ->with(['produk', 'user', 'operator'])
            ->get();

        return [$antrian, $sedangDiproses];
    }

    /**
     * Display order detail
     */
    public function show($id)
    {
        $pesanan = Pesanan::with(['produk', 'ukuranKertas', 'jenisKertas', 'user', 'kasir', 'operator'])
            ->findOrFail($id);

        $sedangDiprosesCount = Pesanan::where('status', 'diproses')
            ->where('diproses_oleh', auth()->id())
            ->count();

        return view('produksi.show', compact('pesanan', 'sedangDiprosesCount'));
    }

    /**
     * Download design file
     */
    public function download($id)
    {
        $pesanan = Pesanan::findOrFail($id);

        if ($pesanan->file_desain === 'NANTI_DIKIRIM') {
            return back()->with('error', 'Foto produk belum diunggah. Pelanggan akan mengirim menyusul.');
        }

        $filePath = storage_path('app/public/' . $pesanan->file_desain);

        if (!file_exists($filePath)) {
            return back()->with('error', 'File tidak ditemukan.');
        }

        return response()->download($filePath, $pesanan->nomor_pesanan . '_desain.' . pathinfo($filePath, PATHINFO_EXTENSION));
    }

    /**
     * Start processing order
     */
    public function mulai($id)
    {
        $pesanan = Pesanan::where('status', 'dalam_antrian')->findOrFail($id);

        $pesanan->update([
            'status' => 'diproses',
            'diproses_oleh' => auth()->id(),
            'mulai_produksi_at' => now(),
        ]);

        return redirect()->route('produksi.antrian')
            ->with('success', 'Mulai memproses pesanan ' . $pesanan->nomor_pesanan);
    }

    /**
     * Mark order as completed
     */
    public function selesai($id)
    {
        $pesanan = Pesanan::where('status', 'diproses')
            ->where('diproses_oleh', auth()->id())
            ->findOrFail($id);

        $pesanan->update([
            'status' => 'selesai',
            'selesai_produksi_at' => now(),
        ]);

        return redirect()->route('produksi.antrian')
            ->with('success', 'Pesanan ' . $pesanan->nomor_pesanan . ' selesai diproduksi!');
    }

    /**
     * Display completed orders
     */
    public function selesaiList()
    {
        $pesanans = Pesanan::where('status', 'selesai')
            ->with(['produk', 'user', 'operator'])
            ->latest('selesai_produksi_at')
            ->paginate(20);

        return view('produksi.selesai', compact('pesanans'));
    }
}
