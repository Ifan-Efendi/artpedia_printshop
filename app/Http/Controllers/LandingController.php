<?php

namespace App\Http\Controllers;

use App\Models\KategoriProduk;
use App\Models\Produk;
use Illuminate\Http\Request;

class LandingController extends Controller
{
    /**
     * Display landing page
     */
    public function index()
    {
        $kategoris = KategoriProduk::aktif()->withCount('produkAktif')->get();
        $produkTerbaru = Produk::aktif()->with('kategori')->latest()->take(6)->get();

        return view('welcome', compact('kategoris', 'produkTerbaru'));
    }

    /**
     * Display katalog page
     */
    public function katalog(Request $request)
    {
        $query = Produk::aktif()->with('kategori');

        // Filter by kategori
        if ($request->filled('kategori')) {
            $query->whereHas('kategori', function ($q) use ($request) {
                $q->where('slug', $request->kategori);
            });
        }

        // Search
        if ($request->filled('search')) {
            $query->where('nama', 'like', '%' . $request->search . '%');
        }

        $produks = $query->paginate(12);
        $kategoris = KategoriProduk::aktif()->get();

        return view('katalog.index', compact('produks', 'kategoris'));
    }

    /**
     * Display single product
     */
    public function show($slug)
    {
        $produk = Produk::where('slug', $slug)->aktif()->with('kategori')->firstOrFail();
        $produkTerkait = Produk::aktif()
            ->where('kategori_id', $produk->kategori_id)
            ->where('id', '!=', $produk->id)
            ->take(4)
            ->get();

        return view('katalog.show', compact('produk', 'produkTerkait'));
    }
}
