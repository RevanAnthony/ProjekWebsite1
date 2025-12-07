<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ulasan', function (Blueprint $table) {
            // Hapus kolom id_produk kalau masih ada
            if (Schema::hasColumn('ulasan', 'id_produk')) {
                $table->dropForeign(['id_produk']);
                $table->dropColumn('id_produk');
            }

            // Tambah kolom id_pesanan kalau belum ada
            if (! Schema::hasColumn('ulasan', 'id_pesanan')) {
                $table->unsignedBigInteger('id_pesanan')->after('id_pengguna');

                $table->foreign('id_pesanan')
                    ->references('id_pesanan')->on('pesanan')
                    ->onDelete('cascade');
            }
        });
    }

    public function down(): void
    {
        Schema::table('ulasan', function (Blueprint $table) {
            // rollback: hapus id_pesanan
            if (Schema::hasColumn('ulasan', 'id_pesanan')) {
                $table->dropForeign(['id_pesanan']);
                $table->dropColumn('id_pesanan');
            }

            // (opsional) balikin id_produk kalau dulu ada
            // $table->unsignedBigInteger('id_produk')->after('id_pengguna');
            // $table->foreign('id_produk')
            //       ->references('id_produk')->on('produk')
            //       ->onDelete('cascade');
        });
    }
};
