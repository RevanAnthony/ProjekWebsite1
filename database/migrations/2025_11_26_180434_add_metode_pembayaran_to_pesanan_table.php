<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambah kolom metode_pembayaran ke tabel pesanan.
     */
    public function up(): void
    {
        Schema::table('pesanan', function (Blueprint $table) {
            // tambah kolom baru setelah kolom metode_pengambilan
            $table->string('metode_pembayaran', 20)
                  ->default('cod')
                  ->after('metode_pengambilan');
        });
    }

    /**
     * Balikkin perubahan kalau di-rollback.
     */
    public function down(): void
    {
        Schema::table('pesanan', function (Blueprint $table) {
            $table->dropColumn('metode_pembayaran');
        });
    }
};
