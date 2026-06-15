<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pesanan extends Model
{
    use HasFactory;

    protected $table = 'pesanan';

    protected $fillable = [
        'nomor_pesanan',
        'user_id',
        'transaksi_id',
        'produk_id',
        'ukuran_kertas_id',
        'jenis_kertas_id',
        'jumlah',
        'file_desain',
        'catatan',
        'harga_satuan',
        'total_harga',
        'bukti_pembayaran',
        'estimasi_waktu',
        'finishing',
        'opsi_potong',
        'status',
        'snap_token',
        'pembayaran_status',
        'alasan_penolakan',
        'dikonfirmasi_oleh',
        'dikonfirmasi_at',
        'diproses_oleh',
        'mulai_produksi_at',
        'selesai_produksi_at',
    ];

    protected $casts = [
        'harga_satuan' => 'decimal:2',
        'total_harga' => 'decimal:2',
        'dikonfirmasi_at' => 'datetime',
        'mulai_produksi_at' => 'datetime',
        'selesai_produksi_at' => 'datetime',
    ];

    /**
     * Status labels for display
     */
    const STATUS_LABELS = [
        'pending' => 'Menunggu Pembayaran',
        'ditolak' => 'Ditolak',
        'dibatalkan' => 'Dibatalkan',
        'dalam_antrian' => 'Dalam Antrian',
        'diproses' => 'Sedang Diproses',
        'selesai' => 'Selesai',
    ];

    /**
     * Status badge colors
     */
    const STATUS_COLORS = [
        'pending' => 'warning',
        'ditolak' => 'danger',
        'dibatalkan' => 'secondary',
        'dalam_antrian' => 'info',
        'diproses' => 'primary',
        'selesai' => 'success',
    ];

    /**
     * Get the customer who made this order
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class);
    }

    /**
     * Get the product ordered
     */
    public function produk()
    {
        return $this->belongsTo(Produk::class);
    }

    /**
     * Get the paper size
     */
    public function ukuranKertas()
    {
        return $this->belongsTo(UkuranKertas::class);
    }

    /**
     * Get the paper type
     */
    public function jenisKertas()
    {
        return $this->belongsTo(JenisKertas::class);
    }

    /**
     * Get the cashier who confirmed this order
     */
    public function kasir()
    {
        return $this->belongsTo(User::class, 'dikonfirmasi_oleh');
    }

    /**
     * Get the operator who processed this order
     */
    public function operator()
    {
        return $this->belongsTo(User::class, 'diproses_oleh');
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute()
    {
        return self::STATUS_LABELS[$this->status] ?? $this->status;
    }

    /**
     * Get status color for badge
     */
    public function getStatusColorAttribute()
    {
        return self::STATUS_COLORS[$this->status] ?? 'secondary';
    }

    /**
     * Get formatted total price
     */
    public function getTotalHargaFormatAttribute()
    {
        return 'Rp ' . number_format((float) $this->total_harga, 0, ',', '.');
    }

    public function getMetodePembayaranAttribute()
    {
        $buktiPembayaran = $this->transaksi->bukti_pembayaran ?? $this->bukti_pembayaran;

        return $buktiPembayaran === 'Pesanan Langsung' ? 'cash' : 'cashless';
    }

    public function getMetodePembayaranLabelAttribute()
    {
        return $this->metode_pembayaran === 'cash' ? 'Cash' : 'Cashless';
    }

    /**
     * Get formatted estimation time
     */
    public function getEstimasiWaktuFormatAttribute()
    {
        $menit = $this->estimasi_waktu;
        if ($menit >= 60) {
            $jam = floor($menit / 60);
            $sisa = $menit % 60;
            return $jam . ' jam ' . ($sisa > 0 ? $sisa . ' menit' : '');
        }
        return $menit . ' menit';
    }

    /**
     * Generate unique order number
     */
    public static function generateNomorPesanan()
    {
        $date = now()->format('Ymd');
        $lastOrder = self::whereDate('created_at', today())->orderBy('id', 'desc')->first();
        $sequence = $lastOrder ? (intval(substr($lastOrder->nomor_pesanan, -3)) + 1) : 1;
        return 'ART-' . $date . '-' . str_pad($sequence, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Scope for orders waiting cashier validation
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for orders in production queue (SJF)
     */
    public function scopeDalamAntrian($query)
    {
        return $query->where('status', 'dalam_antrian')
            ->orderBy('estimasi_waktu', 'asc')
            ->orderBy('dikonfirmasi_at', 'asc')
            ->orderBy('id', 'asc');
    }

    /**
     * Scope for orders being processed
     */
    public function scopeDiproses($query)
    {
        return $query->where('status', 'diproses');
    }

    public function scopeSelesai($query)
    {
        return $query->where('status', 'selesai');
    }
}
