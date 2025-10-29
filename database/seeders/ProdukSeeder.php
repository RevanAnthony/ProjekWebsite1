<?php

namespace Database\Seeders;

use App\Models\KategoriProduk;
use App\Models\Produk;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProdukSeeder extends Seeder
{
    public function run(): void
    {
        $catIds = KategoriProduk::pluck('id_kategori','slug'); // ['bowls'=>1,'sides'=>2,'drinks'=>3]

        $items = [
            // ===== Bowls (12)
            ['cat'=>'bowls','nama'=>'Sambal Bawang','harga'=>25000,'heat'=>3,'img'=>'images/menu-sambal-bawang.jpg','desc'=>'Ayam krispi geprek dengan sambal bawang pedas nendang.'],
            ['cat'=>'bowls','nama'=>'Sambal Ijo','harga'=>25000,'heat'=>2,'img'=>'images/menu-sambal-ijo.jpg','desc'=>'Ayam krispi dengan sambal ijo segar khas Nusantara.'],
            ['cat'=>'bowls','nama'=>'Spicy Mayo','harga'=>27000,'heat'=>1,'img'=>'images/menu-spicy-mayo.jpg','desc'=>'Saus mentai ala Jepang, creamy dan gurih, di-torch.'],
            ['cat'=>'bowls','nama'=>'Balado','harga'=>26000,'heat'=>2,'img'=>'images/placeholder.jpg','desc'=>'Pedas gurih balado khas Minang, meresap dan nagih.'],
            ['cat'=>'bowls','nama'=>'Rica-Rica','harga'=>27000,'heat'=>3,'img'=>'images/placeholder.jpg','desc'=>'Cabai rica harum, pedas berlapis namun seimbang.'],
            ['cat'=>'bowls','nama'=>'Sambal Matah','harga'=>27000,'heat'=>3,'img'=>'images/placeholder.jpg','desc'=>'Matah Bali segar: bawang, serai, jeruk, pedas segar.'],
            ['cat'=>'bowls','nama'=>'Blackpepper','harga'=>26000,'heat'=>1,'img'=>'images/placeholder.jpg','desc'=>'Lada hitam aromatik, gurih-manis dengan sensasi hangat.'],
            ['cat'=>'bowls','nama'=>'Teriyaki','harga'=>26000,'heat'=>0,'img'=>'images/placeholder.jpg','desc'=>'Manis-gurih ala Jepang, cocok untuk yang tidak suka pedas.'],
            ['cat'=>'bowls','nama'=>'BBQ','harga'=>26000,'heat'=>1,'img'=>'images/placeholder.jpg','desc'=>'Smokey BBQ, gurih manis, bikin nafsu makan naik.'],
            ['cat'=>'bowls','nama'=>'Honey Garlic','harga'=>26000,'heat'=>0,'img'=>'images/placeholder.jpg','desc'=>'Madu & bawang, manis aromatik, ramah anak.'],
            ['cat'=>'bowls','nama'=>'Salted Egg','harga'=>28000,'heat'=>1,'img'=>'images/placeholder.jpg','desc'=>'Gurih telur asin creamy, tekstur tebal memuaskan.'],
            ['cat'=>'bowls','nama'=>'Korean Gochujang','harga'=>28000,'heat'=>2,'img'=>'images/placeholder.jpg','desc'=>'Pedas manis gochujang khas Korea.'],

            // ===== Sides (3)
            ['cat'=>'sides','nama'=>'Nasi + Telur','harga'=>12000,'heat'=>0,'img'=>'images/placeholder.jpg','desc'=>'Nasi hangat dengan telur mata sapi.'],
            ['cat'=>'sides','nama'=>'French Fries','harga'=>15000,'heat'=>0,'img'=>'images/placeholder.jpg','desc'=>'Kentang goreng renyah untuk teman makan.'],
            ['cat'=>'sides','nama'=>'Chicken Skin','harga'=>15000,'heat'=>1,'img'=>'images/placeholder.jpg','desc'=>'Kulit ayam crispy, gurih dan nagih.'],

            // ===== Drinks (3)
            ['cat'=>'drinks','nama'=>'Iced Tea','harga'=>8000,'heat'=>0,'img'=>'images/placeholder.jpg','desc'=>'Teh dingin menyegarkan.'],
            ['cat'=>'drinks','nama'=>'Lemon Tea','harga'=>10000,'heat'=>0,'img'=>'images/placeholder.jpg','desc'=>'Teh lemon segar, manis-asam pas.'],
            ['cat'=>'drinks','nama'=>'Mineral Water','harga'=>6000,'heat'=>0,'img'=>'images/placeholder.jpg','desc'=>'Air mineral dingin.'],
        ];

        foreach ($items as $it) {
            Produk::updateOrCreate(
                ['slug' => Str::slug($it['nama'])],
                [
                    'id_kategori' => $catIds[$it['cat']],
                    'nama_produk' => $it['nama'],
                    'slug'        => Str::slug($it['nama']),
                    'harga'       => $it['harga'],
                    'stok'        => 999,
                    'level_pedas' => $it['heat'],
                    'url_gambar'  => $it['img'],
                    'deskripsi'   => $it['desc'],
                ]
            );
        }
    }
}
