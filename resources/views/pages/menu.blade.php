@extends('layouts.app')
@section('title','Menu — Golden Spice')

@push('styles')
<style>
  /* Biar tinggi card rata & tombol di bawah semua */
  .menu-card{
    display:flex;
    flex-direction:column;
  }
  .menu-card .actions{
    margin-top:auto;
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:.75rem;
  }

  /* Toggle tampilan tombol Tambah <-> qty control */
  .menu-card .qty-wrap{display:none;align-items:center;gap:.5rem}
  .menu-card.has-qty .qty-wrap{display:flex}
  .menu-card.has-qty .add-to-cart{display:none}
  .qty-btn{width:36px;height:36px;border-radius:10px;border:1px solid #eee;background:#fff;font-weight:700}
  .qty-val{min-width:24px;text-align:center;font-weight:700}
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
          $cat = match((int)($p->id_kategori ?? 0)) { 1=>'bowls',2=>'sides',3=>'drinks', default=>'' };
          $img = $p->url_gambar && file_exists(public_path($p->url_gambar))
                 ? $p->url_gambar
                 : 'images/placeholder.jpg';
        @endphp

        <article class="menu-card"
                 data-id="{{ $p->id_produk }}"
                 data-pid="{{ $p->id_produk }}"
                 data-cat="{{ $cat }}"
                 data-heat="{{ (int)($p->level_pedas ?? 0) }}"
                 data-price="{{ (int) $p->harga }}"
                 data-name="{{ strtolower($p->nama_produk) }}">
          <img src="{{ asset($img) }}" alt="{{ $p->nama_produk }}" class="menu-img">

          <h3 class="menu-name">{{ strtoupper($p->nama_produk) }}</h3>
          <p class="muted">{{ $p->deskripsi }}</p>
          <div class="price">Rp {{ number_format($p->harga,0,',','.') }}</div>

          <div class="actions">
            {{-- Tombol Tambah (awal tampil) --}}
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

    <div id="emptyState" class="empty-state" hidden>
      Tidak ada item yang cocok dengan filter. Coba ubah kata kunci atau kategori.
    </div>
  </div>
</section>
@endsection

@push('scripts')
<script>
(() => {
  const grid   = document.getElementById('menuGrid');
  const tabs   = document.getElementById('catTabs');
  const search = document.getElementById('menuSearch');
  const range  = document.getElementById('spicyRange');
  const out    = document.getElementById('spicyOut');
  const empty  = document.getElementById('emptyState');

  let cat = 'all';
  let qKey = '';
  let maxHeat = parseInt((range?.value ?? range?.max ?? '3'), 10);
  const clean = s => (s||'').toLowerCase().replace(/[^a-z0-9]/g,'');

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
    if (empty) empty.hidden = shown > 0;
  }

  tabs?.addEventListener('click', (e)=>{
    const btn = e.target.closest('button[data-cat]');
    if(!btn) return;
    cat = btn.dataset.cat || 'all';
    tabs.querySelectorAll('button').forEach(b=>b.classList.toggle('active', b===btn));
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
