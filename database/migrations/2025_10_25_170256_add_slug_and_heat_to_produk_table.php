<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('produk')) return;

        Schema::table('produk', function (Blueprint $table) {
            if (!Schema::hasColumn('produk','slug')) {
                $table->string('slug', 180)->nullable()->unique()->after('nama_produk');
            }
            if (!Schema::hasColumn('produk','level_pedas')) {
                $table->unsignedTinyInteger('level_pedas')->default(0)->after('slug');
            }
            if (!Schema::hasColumn('produk','stok')) {
                $table->integer('stok')->default(0)->after('harga');
            }
            if (!Schema::hasColumn('produk','url_gambar')) {
                $table->string('url_gambar')->nullable()->after('deskripsi');
            }
        });

        // Backfill slug dari nama_produk
        if (Schema::hasColumn('produk','slug')) {
            $rows = DB::table('produk')->select('id_produk','nama_produk','slug')->get();
            foreach ($rows as $row) {
                if (!$row->slug) {
                    DB::table('produk')
                        ->where('id_produk', $row->id_produk)
                        ->update(['slug' => Str::slug($row->nama_produk)]);
                }
            }
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('produk')) return;

        Schema::table('produk', function (Blueprint $table) {
            if (Schema::hasColumn('produk','slug')) {
                $table->dropUnique('produk_slug_unique');
                $table->dropColumn('slug');
            }
            if (Schema::hasColumn('produk','level_pedas')) {
                $table->dropColumn('level_pedas');
            }
            if (Schema::hasColumn('produk','stok')) {
                $table->dropColumn('stok');
            }
            if (Schema::hasColumn('produk','url_gambar')) {
                $table->dropColumn('url_gambar');
            }
        });
    }
};
