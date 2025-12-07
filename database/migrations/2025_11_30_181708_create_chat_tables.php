<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel chat (thread per pesanan)
        if (!Schema::hasTable('chats')) {
            Schema::create('chats', function (Blueprint $table) {
                $table->id('id_chat');
                $table->unsignedBigInteger('id_pesanan')->nullable();
                $table->string('context_type')->default('order'); // order / support / dll
                $table->string('title')->nullable();
                $table->timestamps();

                $table->foreign('id_pesanan')
                    ->references('id_pesanan')
                    ->on('pesanan')
                    ->onDelete('cascade');
            });
        }

        // Tabel pesan chat
        if (!Schema::hasTable('chat_messages')) {
            Schema::create('chat_messages', function (Blueprint $table) {
                $table->id('id_pesan');
                $table->unsignedBigInteger('id_chat');
                $table->unsignedBigInteger('id_pengguna');
                $table->string('sender_role')->nullable(); // customer / admin / owner / kasir
                $table->text('isi_pesan');
                $table->boolean('is_read')->default(false);
                $table->timestamp('read_at')->nullable();
                $table->timestamps();

                $table->foreign('id_chat')
                    ->references('id_chat')
                    ->on('chats')
                    ->onDelete('cascade');

                $table->foreign('id_pengguna')
                    ->references('id_pengguna')
                    ->on('pengguna')
                    ->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('chat_messages')) {
            Schema::dropIfExists('chat_messages');
        }

        if (Schema::hasTable('chats')) {
            Schema::dropIfExists('chats');
        }
    }
};
