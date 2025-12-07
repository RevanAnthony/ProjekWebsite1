<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Keranjang extends Model
{
    protected $table = 'keranjang';
    protected $primaryKey = 'id_keranjang';
    public $timestamps = true;

    // kolom yang pasti ada di DB-mu
    protected $fillable = ['id_pengguna', 'tanggal_dibuat'];

    public function items()
    {
        return $this->hasMany(DetailKeranjang::class, 'id_keranjang', 'id_keranjang')
                    ->with('produk');
    }

    public function total(): int
    {
        // gunakan relasi query agar tidak butuh ->items preloaded
        return (int) $this->items()->sum('subtotal');
    }

    public function count(): int
    {
        return (int) $this->items()->sum('jumlah');
    }
}
