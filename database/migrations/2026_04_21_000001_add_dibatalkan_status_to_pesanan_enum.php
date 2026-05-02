<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddDibatalkanStatusToPesananEnum extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("
            ALTER TABLE pesanan
            MODIFY COLUMN status ENUM('pending', 'ditolak', 'dibatalkan', 'dalam_antrian', 'diproses', 'selesai')
            DEFAULT 'pending'
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("UPDATE pesanan SET status = 'ditolak' WHERE status = 'dibatalkan'");

        DB::statement("
            ALTER TABLE pesanan
            MODIFY COLUMN status ENUM('pending', 'ditolak', 'dalam_antrian', 'diproses', 'selesai')
            DEFAULT 'pending'
        ");
    }
}
