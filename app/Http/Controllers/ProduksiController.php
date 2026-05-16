<?php

namespace App\Http\Controllers;

use App\Models\Pesanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProduksiController extends Controller
{
    /**
     * Display operator dashboard
     */
    public function dashboard()
    {
        [
            $dalamAntrianCount,
            $sedangDiprosesCount,
            $selesaiHariIni,
            $sedangDiproses,
            $antrianBerikutnya
        ] = $this->getDashboardData();

        return view('produksi.dashboard', compact(
            'dalamAntrianCount',
            'sedangDiprosesCount',
            'selesaiHariIni',
            'sedangDiproses',
            'antrianBerikutnya'
        ));
    }

    public function realtimeDashboard()
    {
        [
            $dalamAntrianCount,
            $sedangDiprosesCount,
            $selesaiHariIni,
            $sedangDiproses,
            $antrianBerikutnya
        ] = $this->getDashboardData();

        return view('produksi._dashboard_content', compact(
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

    private function getDashboardData()
    {
        $dalamAntrianCount = Pesanan::where('status', 'dalam_antrian')->count();
        $sedangDiprosesCount = Pesanan::where('status', 'diproses')
            ->where('diproses_oleh', auth()->id())
            ->count();
        $selesaiHariIni = Pesanan::where('diproses_oleh', auth()->id())
            ->whereDate('selesai_produksi_at', today())
            ->count();

        $sedangDiproses = Pesanan::where('status', 'diproses')
            ->where('diproses_oleh', auth()->id())
            ->with(['produk', 'ukuranKertas', 'jenisKertas', 'user'])
            ->orderBy('mulai_produksi_at', 'asc')
            ->orderBy('id', 'asc')
            ->first();

        $antrianBerikutnya = Pesanan::dalamAntrian()
            ->with(['produk', 'user'])
            ->take(5)
            ->get();

        return [
            $dalamAntrianCount,
            $sedangDiprosesCount,
            $selesaiHariIni,
            $sedangDiproses,
            $antrianBerikutnya,
        ];
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

        if (!$pesanan->file_desain || $pesanan->file_desain === 'NANTI_DIKIRIM') {
            return back()->with('error', 'File desain tidak tersedia untuk pesanan ini.');
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
        $startedPesanan = DB::transaction(function () use ($id) {
            $operatorId = auth()->id();

            $hasActiveJob = Pesanan::where('status', 'diproses')
                ->where('diproses_oleh', $operatorId)
                ->lockForUpdate()
                ->exists();

            if ($hasActiveJob) {
                return ['error' => 'Anda masih memiliki pesanan yang sedang dikerjakan. Selesaikan terlebih dahulu sebelum mengambil antrian baru.'];
            }

            $nextPesanan = Pesanan::where('status', 'dalam_antrian')
                ->orderBy('estimasi_waktu', 'asc')
                ->orderBy('dikonfirmasi_at', 'asc')
                ->orderBy('id', 'asc')
                ->lockForUpdate()
                ->first();

            if (!$nextPesanan) {
                return ['error' => 'Tidak ada pesanan dalam antrian produksi.'];
            }

            if ((int) $nextPesanan->id !== (int) $id) {
                return ['error' => 'Pesanan ini belum berada di urutan pertama antrian SJF. Kerjakan pesanan paling atas terlebih dahulu.'];
            }

            $nextPesanan->update([
                'status' => 'diproses',
                'diproses_oleh' => $operatorId,
                'mulai_produksi_at' => now(),
            ]);

            return ['pesanan' => $nextPesanan];
        });

        if (isset($startedPesanan['error'])) {
            return redirect()->route('produksi.antrian');
        }

        return redirect()->route('produksi.antrian');
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
