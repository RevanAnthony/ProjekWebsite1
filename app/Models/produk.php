<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\KategoriProduk;

class Produk extends Model
{
    protected $table      = 'produk';
    protected $primaryKey = 'id_produk';
    public $timestamps    = true;

    protected $fillable = [
        'id_kategori',
        'kode_produk',
        'nama_produk',
        'slug',
        'level_pedas',
        'harga',
        'stok',
        'deskripsi',
        'url_gambar',
    ];

    protected $casts = [
        'level_pedas' => 'integer',
        'harga'       => 'integer',
        'stok'        => 'integer',
    ];

    /**
     * Saat create produk baru, kalau kode_produk belum diisi,
     * generate otomatis berdasarkan kategori.
     */
    protected static function booted(): void
    {
        static::creating(function (Produk $produk): void {
            if (empty($produk->kode_produk)) {
                $produk->kode_produk = $produk->generateKode();
            }
        });
    }

    /**
     * Tentukan prefix berdasarkan slug kategori:
     *  - mengandung "rice"  => RB
     *  - mengandung "side"  => SD
     *  - mengandung "drink" => D
     *  - lainnya            => P
     */
    protected function prefixFromSlug(?string $slug): string
    {
        $slug = strtolower((string) $slug);

        if (strpos($slug, 'rice') !== false) {
            return 'RB';
        }

        if (strpos($slug, 'side') !== false) {
            return 'SD';
        }

        if (strpos($slug, 'drink') !== false) {
            return 'D';
        }

        return 'P';
    }

    /**
     * Generate kode baru untuk produk ini.
     * Contoh: RB01, RB02, SD01, D03, dst.
     */
    public function generateKode(): string
    {
        // ambil slug kategori dari relasi
        $kategori = $this->kategori()->first();
        $slug     = $kategori ? $kategori->slug : null;

        $prefix = $this->prefixFromSlug($slug);

        // cari kode terakhir di kategori yang sama
        $lastKode = static::where('id_kategori', $this->id_kategori)
            ->whereNotNull('kode_produk')
            ->where('kode_produk', 'like', $prefix.'%')
            ->orderBy('kode_produk', 'desc')
            ->value('kode_produk');

        $nextNumber = 1;

        if ($lastKode) {
            $numberPart = preg_replace('/\D/', '', $lastKode);

            if ($numberPart !== '') {
                $nextNumber = ((int) $numberPart) + 1;
            }
        }

        // RB01, SD02, D03, dst
        return sprintf('%s%02d', $prefix, $nextNumber);
    }

    /**
     * Label kode untuk tampilan.
     * Kalau kolom kode_produk ada, pakai itu (upper).
     * Kalau belum ada, fallback berdasarkan kategori + id_produk.
     */
    public function getKodeLabelAttribute(): string
    {
        if (!empty($this->kode_produk)) {
            return strtoupper($this->kode_produk);
        }

        $kategori = $this->kategori()->first();
        $slug     = $kategori ? $kategori->slug : null;
        $prefix   = $this->prefixFromSlug($slug);

        return sprintf('%s%02d', $prefix, (int) $this->id_produk);
    }

    /**
     * Relasi ke kategori.
     */
    public function kategori(): BelongsTo
    {
        return $this->belongsTo(KategoriProduk::class, 'id_kategori', 'id_kategori');
    }
}
