<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

// Model lain satu namespace, sebenarnya bisa tanpa "use", tapi gpp lebih jelas:
use App\Models\User;
use App\Models\DetailPesanan;
use App\Models\Ulasan;
use App\Models\Chat;

class Pesanan extends Model
{
    protected $table = 'pesanan';
    protected $primaryKey = 'id_pesanan';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;

    protected $fillable = [
        'id_pengguna',
        'id_driver',
        'id_alamat_pengiriman',
        'biaya_produk',
        'biaya_ongkir',
        'total_pembayaran',
        'metode_pengambilan',
        'metode_pembayaran',   // kolom baru dari migration
        'status_pesanan',
        'catatan_pesanan',
        'estimasi_waktu',
        'tanggal_pesanan',
        // kalau di DB ada kolom lain (confirmed_by, confirmed_store_at, dll)
        // dan lu mau mass assign, tinggal tambahin di sini.
    ];

    /**
     * Detail pesanan (item-menu yang dipesan)
     */
    public function items(): HasMany
    {
        return $this->hasMany(DetailPesanan::class, 'id_pesanan', 'id_pesanan');
    }

    /**
     * Relasi ke user/pelanggan
     */
    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_pengguna', 'id_pengguna');
    }

    /**
     * Satu pesanan punya satu ulasan (kalau sudah diberi review)
     */
    public function ulasan(): HasOne
    {
        return $this->hasOne(Ulasan::class, 'id_pesanan', 'id_pesanan');
    }

    /**
     * Relasi ke chat pesanan (fitur chat kasir/user)
     */
    public function chat(): HasOne
    {
        return $this->hasOne(Chat::class, 'id_pesanan', 'id_pesanan');
    }
}
