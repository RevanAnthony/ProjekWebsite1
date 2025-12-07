<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produk;
use App\Models\KategoriProduk;
use App\Models\Pesanan;

class PageController extends Controller
{
    // ====== Halaman Beranda ======
    public function home()
    {
        return view('pages.home');
    }

    // ====== About -> arahkan ke Menu (sesuai penggunaanmu di beranda)
    public function about()
    {
        return redirect()->route('menu');
    }

    // ====== Halaman Kontak (GET) ======
    public function contact()
    {
        return view('pages.contact');
    }

    // ====== Proses form Kontak (POST) ======
    public function sendContact(Request $request)
    {
        // TODO: validasi & proses kirim/simpan jika diperlukan
        return redirect()
            ->route('contact')
            ->with('success', 'Pesan Anda telah terkirim!');
    }

    // ====== Halaman Menu (ambil dari DB) ======
    public function menu()
    {
        // Ambil semua produk dari database
        $produk = Produk::orderBy('id_produk')->get();

        // Kirim ke view pages/menu.blade.php
        return view('pages.menu', compact('produk'));
    }

    /**
     * Halaman chat user untuk 1 pesanan tertentu.
     * Dibuka dari tombol chat di halaman order-status.
     */
    public function userChat($orderId)
    {
        // Ambil pesanan + relasi pelanggan
        $order = Pesanan::with('pengguna')->findOrFail($orderId);

        return view('pages.user-chat', [
            'order' => $order,
            'user'  => $order->pengguna,   // bisa null kalau belum di-link
        ]);
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

    /**
     * Seed produk berdasarkan menuItems() ke tabel produk + kategori.
     * (Hanya dipakai sekali/kalau perlu sync data.)
     */
    public function seedProductsFromMenuItems()
    {
        // 1) Pastikan kategori ada dulu, dan ambil ID-nya dari database
        $kategoriSumber = [
            'bowls'  => 'Rice Bowls',
            'sides'  => 'Side Dishes',
            'drinks' => 'Drinks',
        ];

        $kategoriMap = [];

        foreach ($kategoriSumber as $slug => $nama) {
            $kategori = KategoriProduk::updateOrCreate(
                ['slug' => $slug],            // cari berdasarkan slug
                ['nama_kategori' => $nama]    // kalau belum ada, buat
            );

            $kategoriMap[$slug] = $kategori->id_kategori;
        }

        // 2) Seed produk dengan id_kategori yang benar (dari $kategoriMap)
        $rows  = $this->menuItems();
        $count = 0;

        // default kalau entah kenapa cat-nya tidak dikenal
        $defaultKategoriId = $kategoriMap['bowls'] ?? reset($kategoriMap);

        foreach ($rows as $r) {
            $slugKategori = $r['cat']; // 'bowls' / 'sides' / 'drinks'
            $idKategori   = $kategoriMap[$slugKategori] ?? $defaultKategoriId;

            Produk::updateOrCreate(
                ['slug' => $r['slug']], // unik per produk
                [
                    'id_kategori' => $idKategori,
                    'nama_produk' => $r['name'],
                    'level_pedas' => $r['heat'] ?? 0,
                    'harga'       => $r['price'],
                    'stok'        => 999,
                    'deskripsi'   => $r['desc'],
                    'url_gambar'  => $r['img'], // mis. images/red-tea-foto.jpg
                ]
            );
            $count++;
        }

        return response("Seeded/updated $count produk.", 200);
    }
}
