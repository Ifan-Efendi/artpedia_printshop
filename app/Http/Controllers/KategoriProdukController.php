<?php

namespace App\Http\Controllers;

use App\Models\KategoriProduk;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class KategoriProdukController extends Controller
{
    /**
     * Display list of categories
     */
    public function index(Request $request)
    {
        $query = KategoriProduk::withCount('produk');

        if ($request->filled('search')) {
            $query->where('nama', 'like', '%' . $request->search . '%');
        }


        $kategoris = $query->latest()->paginate(15)->withQueryString();

        return view('kasir.kategori.index', compact('kategoris'));
    }

    /**
     * Show form for creating a new category
     */
    public function create()
    {
        return view('kasir.kategori.create');
    }

    /**
     * Store a newly created category
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama'      => 'required|string|max:255',
            'deskripsi' => 'nullable|string|max:5000',
        ], [
            'nama.required' => 'Nama kategori wajib diisi.',
        ]);

        $slug = $this->uniqueSlug(Str::slug($request->nama));

        KategoriProduk::create([
            'nama'      => $request->nama,
            'slug'      => $slug,
            'deskripsi' => $request->deskripsi,
            'gambar'    => null,
            'aktif'     => true,
        ]);

        return redirect()->route('kasir.kategori.index')
            ->with('success', 'Kategori "' . $request->nama . '" berhasil ditambahkan.');
    }

    /**
     * Show form for editing a category
     */
    public function edit($id)
    {
        $kategori = KategoriProduk::findOrFail($id);

        return view('kasir.kategori.edit', compact('kategori'));
    }

    /**
     * Update the specified category
     */
    public function update(Request $request, $id)
    {
        $kategori = KategoriProduk::findOrFail($id);

        $request->validate([
            'nama'      => 'required|string|max:255',
            'deskripsi' => 'nullable|string|max:5000',
        ], [
            'nama.required' => 'Nama kategori wajib diisi.',
        ]);

        $slug = $this->uniqueSlug(Str::slug($request->nama), $kategori->id);

        $kategori->update([
            'nama'      => $request->nama,
            'slug'      => $slug,
            'deskripsi' => $request->deskripsi,
            'aktif'     => true,
        ]);

        return redirect()->route('kasir.kategori.index')
            ->with('success', 'Kategori "' . $request->nama . '" berhasil diperbarui.');
    }


    /**
     * Generate a unique slug
     */
    private function uniqueSlug(string $slug, ?int $excludeId = null): string
    {
        $original = $slug;
        $counter = 1;

        while (true) {
            $query = KategoriProduk::where('slug', $slug);
            if ($excludeId) {
                $query->where('id', '!=', $excludeId);
            }

            if (!$query->exists()) {
                break;
            }

            $slug = $original . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
}
