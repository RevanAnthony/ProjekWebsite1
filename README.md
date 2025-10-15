
# Golden Spice (PHP + Bootstrap)

Website restoran sederhana seperti desain pada screenshot: beranda, menu, tentang, kontak, keranjang & checkout,
serta halaman admin untuk CRUD produk, kategori, alasan (why us), pengaturan hero, dan melihat pesanan.

## Cara Menjalankan (Localhost/XAMPP/Laragon)

1. Buat database MySQL bernama `golden_spice` lalu import file `schema.sql`.
2. Salin folder **golden-spice** ini ke `htdocs` (XAMPP) atau `www`/`public_html` sesuai environment.
3. Ubah `config.php` sesuai kredensial MySQL Anda.
4. Buka `http://localhost/golden-spice`.
5. Admin: buka `http://localhost/golden-spice/admin/login.php`
   - **Pertama kali**, sistem otomatis membuat akun admin default:
     - Username: `admin`
     - Password: `admin123`
   - Setelah login, ubah password pada menu *Settings* bila perlu.

## Struktur
- `index.php` – Beranda (hero + reasons + highlight produk)
- `menu.php` – Daftar menu (grid produk)
- `cart.php` – Keranjang
- `checkout.php` – Checkout (simpan pesanan ke DB)
- `about.php`, `contact.php`
- `admin/` – Login + dashboard & CRUD (products, categories, reasons, settings), orders
- `uploads/` – Folder upload gambar produk
- `assets/` – CSS + gambar

## Catatan
- Gambar pada `assets/img` hanyalah placeholder. Ganti dengan gambar dari Google/Unsplash sesuai kebutuhan.
- Semua query menggunakan prepared statements.
- Untuk production, pastikan:
  - Mengaktifkan HTTPS
  - Membatasi ukuran & tipe file upload
  - Melindungi folder `admin/` (misal `.htaccess`), dan mengganti password default.
