<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    use HasFactory;

    protected $fillable = [
        'nomor_transaksi',
        'user_id',
        'total_harga',
        'bukti_pembayaran',
        'status',
        'alasan_penolakan',
        'dikonfirmasi_oleh',
        'dikonfirmasi_at',
    ];

    protected $casts = [
        'total_harga' => 'decimal:2',
        'dikonfirmasi_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function kasir()
    {
        return $this->belongsTo(User::class, 'dikonfirmasi_oleh');
    }

    public function pesanans()
    {
        return $this->hasMany(Pesanan::class);
    }

    public function getTotalHargaFormatAttribute()
    {
        return 'Rp ' . number_format($this->total_harga, 0, ',', '.');
    }

    public static function generateNomorTransaksi()
    {
        $prefix = 'TRX-' . date('Ymd');
        $lastTransaksi = self::where('nomor_transaksi', 'like', $prefix . '%')
            ->orderBy('nomor_transaksi', 'desc')
            ->first();

        if (!$lastTransaksi) {
            $number = '001';
        } else {
            $lastNumber = intval(substr($lastTransaksi->nomor_transaksi, -3));
            $number = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        }

        return $prefix . '-' . $number;
    }
}
