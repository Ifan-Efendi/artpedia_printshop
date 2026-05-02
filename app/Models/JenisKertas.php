<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisKertas extends Model
{
    use HasFactory;

    protected $table = 'jenis_kertas';

    protected $fillable = [
        'nama',
        'deskripsi',
        'harga_tambahan',
        'aktif',
    ];

    protected $casts = [
        'harga_tambahan' => 'decimal:2',
        'aktif' => 'boolean',
    ];

    /**
     * Scope for active paper types
     */
    public function scopeAktif($query)
    {
        return $query->where('aktif', true);
    }

    /**
     * Get formatted additional price
     */
    public function getHargaTambahanFormatAttribute()
    {
        if ($this->harga_tambahan > 0) {
            return '+Rp ' . number_format($this->harga_tambahan, 0, ',', '.');
        }
        return '-';
    }
}
