<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PageController extends Controller
{
    public function home()    { return view('pages.home'); }
    public function contact() { return view('pages.contact'); }

    // Route /about akan memakai method ini
    public function menu()
    {
        $items = [
            // ===== Bowls (12)
            ['name'=>'Sambal Bawang',    'slug'=>'sambal-bawang', 'price'=>25000, 'heat'=>3, 'cat'=>'bowls',  'img'=>'images/menu-sambal-bawang.jpg', 'desc'=>'Ayam krispi geprek dengan sambal bawang pedas nendang.'],
            ['name'=>'Sambal Ijo',       'slug'=>'sambal-ijo',    'price'=>25000, 'heat'=>2, 'cat'=>'bowls',  'img'=>'images/menu-sambal-ijo.jpg',    'desc'=>'Ayam krispi dengan sambal ijo segar khas Nusantara.'],
            ['name'=>'Spicy Mayo',       'slug'=>'spicy-mayo',    'price'=>27000, 'heat'=>1, 'cat'=>'bowls',  'img'=>'images/menu-spicy-mayo.jpg',    'desc'=>'Saus mentai ala Jepang, creamy dan gurih, di-torch.'],
            ['name'=>'Balado',           'slug'=>'balado',        'price'=>26000, 'heat'=>2, 'cat'=>'bowls',  'img'=>'images/placeholder.jpg',        'desc'=>'Pedas gurih balado khas Minang, meresap dan nagih.'],
            ['name'=>'Rica-Rica',        'slug'=>'rica-rica',     'price'=>27000, 'heat'=>3, 'cat'=>'bowls',  'img'=>'images/placeholder.jpg',        'desc'=>'Cabai rica harum, pedas berlapis namun seimbang.'],
            ['name'=>'Sambal Matah',     'slug'=>'sambal-matah',  'price'=>27000, 'heat'=>3, 'cat'=>'bowls',  'img'=>'images/placeholder.jpg',        'desc'=>'Matah Bali segar: bawang, serai, jeruk, pedas segar.'],
            ['name'=>'Blackpepper',      'slug'=>'blackpepper',   'price'=>26000, 'heat'=>1, 'cat'=>'bowls',  'img'=>'images/placeholder.jpg',        'desc'=>'Lada hitam aromatik, gurih-manis dengan sensasi hangat.'],
            ['name'=>'Teriyaki',         'slug'=>'teriyaki',      'price'=>26000, 'heat'=>0, 'cat'=>'bowls',  'img'=>'images/placeholder.jpg',        'desc'=>'Manis-gurih ala Jepang, cocok untuk yang tidak suka pedas.'],
            ['name'=>'BBQ',              'slug'=>'bbq',           'price'=>26000, 'heat'=>1, 'cat'=>'bowls',  'img'=>'images/placeholder.jpg',        'desc'=>'Smokey BBQ, gurih manis, bikin nafsu makan naik.'],
            ['name'=>'Honey Garlic',     'slug'=>'honey-garlic',  'price'=>26000, 'heat'=>0, 'cat'=>'bowls',  'img'=>'images/placeholder.jpg',        'desc'=>'Madu & bawang, manis aromatik, ramah anak.'],
            ['name'=>'Salted Egg',       'slug'=>'salted-egg',    'price'=>28000, 'heat'=>1, 'cat'=>'bowls',  'img'=>'images/placeholder.jpg',        'desc'=>'Gurih telur asin creamy, tekstur tebal memuaskan.'],
            ['name'=>'Korean Gochujang', 'slug'=>'gochujang',     'price'=>28000, 'heat'=>2, 'cat'=>'bowls',  'img'=>'images/placeholder.jpg',        'desc'=>'Pedas manis gochujang khas Korea.'],

            // ===== Sides (3)
            ['name'=>'Nasi + Telur',     'slug'=>'nasi-telur',    'price'=>12000, 'heat'=>0, 'cat'=>'sides',  'img'=>'images/placeholder.jpg',        'desc'=>'Nasi hangat dengan telur mata sapi.'],
            ['name'=>'French Fries',     'slug'=>'fries',         'price'=>15000, 'heat'=>0, 'cat'=>'sides',  'img'=>'images/placeholder.jpg',        'desc'=>'Kentang goreng renyah untuk teman makan.'],
            ['name'=>'Chicken Skin',     'slug'=>'chicken-skin',  'price'=>15000, 'heat'=>1, 'cat'=>'sides',  'img'=>'images/placeholder.jpg',        'desc'=>'Kulit ayam crispy, gurih dan nagih.'],

            // ===== Drinks (3)
            ['name'=>'Iced Tea',         'slug'=>'iced-tea',      'price'=>8000,  'heat'=>0, 'cat'=>'drinks', 'img'=>'images/placeholder.jpg',        'desc'=>'Teh dingin menyegarkan.'],
            ['name'=>'Lemon Tea',        'slug'=>'lemon-tea',     'price'=>10000, 'heat'=>0, 'cat'=>'drinks', 'img'=>'images/placeholder.jpg',        'desc'=>'Teh lemon segar, manis-asam pas.'],
            ['name'=>'Mineral Water',    'slug'=>'mineral-water', 'price'=>6000,  'heat'=>0, 'cat'=>'drinks', 'img'=>'images/placeholder.jpg',        'desc'=>'Air mineral dingin.'],
        ];

        // kirim $items ke view about (karena about.blade.php yang berisi listing menu)
        return view('pages.about', compact('items'));
    }

    // (opsional) kalau kamu tidak pakai halaman "tentang kami" terpisah, method about() boleh dihapus.
}
