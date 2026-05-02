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
        Schema::create('ukuran_kertas', function (Blueprint $table) {
            $table->id();
            $table->string('nama');              // A3, A4, A5, 10x15cm, dll
            $table->string('dimensi');           // 297x420mm
            $table->decimal('faktor_harga', 5, 2)->default(1.00);  // Pengali harga
            $table->decimal('faktor_waktu', 5, 2)->default(1.00);  // Pengali waktu
            $table->boolean('aktif')->default(true);
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
        Schema::dropIfExists('ukuran_kertas');
    }
};
