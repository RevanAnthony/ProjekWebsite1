{{-- resources/views/owner/menu/index.blade.php --}}
@extends('owner.layout')

@section('title','Manajemen Menu — Owner')

@push('styles')
<style>
    .om-page{
        /* wrapper halaman menu */
    }

    /* HEADER ATAS: JUDUL + SUBTITLE */
    .om-header{
        margin-bottom:18px;
    }
    .om-title{
        font-family:'Koulen',system-ui;
        letter-spacing:.04em;
        font-size:26px;
        margin-bottom:4px;
    }
    .om-sub{
        font-size:13px;
        color:#777;
    }

    /* SEARCH BAR LEBAR */
    .om-search{
        margin-top:18px;
        margin-bottom:18px;
    }
    .om-search-box{
        display:flex;
        align-items:center;
        gap:10px;
        background:#fff;
        border-radius:999px;
        padding:10px 18px;
        border:1px solid #eee;
        box-shadow:0 3px 12px rgba(0,0,0,.04);
    }
    .om-search-box span.icon{
        font-family:'Material Symbols Rounded';
        font-size:20px;
        color:#999;
    }
    .om-search-box input{
        border:none;
        outline:none;
        background:transparent;
        width:100%;
        font-size:14px;
    }

    /* KATEGORI + EDIT MODE DI KANAN */
    .om-toolbar{
        display:flex;
        justify-content:space-between;
        align-items:flex-start;
        gap:18px;
        margin-bottom:14px;
    }
    .om-kat-label{
        font-size:13px;
        font-weight:600;
        margin-bottom:8px;
    }

    .om-kat-tabs{
        display:flex;
        gap:10px;
        flex-wrap:wrap;
    }
    .om-kat-tab{
        min-width:90px;
        padding:10px 16px;
        border-radius:18px;
        border:1px solid #e5e5e5;
        background:#fff;
        display:flex;
        flex-direction:column;
        align-items:center;
        justify-content:center;
        gap:4px;
        font-size:12px;
        color:#d50505;
        text-decoration:none;
        box-shadow:0 2px 8px rgba(0,0,0,.03);
    }
    .om-kat-tab span.icon{
        font-family:'Material Symbols Rounded';
        font-size:20px;
    }
    .om-kat-tab.om-all{
        border-radius:20px;
    }
    .om-kat-tab.is-active{
        background:#D50505;
        border-color:#D50505;
        color:#fff;
        box-shadow:0 6px 18px rgba(213,5,5,.35);
    }

    /* EDIT MODE TOGGLE */
    .om-edit-mode{
        display:flex;
        align-items:center;
        gap:10px;
        font-size:13px;
        white-space:nowrap;
        margin-top:26px;
    }
    .om-toggle{
        position:relative;
        width:50px;
        height:24px;
        border-radius:999px;
        border:1px solid #ddd;
        background:#f5f5f5;
        padding:0;
        cursor:pointer;
        display:flex;
        align-items:center;
        transition:background .18s ease, border-color .18s ease;
    }
    .om-toggle-knob{
        width:20px;
        height:20px;
        border-radius:999px;
        background:#ffffff;
        box-shadow:0 2px 6px rgba(0,0,0,.18);
        transform:translateX(3px);
        transition:transform .18s ease;
    }
    .om-toggle.is-on{
        background:#D50505;
        border-color:#D50505;
    }
    .om-toggle.is-on .om-toggle-knob{
        transform:translateX(25px);
    }

    /* HEADER KOLOM */
    .om-table-head{
        margin-top:10px;
        margin-bottom:6px;
        font-size:12px;
        color:#888;
        display:grid;
        grid-template-columns:60px 90px minmax(0,1.7fr) 120px minmax(0,2fr) 140px 80px;
        padding:8px 18px;
        text-transform:uppercase;
        letter-spacing:.04em;
        font-weight:700;
    }
    .om-table-head > div{
        display:flex;
        align-items:center;
        justify-content:center;
    }
    .om-table-head > div:nth-child(3),
    .om-table-head > div:nth-child(4),
    .om-table-head > div:nth-child(5){
        justify-content:flex-start;
    }
    .om-table-head > div:nth-child(7){
        justify-content:flex-end;
    }

    /* SECTION HEADER KATEGORI */
    .om-section-row{
        display:flex;
        align-items:center;
        justify-content:space-between;
        background:#fff;
        border-radius:18px;
        padding:10px 18px;
        margin-bottom:8px;
        box-shadow:0 3px 10px rgba(0,0,0,.04);
    }
    .om-section-left{
        display:flex;
        align-items:center;
        gap:8px;
        font-size:14px;
        font-weight:600;
        color:#D50505;
    }
    .om-section-left span.icon{
        font-family:'Material Symbols Rounded';
        font-size:20px;
    }
    .om-section-right span{
        font-family:'Material Symbols Rounded';
        font-size:20px;
        color:#999;
    }

    /* ROW MENU */
    .om-list{
        margin-top:4px;
    }
    .om-row{
        display:grid;
        grid-template-columns:60px 90px minmax(0,1.7fr) 120px minmax(0,2fr) 140px 80px;
        gap:10px;
        padding:12px 18px;
        margin-bottom:10px;
        background:#fff;
        border-radius:22px;
        box-shadow:0 5px 18px rgba(0,0,0,.06);
        align-items:center;
        font-size:13px;
    }

    .om-no{
        color:#666;
        font-size:13px;
        text-align:center;
    }

    .om-img-cell{
        display:flex;
        justify-content:center;
    }
    .om-img{
        width:72px;
        height:72px;
        border-radius:18px;
        overflow:hidden;
        background:#f5f5f5;
    }
    .om-img img{
        width:100%;
        height:100%;
        object-fit:cover;
    }

    .om-main{
        display:flex;
        flex-direction:column;
        gap:2px;
    }
    .om-name{
        font-weight:600;
    }
    .om-cat{
        font-size:12px;
        color:#999;
    }

    .om-price{
        font-weight:700;
    }

    .om-desc{
        font-size:13px;
        color:#555;
    }

    .om-code{
        display:flex;
        flex-direction:column;
        align-items:flex-end;
        text-align:right;
        gap:2px;
    }
    .om-code-main{
        font-size:13px;
        font-weight:700;
        color:#222;
    }
    .om-code-id{
        font-size:11px;
        color:#999;
    }

    /* AKSI EDIT / TRASH DI KANAN */
    .om-actions{
        display:flex;
        justify-content:flex-end;
        align-items:center;
        gap:6px;
    }
    .om-btn-icon{
        width:32px;
        height:32px;
        border-radius:10px;
        border:none;
        display:grid;
        place-items:center;
        cursor:pointer;
        background:#fff;
        box-shadow:0 4px 10px rgba(0,0,0,.10);
    }
    .om-btn-icon span{
        font-family:'Material Symbols Rounded';
        font-size:18px;
    }
    .om-btn-icon.edit{ color:#ff7b00; }
    .om-btn-icon.delete{ color:#e11d48; }

    .om-empty{
        padding:32px 16px;
        text-align:center;
        font-size:14px;
        color:#777;
    }

    /* EDIT MODE EFFECT */
    .om-page:not(.is-edit-mode) .om-actions{
        display:none;
    }
    .om-page.is-edit-mode .om-row{
        border:1px solid #ffd0d0;
        box-shadow:0 8px 24px rgba(213,5,5,.16);
    }
</style>
@endpush

@section('content')

@php
    /**
     * @var \Illuminate\Support\Collection|\App\Models\Produk[] $produk
     * @var \Illuminate\Support\Collection|\App\Models\KategoriProduk[] $kategori
     */

    $catParam   = request('cat');
    $currentCat = null;

    if ($catParam) {
        $currentCat = $kategori->first(function ($k) use ($catParam) {
            return (string)$k->slug === (string)$catParam
                || (string)$k->id_kategori === (string)$catParam;
        });
    }

    /**
     * Helper kode produk:
     * - Kalau di DB sudah ada $item->kode_produk, pakai itu.
     * - Kalau belum, generate dari kategori:
     *   Ricebowl   -> RB
     *   Side Dish  -> SD
     *   Drink/Minum-> D
     *   lainnya    -> P
     *   + 2 digit dari id_produk.
     */
    $buildCode = function ($item) {
        if (!empty($item->kode_produk)) {
            return strtoupper($item->kode_produk);
        }

        $prefix  = 'P';
        $katName = strtolower(trim($item->kategori->nama_kategori ?? ''));

        if (strpos($katName, 'rice') !== false) {
            $prefix = 'RB';
        } elseif (strpos($katName, 'side') !== false) {
            $prefix = 'SD';
        } elseif (strpos($katName, 'drink') !== false || strpos($katName, 'minum') !== false) {
            $prefix = 'D';
        }

        $number = (string) ($item->id_produk ?? 0);
        $number = str_pad($number, 2, '0', STR_PAD_LEFT);

        return $prefix . $number;
    };
@endphp

<div class="om-page" id="ownerMenuPage">
    {{-- HEADER --}}
    <div class="om-header">
        <div class="om-title">Manajemen Menu</div>
        <div class="om-sub">Kelola menu yang tampil di aplikasi Golden Spice.</div>
    </div>

    {{-- SEARCH --}}
    <div class="om-search">
        <form method="GET" action="{{ route('owner.menu.index') }}">
            <div class="om-search-box">
                <span class="icon">search</span>
                <input type="text"
                       name="q"
                       placeholder="Cari menu..."
                       value="{{ request('q') }}">
            </div>
        </form>
    </div>

    {{-- KATEGORI + EDIT MODE --}}
    <div class="om-toolbar">
        <div class="om-kat-block">
            <div class="om-kat-label">Kategori Produkmu</div>
            <div class="om-kat-tabs">
                {{-- Tab Semua --}}
                <a href="{{ route('owner.menu.index', ['q' => request('q')]) }}"
                   class="om-kat-tab om-all {{ $catParam ? '' : 'is-active' }}">
                    <span class="icon">apps</span>
                    <span>Semua</span>
                </a>

                {{-- Tab per kategori --}}
                @foreach($kategori as $kat)
                    @php
                        $isActive = $catParam && (
                            (string)$kat->slug === (string)$catParam ||
                            (string)$kat->id_kategori === (string)$catParam
                        );
                    @endphp
                    <a href="{{ route('owner.menu.index', ['cat' => $kat->slug, 'q' => request('q')]) }}"
                       class="om-kat-tab {{ $isActive ? 'is-active' : '' }}">
                        <span class="icon">ramen_dining</span>
                        <span>{{ $kat->nama_kategori }}</span>
                    </a>
                @endforeach
            </div>
        </div>

        <div class="om-edit-mode">
            <span>Edit Mode</span>
            <button type="button"
                    class="om-toggle"
                    id="editModeToggle"
                    aria-pressed="false">
                <span class="om-toggle-knob"></span>
            </button>
        </div>
    </div>

    {{-- HEADER KOLOM --}}
    <div class="om-table-head">
        <div>No</div>
        <div>Gambar</div>
        <div>Nama Produk</div>
        <div>Harga</div>
        <div>Deskripsi</div>
        <div>Kode Produk</div>
        <div></div> {{-- kolom aksi --}}
    </div>

    {{-- SECTION HEADER KATEGORI --}}
    <div class="om-section-row">
        <div class="om-section-left">
            <span class="icon">restaurant</span>
            @if($currentCat)
                <span>{{ $currentCat->nama_kategori }} ({{ $produk->count() }})</span>
            @else
                <span>Semua Menu ({{ $produk->count() }})</span>
            @endif
        </div>
        <div class="om-section-right">
            <span>expand_more</span>
        </div>
    </div>

    {{-- LIST MENU --}}
    <div class="om-list">
        @forelse($produk as $i => $item)
            <div class="om-row">
                {{-- No --}}
                <div class="om-no">{{ $i + 1 }}</div>

                {{-- Gambar --}}
                <div class="om-img-cell">
                    <div class="om-img">
                        @if($item->url_gambar)
                            <img src="{{ asset($item->url_gambar) }}" alt="{{ $item->nama_produk }}">
                        @else
                            <img src="{{ asset('images/menu-placeholder.png') }}" alt="">
                        @endif
                    </div>
                </div>

                {{-- Nama + kategori --}}
                <div class="om-main">
                    <div class="om-name">{{ $item->nama_produk }}</div>
                    <div class="om-cat">
                        {{ $item->kategori->nama_kategori ?? '-' }}
                    </div>
                </div>

                {{-- Harga --}}
                <div class="om-price">
                    Rp{{ number_format((int)$item->harga, 0, ',', '.') }}
                </div>

                {{-- Deskripsi --}}
                <div class="om-desc">
                    {{ $item->deskripsi }}
                </div>

                {{-- Kode Produk (RB01 / SD01 / D01 / dst) + ID --}}
                <div class="om-code">
                    <span class="om-code-main">
                        {{ $buildCode($item) }}
                    </span>
                    <span class="om-code-id">
                        ID: {{ $item->id_produk }}
                    </span>
                </div>

                {{-- Aksi kanan (edit + delete) --}}
                <div class="om-actions">
                    <a href="{{ route('owner.menu.edit', $item->id_produk) }}"
                       class="om-btn-icon edit"
                       title="Edit menu">
                        <span>edit</span>
                    </a>

                    <form method="POST"
                          action="{{ route('owner.menu.destroy', $item->id_produk) }}"
                          onsubmit="return confirm('Hapus menu ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="om-btn-icon delete"
                                title="Hapus menu">
                            <span>delete</span>
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="om-empty">
                Belum ada menu yang cocok dengan filter / pencarian ini.
            </div>
        @endforelse
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const page   = document.getElementById('ownerMenuPage');
    const toggle = document.getElementById('editModeToggle');

    if (!page || !toggle) return;

    toggle.addEventListener('click', function () {
        const isOn = page.classList.toggle('is-edit-mode');
        toggle.classList.toggle('is-on', isOn);
        toggle.setAttribute('aria-pressed', isOn ? 'true' : 'false');
    });
});
</script>
@endpush
