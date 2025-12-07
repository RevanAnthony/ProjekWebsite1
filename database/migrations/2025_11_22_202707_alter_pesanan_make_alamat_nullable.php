<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1) Lepas foreign key (kalau ada), lalu drop kolom lama
        Schema::table('pesanan', function (Blueprint $table) {
            if (Schema::hasColumn('pesanan', 'id_alamat_pengiriman')) {
                // Nama FK biasanya: pesanan_id_alamat_pengiriman_foreign
                try {
                    $table->dropForeign(['id_alamat_pengiriman']);
                } catch (\Throwable $e) {
                    // kalau belum ada FK, cuek saja
                }

                $table->dropColumn('id_alamat_pengiriman');
            }
        });

        // 2) Tambah ulang kolom yang sama tapi boleh NULL + FK set null
        Schema::table('pesanan', function (Blueprint $table) {
            if (!Schema::hasColumn('pesanan', 'id_alamat_pengiriman')) {
                $table->unsignedBigInteger('id_alamat_pengiriman')
                      ->nullable()
                      ->after('id_driver');

                try {
                    $table->foreign('id_alamat_pengiriman')
                          ->references('id_alamat')->on('alamat_pengguna')
                          ->onDelete('set null');
                } catch (\Throwable $e) {
                    // kalau gagal bikin FK, tetap jalan (kolomnya sudah nullable)
                }
            }
        });
    }

    public function down(): void
    {
        // Balik ke versi lama: kolom wajib isi (NOT NULL)
        Schema::table('pesanan', function (Blueprint $table) {
            if (Schema::hasColumn('pesanan', 'id_alamat_pengiriman')) {
                try {
                    $table->dropForeign(['id_alamat_pengiriman']);
                } catch (\Throwable $e) {}
                $table->dropColumn('id_alamat_pengiriman');
            }
        });

        Schema::table('pesanan', function (Blueprint $table) {
            if (!Schema::hasColumn('pesanan', 'id_alamat_pengiriman')) {
                $table->unsignedBigInteger('id_alamat_pengiriman')
                      ->after('id_driver');

                try {
                    $table->foreign('id_alamat_pengiriman')
                          ->references('id_alamat')->on('alamat_pengguna')
                          ->onDelete('cascade');
                } catch (\Throwable $e) {}
            }
        });
    }
};
