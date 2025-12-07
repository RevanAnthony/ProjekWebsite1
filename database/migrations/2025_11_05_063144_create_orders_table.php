<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('keranjang')) return;

        Schema::table('keranjang', function (Blueprint $table) {
            if (!Schema::hasColumn('keranjang','status')) {
                $table->enum('status', ['draft', 'checkout'])->default('draft')->after('id_pengguna');
                $table->index('status');
            }
        });

        // Backfill nilai default untuk data lama yang masih NULL
        if (Schema::hasColumn('keranjang','status')) {
            DB::table('keranjang')->whereNull('status')->update(['status' => 'draft']);
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('keranjang')) return;

        Schema::table('keranjang', function (Blueprint $table) {
            if (Schema::hasColumn('keranjang','status')) {
                $table->dropIndex(['status']);
                $table->dropColumn('status');
            }
        });
    }
};
