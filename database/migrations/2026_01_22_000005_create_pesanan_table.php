<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pesanan', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_pesanan')->unique();   // Format: ART-20260122-001
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Detail Pesanan
            $table->foreignId('produk_id')->constrained('produk')->onDelete('cascade');
            $table->foreignId('ukuran_kertas_id')->constrained('ukuran_kertas')->onDelete('cascade');
            $table->foreignId('jenis_kertas_id')->constrained('jenis_kertas')->onDelete('cascade');
            $table->integer('jumlah');
            $table->string('file_desain');               // Path file upload (wajib)
            $table->text('catatan')->nullable();         // Instruksi khusus

            // Harga
            $table->decimal('harga_satuan', 12, 2);
            $table->decimal('total_harga', 12, 2);

            // Bukti Pembayaran (diupload pelanggan saat submit)
            $table->string('bukti_pembayaran');          // Path bukti bayar (wajib)

            // SJF - Estimasi Waktu (dalam menit)
            $table->integer('estimasi_waktu');           // Dihitung: jumlah × waktu_per_unit × faktor

            // Status Pesanan
            $table->enum('status', [
                'pending',          // Menunggu validasi kasir
                'ditolak',          // Pembayaran ditolak kasir
                'dalam_antrian',    // Pembayaran valid, masuk antrian SJF
                'diproses',         // Sedang dikerjakan operator produksi
                'selesai',          // Produksi selesai
            ])->default('pending');

            // Alasan penolakan (jika ditolak)
            $table->text('alasan_penolakan')->nullable();

            // Tracking Waktu & Petugas
            $table->foreignId('dikonfirmasi_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('dikonfirmasi_at')->nullable();
            $table->foreignId('diproses_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('mulai_produksi_at')->nullable();
            $table->timestamp('selesai_produksi_at')->nullable();
            $table->timestamp('diambil_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pesanan');
    }
};
