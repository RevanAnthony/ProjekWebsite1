<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Pengguna
        Schema::create('pengguna', function (Blueprint $table) {
            $table->id('id_pengguna');
            $table->string('nama');
            $table->string('email')->unique();
            $table->string('nomor_telepon')->nullable();
            $table->string('password');
            $table->timestamps();
        });

        // 2. Alamat Pengguna
        Schema::create('alamat_pengguna', function (Blueprint $table) {
            $table->id('id_alamat');
            $table->unsignedBigInteger('id_pengguna');
            $table->text('alamat_lengkap');
            $table->string('kota');
            $table->string('label_alamat')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->foreign('id_pengguna')
                  ->references('id_pengguna')->on('pengguna')
                  ->onDelete('cascade');
        });

        // 3. Kategori Produk
        Schema::create('kategori_produk', function (Blueprint $table) {
            $table->id('id_kategori');
            $table->string('nama_kategori');
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });

        // 4. Produk
        Schema::create('produk', function (Blueprint $table) {
            $table->id('id_produk');
            $table->unsignedBigInteger('id_kategori');
            $table->string('nama_produk');
            $table->decimal('harga', 10, 2);
            $table->integer('stok');
            $table->text('deskripsi')->nullable();
            $table->string('url_gambar')->nullable();
            $table->timestamps();

            $table->foreign('id_kategori')
                  ->references('id_kategori')->on('kategori_produk')
                  ->onDelete('cascade');
        });

        // 5. Keranjang
        Schema::create('keranjang', function (Blueprint $table) {
            $table->id('id_keranjang');
            $table->unsignedBigInteger('id_pengguna');
            $table->dateTime('tanggal_dibuat')->nullable();
            $table->timestamps();

            $table->foreign('id_pengguna')
                  ->references('id_pengguna')->on('pengguna')
                  ->onDelete('cascade');
        });

        // 6. Detail Keranjang
        Schema::create('detail_keranjang', function (Blueprint $table) {
            $table->id('id_detail_keranjang');
            $table->unsignedBigInteger('id_keranjang');
            $table->unsignedBigInteger('id_produk');
            $table->integer('jumlah');
            $table->decimal('subtotal', 10, 2);
            $table->timestamps();

            $table->foreign('id_keranjang')
                  ->references('id_keranjang')->on('keranjang')
                  ->onDelete('cascade');

            $table->foreign('id_produk')
                  ->references('id_produk')->on('produk')
                  ->onDelete('cascade');
        });

        // 7. Driver
        Schema::create('driver', function (Blueprint $table) {
            $table->id('id_driver');
            $table->string('nama_driver');
            $table->string('nomor_telepon');
            $table->string('status')->default('aktif');
            $table->timestamps();
        });

        // 8. Pesanan
        Schema::create('pesanan', function (Blueprint $table) {
            $table->id('id_pesanan');
            $table->unsignedBigInteger('id_pengguna');
            $table->unsignedBigInteger('id_driver')->nullable();
            $table->unsignedBigInteger('id_alamat_pengiriman');
            $table->decimal('biaya_produk', 10, 2)->default(0);
            $table->decimal('biaya_ongkir', 10, 2)->default(0);
            $table->decimal('total_pembayaran', 10, 2)->default(0);
            $table->string('metode_pengambilan')->nullable();
            $table->string('status_pesanan')->default('diproses');
            $table->text('catatan_pesanan')->nullable();
            $table->string('estimasi_waktu')->nullable();
            $table->dateTime('tanggal_pesanan');
            $table->timestamps();

            $table->foreign('id_pengguna')
                  ->references('id_pengguna')->on('pengguna')
                  ->onDelete('cascade');

            $table->foreign('id_driver')
                  ->references('id_driver')->on('driver')
                  ->onDelete('set null');

            $table->foreign('id_alamat_pengiriman')
                  ->references('id_alamat')->on('alamat_pengguna')
                  ->onDelete('cascade');
        });

        // 9. Detail Pesanan
        Schema::create('detail_pesanan', function (Blueprint $table) {
            $table->id('id_detail_pesanan');
            $table->unsignedBigInteger('id_pesanan');
            $table->unsignedBigInteger('id_produk');
            $table->integer('jumlah');
            $table->decimal('subtotal', 10, 2);
            $table->timestamps();

            $table->foreign('id_pesanan')
                  ->references('id_pesanan')->on('pesanan')
                  ->onDelete('cascade');

            $table->foreign('id_produk')
                  ->references('id_produk')->on('produk')
                  ->onDelete('cascade');
        });

        // 10. Pembayaran
        Schema::create('pembayaran', function (Blueprint $table) {
            $table->id('id_pembayaran');
            $table->unsignedBigInteger('id_pesanan');
            $table->unsignedBigInteger('id_pengguna');
            $table->string('metode_pembayaran');
            $table->string('referensi_transaksi')->nullable();
            $table->string('status_pembayaran')->default('pending');
            $table->decimal('total_pembayaran', 10, 2);
            $table->dateTime('tanggal_pembayaran');
            $table->timestamps();

            $table->foreign('id_pesanan')
                  ->references('id_pesanan')->on('pesanan')
                  ->onDelete('cascade');

            $table->foreign('id_pengguna')
                  ->references('id_pengguna')->on('pengguna')
                  ->onDelete('cascade');
        });

        // 11. Ulasan
        Schema::create('ulasan', function (Blueprint $table) {
            $table->id('id_ulasan');
            $table->unsignedBigInteger('id_pengguna');
            $table->unsignedBigInteger('id_produk');
            $table->text('komentar')->nullable();
            $table->integer('rating')->default(0);
            $table->dateTime('tanggal_ulasan');
            $table->timestamps();

            $table->foreign('id_pengguna')
                  ->references('id_pengguna')->on('pengguna')
                  ->onDelete('cascade');

            $table->foreign('id_produk')
                  ->references('id_produk')->on('produk')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        // Urutan drop harus dari tabel paling bergantung
        Schema::disableForeignKeyConstraints();

        Schema::dropIfExists('ulasan');
        Schema::dropIfExists('pembayaran');
        Schema::dropIfExists('detail_pesanan');
        Schema::dropIfExists('pesanan');
        Schema::dropIfExists('driver');
        Schema::dropIfExists('detail_keranjang');
        Schema::dropIfExists('keranjang');
        Schema::dropIfExists('produk');
        Schema::dropIfExists('kategori_produk');
        Schema::dropIfExists('alamat_pengguna');
        Schema::dropIfExists('pengguna');

        Schema::enableForeignKeyConstraints();
    }
};
