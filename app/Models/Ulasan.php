<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ulasan extends Model
{
    protected $table      = 'ulasan';
    protected $primaryKey = 'id_ulasan';
    public $timestamps    = true;

    protected $fillable = [
        'id_pesanan',
        'id_pengguna',
        'rating',
        'komentar',
        'tanggal_ulasan'
    ];

    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_pengguna', 'id_pengguna');
    }

    public function pesanan(): BelongsTo
    {
        return $this->belongsTo(Pesanan::class, 'id_pesanan', 'id_pesanan');
    }
}
