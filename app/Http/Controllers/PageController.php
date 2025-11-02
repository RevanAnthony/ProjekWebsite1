<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PageController extends Controller
{
    // ====== Halaman Beranda ======
    public function home()
    {
        return view('pages.home');
    }

    // ====== Halaman Tentang Kami (About) ======
    public function about()
    {
        return view('pages.menu');
    }

    // ====== Halaman Kontak (GET) ======
    public function contact()
    {
        return view('pages.contact');
    }

    // ====== Proses form Kontak (POST) ======
    public function sendContact(Request $request)
    {
        // TODO: validasi & simpan/kirim email jika diperlukan
        return redirect()->route('contact')->with('success', 'Pesan Anda telah terkirim!');
    }

    // ====== Halaman Menu ======
    public function menu()
    {
        $items = $this->menuItems();

        // render ke view menu (BUKAN pages.about)
        return view('pages.menu', compact('items'));
    }

    /**
     * Kumpulan item menu (sementara hard-coded).
     * Nanti enak dipindah ke database / config.
     */
    private function menuItems(): array
    {
        return [
            // --- RICE BOWLS ---
            [
                'name'  => 'Nasi Ayam Sambal Bawang',
                'desc'  => 'Ayam Krispy dengan sambal bawang yang pedas nikmat!',
                'img'   => 'images/menu-sambal-bawang-foto.jpg',
                'price' => 25000, 'cat' => 'bowls', 'heat' => 3, 'slug' => 'nasi-ayam-sambal-bawang',
            ],
            [
                'name'  => 'Nasi Ayam Saus Spicy Mayo',
                'desc'  => 'Ayam Krispy dengan saus mayo yang gurih dan sedikit asam!',
                'img'   => 'images/menu-spicy-mayo-foto.jpg',
                'price' => 18000, 'cat' => 'bowls', 'heat' => 1, 'slug' => 'nasi-ayam-spicy-mayo',
            ],
            [
                'name'  => 'Nasi Ayam Saus Barbeque',
                'desc'  => 'Ayam Krispy dengan saus barbeque yang manis dan gurih!',
                'img'   => 'images/menu-barbeque-foto.jpg',
                'price' => 25000, 'cat' => 'bowls', 'heat' => 0, 'slug' => 'nasi-ayam-barbeque',
            ],
            [
                'name'  => 'Nasi Ayam Saus Mentai',
                'desc'  => 'Ayam Krispy dengan saus mentai yang sedikit pedas, gurih dan umami!',
                'img'   => 'images/menu-mentai-foto.jpg',
                'price' => 25000, 'cat' => 'bowls', 'heat' => 1, 'slug' => 'nasi-ayam-mentai',
            ],
            [
                'name'  => 'Nasi Ayam Sambal Ijo',
                'desc'  => 'Ayam Krispy dengan sambal hijau yang pedas gurih!',
                'img'   => 'images/menu-sambal-ijo-foto.jpg',
                'price' => 25000, 'cat' => 'bowls', 'heat' => 2, 'slug' => 'nasi-ayam-sambal-ijo',
            ],
            [
                'name'  => 'Paket Hemat 1',
                'desc'  => 'Paket hemat! 2 Ricebowl + minuman pilihanmu, lebih murah!',
                'img'   => 'images/hemat-foto.jpg',
                'price' => 60000, 'cat' => 'bowls', 'heat' => 0, 'slug' => 'paket-hemat-1',
            ],

            // --- SIDES ---
            [
                'name'  => 'Golden Crispy Skin',
                'desc'  => 'Kulit Ayam Krispy dengan garam dan lada yang klasik!',
                'img'   => 'images/crispy-skin-foto.png',
                'price' => 18000, 'cat' => 'sides', 'heat' => 0, 'slug' => 'golden-crispy-skin',
            ],
            [
                'name'  => 'Golden Saucy Skin',
                'desc'  => 'Kulit Ayam Krispy dengan saus pilihanmu!',
                'img'   => 'images/saucy-skin-foto.png',
                'price' => 18000, 'cat' => 'sides', 'heat' => 0, 'slug' => 'golden-saucy-skin',
            ],
            [
                'name'  => 'Golden Saucy Fries',
                'desc'  => 'Kentang Goreng Krispy dengan saus pilihanmu!',
                'img'   => 'images/saucy-fries-foto.jpg',
                'price' => 18000, 'cat' => 'sides', 'heat' => 0, 'slug' => 'golden-saucy-fries',
            ],
            [
                'name'  => 'Golden Strips',
                'desc'  => 'Strips Ayam Krispy dengan garam dan lada yang klasik!',
                'img'   => 'images/strips-foto.png',
                'price' => 18000, 'cat' => 'sides', 'heat' => 0, 'slug' => 'golden-strips',
            ],
            [
                'name'  => 'Golden Tempe Bites',
                'desc'  => 'Tempe Goreng Krispy dengan garam dan lada yang klasik!',
                'img'   => 'images/tempe-foto.png',
                'price' => 18000, 'cat' => 'sides', 'heat' => 0, 'slug' => 'golden-tempe-bites',
            ],
            [
                'name'  => 'Golden Sausage Pops',
                'desc'  => 'Sosis Goreng Krispy dengan garam dan lada yang klasik!',
                'img'   => 'images/sausage-foto.png',
                'price' => 22000, 'cat' => 'sides', 'heat' => 0, 'slug' => 'golden-sausage-pops',
            ],

            // --- DRINKS ---
            [
                'name'  => 'Green Go',
                'desc'  => 'Minuman soda menyegarkan dengan rasa melon.',
                'img'   => 'images/green-go-foto.jpg',
                'price' => 19000, 'cat' => 'drinks', 'heat' => 0, 'slug' => 'green-go',
            ],
            [
                'name'  => 'Red Stop',
                'desc'  => 'Minuman soda menyegarkan dengan rasa stroberi.',
                'img'   => 'images/red-stop-foto.jpg',
                'price' => 19000, 'cat' => 'drinks', 'heat' => 0, 'slug' => 'red-stop',
            ],
            [
                'name'  => 'Yellow Wait',
                'desc'  => 'Minuman soda menyegarkan dengan rasa jeruk.',
                'img'   => 'images/yellow-wait-foto.jpg',
                'price' => 19000, 'cat' => 'drinks', 'heat' => 0, 'slug' => 'yellow-wait',
            ],
            [
                'name'  => 'Red Ant Tea',
                'desc'  => 'Teh leci klasik yang manis dan menyegarkan.',
                'img'   => 'images/red-tea-foto.jpg',
                'price' => 10000, 'cat' => 'drinks', 'heat' => 0, 'slug' => 'red-ant-tea',
            ],
            [
                'name'  => 'Blue Ant Tea',
                'desc'  => 'Teh bunga telang dengan perasan lemon segar.',
                'img'   => 'images/blue-tea-foto.jpg',
                'price' => 10000, 'cat' => 'drinks', 'heat' => 0, 'slug' => 'blue-ant-tea',
            ],
            [
                'name'  => 'Black Ant Tea',
                'desc'  => 'Teh original klasik yang menyegarkan.',
                'img'   => 'images/black-tea-foto.jpg',
                'price' => 10000, 'cat' => 'drinks', 'heat' => 0, 'slug' => 'black-ant-tea',
            ],
        ];
    }
}
