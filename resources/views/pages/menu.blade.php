@extends('layouts.app')
@section('title','Menu — Golden Spice')

@push('styles')
<style>
  /* Biar tinggi card rata & tombol di bawah semua */
  .menu-card{display:flex;flex-direction:column}
.menu-card .actions{
  margin-top:0; /* hapus auto, karena auto-nya dipindah ke .price */
  display:flex;
  align-items:center;
  justify-content:space-between;
  gap:.75rem;
}


  /* ===== Overlay di atas gambar: ribbon terlaris + rating pill ===== */
  .menu-media{
    position:relative;
    margin-bottom:14px;
    border-radius:14px;
    overflow:hidden; /* penting: biar hover image nggak bikin “bolong” */
  }
  .menu-media .menu-img{
    margin-bottom:0 !important;
    display:block;
    border-radius:0 !important; /* radius ikut wrapper */
  }

  /* Ribbon TERLARIS KE-x */
  .menu-ribbon{
    position:absolute;
    top:12px;
    left:-1px; /* nutup gap tipis saat hover */
    z-index:3;
    padding:8px 14px;
    font-size:12px;
    font-weight:900;
    letter-spacing:.4px;
    color:#fff;
    background:linear-gradient(90deg,#c7a12b,#f0c24d);
    border-radius:0 999px 999px 0;
    box-shadow:0 8px 18px rgba(0,0,0,.18);
  }

  /* Pill rating */
  .menu-rating{
    position:absolute;
    left:12px;
    bottom:12px;
    z-index:3;
    display:inline-flex;
    align-items:center;
    gap:8px;
    padding:8px 12px;
    border-radius:14px;
    background:rgba(255,255,255,.95);
    border:1px solid rgba(0,0,0,.06);
    box-shadow:0 8px 18px rgba(0,0,0,.12);
    backdrop-filter: blur(6px);
  }

  /* Material Symbols star */
  .menu-rating .star{
    color:#f59e0b;
    font-size:18px;
    line-height:1;
    font-variation-settings:'FILL' 1,'wght' 700,'opsz' 24;
  }

  /* Angka rating: bold tapi tidak terlalu heavy + size +1 */
  .menu-rating .val{
    font-weight:700;
    font-size:15px;   /* +1 dari 14px */
    line-height:1;
    color:#111;
    font-family:"Questrial",system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;
  }

  /* Angka detail order (dalam kurung): light + jelas */
  .menu-rating .cnt{
    opacity:1;
    color:#444;
    font-weight:400;
    font-size:13px;
    font-family:"Questrial",system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;
  }

  /* Nama menu merah */
  .menu-name{color:var(--red) !important;}

  /* Harga: Questrial + merah + bold + size +3 */
.menu-card .price{
  color:var(--red) !important;
  font-family:"Questrial",system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;
  font-weight:800;
  font-size:calc(1rem + 2px); /* -1px dari sebelumnya */
  margin-top:auto;           /* biar harga “nempel bawah” dan sejajar antar card */
  margin-bottom:10px;        /* jarak rapi sebelum tombol */
}


  /* Sort pills */
  .filters-right{display:flex;align-items:center;gap:12px;flex-wrap:wrap}
  .sort-pills{display:flex;gap:8px}

  /* Toggle tampilan tombol Tambah <-> qty control */
  .menu-card .qty-wrap{display:none;align-items:center;gap:.5rem}
  .menu-card.has-qty .qty-wrap{display:flex}
  .menu-card.has-qty .add-to-cart{display:none}
  .qty-btn{width:36px;height:36px;border-radius:10px;border:1px solid #eee;background:#fff;font-weight:700}
  .qty-val{min-width:24px;text-align:center;font-weight:700}

  /* ===== Pagination (1 page only) ===== */
  .gs-pagination{
    margin-top:22px;
    display:flex;
    justify-content:center;
    align-items:center;
    gap:10px;
  }
  .gs-pagination .pg-btn,
  .gs-pagination .pg-page{
    width:42px;
    height:42px;
    border-radius:14px;
    border:1px solid rgba(0,0,0,.08);
    background:rgba(255,255,255,.92);
    box-shadow:0 10px 22px rgba(0,0,0,.10);
    display:inline-flex;
    align-items:center;
    justify-content:center;
    cursor:pointer;
    transition:.15s ease;
  }
.gs-pagination .pg-page{
  font-family:"Questrial",system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;
  font-weight:700;
  color:#111;
  font-size:16px; /* +2px */
}

  .gs-pagination .pg-page.is-active{
    background:var(--red);
    color:#fff;
    border-color:transparent;
  }
  .gs-pagination .pg-btn .material-symbols-rounded{
    font-size:22px;
    font-variation-settings:'FILL' 0,'wght' 600,'opsz' 24;
    color:#111;
  }
  .gs-pagination .pg-btn[disabled]{
    opacity:.45;
    cursor:not-allowed;
    box-shadow:none;
  }
  .gs-pagination .pg-btn:focus-visible,
  .gs-pagination .pg-page:focus-visible{
    outline:3px solid rgba(220,38,38,.25);
    outline-offset:2px;
  }
</style>
@endpush

@section('content')
<section class="section page-menu">
  <div class="gs-container">
    <h1 class="section-title red">MENU GOLDEN SPICE</h1>
    <p class="section-sub">Pilih favoritmu. Kamu bisa filter kategori, cari nama menu, dan atur level pedas.</p>

    {{-- Filters --}}
    <div class="filters">
      <div id="catTabs" class="pill-group" role="tablist" aria-label="Kategori">
        <button class="pill active" data-cat="">Semua</button>
        <button class="pill" data-cat="bowls">Rice Bowls</button>
        <button class="pill" data-cat="sides">Side Dishes</button>
        <button class="pill" data-cat="drinks">Drinks</button>
      </div>

      <div class="filters-right">
        <div id="sortTabs" class="pill-group sort-pills" role="tablist" aria-label="Urutkan">
          <button class="pill active" data-sort="default">Default</button>
          <button class="pill" data-sort="best">Terlaris</button>
        </div>

        <label class="search">
          <input id="menuSearch" type="search" placeholder="Cari menu…">
          <span class="icon">🔍</span>
        </label>

        <label class="spicy">
          <span>Pedas</span>
          <input id="spicyRange" type="range" min="0" max="3" step="1" value="3">
          <output id="spicyOut">0–3</output>
        </label>
      </div>
    </div>

    <div id="menuGrid" class="grid-3">
      @foreach($produk as $p)
        @php
          // mapping kategori untuk filter (tetap dipakai untuk tabs)
          $cat = match((int)($p->id_kategori ?? 0)) { 1=>'bowls',2=>'sides',3=>'drinks', default=>'' };

          $img = $p->url_gambar && file_exists(public_path($p->url_gambar))
                 ? $p->url_gambar
                 : 'images/placeholder.jpg';

          $rank = isset($topRanks) ? ($topRanks[(int)($p->id_produk ?? 0)] ?? null) : null;

          // count distinct (sesuai request kamu)
          $orderCount = (int)($p->order_count ?? 0);

          // rating_avg diambil dari DB (AVG ulasan.rating)
          $ratingAvg = $p->rating_avg;
          $ratingText = ($ratingAvg === null) ? '—' : number_format((float)$ratingAvg, 1);

          // format Indonesia: 1.202
          $orderText = number_format((int)$orderCount, 0, ',', '.');
        @endphp

        <article class="menu-card"
                 data-id="{{ $p->id_produk }}"
                 data-pid="{{ $p->id_produk }}"
                 data-cat="{{ $cat }}"
                 data-heat="{{ (int)($p->level_pedas ?? 0) }}"
                 data-price="{{ (int) $p->harga }}"
                 data-name="{{ strtolower($p->nama_produk) }}"
                 data-sold="{{ $orderCount }}"
                 data-order="{{ $loop->index }}">

          <div class="menu-media">
            <img src="{{ asset($img) }}" alt="{{ $p->nama_produk }}" class="menu-img">

            @if($rank)
              <div class="menu-ribbon">TERLARIS KE-{{ $rank }}</div>
            @endif

            <div class="menu-rating" title="Dipesan {{ $orderText }}x">
              <span class="material-symbols-rounded star" aria-hidden="true">star</span>
              <span class="val">{{ $ratingText }}</span>
              <span class="cnt">({{ $orderText }})</span>
            </div>
          </div>

          <h3 class="menu-name">{{ strtoupper($p->nama_produk) }}</h3>
          <p class="muted">{{ $p->deskripsi }}</p>
          <div class="price">Rp {{ number_format($p->harga,0,',','.') }}</div>

          <div class="actions">
            {{-- Tombol Tambah (biarkan persis seperti koding kamu) --}}
            <button class="gs-btn gs-btn--sm add-to-cart"
                    data-add-to-cart="{{ $p->id_produk }}"
                    data-id="{{ $p->id_produk }}"
                    data-name="{{ $p->nama_produk }}"
                    data-price="{{ (int) $p->harga }}"
                    data-img="{{ asset($img) }}">
              Tambah <span class="chev">›</span>
            </button>

            {{-- Qty control (diswitch via .has-qty) --}}
            <div class="qty-wrap">
              <button class="qty-btn dec" data-qty-dec data-pid="{{ $p->id_produk }}">−</button>
              <span class="qty-val">0</span>
              <button class="qty-btn inc" data-qty-inc data-pid="{{ $p->id_produk }}">+</button>
            </div>
          </div>
        </article>
      @endforeach
    </div>

    {{-- Pagination (1 page only) --}}
    <nav class="gs-pagination" aria-label="Pagination">
      <button class="pg-btn" type="button" disabled aria-disabled="true" aria-label="Sebelumnya">
        <span class="material-symbols-rounded" aria-hidden="true">chevron_left</span>
      </button>

      <button class="pg-page is-active" type="button" aria-current="page">1</button>

      <button class="pg-btn" type="button" disabled aria-disabled="true" aria-label="Berikutnya">
        <span class="material-symbols-rounded" aria-hidden="true">chevron_right</span>
      </button>
    </nav>

    <div id="emptyState" class="empty-state" hidden>
      Tidak ada item yang cocok dengan filter. Coba ubah kata kunci atau kategori.
    </div>
  </div>
</section>
@endsection

@push('scripts')
<script>
(() => {
  const grid     = document.getElementById('menuGrid');
  const catTabs  = document.getElementById('catTabs');
  const sortTabs = document.getElementById('sortTabs');
  const search   = document.getElementById('menuSearch');
  const range    = document.getElementById('spicyRange');
  const out      = document.getElementById('spicyOut');
  const empty    = document.getElementById('emptyState');

  let cat = 'all';
  let sortMode = 'default';
  let qKey = '';
  let maxHeat = parseInt((range?.value ?? range?.max ?? '3'), 10);
  const clean = s => (s||'').toLowerCase().replace(/[^a-z0-9]/g,'');

  function applySort(){
    const cards = Array.from(grid.querySelectorAll('.menu-card'));
    cards.sort((a,b)=>{
      if(sortMode === 'best'){
        const sa = parseInt(a.dataset.sold || '0', 10);
        const sb = parseInt(b.dataset.sold || '0', 10);
        if(sb !== sa) return sb - sa;
      }
      const oa = parseInt(a.dataset.order || '0', 10);
      const ob = parseInt(b.dataset.order || '0', 10);
      return oa - ob;
    });
    cards.forEach(c => grid.appendChild(c));
  }

  function applyFilters(){
    let shown = 0;
    grid.querySelectorAll('.menu-card').forEach(card => {
      const passCat  = (cat==='all') || ((card.dataset.cat||'')===cat);
      const passText = !qKey || (clean(card.dataset.name||'').includes(qKey));
      const passHeat = (parseInt(card.dataset.heat||'0',10) <= maxHeat);
      const ok = passCat && passText && passHeat;
      card.style.display = ok ? '' : 'none';
      if (ok) shown++;
    });
    applySort();
    if (empty) empty.hidden = shown > 0;
  }

  catTabs?.addEventListener('click', (e)=>{
    const btn = e.target.closest('button[data-cat]');
    if(!btn) return;
    cat = btn.dataset.cat || 'all';
    catTabs.querySelectorAll('button').forEach(b=>b.classList.toggle('active', b===btn));
    applyFilters();
  });

  sortTabs?.addEventListener('click', (e)=>{
    const btn = e.target.closest('button[data-sort]');
    if(!btn) return;
    sortMode = btn.dataset.sort || 'default';
    sortTabs.querySelectorAll('button').forEach(b=>b.classList.toggle('active', b===btn));
    applyFilters();
  });

  search?.addEventListener('input', e => { qKey = clean(e.target.value); applyFilters(); });

  range?.addEventListener('input', e => {
    maxHeat = parseInt(e.target.value||'3',10);
    if(out) out.textContent = `0–${maxHeat}`;
    applyFilters();
  });

  if(out) out.textContent = `0–${maxHeat}`;
  applyFilters();
})();
</script>
@endpush
