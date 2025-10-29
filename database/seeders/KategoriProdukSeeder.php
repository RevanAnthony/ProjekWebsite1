<?php

namespace Database\Seeders;

use App\Models\KategoriProduk;
use Illuminate\Database\Seeder;

class KategoriProdukSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['nama_kategori' => 'Rice Bowls',  'slug' => 'bowls', 'deskripsi' => null],
            ['nama_kategori' => 'Side Dishes', 'slug' => 'sides', 'deskripsi' => null],
            ['nama_kategori' => 'Drinks',      'slug' => 'drinks','deskripsi' => null],
        ];

        foreach ($data as $row) {
            KategoriProduk::updateOrCreate(['slug'=>$row['slug']], $row);
        }
    }
}
