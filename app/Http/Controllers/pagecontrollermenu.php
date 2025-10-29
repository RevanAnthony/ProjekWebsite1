use Illuminate\Support\Facades\DB;

public function menu()
{
    $items = DB::table('produk')
        ->join('kategori_produk','produk.id_kategori','=','kategori_produk.id_kategori')
        ->select(
            'produk.nama_produk as name',
            'produk.harga as price',
            'produk.deskripsi as desc',
            'produk.url_gambar as img',
            'kategori_produk.nama_kategori as cat'
        )
        ->get()
        ->map(function ($r) {
            // map ke struktur yang dipakai blade sekarang
            return [
                'name' => $r->name,
                'slug' => \Str::slug($r->name),
                'price'=> (int)$r->price,
                'heat' => 0, // nanti bisa tambahkan kolom heat di tabel produk kalau perlu
                'cat'  => $r->cat, // 'bowls' | 'sides' | 'drinks'
                'img'  => $r->img ?: 'images/placeholder.jpg',
                'desc' => $r->desc ?? '',
            ];
        })->toArray();

    return view('pages.menu', compact('items'));
}
