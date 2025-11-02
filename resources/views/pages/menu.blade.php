@extends('layouts.app')
@section('title','Menu — Golden Spice')

@push('styles')
  {{-- Google Material Icons untuk ikon cart --}}
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
  <style>
    /* Qty controls */
    .actions{display:flex; align-items:center; gap:10px; margin-top:12px}
    .qty-wrap{display:none; align-items:center; gap:12px; background:#fff; border:1px solid #eee; border-radius:10px; padding:6px 10px}
    .qty-btn{width:28px;height:28px;border-radius:8px;border:1px solid #eee;background:#fff;cursor:pointer;font-weight:700;line-height:1}
    .qty-val{min-width:20px;text-align:center;font-weight:700}
    /* Toggle: saat punya qty */
    .menu-card.has-qty .add-btn{display:none}
    .menu-card.has-qty .qty-wrap{display:inline-flex}

    /* Floating Cart (FAB) */
    .cart-fab{
      position:fixed; right:22px; bottom:22px; z-index:60;
      width:56px;height:56px;border-radius:9999px;border:0; cursor:pointer;
      background:#D50505; color:#fff; box-shadow:0 10px 20px rgba(0,0,0,.15);
      display:flex; align-items:center; justify-content:center;
    }
    .cart-fab .material-icons-outlined{font-size:28px}
    .cart-fab .badge{
      position:absolute; top:-6px; right:-6px; background:#fff; color:#D50505;
      border:2px solid #D50505; font-weight:800; min-width:22px; height:22px;
      font-size:12px; border-radius:9999px; display:flex; align-items:center; justify-content:center;
    }

    /* Cart Panel (bottom sheet) */
    .cart-panel{
      position:fixed; left:0; right:0; bottom:-100%; z-index:70;
      background:#fff; box-shadow:0 -14px 40px rgba(0,0,0,.18);
      border-radius:18px 18px 0 0; transition:bottom .25s ease;
      max-height:70vh; overflow:auto;
    }
    .cart-panel.open{ bottom:0; }
    .cart-head{display:flex; align-items:center; justify-content:space-between; padding:16px 18px; border-bottom:1px solid #eee}
    .cart-body{padding:10px 18px 18px}
    .cart-row{display:grid; grid-template-columns:1fr auto; gap:8px; align-items:center; padding:10px 0; border-bottom:1px dashed #eee}
    .cart-row .name{font-weight:700}
    .cart-row .sum{font-variant-numeric:tabular-nums}
    .cart-empty{padding:14px; color:#666}
    .cart-foot{position:sticky; bottom:0; background:#fff; border-top:1px solid #eee; padding:12px 18px; display:flex; align-items:center; justify-content:space-between; gap:14px}
    .cart-total{font-weight:800; font-size:18px}
    .btn-clear{background:#fff;border:1px solid #eee;border-radius:10px;padding:10px 12px;cursor:pointer}
  </style>
@endpush

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
          <input id="spicyRange" type="range" min="0" max="3" step="1" value="3">
          <output id="spicyOut">0–3</output>
        </label>
      </div>
    </div>

    <div id="menuGrid" class="grid-3">
      @foreach($items as $it)
        <article class="menu-card heat-{{ $it['heat'] }}"
                 data-id="{{ $it['slug'] }}"
                 data-cat="{{ $it['cat'] }}"
                 data-heat="{{ $it['heat'] }}"
                 data-name="{{ strtolower($it['name']) }}"
                 data-key="{{ strtolower(preg_replace('/[^a-z0-9]/i','', $it['name'])) }}"
                 data-price="{{ $it['price'] }}">
          <img src="{{ asset($it['img']) }}" alt="{{ $it['name'] }}" class="menu-img">
          <h3 class="menu-name">{{ strtoupper($it['name']) }}</h3>
          <p class="muted">{{ $it['desc'] }}</p>
          <div class="price">Rp {{ number_format($it['price'],0,',','.') }}</div>

          <div class="actions">
            <button class="gs-btn gs-btn--sm add-btn">Tambah <span class="chev">›</span></button>
            <div class="qty-wrap">
              <button class="qty-btn dec" aria-label="Kurangi">−</button>
              <span class="qty-val">1</span>
              <button class="qty-btn inc" aria-label="Tambah">+</button>
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

{{-- Floating Cart button --}}
<button id="cartFab" class="cart-fab" aria-label="Lihat Keranjang">
  <span class="material-icons-outlined">shopping_cart</span>
  <span class="badge" id="cartBadge">0</span>
</button>

{{-- Cart panel --}}
<div id="cartPanel" class="cart-panel" aria-hidden="true">
  <div class="cart-head">
    <strong>Keranjang</strong>
    <button class="btn-clear" id="cartClose">Tutup</button>
  </div>
  <div class="cart-body">
    <div id="cartList" class="cart-empty">Keranjang masih kosong.</div>
  </div>
  <div class="cart-foot">
    <button class="btn-clear" id="cartClear">Kosongkan</button>
    <div class="cart-total">Total: <span id="cartTotal">Rp 0</span></div>
  </div>
</div>
@endsection

@push('scripts')
<script>
(() => {
  // ---------- Elemen dasar ----------
  const grid   = document.getElementById('menuGrid');
  const tabs   = document.getElementById('catTabs');
  const search = document.getElementById('menuSearch');
  const range  = document.getElementById('spicyRange');
  const out    = document.getElementById('spicyOut');
  const empty  = document.getElementById('emptyState');

  const fab    = document.getElementById('cartFab');
  const badge  = document.getElementById('cartBadge');
  const panel  = document.getElementById('cartPanel');
  const cartList  = document.getElementById('cartList');
  const cartTotal = document.getElementById('cartTotal');
  const cartClose = document.getElementById('cartClose');
  const cartClear = document.getElementById('cartClear');

  // ---------- State filter ----------
  let cat = 'all';
  let qKey = '';
  let maxHeat = parseInt((range?.value ?? range?.max ?? '3'), 10);

  // ---------- State cart ----------
  const STORE_KEY = 'gs_cart_v1';
  /** @type {Map<string,{id:string,name:string,price:number,qty:number}>} */
  const cart = new Map();

  const clean = s => (s || '').toLowerCase().replace(/[^a-z0-9]/g,'');
  const money = n => 'Rp ' + (n||0).toLocaleString('id-ID', {maximumFractionDigits:0});

  // Load cart from storage
  try {
    const raw = localStorage.getItem(STORE_KEY);
    if (raw) {
      const obj = JSON.parse(raw);
      Object.values(obj).forEach(it => cart.set(it.id, it));
    }
  } catch(_) {}

  function saveCart(){
    const obj = {};
    cart.forEach((v,k)=> obj[k]=v);
    localStorage.setItem(STORE_KEY, JSON.stringify(obj));
    refreshCartUI();
    syncCardsQtyFromCart();
  }

  // ---------- Filter ----------
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

  tabs?.addEventListener('click', (e) => {
    const btn = e.target.closest('button[data-cat]');
    if (!btn) return;
    const val = btn.dataset.cat;
    cat = (val && val.length) ? val : 'all';
    tabs.querySelectorAll('button').forEach(b => b.classList.toggle('active', b === btn));
    apply();
  });

  search?.addEventListener('input', e => { qKey = clean(e.target.value); apply(); });

  range?.addEventListener('input', e => {
    maxHeat = parseInt(e.target.value || '3', 10);
    if (out) out.textContent = `0–${maxHeat}`;
    apply();
  });

  if (out) out.textContent = `0–${maxHeat}`;

  // ---------- Card Qty ----------
  function getCardData(card){
    return {
      id: card.dataset.id,
      name: (card.querySelector('.menu-name')?.textContent || '').trim(),
      price: parseInt(card.dataset.price || '0', 10) || 0
    };
  }

  function setCardQty(card, qty){
    const val  = card.querySelector('.qty-val');
    if (qty > 0){
      card.classList.add('has-qty');
      if (val) val.textContent = qty;
    } else {
      card.classList.remove('has-qty');
      if (val) val.textContent = 1; // default view
    }
  }

  function syncCardsQtyFromCart(){
    grid.querySelectorAll('.menu-card').forEach(card=>{
      const id = card.dataset.id;
      const qty = cart.get(id)?.qty || 0;
      setCardQty(card, qty);
    });
  }

  // Delegasi event di grid
  grid?.addEventListener('click', (e)=>{
    const addBtn = e.target.closest('.add-btn');
    const incBtn = e.target.closest('.qty-btn.inc');
    const decBtn = e.target.closest('.qty-btn.dec');
    const card = e.target.closest('.menu-card');
    if (!card) return;

    const {id,name,price} = getCardData(card);

    if (addBtn || incBtn){
      const cur = cart.get(id)?.qty || 0;
      cart.set(id, {id,name,price,qty: cur+1});
      saveCart();
    } else if (decBtn){
      const cur = cart.get(id)?.qty || 0;
      const next = Math.max(0, cur-1);
      if (next === 0) cart.delete(id);
      else cart.set(id, {id,name,price,qty: next});
      saveCart();
    }
  });

  // ---------- Cart UI ----------
  function refreshCartUI(){
    let count = 0, total = 0;
    cart.forEach(it => { count += it.qty; total += it.qty * it.price; });
    badge.textContent = count;

    if (count === 0){
      cartList.className = 'cart-empty';
      cartList.textContent = 'Keranjang masih kosong.';
    } else {
      cartList.className = '';
      cartList.innerHTML = '';
      cart.forEach(it=>{
        const row = document.createElement('div');
        row.className = 'cart-row';
        row.innerHTML = `
          <div class="name">${it.name}</div>
          <div class="sum">${it.qty} × ${money(it.price)}</div>
        `;
        cartList.appendChild(row);
      });
    }
    cartTotal.textContent = money(total);
  }

  fab?.addEventListener('click', ()=>{
    panel.classList.add('open');
    panel.setAttribute('aria-hidden','false');
  });
  cartClose?.addEventListener('click', ()=>{
    panel.classList.remove('open');
    panel.setAttribute('aria-hidden','true');
  });
  cartClear?.addEventListener('click', ()=>{
    cart.clear();
    saveCart();
  });

  // ---------- Init ----------
  apply();
  refreshCartUI();
  syncCardsQtyFromCart();
})();
</script>
@endpush
