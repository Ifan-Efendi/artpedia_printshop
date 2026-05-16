<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
        'is_finishing',
        'is_cutting',
        'aktif',
    ];

    protected $casts = [
        'harga_satuan' => 'decimal:2',
        'is_finishing' => 'boolean',
        'is_cutting' => 'boolean',
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

    public function getUnitLabelAttribute(): string
    {
        $text = strtolower(trim(($this->nama ?? '') . ' ' . ($this->slug ?? '') . ' ' . ($this->deskripsi ?? '')));

        if (str_contains($text, 'box')) {
            return 'box';
        }

        if (
            str_contains($text, 'kartu nama') ||
            str_contains($text, 'kartu-nama') ||
            str_contains($text, 'id card') ||
            str_contains($text, 'id-card')
        ) {
            return 'pcs';
        }

        return 'lembar';
    }

    public function getHargaUnitLabelAttribute(): string
    {
        return $this->unit_label;
    }

    public function getGambarUrlAttribute(): string
    {
        if ($this->gambar && Storage::disk('public')->exists($this->gambar)) {
            return asset('storage/' . $this->gambar);
        }

        $fallback = $this->gambarFallbackPath();

        if ($fallback && Storage::disk('public')->exists($fallback)) {
            return asset('storage/' . $fallback);
        }

        return asset('images/noImage.jpg');
    }

    private function gambarFallbackPath(): ?string
    {
        $text = Str::lower(trim(($this->nama ?? '') . ' ' . ($this->slug ?? '') . ' ' . ($this->deskripsi ?? '')));

        if (Str::contains($text, 'sticker chromo')) {
            return 'produk/sticker-chromo-a3.png';
        }

        if (Str::contains($text, ['sticker', 'stiker'])) {
            return 'produk/sticker-vinyl-a3.png';
        }

        if (Str::contains($text, ['kartu nama', 'kartu-nama', 'id card', 'id-card'])) {
            return 'produk/kartu-nama.png';
        }

        if (Str::contains($text, 'kartu ucapan')) {
            return 'produk/kartu-ucapan.png';
        }

        if (Str::contains($text, 'poster') && Str::contains($text, 'a4')) {
            return 'produk/poster-a4.png';
        }

        if (Str::contains($text, 'poster')) {
            return 'produk/poster-a3.png';
        }

        if (Str::contains($text, 'brosur') && Str::contains($text, 'a5')) {
            return 'produk/brosur-a5.png';
        }

        if (Str::contains($text, 'brosur')) {
            return 'produk/brosur-a4.png';
        }

        return null;
    }
}
