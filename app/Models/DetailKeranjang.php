<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailKeranjang extends Model
{
    protected $table = 'detail_keranjang';
    protected $primaryKey = 'id_detail_keranjang';
    public $timestamps = true;

    protected $fillable = ['id_keranjang', 'id_produk', 'jumlah', 'subtotal'];

    protected $casts = [
        'jumlah'   => 'integer',
        'subtotal' => 'integer',
    ];

    public function keranjang()
    {
        return $this->belongsTo(Keranjang::class, 'id_keranjang', 'id_keranjang');
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'id_produk', 'id_produk');
    }

    /** biar {detail} di route pakai kolom id_detail_keranjang */
    public function getRouteKeyName()
    {
        return 'id_detail_keranjang';
    }
}
