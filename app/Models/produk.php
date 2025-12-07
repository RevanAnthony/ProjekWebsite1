<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Produk extends Model
{
    protected $table      = 'produk';
    protected $primaryKey = 'id_produk';
    public $timestamps    = true;

    protected $fillable = [
        'id_kategori','nama_produk','slug','level_pedas','harga','stok',
        'deskripsi','url_gambar'
    ];

    protected $casts = [
        'level_pedas' => 'integer',
        'harga'       => 'integer',
        'stok'        => 'integer',
    ];

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(KategoriProduk::class, 'id_kategori', 'id_kategori');
    }
}
