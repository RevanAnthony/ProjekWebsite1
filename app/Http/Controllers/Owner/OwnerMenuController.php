<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Produk;
use App\Models\KategoriProduk;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OwnerMenuController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('q');
        $cat    = $request->query('cat');

        $query = Produk::query()->orderBy('id_kategori')->orderBy('nama_produk');

        if ($search) {
            $query->where('nama_produk', 'like', "%{$search}%");
        }

        if ($cat) {
            // cat bisa slug atau id
            $query->where(function ($q) use ($cat) {
                $q->whereHas('kategori', function ($qq) use ($cat) {
                    $qq->where('slug', $cat);
                })->orWhere('id_kategori', $cat);
            });
        }

        $produk    = $query->get();
        $kategori  = KategoriProduk::orderBy('id_kategori')->get();
        $katById   = $kategori->keyBy('id_kategori');

        return view('owner.menu.index', [
            'produk'   => $produk,
            'kategori' => $kategori,
            'katById'  => $katById,
        ]);
    }

    public function edit(Produk $produk)
    {
        $kategori = KategoriProduk::orderBy('id_kategori')->get();

        return view('owner.menu.edit', [
            'produk'   => $produk,
            'kategori' => $kategori,
        ]);
    }

    public function update(Request $request, Produk $produk)
    {
        $data = $request->validate([
            'nama_produk' => ['required', 'string', 'max:150'],
            'id_kategori' => ['required', 'exists:kategori_produk,id_kategori'],
            'harga'       => ['required', 'integer', 'min:0'],
            'level_pedas' => ['required', 'integer', 'min:0', 'max:3'],
            'stok'        => ['required', 'integer', 'min:0'],
            'deskripsi'   => ['nullable', 'string'],
            'url_gambar'  => ['nullable', 'string', 'max:255'],
        ]);

        $data['slug'] = Str::slug($data['nama_produk']);

        $produk->update($data);

        return redirect()
            ->route('owner.menu.index')
            ->with('success', 'Menu berhasil diperbarui.');
    }

    public function destroy(Produk $produk)
    {
        $produk->delete();

        return redirect()
            ->route('owner.menu.index')
            ->with('success', 'Menu berhasil dihapus.');
    }
}
    