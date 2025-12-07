<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('keranjang')) return;

        if (!Schema::hasColumn('keranjang','status')) {
            Schema::table('keranjang', function (Blueprint $table) {
                $table->enum('status', ['draft', 'checkout'])->default('draft')->after('id_pengguna');
                $table->index('status');
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('keranjang')) return;

        if (Schema::hasColumn('keranjang','status')) {
            Schema::table('keranjang', function (Blueprint $table) {
                $table->dropIndex(['status']);
                $table->dropColumn('status');
            });
        }
    }
};
