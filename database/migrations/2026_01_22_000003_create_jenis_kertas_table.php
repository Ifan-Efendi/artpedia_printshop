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
        Schema::create('jenis_kertas', function (Blueprint $table) {
            $table->id();
            $table->string('nama');              // HVS 80gr, Art Paper 120gr, dll
            $table->text('deskripsi')->nullable();
            $table->decimal('harga_tambahan', 12, 2)->default(0);
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
        Schema::dropIfExists('jenis_kertas');
    }
};
