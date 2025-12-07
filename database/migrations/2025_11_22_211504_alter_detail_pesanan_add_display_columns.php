<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('detail_pesanan', function (Blueprint $table) {
            // Tambah kolom nama_produk & harga jika belum ada
            if (!Schema::hasColumn('detail_pesanan', 'nama_produk')) {
                $table->string('nama_produk', 150)->after('id_produk');
            }

            if (!Schema::hasColumn('detail_pesanan', 'harga')) {
                $table->decimal('harga', 10, 2)->after('nama_produk');
            }

            // Tambah kolom url_gambar & catatan jika belum ada
            if (!Schema::hasColumn('detail_pesanan', 'url_gambar')) {
                $table->string('url_gambar', 255)->nullable()->after('subtotal');
            }

            if (!Schema::hasColumn('detail_pesanan', 'catatan')) {
                $table->text('catatan')->nullable()->after('url_gambar');
            }
        });
    }

    public function down(): void
    {
        Schema::table('detail_pesanan', function (Blueprint $table) {
            if (Schema::hasColumn('detail_pesanan', 'nama_produk')) {
                $table->dropColumn('nama_produk');
            }

            if (Schema::hasColumn('detail_pesanan', 'harga')) {
                $table->dropColumn('harga');
            }

            if (Schema::hasColumn('detail_pesanan', 'url_gambar')) {
                $table->dropColumn('url_gambar');
            }

            if (Schema::hasColumn('detail_pesanan', 'catatan')) {
                $table->dropColumn('catatan');
            }
        });
    }
};
