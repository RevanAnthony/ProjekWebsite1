<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('pengguna')) {
            return;
        }

        Schema::table('pengguna', function (Blueprint $table) {
            if (!Schema::hasColumn('pengguna', 'role')) {
                $table->enum('role', ['customer', 'owner', 'kasir', 'admin'])
                    ->default('customer')
                    ->after('id_pengguna');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('pengguna')) {
            return;
        }

        Schema::table('pengguna', function (Blueprint $table) {
            if (Schema::hasColumn('pengguna', 'role')) {
                $table->dropColumn('role');
            }
        });
    }
};
