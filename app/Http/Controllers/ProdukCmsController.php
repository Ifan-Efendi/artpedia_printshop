<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\KategoriProduk;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ProdukCmsController extends Controller
{
    /**
     * Display list of products with search & filter
     */
    public function index(Request $request)
    {
        $kategoris = KategoriProduk::with(['produk' => function($query) {
            $query->latest();
        }])->withCount('produk')->orderBy('nama')->get();

        return view('kasir.produk.index', compact('kategoris'));
    }

    /**
     * Show form for creating a new product
     */
    public function create(Request $request)
    {
        $kategoris = KategoriProduk::aktif()->orderBy('nama')->get();
        $selectedKategoriId = (int) $request->input('kategori_id', 0);

        return view('kasir.produk.create', compact('kategoris', 'selectedKategoriId'));
    }

    /**
     * Store a newly created product
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama'                    => 'required|string|max:255',
            'kategori_id'             => 'required|exists:kategori_produk,id',
            'gambar'                  => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'harga_satuan'            => 'required|numeric|min:0',
            'min_order'               => 'required|integer|min:1',
            'estimasi_waktu_per_unit' => 'nullable|integer|min:1',
            'deskripsi'               => 'nullable|string|max:5000',
        ], [
            'nama.required'         => 'Nama produk wajib diisi.',
            'kategori_id.required'  => 'Kategori wajib dipilih.',
            'kategori_id.exists'    => 'Kategori tidak valid.',
            'gambar.image'          => 'File harus berupa gambar.',
            'gambar.mimes'          => 'Format gambar harus JPG, JPEG, atau PNG.',
            'gambar.max'            => 'Ukuran gambar maksimal 2MB.',
            'harga_satuan.required' => 'Harga satuan wajib diisi.',
            'harga_satuan.numeric'  => 'Harga satuan harus berupa angka.',
            'harga_satuan.min'      => 'Harga satuan tidak boleh negatif.',
            'min_order.required'    => 'Minimal order wajib diisi.',
            'min_order.min'         => 'Minimal order harus minimal 1.',
        ]);

        // Generate slug
        $slug = Str::slug($request->nama);

        // Ensure slug is unique
        $slug = $this->uniqueSlug($slug);

        // Handle image upload
        $gambarPath = null;
        if ($request->hasFile('gambar')) {
            $gambarPath = $request->file('gambar')->store('produk', 'public');
        }

        Produk::create([
            'nama'                    => $request->nama,
            'kategori_id'             => $request->kategori_id,
            'slug'                    => $slug,
            'deskripsi'               => $request->deskripsi,
            'gambar'                  => $gambarPath,
            'harga_satuan'            => $request->harga_satuan,
            'min_order'               => $request->min_order,
            'estimasi_waktu_per_unit' => $request->estimasi_waktu_per_unit ?? 5,
            'aktif'                   => $request->boolean('aktif', true),
        ]);

        return redirect()->route('kasir.produk.index')
            ->with('success', 'Produk "' . $request->nama . '" berhasil ditambahkan.');
    }

    /**
     * Show form for editing a product
     */
    public function edit($id)
    {
        $produk = Produk::findOrFail($id);
        $kategoris = KategoriProduk::aktif()->orderBy('nama')->get();

        return view('kasir.produk.edit', compact('produk', 'kategoris'));
    }

    /**
     * Update the specified product
     */
    public function update(Request $request, $id)
    {
        $produk = Produk::findOrFail($id);

        $request->validate([
            'nama'                    => 'required|string|max:255',
            'kategori_id'             => 'required|exists:kategori_produk,id',
            'gambar'                  => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'harga_satuan'            => 'required|numeric|min:0',
            'min_order'               => 'required|integer|min:1',
            'estimasi_waktu_per_unit' => 'nullable|integer|min:1',
            'deskripsi'               => 'nullable|string|max:5000',
        ], [
            'nama.required'         => 'Nama produk wajib diisi.',
            'kategori_id.required'  => 'Kategori wajib dipilih.',
            'kategori_id.exists'    => 'Kategori tidak valid.',
            'gambar.image'          => 'File harus berupa gambar.',
            'gambar.mimes'          => 'Format gambar harus JPG, JPEG, atau PNG.',
            'gambar.max'            => 'Ukuran gambar maksimal 2MB.',
            'harga_satuan.required' => 'Harga satuan wajib diisi.',
            'harga_satuan.numeric'  => 'Harga satuan harus berupa angka.',
            'harga_satuan.min'      => 'Harga satuan tidak boleh negatif.',
            'min_order.required'    => 'Minimal order wajib diisi.',
            'min_order.min'         => 'Minimal order harus minimal 1.',
        ]);

        // Generate slug
        $slug = Str::slug($request->nama);

        // Ensure slug is unique (exclude current product)
        $slug = $this->uniqueSlug($slug, $produk->id);

        // Handle image upload
        if ($request->hasFile('gambar')) {
            // Delete old image
            if ($produk->gambar && Storage::disk('public')->exists($produk->gambar)) {
                Storage::disk('public')->delete($produk->gambar);
            }
            $gambarPath = $request->file('gambar')->store('produk', 'public');
        } else {
            $gambarPath = $produk->gambar;
        }

        $produk->update([
            'nama'                    => $request->nama,
            'kategori_id'             => $request->kategori_id,
            'slug'                    => $slug,
            'deskripsi'               => $request->deskripsi,
            'gambar'                  => $gambarPath,
            'harga_satuan'            => $request->harga_satuan,
            'min_order'               => $request->min_order,
            'estimasi_waktu_per_unit' => $request->estimasi_waktu_per_unit ?? $produk->estimasi_waktu_per_unit,
            'aktif'                   => $request->boolean('aktif', true),
        ]);

        return redirect()->route('kasir.produk.index')
            ->with('success', 'Produk "' . $request->nama . '" berhasil diperbarui.');
    }

    /**
     * Toggle product active status
     */
    public function toggleAktif($id)
    {
        $produk = Produk::findOrFail($id);
        $produk->update(['aktif' => !$produk->aktif]);

        $status = $produk->aktif ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', 'Produk "' . $produk->nama . '" berhasil ' . $status . '.');
    }

    /**
     * Generate a unique slug
     */
    private function uniqueSlug(string $slug, ?int $excludeId = null): string
    {
        $original = $slug;
        $counter = 1;

        while (true) {
            $query = Produk::where('slug', $slug);
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
