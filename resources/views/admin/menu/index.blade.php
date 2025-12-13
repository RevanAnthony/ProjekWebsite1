{{-- resources/views/admin/menu/index.blade.php --}}
@extends('admin.layouts.panel')

@section('title', 'Manajemen Menu — Golden Spice')
@section('page-title', 'Manajemen Menu')
@section('page-subtitle', 'Kelola stok menu dan kirim catatan ke owner.')

@php
  // filter & search
  $currentKategori = $currentKategori ?? request('kategori', 'all');
  $search          = request('q');

  // label yang ditampilkan di pill dropdown
  $currentKategoriLabel = 'Semua kategori';
  if (isset($categories)) {
      foreach ($categories as $kat) {
          if ((string) $currentKategori === (string) $kat->id_kategori) {
              $currentKategoriLabel = $kat->nama_kategori;
              break;
          }
      }
  }
@endphp

@push('styles')

<style>
    .am-code{
        display:flex;
        flex-direction:column;
        gap:2px;
        align-items:flex-start;
    }
    .am-code-main{
        font-size:13px;
        font-weight:700;
    }
    .am-code-id{
        font-size:11px;
        color:#999;
    }
</style>

<style>
  .gs-menu-page{
    padding-bottom:40px;
  }

  /* ===== TOP BAR (search/filter bar) ===== */
  .gs-menu-topbar{
    background:#fff;
    border-radius:20px;
    box-shadow:0 14px 40px rgba(0,0,0,.06);
    padding:12px 18px;
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:16px;
    margin-bottom:18px;
  }
  .gs-menu-left{
    display:flex;
    align-items:center;
    gap:16px;
    flex:1;
  }
  .gs-menu-layout-btn{
    width:40px;
    height:40px;
    border-radius:12px;
    border:0;
    background:#ff4c39;
    color:#fff;
    display:flex;
    align-items:center;
    justify-content:center;
    cursor:pointer;
    box-shadow:0 8px 20px rgba(255,76,57,.4);
  }
  .gs-menu-layout-btn span{
    font-size:22px;
  }

  .gs-menu-filter-pill{
    border-radius:999px;
    border:0;
    padding:10px 20px;
    background:#fff3f2;
    color:#ff4c39;
    font-weight:800;
    font-size:14px;
    cursor:default;
  }

  .gs-menu-right{
    display:flex;
    align-items:center;
    gap:10px;
  }

  /* ===== CUSTOM DROPDOWN KATEGORI ===== */
  .gs-menu-dropdown{
    position:relative;
    display:inline-flex;
    align-items:center;
    gap:6px;
    padding:6px 12px;
    border-radius:999px;
    border:1px solid #eee;
    background:#fff;
    font-size:12px;
    font-weight:600;
    cursor:pointer;
    min-width:170px;
    transition:box-shadow .15s ease, border-color .15s ease, background .15s ease;
  }
  .gs-menu-dropdown:hover{
    box-shadow:0 6px 16px rgba(0,0,0,.10);
    border-color:#e0e0e0;
    background:#fff;
  }
  .gs-menu-dropdown-icon{
    font-size:18px;
    color:#777;
  }
  .gs-menu-dropdown-label{
    white-space:nowrap;
  }
  .gs-menu-dropdown-caret{
    font-size:18px;
    color:#999;
    margin-left:auto;
  }

  .gs-menu-dropdown-menu{
    position:absolute;
    top:115%;
    right:0;
    min-width:190px;
    background:#fff;
    border-radius:14px;
    box-shadow:0 14px 40px rgba(0,0,0,.18);
    padding:6px 0;
    z-index:100;
    display:none;
  }
  .gs-menu-dropdown.is-open .gs-menu-dropdown-menu{
    display:block;
  }
  .gs-menu-dropdown-item{
    width:100%;
    text-align:left;
    border:0;
    background:none;
    padding:7px 14px;
    font-size:12px;
    cursor:pointer;
    display:flex;
    align-items:center;
    justify-content:space-between;
  }
  .gs-menu-dropdown-item:hover{
    background:#fff5f4;
  }
  .gs-menu-dropdown-item.is-active{
    font-weight:700;
    color:#ff4c39;
  }
  .gs-menu-dropdown-item-check{
    font-size:16px;
    color:#ff4c39;
  }

  /* ===== SEARCH ===== */
  .gs-menu-search{
    display:flex;
    align-items:center;
    gap:6px;
    padding:6px 10px;
    border-radius:999px;
    border:1px solid #eee;
    background:#fff;
    font-size:12px;
  }
  .gs-menu-search span{
    font-size:18px;
    color:#888;
  }
  .gs-menu-search input{
    border:0;
    outline:none;
    font-size:12px;
    min-width:160px;
  }

  /* ===== GRID KARTU ===== */
  .gs-menu-grid{
    display:grid;
    grid-template-columns:repeat(auto-fill,minmax(210px,1fr));
    gap:18px;
  }

  .gs-menu-card{
    border:0;
    padding:0;
    margin:0;
    text-align:left;
    background:#fff;
    border-radius:20px;
    box-shadow:0 16px 40px rgba(0,0,0,.10);
    overflow:hidden;
    cursor:pointer;
    display:flex;
    flex-direction:column;
    min-height:230px;
    position:relative;
    transition:transform .15s ease, box-shadow .15s ease;
  }
  .gs-menu-card:hover{
    transform:translateY(-2px);
    box-shadow:0 20px 50px rgba(0,0,0,.16);
  }

  .gs-menu-card-image{
    width:100%;
    padding-top:70%;
    background:#f2f2f2 center center / cover no-repeat;
    position:relative;
  }

  .gs-menu-card-body{
    padding:10px 14px 12px;
    display:flex;
    flex-direction:column;
    gap:6px;
  }
  .gs-menu-card-name{
    font-size:14px;
    font-weight:700;
    color:#111;
    min-height:36px;
  }
  .gs-menu-card-price{
    font-size:13px;
    font-weight:700;
    text-align:right;
  }

  /* Overlay STOK HABIS di gambar */
  .gs-card-out-badge{
    position:absolute;
    inset:0;
    background:rgba(40, 40, 40, 0.55); /* abu gelap transparan */
    display:flex;
    align-items:center;
    justify-content:center;
    color:#fff;
    font-weight:900;
    font-size:18px;
    letter-spacing:.08em;
  }
  .gs-menu-card.is-out .gs-menu-card-image::after{
    content:'';
    position:absolute;
    inset:0;
    background:rgba(0, 0, 0, 0.30); /* overlay tipis di belakang */
  }
  .gs-menu-card.is-out .gs-card-out-badge{
    position:absolute;
  }

  /* ===== DRAWER (panel kanan) ===== */
  .gs-drawer-backdrop{
    position:fixed;
    inset:0;
    background:rgba(0,0,0,.25);
    display:flex;
    justify-content:flex-end;
    z-index:50;
    opacity:0;
    pointer-events:none;
    transition:opacity .18s ease;
  }
  .gs-drawer-backdrop.is-open{
    opacity:1;
    pointer-events:auto;
  }
  .gs-drawer{
    width:420px;
    max-width:100%;
    background:#fff;
    height:100%;
    display:flex;
    flex-direction:column;
  }
  .gs-drawer-header{
    background:#ff1f2a;
    color:#fff;
    padding:10px 18px;
    display:flex;
    align-items:center;
    justify-content:space-between;
  }
  .gs-drawer-back{
    border:0;
    background:none;
    color:#fff;
    font-weight:700;
    display:flex;
    align-items:center;
    gap:6px;
    cursor:pointer;
  }
  .gs-drawer-back span{
    font-size:20px;
  }
  .gs-drawer-title{
    font-weight:800;
    text-transform:uppercase;
    font-size:14px;
  }

  .gs-drawer-body{
    padding:16px 18px 20px;
    overflow-y:auto;
  }

  .gs-drawer-product-img{
    width:120px;
    height:120px;
    border-radius:14px;
    overflow:hidden;
    margin:8px auto 10px;
    background:#f2f2f2 center center / cover no-repeat;
  }
  .gs-drawer-product-name{
    text-align:center;
    font-weight:700;
    font-size:14px;
    margin-bottom:10px;
  }
  .gs-divider{
    margin:10px 0 16px;
    border:0;
    border-top:1px solid #eee;
  }

  .gs-info-row{
    display:flex;
    justify-content:space-between;
    font-size:13px;
    margin-bottom:6px;
  }
  .gs-info-row span:first-child{
    color:#777;
  }
  .gs-info-row span:last-child{
    font-weight:600;
  }

  .gs-field-label{
    font-size:13px;
    font-weight:700;
    margin:12px 0 6px;
  }

  /* toggle produk habis */
  .gs-toggle-row{
    display:flex;
    align-items:center;
    justify-content:space-between;
    margin-top:4px;
  }
  .gs-toggle-label{
    font-size:13px;
    font-weight:600;
  }
  .gs-toggle-hint{
    font-size:11px;
    color:#999;
  }
  .gs-switch{
    position:relative;
    width:46px;
    height:24px;
  }
  .gs-switch input{
    opacity:0;
    width:0;
    height:0;
  }
  .gs-switch-slider{
    position:absolute;
    cursor:pointer;
    inset:0;
    background:#ddd;
    border-radius:999px;
    transition:.18s;
  }
  .gs-switch-slider::before{
    content:'';
    position:absolute;
    height:18px;
    width:18px;
    left:3px; top:3px;
    background:#fff;
    border-radius:50%;
    transition:.18s;
    box-shadow:0 2px 4px rgba(0,0,0,.25);
  }
  .gs-switch input:checked + .gs-switch-slider{
    background:#26c15f;
  }
  .gs-switch input:checked + .gs-switch-slider::before{
    transform:translateX(20px);
  }

  textarea.gs-note{
    width:100%;
    min-height:90px;
    border-radius:10px;
    border:1px solid #dedede;
    padding:8px 10px;
    font-size:13px;
    resize:vertical;
    transition:background .15s ease, color .15s ease, border-color .15s ease;
  }
  textarea.gs-note:disabled{
    background:#e0e3ec;      /* lebih gelap saat OFF */
    color:#777;
    border-color:#c0c4d0;
  }
  textarea.gs-note:not(:disabled){
    background:#fff;          /* putih saat ON */
    color:#222;
  }

  .gs-drawer-footer{
    padding:14px 18px 18px;
    border-top:1px solid #f1f1f1;
  }
  .gs-btn-primary{
    width:100%;
    border:0;
    border-radius:999px;
    padding:10px 18px;
    background:#ff1f2a;
    color:#fff;
    font-weight:800;
    cursor:pointer;
  }

  /* ===== MODAL SUKSES ===== */
  .gs-modal-backdrop{
    position:fixed;
    inset:0;
    background:rgba(0,0,0,.35);
    display:flex;
    align-items:center;
    justify-content:center;
    z-index:60;
    opacity:0;
    pointer-events:none;
    transition:opacity .18s ease;
  }
  .gs-modal-backdrop.is-open{
    opacity:1;
    pointer-events:auto;
  }
  .gs-modal{
    background:#fff;
    border-radius:16px;
    padding:16px 20px 18px;
    min-width:260px;
    max-width:90vw;
    text-align:center;
    box-shadow:0 20px 50px rgba(0,0,0,.25);
  }
  .gs-modal-title{
    color:#ff1f2a;
    font-weight:900;
    font-size:20px;
    margin-bottom:4px;
  }
  .gs-modal-text{
    font-size:13px;
    margin-bottom:12px;
  }
  .gs-modal-actions{
    display:flex;
    gap:10px;
    justify-content:center;
  }
  .gs-modal-btn{
    flex:1;
    border-radius:999px;
    padding:8px 14px;
    font-size:13px;
    font-weight:700;
    cursor:pointer;
  }
  .gs-modal-btn-ghost{
    border:1px solid #ddd;
    background:#fff;
  }
  .gs-modal-btn-main{
    border:0;
    background:#ff1f2a;
    color:#fff;
  }

  @media (max-width:1024px){
    .gs-drawer{
      width:100%;
    }
    .gs-menu-topbar{
      flex-direction:column;
      align-items:flex-start;
    }
    .gs-menu-right{
      width:100%;
      justify-content:space-between;
    }
    .gs-menu-search{
      flex:1;
    }
    .gs-menu-search input{
      width:100%;
    }
  }
</style>
@endpush

@section('content')
<div class="gs-menu-page">

  {{-- TOP BAR --}}
  <form class="gs-menu-topbar" method="GET" action="{{ route('admin.menu.index') }}">
    <div class="gs-menu-left">
      <button type="button" class="gs-menu-layout-btn" aria-label="Grid layout">
        <span class="material-symbols-rounded">grid_view</span>
      </button>

      <button type="button" class="gs-menu-filter-pill">
        Semua ({{ $products->count() ?? 0 }})
      </button>
    </div>

    <div class="gs-menu-right">
      {{-- Custom dropdown kategori --}}
      <div class="gs-menu-dropdown" id="gsCategoryDropdown">
        <span class="material-symbols-rounded gs-menu-dropdown-icon">tune</span>
        <span class="gs-menu-dropdown-label" data-current-label>
          {{ $currentKategoriLabel }}
        </span>
        <span class="material-symbols-rounded gs-menu-dropdown-caret">expand_more</span>

        {{-- nilai yang dikirim ke backend --}}
        <input type="hidden" name="kategori" id="gsKategoriInput" value="{{ $currentKategori }}">

        <div class="gs-menu-dropdown-menu">
          <button type="button"
                  class="gs-menu-dropdown-item {{ $currentKategori === 'all' ? 'is-active' : '' }}"
                  data-value="all">
            <span>Semua kategori</span>
            @if($currentKategori === 'all')
              <span class="material-symbols-rounded gs-menu-dropdown-item-check">check</span>
            @endif
          </button>

          @isset($categories)
            @foreach($categories as $kat)
              @php $isActive = (string)$currentKategori === (string)$kat->id_kategori; @endphp
              <button type="button"
                      class="gs-menu-dropdown-item {{ $isActive ? 'is-active' : '' }}"
                      data-value="{{ $kat->id_kategori }}">
                <span>{{ $kat->nama_kategori }}</span>
                @if($isActive)
                  <span class="material-symbols-rounded gs-menu-dropdown-item-check">check</span>
                @endif
              </button>
            @endforeach
          @endisset
        </div>
      </div>

      {{-- Search --}}
      <div class="gs-menu-search">
        <span class="material-symbols-rounded">search</span>
        <input type="search"
               name="q"
               value="{{ $search }}"
               placeholder="Cari menu…">
      </div>
    </div>
  </form>

  {{-- GRID MENU --}}
  <div class="gs-menu-grid">
    @forelse ($products as $product)
      @php
        $id        = $product->id_produk ?? $product->id;
        $name      = $product->nama_produk ?? $product->nama ?? 'Nama Produk';
        $priceRaw  = $product->harga ?? $product->harga_jual ?? 0;
        $price     = (int) $priceRaw;
        $priceText = 'Rp '.number_format($price, 0, ',', '.');

        // url_gambar prioritas
        $imagePath = $product->url_gambar ?? $product->foto ?? $product->gambar ?? $product->image_url ?? null;
        $imageUrl  = $imagePath ? asset($imagePath) : asset('images/menu-placeholder.jpg');

        $stok      = $product->stok ?? $product->stok_tersedia ?? 0;
        $isOut     = isset($product->is_available)
                      ? !$product->is_available
                      : ($stok !== null && $stok <= 0);

        $kode      = $product->kode_produk ?? ('#'.str_pad($id, 3, '0', STR_PAD_LEFT));
        $terjual   = $product->terjual ?? $product->jumlah_terjual ?? 0;
        $note      = $product->note_owner ?? $product->catatan_owner ?? '';
      @endphp

      <button type="button"
              class="gs-menu-card {{ $isOut ? 'is-out' : '' }}"
              data-menu-card
              data-id="{{ $id }}"
              data-name="{{ e($name) }}"
              data-price="{{ $priceText }}"
              data-image="{{ $imageUrl }}"
              data-kode="{{ $kode }}"
              data-terjual="{{ $terjual }}"
              data-stok="{{ $stok }}"
              data-note="{{ e($note) }}"
              title="{{ $note ? 'Note dari Admin untuk Owner: '.$note : '' }}"
      >
        <div class="gs-menu-card-image" style="background-image:url('{{ $imageUrl }}')">
          @if ($isOut)
            <div class="gs-card-out-badge">STOK HABIS</div>
          @else
            <div class="gs-card-out-badge" style="display:none;">STOK HABIS</div>
          @endif
        </div>
        <div class="gs-menu-card-body">
          <div class="gs-menu-card-name">{{ $name }}</div>
          <div class="gs-menu-card-price">{{ $priceText }}</div>
        </div>
      </button>
    @empty
      <p>Belum ada produk pada menu.</p>
    @endforelse
  </div>
</div>

{{-- DRAWER: MANAJER MENU --}}
<div class="gs-drawer-backdrop" id="gsDrawer" aria-hidden="true">
  <div class="gs-drawer" role="dialog" aria-modal="true">
    <div class="gs-drawer-header">
      <button type="button" class="gs-drawer-back" data-drawer-close>
        <span class="material-symbols-rounded">arrow_back</span>
        <span>Back</span>
      </button>
      <div class="gs-drawer-title">MANAJER MENU</div>
      <div style="width:32px;"></div>
    </div>

    <form id="gsDrawerForm">
      <div class="gs-drawer-body">
        <div class="gs-drawer-product-img" id="gsDrawerImage"></div>
        <div class="gs-drawer-product-name" id="gsDrawerName">Nama Produk</div>

        <hr class="gs-divider">

        <div class="gs-info-row">
          <span>Kode / ID
</span>
          <span id="gsDrawerKode">#R001</span>
        </div>
        <div class="gs-info-row">
          <span>Jumlah Terjual</span>
          <span id="gsDrawerTerjual">0</span>
        </div>
        <div class="gs-info-row">
          <span>Jumlah Stok</span>
          <span id="gsDrawerStok">0 Tersedia</span>
        </div>
        <div class="gs-info-row">
          <span>Harga</span>
          <span id="gsDrawerHarga">Rp 0</span>
        </div>

        <div class="gs-field-label">Status stok</div>
        <div class="gs-toggle-row">
          <div>
            <div class="gs-toggle-label">Tandai produk habis</div>
            <div class="gs-toggle-hint">Aktifkan jika stok benar-benar habis, dan beri catatan singkat.</div>
          </div>
          <label class="gs-switch">
            <input type="checkbox" id="gsDrawerHabis">
            <span class="gs-switch-slider"></span>
          </label>
        </div>

        <div class="gs-field-label" style="margin-top:12px;">Catatan untuk Owner</div>
        <textarea class="gs-note"
                  id="gsDrawerNote"
                  placeholder="Contoh: Bahan baku habis, estimasi besok tersedia lagi."
                  disabled></textarea>
      </div>

      <div class="gs-drawer-footer">
        <button type="submit" class="gs-btn-primary">
          Set dan Kirim Note
        </button>
      </div>
    </form>
  </div>
</div>

{{-- MODAL SUKSES --}}
<div class="gs-modal-backdrop" id="gsSuccessModal" aria-hidden="true">
  <div class="gs-modal">
    <div class="gs-modal-title">SUKSES!</div>
    <div class="gs-modal-text">Status menu telah disimpan.</div>
    <div class="gs-modal-actions">
      <button type="button" class="gs-modal-btn gs-modal-btn-ghost" data-modal-close="only">
        Tidak
      </button>
      <button type="button" class="gs-modal-btn gs-modal-btn-main" data-modal-close="all">
        Balik
      </button>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
  /* ===== DRAWER LOGIC ===== */
  const drawerBackdrop = document.getElementById('gsDrawer');
  const successModal   = document.getElementById('gsSuccessModal');
  const form           = document.getElementById('gsDrawerForm');

  if (!drawerBackdrop || !form) return;

  let currentCard = null;

  const imgEl    = document.getElementById('gsDrawerImage');
  const nameEl   = document.getElementById('gsDrawerName');
  const kodeEl   = document.getElementById('gsDrawerKode');
  const terjualEl= document.getElementById('gsDrawerTerjual');
  const stokEl   = document.getElementById('gsDrawerStok');
  const hargaEl  = document.getElementById('gsDrawerHarga');
  const habisEl  = document.getElementById('gsDrawerHabis');
  const noteEl   = document.getElementById('gsDrawerNote');

  function openDrawerFromCard(card){
    currentCard = card;

    const name   = card.dataset.name || 'Nama Produk';
    const price  = card.dataset.price || 'Rp 0';
    const kode   = card.dataset.kode || '#R001';
    const terjual= card.dataset.terjual || '0';
    const stok   = card.dataset.stok || '0';
    const note   = card.dataset.note || '';
    const image  = card.dataset.image;

    if (imgEl)   imgEl.style.backgroundImage = image ? `url('${image}')` : 'none';
    if (nameEl)  nameEl.textContent = name;
    if (hargaEl) hargaEl.textContent = price;
    if (kodeEl)  kodeEl.textContent = kode;
    if (terjualEl) terjualEl.textContent = terjual;
    if (stokEl)  stokEl.textContent = `${stok} Tersedia`;

    const isOut = card.classList.contains('is-out');
    habisEl.checked = isOut;
    noteEl.disabled = !isOut;
    noteEl.value = note;

    drawerBackdrop.classList.add('is-open');
    drawerBackdrop.setAttribute('aria-hidden','false');
  }

  function closeDrawer(){
    drawerBackdrop.classList.remove('is-open');
    drawerBackdrop.setAttribute('aria-hidden','true');
  }

  document.querySelectorAll('[data-menu-card]').forEach(card => {
    card.addEventListener('click', () => openDrawerFromCard(card));
  });

  drawerBackdrop.querySelectorAll('[data-drawer-close]').forEach(btn => {
    btn.addEventListener('click', closeDrawer);
  });

  habisEl.addEventListener('change', () => {
    noteEl.disabled = !habisEl.checked;
    if (!habisEl.checked) {
      noteEl.value = '';
    }
  });

  form.addEventListener('submit', function (e) {
    e.preventDefault();
    if (!currentCard) return;

    const isOut = habisEl.checked;
    const note  = noteEl.value.trim();

    currentCard.classList.toggle('is-out', isOut);
    currentCard.dataset.note = note;

    const badge = currentCard.querySelector('.gs-card-out-badge');
    if (badge) {
      badge.style.display = isOut ? 'flex' : 'none';
    }

    if (isOut && note) {
      currentCard.title = 'Note dari Admin untuk Owner: ' + note;
    } else {
      currentCard.removeAttribute('title');
    }

    // TODO: sambungkan ke backend (AJAX / POST) untuk simpan status & note.

    successModal.classList.add('is-open');
    successModal.setAttribute('aria-hidden','false');
  });

  successModal.querySelectorAll('[data-modal-close]').forEach(btn => {
    btn.addEventListener('click', () => {
      successModal.classList.remove('is-open');
      successModal.setAttribute('aria-hidden','true');

      if (btn.getAttribute('data-modal-close') === 'all') {
        closeDrawer();
      }
    });
  });
})();

(function () {
  /* ===== DROPDOWN KATEGORI ===== */
  const dropdown   = document.getElementById('gsCategoryDropdown');
  if (!dropdown) return;

  const hiddenInput = document.getElementById('gsKategoriInput');
  const labelEl     = dropdown.querySelector('[data-current-label]');
  const menu        = dropdown.querySelector('.gs-menu-dropdown-menu');
  const form        = dropdown.closest('form');

  function closeMenu() {
    dropdown.classList.remove('is-open');
  }

  dropdown.addEventListener('click', function(e) {
    if (e.target.closest('.gs-menu-dropdown-menu')) {
      return;
    }
    dropdown.classList.toggle('is-open');
  });

  menu.querySelectorAll('.gs-menu-dropdown-item').forEach(function(item) {
    item.addEventListener('click', function(e) {
      e.preventDefault();
      e.stopPropagation();

      const value = this.dataset.value;
      const text  = this.querySelector('span').textContent.trim();

      if (hiddenInput) hiddenInput.value = value;
      if (labelEl)     labelEl.textContent = text;

      // update visual active
      menu.querySelectorAll('.gs-menu-dropdown-item').forEach(function(el) {
        el.classList.remove('is-active');
        const check = el.querySelector('.gs-menu-dropdown-item-check');
        if (check) check.remove();
      });
      this.classList.add('is-active');
      if (!this.querySelector('.gs-menu-dropdown-item-check')) {
        const icon = document.createElement('span');
        icon.className = 'material-symbols-rounded gs-menu-dropdown-item-check';
        icon.textContent = 'check';
        this.appendChild(icon);
      }

      closeMenu();
      if (form) form.submit();
    });
  });

  document.addEventListener('click', function(e) {
    if (!dropdown.contains(e.target)) {
      closeMenu();
    }
  });
})();
</script>
@endpush
