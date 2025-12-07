<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetailPesanan extends Model
{
    protected $table = 'detail_pesanan';
    protected $primaryKey = 'id_detail_pesanan';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;

    protected $fillable = [
        'id_pesanan',
        'id_produk',
        'nama_produk',
        'harga',
        'jumlah',
        'subtotal',
        'url_gambar',
        'catatan',
    ];

    public function pesanan(): BelongsTo
    {
        return $this->belongsTo(Pesanan::class, 'id_pesanan', 'id_pesanan');
    }

    public function produk(): BelongsTo
    {
        // aktif jika tabel produk punya PK `id_produk`
        return $this->belongsTo(Produk::class, 'id_produk', 'id_produk');
    }
}
