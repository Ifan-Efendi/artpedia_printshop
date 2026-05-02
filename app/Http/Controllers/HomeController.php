<?php

namespace App\Http\Controllers;

use App\Models\Pesanan;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Redirect to appropriate dashboard based on role.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function index()
    {
        $user = auth()->user();

        switch ($user->role) {
            case 'kasir':
                return redirect()->route('kasir.dashboard');
            case 'operator_produksi':
                return redirect()->route('produksi.dashboard');
            case 'pelanggan':
            default:
                return redirect()->route('pelanggan.dashboard');
        }
    }

    /**
     * Display pelanggan dashboard.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function pelangganDashboard()
    {
        $user = auth()->user();

        $totalPesanan = Pesanan::where('user_id', $user->id)->count();
        $pesananAktif = Pesanan::where('user_id', $user->id)
            ->whereNotIn('status', ['selesai', 'ditolak'])
            ->count();
        $pesananSelesai = Pesanan::where('user_id', $user->id)
            ->where('status', 'selesai')
            ->count();

        $recentPesanan = Pesanan::where('user_id', $user->id)
            ->with(['produk'])
            ->latest()
            ->take(5)
            ->get();

        return view('pelanggan.dashboard', compact('totalPesanan', 'pesananAktif', 'pesananSelesai', 'recentPesanan'));
    }
}
