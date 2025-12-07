use App\Models\Produk;

public function menu()
{
    // ambil semua produk; kalau mau, filter/urutkan sesuai kategori
    $produk = Produk::orderBy('id_produk')->get();

    return view('pages.menu', compact('produk'));
}
