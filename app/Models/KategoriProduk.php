<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KategoriProduk extends Model
{
    use HasFactory;

    protected $table = 'kategori_produk';

    protected $fillable = [
        'nama',
        'slug',
        'deskripsi',
        'gambar',
        'aktif',
    ];

    protected $casts = [
        'aktif' => 'boolean',
    ];

    /**
     * Get all products in this category
     */
    public function produk()
    {
        return $this->hasMany(Produk::class, 'kategori_id');
    }

    /**
     * Get active products only
     */
    public function produkAktif()
    {
        return $this->hasMany(Produk::class, 'kategori_id')->where('aktif', true);
    }

    /**
     * Scope for active categories
     */
    public function scopeAktif($query)
    {
        return $query->where('aktif', true);
    }
}
