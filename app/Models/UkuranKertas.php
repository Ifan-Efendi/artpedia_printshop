<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UkuranKertas extends Model
{
    use HasFactory;

    protected $table = 'ukuran_kertas';

    protected $fillable = [
        'nama',
        'dimensi',
        'faktor_harga',
        'faktor_waktu',
        'aktif',
    ];

    protected $casts = [
        'faktor_harga' => 'decimal:2',
        'faktor_waktu' => 'decimal:2',
        'aktif' => 'boolean',
    ];

    /**
     * Scope for active sizes
     */
    public function scopeAktif($query)
    {
        return $query->where('aktif', true);
    }

    /**
     * Get display name with dimension
     */
    public function getNamaLengkapAttribute()
    {
        return $this->nama . ' (' . $this->dimensi . ')';
    }
}
