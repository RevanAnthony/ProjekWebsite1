<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('pengguna', function (Blueprint $table) {
            if (!Schema::hasColumn('pengguna','remember_token')) {
                $table->rememberToken()->after('password');
            }

            if (!Schema::hasColumn('pengguna','google_id')) {
                // 191 agar aman untuk unique index di MySQL/MariaDB lama
                $table->string('google_id', 191)->nullable()->unique()->after('email');
            }

            if (!Schema::hasColumn('pengguna','email_verified_at')) {
                $table->timestamp('email_verified_at')->nullable()->after('google_id');
            }
        });
    }

    public function down(): void {
        Schema::table('pengguna', function (Blueprint $table) {
            if (Schema::hasColumn('pengguna', 'email_verified_at')) {
                $table->dropColumn('email_verified_at');
            }
            if (Schema::hasColumn('pengguna', 'google_id')) {
                $table->dropColumn('google_id');
            }
            if (Schema::hasColumn('pengguna', 'remember_token')) {
                $table->dropColumn('remember_token');
            }
        });
    }
};
