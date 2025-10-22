@extends('layouts.app')
@section('title','Menu — Golden Spice')

@section('content')
<section class="section page-menu">
  <div class="gs-container">
    <h1 class="section-title red">MENU GOLDEN SPICE</h1>
    <p class="section-sub">Pilih favoritmu. Kamu bisa filter kategori, cari nama menu, dan atur level pedas.</p>

    <div class="filters">
      {{-- Kategori --}}
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
          {{-- default 3 supaya semua item muncul saat load --}}
          <input id="spicyRange" type="range" min="0" max="3" step="1" value="3">
          <output id="spicyOut">0–3</output>
        </label>
      </div>
    </div>

    <div id="menuGrid" class="grid-3">
      @foreach($items as $it)
        <article class="menu-card heat-{{ $it['heat'] }}"
                 data-cat="{{ $it['cat'] }}"
                 data-heat="{{ $it['heat'] }}"
                 data-name="{{ strtolower($it['name']) }}"
                 {{-- kunci pencarian dinormalisasi agar "Rica-Rica" cocok dengan "rica rica" --}}
                 data-key="{{ strtolower(preg_replace('/[^a-z0-9]/i','', $it['name'])) }}">
          <img src="{{ asset($it['img']) }}" alt="{{ $it['name'] }}" class="menu-img">
          <h3 class="menu-name">{{ strtoupper($it['name']) }}</h3>
          <p class="muted">{{ $it['desc'] }}</p>
          <div class="price">Rp {{ number_format($it['price'],0,',','.') }}</div>
          <button class="gs-btn gs-btn--sm">Tambah <span class="chev">›</span></button>
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

  const clean = s => (s || '').toLowerCase().replace(/[^a-z0-9]/g,'');

  function apply() {
    let shown = 0;
    grid.querySelectorAll('.menu-card').forEach(card => {
      const ccat = card.dataset.cat || '';
      const heat = parseInt(card.dataset.heat || '0',10);
      const key  = card.dataset.key || clean(card.dataset.name || '');

      const passCat  = (cat === 'all') || (ccat === cat);
      const passText = !qKey || key.includes(qKey);
      const passHeat = heat <= maxHeat;

      const ok = passCat && passText && passHeat;
      card.style.display = ok ? '' : 'none';
      if (ok) shown++;
    });

    if (empty) empty.hidden = shown > 0;
  }

  // Klik kategori (pakai data-cat)
  tabs?.addEventListener('click', (e) => {
    const btn = e.target.closest('button[data-cat]');
    if (!btn) return;
    const val = btn.dataset.cat;
    cat = (val && val.length) ? val : 'all';
    tabs.querySelectorAll('button').forEach(b => b.classList.toggle('active', b === btn));
    apply();
  });

  // Pencarian dinormalisasi (hapus spasi/tanda baca)
  search?.addEventListener('input', e => {
    qKey = clean(e.target.value);
    apply();
  });

  // Slider pedas (batas maksimum)
  range?.addEventListener('input', e => {
    maxHeat = parseInt(e.target.value || '3', 10);
    if (out) out.textContent = `0–${maxHeat}`;
    apply();
  });

  if (out) out.textContent = `0–${maxHeat}`;
  apply();
})();
</script>
@endpush
