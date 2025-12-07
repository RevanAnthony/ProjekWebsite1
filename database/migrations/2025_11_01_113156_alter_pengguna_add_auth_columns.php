<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('pengguna')) return;

        Schema::table('pengguna', function (Blueprint $table) {
            if (!Schema::hasColumn('pengguna','google_id')) {
                $table->string('google_id')->nullable()->after('password');
            }
            if (!Schema::hasColumn('pengguna','nomor_telepon')) {
                $table->string('nomor_telepon', 30)->nullable()->after('google_id');
            }
            if (!Schema::hasColumn('pengguna','remember_token')) {
                $table->rememberToken()->after('nomor_telepon');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('pengguna')) return;

        Schema::table('pengguna', function (Blueprint $table) {
            if (Schema::hasColumn('pengguna','remember_token')) {
                $table->dropColumn('remember_token');
            }
            if (Schema::hasColumn('pengguna','nomor_telepon')) {
                $table->dropColumn('nomor_telepon');
            }
            if (Schema::hasColumn('pengguna','google_id')) {
                $table->dropColumn('google_id');
            }
        });
    }
};
