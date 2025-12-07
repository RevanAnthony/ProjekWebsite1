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
        if (Schema::hasTable('kategori_produk') && !Schema::hasColumn('kategori_produk', 'slug')) {
            Schema::table('kategori_produk', function (Blueprint $table) {
                $table->string('slug', 120)->nullable()->unique()->after('nama_kategori');
            });

            // Backfill slug dari nama_kategori
            $rows = DB::table('kategori_produk')->select('id_kategori', 'nama_kategori')->get();
            foreach ($rows as $row) {
                DB::table('kategori_produk')
                    ->where('id_kategori', $row->id_kategori)
                    ->update(['slug' => Str::slug($row->nama_kategori)]);
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('kategori_produk') && Schema::hasColumn('kategori_produk', 'slug')) {
            Schema::table('kategori_produk', function (Blueprint $table) {
                $table->dropUnique('kategori_produk_slug_unique');
                $table->dropColumn('slug');
            });
        }
    }
};
