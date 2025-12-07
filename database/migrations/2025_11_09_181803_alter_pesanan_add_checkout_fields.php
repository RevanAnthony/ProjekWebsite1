<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('pesanan')) return;

        Schema::table('pesanan', function (Blueprint $table) {
            if (!Schema::hasColumn('pesanan','status_pesanan')) {
                $table->string('status_pesanan', 40)->default('menunggu_pembayaran')->after('metode_pengambilan');
            }
            if (!Schema::hasColumn('pesanan','confirmed_store_at')) {
                $table->timestamp('confirmed_store_at')->nullable()->after('status_pesanan');
            }
            if (!Schema::hasColumn('pesanan','confirmed_by')) {
                $table->unsignedBigInteger('confirmed_by')->nullable()->after('confirmed_store_at');
            }
            if (!Schema::hasColumn('pesanan','catatan_pesanan')) {
                $table->text('catatan_pesanan')->nullable()->after('confirmed_by');
            }
            if (!Schema::hasColumn('pesanan','estimasi_waktu')) {
                $table->dateTime('estimasi_waktu')->nullable()->after('catatan_pesanan');
            }
            if (!Schema::hasColumn('pesanan','tanggal_pesanan')) {
                $table->dateTime('tanggal_pesanan')->nullable()->after('estimasi_waktu');
            }
        });

        // FK optional untuk confirmed_by (ke tabel pengguna)
        if (!Schema::hasColumn('pesanan','confirmed_by')) return;
        Schema::table('pesanan', function (Blueprint $table) {
            // hindari error kalau FK sudah ada
            try {
                $table->foreign('confirmed_by')->references('id_pengguna')->on('pengguna')->nullOnDelete();
            } catch (\Throwable $e) {
                // abaikan jika sudah ada
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('pesanan')) return;

        Schema::table('pesanan', function (Blueprint $table) {
            if (Schema::hasColumn('pesanan','tanggal_pesanan')) $table->dropColumn('tanggal_pesanan');
            if (Schema::hasColumn('pesanan','estimasi_waktu')) $table->dropColumn('estimasi_waktu');
            if (Schema::hasColumn('pesanan','catatan_pesanan')) $table->dropColumn('catatan_pesanan');
            if (Schema::hasColumn('pesanan','confirmed_by')) $table->dropColumn('confirmed_by');
            if (Schema::hasColumn('pesanan','confirmed_store_at')) $table->dropColumn('confirmed_store_at');
            if (Schema::hasColumn('pesanan','status_pesanan')) $table->dropColumn('status_pesanan');
        });
    }
};
