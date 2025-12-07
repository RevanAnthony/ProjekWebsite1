<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pesanan', function (Blueprint $table) {
            // Pastikan kolom status_pesanan ada & punya default yang jelas
            if (!Schema::hasColumn('pesanan', 'status_pesanan')) {
                $table->string('status_pesanan', 40)
                      ->default('menunggu_pembayaran')
                      ->after('metode_pengambilan');
            }

            // Waktu pesanan DIKONFIRMASI OLEH TOKO (kasir)
            if (!Schema::hasColumn('pesanan', 'confirmed_store_at')) {
                $table->timestamp('confirmed_store_at')
                      ->nullable()
                      ->after('status_pesanan');
            }

            // Siapa kasir yang mengkonfirmasi (opsional)
            if (!Schema::hasColumn('pesanan', 'confirmed_by')) {
                $table->unsignedBigInteger('confirmed_by')
                      ->nullable()
                      ->after('confirmed_store_at');
                // Kalau mau, nanti bisa ditambah foreign key ke tabel `pengguna`
                // $table->foreign('confirmed_by')->references('id_pengguna')->on('pengguna')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('pesanan', function (Blueprint $table) {
            if (Schema::hasColumn('pesanan', 'confirmed_by')) {
                // Jika sebelumnya ditambah foreign key, ini bisa di-uncomment:
                // $table->dropForeign(['confirmed_by']);
                $table->dropColumn('confirmed_by');
            }

            if (Schema::hasColumn('pesanan', 'confirmed_store_at')) {
                $table->dropColumn('confirmed_store_at');
            }

            // status_pesanan jangan di-drop, karena sudah dipakai aplikasi.
        });
    }
};
