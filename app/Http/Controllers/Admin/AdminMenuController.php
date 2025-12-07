<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Produk;
use App\Models\KategoriProduk;
use Illuminate\Http\Request;

class AdminMenuController extends Controller
{
    public function index(Request $request)
    {
        // kategori dari query string, default 'all'
        $kat = $request->query('kategori', 'all');

        $categories = KategoriProduk::orderBy('nama_kategori')->get();

       $query = Produk::orderBy('nama_produk');
if ($kat !== 'all') {
    $query->where('id_kategori', $kat);
}

$products = $query->get();

return view('admin.menu.index', [
    'categories'       => $categories,
    'currentKategori'  => $kat,
    'products'         => $products,
]);


    }
}
