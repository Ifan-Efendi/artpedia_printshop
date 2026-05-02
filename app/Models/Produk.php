<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    use HasFactory;

    protected $table = 'produk';

    protected $fillable = [
        'kategori_id',
        'nama',
        'slug',
        'deskripsi',
        'gambar',
        'harga_satuan',
        'min_order',
        'estimasi_waktu_per_unit',
        'aktif',
    ];

    protected $casts = [
        'harga_satuan' => 'decimal:2',
        'aktif' => 'boolean',
    ];

    /**
     * Get the category of this product
     */
    public function kategori()
    {
        return $this->belongsTo(KategoriProduk::class, 'kategori_id');
    }

    /**
     * Get all orders for this product
     */
    public function pesanan()
    {
        return $this->hasMany(Pesanan::class, 'produk_id');
    }

    /**
     * Scope for active products
     */
    public function scopeAktif($query)
    {
        return $query->where('aktif', true);
    }

    /**
     * Get formatted price
     */
    public function getHargaFormatAttribute()
    {
        return 'Rp ' . number_format($this->harga_satuan, 0, ',', '.');
    }
}
