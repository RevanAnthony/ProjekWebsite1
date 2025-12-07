@extends('owner.layout')

@section('title','Edit Menu — Owner')

@section('content')
<h1 style="font-family:'Koulen';font-size:24px;margin-bottom:10px;">Edit Menu</h1>

<form method="POST" action="{{ route('owner.menu.update', $produk->id_produk) }}">
    @csrf
    @method('PUT')

    <div style="display:grid;grid-template-columns:1.2fr 1fr;gap:16px;max-width:720px;">
        <div>
            <div class="form-group">
                <label>Nama Menu</label>
                <input type="text" name="nama_produk" value="{{ old('nama_produk',$produk->nama_produk) }}" required>
            </div>
            <div class="form-group">
                <label>Deskripsi</label>
                <textarea name="deskripsi" rows="4" style="width:100%;border-radius:16px;border:1px solid #ddd;padding:10px 12px;font-size:13px;">{{ old('deskripsi',$produk->deskripsi) }}</textarea>
            </div>
            <div class="form-group">
                <label>URL Gambar</label>
                <input type="text" name="url_gambar" value="{{ old('url_gambar',$produk->url_gambar) }}">
            </div>
        </div>

        <div>
            <div class="form-group">
                <label>Kategori</label>
                <select name="id_kategori" style="width:100%;border-radius:999px;border:1px solid #ddd;padding:9px 12px;font-size:13px;">
                    @foreach($kategori as $kat)
                        <option value="{{ $kat->id_kategori }}"
                            {{ old('id_kategori',$produk->id_kategori) == $kat->id_kategori ? 'selected' : '' }}>
                            {{ $kat->nama_kategori }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>Harga</label>
                <input type="number" name="harga" value="{{ old('harga',$produk->harga) }}" required>
            </div>
            <div class="form-group">
                <label>Level Pedas (0–3)</label>
                <input type="number" name="level_pedas" value="{{ old('level_pedas',$produk->level_pedas) }}" min="0" max="3" required>
            </div>
            <div class="form-group">
                <label>Stok</label>
                <input type="number" name="stok" value="{{ old('stok',$produk->stok) }}" min="0" required>
            </div>
        </div>
    </div>

    <button type="submit"
            style="margin-top:16px;border:none;border-radius:999px;background:#D50505;color:#fff;padding:10px 20px;font-weight:600;cursor:pointer;">
        Simpan Perubahan
    </button>
</form>
@endsection
