<?php

namespace App\Http\Controllers;

use App\Models\Pesanan;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class LaporanController extends Controller
{
    private function getDailyReportData(string $date): array
    {
        $pesanans = Pesanan::where('status', 'selesai')
            ->whereDate('selesai_produksi_at', $date)
            ->with(['user', 'produk', 'ukuranKertas', 'jenisKertas', 'transaksi'])
            ->orderBy('selesai_produksi_at')
            ->get();

        $pesananCash = $pesanans->where('metode_pembayaran', 'cash');
        $pesananCashless = $pesanans->where('metode_pembayaran', 'cashless');

        $stats = [
            'total_pesanan' => $pesanans->count(),
            'total_pelanggan' => $pesanans->pluck('user_id')->unique()->count(),
            'total_pendapatan' => $pesanans->sum('total_harga'),
            'total_pesanan_cash' => $pesananCash->count(),
            'total_pesanan_cashless' => $pesananCashless->count(),
            'total_pendapatan_cash' => $pesananCash->sum('total_harga'),
            'total_pendapatan_cashless' => $pesananCashless->sum('total_harga'),
            'tanggal' => Carbon::parse($date)->format('d F Y'),
        ];

        return compact('pesanans', 'stats');
    }

    /**
     * Display daily report logic
     */
    public function index(Request $request)
    {
        $date = $request->get('tanggal', Carbon::today()->format('Y-m-d'));
        $reportData = $this->getDailyReportData($date);

        return view('kasir.laporan.index', array_merge($reportData, compact('date')));
    }

    /**
     * Export daily report to PDF
     */
    public function exportPdf(Request $request)
    {
        $date = $request->get('tanggal', Carbon::today()->format('Y-m-d'));
        $reportData = $this->getDailyReportData($date);

        $pdf = Pdf::loadView('kasir.laporan.pdf', $reportData);
        
        return $pdf->download('laporan-cetak-' . $date . '.pdf');
    }
}
