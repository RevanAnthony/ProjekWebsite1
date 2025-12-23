{{-- resources/views/owner/dashboard/index.blade.php --}}
@extends('owner.layout')

@section('title','Dashboard — Owner')

@push('styles')
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Questrial&display=swap" />
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,500,0,0" />

<style>
  /* ===== Scoped biar gak ketimpa golden.css ===== */
  .owner-dashboard, .owner-dashboard *{
    font-family:'Questrial',system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif !important;
  }

  /* Material Symbols fix (biar gak jadi teks "search/tune/close") */
  .material-symbols-rounded{
    font-family:'Material Symbols Rounded' !important;
    font-weight:normal;
    font-style:normal;
    line-height:1;
    letter-spacing:normal;
    text-transform:none;
    display:inline-block;
    white-space:nowrap;
    word-wrap:normal;
    direction:ltr;
    -webkit-font-feature-settings:'liga';
    -webkit-font-smoothing:antialiased;
    font-variation-settings:'FILL' 0,'wght' 500,'GRAD' 0,'opsz' 24;
  }

  .od-title{
    font-size:22px;
    font-weight:900;
    letter-spacing:.02em;
    margin:6px 0 18px;
    color:#111;
  }

  .od-grid-top{
    display:grid;
    grid-template-columns:repeat(4, minmax(0,1fr));
    gap:14px;
    margin-bottom:14px;
  }

  .od-grid-mid{
    display:grid;
    grid-template-columns: minmax(0,1.6fr) minmax(0,1fr);
    gap:14px;
    margin-top:10px;
  }

  .od-card{
    background:#fff;
    border-radius:18px;
    padding:14px 16px;
    box-shadow:0 10px 26px rgba(0,0,0,.05);
  }

  .od-kpi-label{ font-size:12px; color:#888; margin-bottom:6px; }
  .od-kpi-value{ font-size:18px; font-weight:900; color:#111; line-height:1.2; }
  .od-kpi-sub{ font-size:11px; color:#9a9a9a; margin-top:4px; }

  .od-section-title{
    font-size:14px;
    font-weight:900;
    margin:0 0 6px;
    color:#111;
  }
  .od-section-sub{
    font-size:11px;
    color:#9a9a9a;
    margin:0 0 10px;
  }

  /* ===== Chart ===== */
  .od-chart-wrap{ height:280px; }
  .od-chart-wrap canvas{ width:100% !important; height:100% !important; }

  /* ===== Menu Terlaris (warna selaras) ===== */
  .od-toplist{display:flex;flex-direction:column;margin-top:8px}

  .od-topitem{
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:12px;
    padding:12px 0;
    background:transparent;
    cursor:pointer;
    border-bottom:1px dashed rgba(0,0,0,.07);
    transition:.12s ease;
  }
  .od-topitem:last-child{border-bottom:none}
  .od-topitem:hover{opacity:.96}

  .od-rank{
    width:36px;height:36px;
    border-radius:12px;
    display:grid;place-items:center;
    font-weight:900;
    font-size:13px;
    flex:0 0 auto;
  }

  /* Palet selaras (no hijau/biru/tosca) */
  .od-rank.rank-1{background:#ff7a1a;color:#fff}
  .od-rank.rank-2{background:#ff9a3d;color:#fff}
  .od-rank.rank-3{background:#ffd1a3;color:#7a3b00}
  .od-rank.rank-4{background:#f3f4f6;color:#111}
  .od-rank.rank-5{background:#f3f4f6;color:#111}

  .od-topmeta{min-width:0;flex:1}
  .od-topname{
    font-weight:900;
    font-size:13px;
    color:#111;
    white-space:nowrap;overflow:hidden;text-overflow:ellipsis;
  }
  .od-topsold{font-size:11px;color:#9a9a9a;margin-top:2px}
  .od-topprice{
    font-weight:900;
    font-size:13px;
    color:#6b7280;
    white-space:nowrap;
  }

  /* ===== Kritik Pelanggan ===== */
  .od-reviews-head{
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:12px;
    margin-bottom:10px;
  }
  .od-search{
    flex:1;
    display:flex;
    align-items:center;
    gap:10px;
    background:#fff;
    border:1px solid #eee;
    border-radius:14px;
    padding:10px 12px;
    box-shadow:0 8px 18px rgba(0,0,0,.05);
  }
  .od-search .material-symbols-rounded{
    font-size:22px;
    color:#777;
  }
  .od-search input{
    border:none; outline:none; width:100%;
    font-size:13px;
    background:transparent;
  }
  .od-filter{
    width:44px; height:44px;
    border-radius:12px;
    border:1px solid #eee;
    background:#fff;
    cursor:pointer;
    display:grid; place-items:center;
    box-shadow:0 8px 18px rgba(0,0,0,.05);
  }
  .od-filter .material-symbols-rounded{ font-size:22px; color:#111; }
  .od-filter.is-active{ outline:3px solid rgba(255,122,26,.25); }

  .od-review-list{ display:flex; flex-direction:column; gap:14px; margin-top:8px; }
  .od-review{ display:flex; gap:12px; }

  .od-ava{
    width:46px; height:46px;
    border-radius:999px;
    background:#e9e9ee;
    display:grid; place-items:center;
    font-weight:900;
    color:#666;
    flex:0 0 auto;
  }

  .od-r-body{ min-width:0; flex:1; }

  .od-r-top{
    display:flex;
    align-items:center;
    gap:10px;
    flex-wrap:wrap;
  }

  /* Nama user +3 */
  .od-r-name{ font-weight:900; font-size:16px; color:#111; }
  .od-r-time{ font-size:11px; color:#777; }

  /* ===== Bintang ikuti Inbox (★ ☆) ===== */
  .od-rating{
    display:flex;
    align-items:center;
    gap:8px;
    margin-top:2px;
  }
  .od-stars{
    letter-spacing:1px;
    font-size:19px; /* -2 dari sebelumnya */
    line-height:1;
    color:#f59e0b;
  }
  .od-stars .empty{ color:#e5e7eb; }

  .od-stars-label{ font-size:11px; color:#777; }

  .od-r-text{
    margin-top:6px;
    font-size:13px;
    color:#222;
    line-height:1.35;
  }
  .od-r-order{
    margin-top:6px;
    font-size:11px;
    color:#9a9a9a;
  }

  .od-seeall{ margin-top:10px; text-align:right; }
  .od-seeall a{
    font-size:12px;
    font-weight:900;
    color:#111;
    text-decoration:none;
  }

  /* ===== Modal chart per-menu ===== */
  .od-modal{
    position:fixed;
    inset:0;
    background:rgba(0,0,0,.38);
    display:none;
    align-items:center;
    justify-content:center;
    padding:18px;
    z-index:9999;
  }
  .od-modal.show{ display:flex; }
  .od-modal-card{
    width:min(980px, 96vw);
    background:#fff;
    border-radius:22px;
    box-shadow:0 20px 60px rgba(0,0,0,.25);
    overflow:hidden;
  }
  .od-modal-head{
    padding:14px 16px;
    display:flex;
    align-items:flex-start;
    justify-content:space-between;
    gap:12px;
    border-bottom:1px solid #eee;
  }
  .od-modal-title{ font-weight:900; font-size:16px; margin:0; color:#111; }
  .od-modal-sub{ font-size:11px; color:#777; margin-top:2px; }
  .od-modal-close{
    border:1px solid #eee;
    background:#fff;
    width:40px; height:40px;
    border-radius:12px;
    cursor:pointer;
    display:grid; place-items:center;
  }
  .od-modal-close .material-symbols-rounded{ font-size:22px; }

  .od-modal-body{ padding:14px 16px 18px; }
  .od-range-tabs{
    display:flex;
    justify-content:flex-end;
    gap:10px;
    margin-bottom:10px;
  }
  .od-tab{
    border:none;
    background:transparent;
    padding:8px 12px;
    border-radius:12px;
    font-weight:900;
    font-size:12px;
    color:#777;
    cursor:pointer;
  }
  .od-tab.active{
    background:rgba(255,122,26,.14);
    color:#ff7a1a;
  }
  .od-menu-chart{ height:320px; }
  .od-menu-chart canvas{ width:100% !important; height:100% !important; }

  @media (max-width:1024px){
    .od-grid-top{ grid-template-columns:repeat(2, minmax(0,1fr)); }
    .od-grid-mid{ grid-template-columns:1fr; }
    .od-chart-wrap{ height:260px; }
  }
</style>
@endpush

@section('content')
@php
  $rp = fn($n) => 'Rp ' . number_format((int)($n ?? 0),0,',','.');
@endphp

<div class="owner-dashboard">
  <div class="od-title">Dashboard Overview</div>

  {{-- KPI --}}
  <div class="od-grid-top">
    <div class="od-card">
      <div class="od-kpi-label">Total Penjualan</div>
      <div class="od-kpi-value">{{ $rp($totalSales ?? 0) }}</div>
      <div class="od-kpi-sub">Bulan ini</div>
    </div>
    <div class="od-card">
      <div class="od-kpi-label">Total Transaksi</div>
      <div class="od-kpi-value">{{ (int)($totalTransactions ?? 0) }}</div>
      <div class="od-kpi-sub">Bulan ini</div>
    </div>
    <div class="od-card">
      <div class="od-kpi-label">Menu Terjual</div>
      <div class="od-kpi-value">{{ (int)($menuSold ?? 0) }}</div>
      <div class="od-kpi-sub">Item bulan ini</div>
    </div>
    <div class="od-card">
      <div class="od-kpi-label">Rata-rata Nilai</div>
      <div class="od-kpi-value">{{ number_format((float)($ratingAvg ?? 0),1,',','.') }}/5</div>
      <div class="od-kpi-sub">Berdasarkan {{ (int)($ratingCount ?? 0) }} ulasan</div>
    </div>
  </div>

  {{-- CHART + TOP MENU --}}
  <div class="od-grid-mid">
    <div class="od-card">
      <div class="od-section-title">Grafik Penjualan</div>
      <div class="od-section-sub">7 hari terakhir</div>
      <div class="od-chart-wrap">
        <canvas id="overallChart"></canvas>
      </div>
    </div>

    <div class="od-card">
      <div class="od-section-title">Menu Terlaris</div>
      <div class="od-section-sub">Top 5 item</div>

      <div class="od-toplist">
        @forelse(($topProducts ?? []) as $i => $p)
          @php
            $id    = data_get($p,'id_produk');
            $nama  = data_get($p,'nama_produk') ?: data_get($p,'produk.nama_produk') ?: 'Menu';
            $harga = data_get($p,'harga') ?: data_get($p,'produk.harga') ?: 0;
            $qty   = (int)(data_get($p,'qty') ?? data_get($p,'total_qty') ?? 0);

            $url = '#';
            if (\Illuminate\Support\Facades\Route::has('owner.menu.sales') && $id) {
              $url = route('owner.menu.sales', ['produk' => (int)$id]);
            } elseif ($id) {
              $url = url('/menu-sales/'.$id);
            }
          @endphp

          <div class="od-topitem"
               role="button"
               tabindex="0"
               data-sales-url="{{ $url }}"
               data-produk-id="{{ (int)$id }}"
               data-produk-nama="{{ e($nama) }}">
            <div class="od-rank rank-{{ $i+1 }}">{{ $i+1 }}</div>

            <div class="od-topmeta">
              <div class="od-topname">{{ $nama }}</div>
              <div class="od-topsold">Terjual {{ $qty }}x</div>
            </div>

            <div class="od-topprice">{{ $rp($harga) }}</div>
          </div>
        @empty
          <div style="font-size:13px;color:#777;">Belum ada data menu terlaris.</div>
        @endforelse
      </div>
    </div>
  </div>

  {{-- KRITIK --}}
  <div class="od-card" style="margin-top:14px;">
    <div class="od-section-title">Kritik Pelanggan terbaru</div>

    <div class="od-reviews-head">
      <div class="od-search">
        <span class="material-symbols-rounded">search</span>
        <input id="reviewSearch" type="search" placeholder="Cari Menu..." />
      </div>
      <button class="od-filter" id="reviewFilterBtn" type="button" title="Filter rating rendah (≤2)">
        <span class="material-symbols-rounded">tune</span>
      </button>
    </div>

    <div class="od-review-list" id="reviewList">
      @forelse(($recentReviews ?? []) as $r)
        @php
          $name = data_get($r,'pengguna.nama') ?? data_get($r,'pengguna.name') ?? 'Pelanggan';
          $initial = strtoupper(mb_substr($name,0,1));

          $ts = data_get($r,'tanggal_ulasan') ?? data_get($r,'created_at');
          try { $when = \Carbon\Carbon::parse($ts)->diffForHumans(); } catch (\Throwable $e) { $when = ''; }

          $rate = (int)(data_get($r,'rating') ?? 0);
          $starsFull  = str_repeat('★', $rate);
          $starsEmpty = str_repeat('☆', max(0, 5 - $rate));

          $itemsRaw = data_get($r,'pesanan.items', []);
          $items = collect(is_array($itemsRaw) ? $itemsRaw : ($itemsRaw ? $itemsRaw->all() : []));
          $itemsText = $items->take(2)->map(function($it){
              $j = (int)(data_get($it,'jumlah') ?? 0);
              $n = data_get($it,'nama_produk') ?? 'Menu';
              return ($j ? $j.'x ' : '').$n;
          })->implode(', ');
          if ($items->count() > 2) $itemsText .= ', ...';

          $searchHay = strtolower($name.' '.$itemsText.' '.(data_get($r,'komentar') ?? ''));
        @endphp

        <div class="od-review review-item"
             data-search="{{ e($searchHay) }}"
             data-rating="{{ $rate }}">
          <div class="od-ava">{{ $initial }}</div>

          <div class="od-r-body">
            <div class="od-r-top">
              <div class="od-r-name">{{ $name }}</div>
              <div class="od-r-time">{{ $when }}</div>
            </div>

            <div class="od-rating">
              <div class="od-stars">
                {!! e($starsFull) !!}<span class="empty">{!! e($starsEmpty) !!}</span>
              </div>
              <div class="od-stars-label">{{ $rate }} Bintang</div>
            </div>

            <div class="od-r-text">{{ data_get($r,'komentar') }}</div>

            @if(trim($itemsText) !== '')
              <div class="od-r-order">Pesanan: {{ $itemsText }}</div>
            @endif
          </div>
        </div>
      @empty
        <div style="font-size:13px;color:#777;">Belum ada ulasan masuk.</div>
      @endforelse
    </div>

    <div class="od-seeall">
      @php
        $inboxUrl = \Illuminate\Support\Facades\Route::has('owner.inbox.index')
          ? route('owner.inbox.index')
          : '#';
      @endphp
      <a href="{{ $inboxUrl }}">Lihat semua</a>
    </div>
  </div>
</div>

{{-- MODAL: CHART PER MENU --}}
<div class="od-modal" id="menuModal" aria-hidden="true">
  <div class="od-modal-card" role="dialog" aria-modal="true" aria-labelledby="menuModalTitle">
    <div class="od-modal-head">
      <div>
        <div class="od-modal-title" id="menuModalTitle">Grafik Penjualan</div>
        <div class="od-modal-sub" id="menuModalSub">—</div>
      </div>
      <button class="od-modal-close" id="menuModalClose" type="button" aria-label="Tutup">
        <span class="material-symbols-rounded">close</span>
      </button>
    </div>

    <div class="od-modal-body">
      <div class="od-range-tabs">
        <button class="od-tab active" data-range="7d"  type="button">7 Hari</button>
        <button class="od-tab"        data-range="30d" type="button">30 Hari</button>
        <button class="od-tab"        data-range="1y"  type="button">1 Tahun</button>
      </div>

      <div class="od-menu-chart">
        <canvas id="menuChart"></canvas>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
(() => {
  const overallLabels = @json($overallLabels ?? []);
  const overallValues = @json($overallValues ?? []);

  const shortID = (n) => {
    n = Number(n || 0);
    if (n >= 1e9) return (n/1e9).toFixed(1).replace('.',',') + 'B';
    if (n >= 1e6) return (n/1e6).toFixed(1).replace('.',',') + 'M';
    if (n >= 1e3) return (n/1e3).toFixed(0) + 'K';
    return String(Math.round(n));
  };

  // ===== Overall chart
  const oc = document.getElementById('overallChart');
  if (oc && window.Chart) {
    new Chart(oc.getContext('2d'), {
      type: 'line',
      data: {
        labels: overallLabels,
        datasets: [{
          label: 'Omset',
          data: overallValues,
          borderWidth: 3,
          pointRadius: 0,
          tension: 0.35,
          fill: true,
          borderColor: '#2c7be5',
          backgroundColor: 'rgba(44,123,229,.18)'
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false }},
        scales: {
          y: { ticks: { callback: (v) => shortID(v) }, grid: { color: 'rgba(0,0,0,.06)' } },
          x: { grid: { display: false } }
        }
      }
    });
  }

  // ===== Modal per menu chart
  const modal = document.getElementById('menuModal');
  const closeBtn = document.getElementById('menuModalClose');
  const titleEl = document.getElementById('menuModalTitle');
  const subEl   = document.getElementById('menuModalSub');
  const menuCanvas = document.getElementById('menuChart');

  let menuChart = null;
  let activeUrl = '';
  let activeRange = '7d';

  function openModal(){
    modal.classList.add('show');
    modal.setAttribute('aria-hidden','false');
  }
  function closeModal(){
    modal.classList.remove('show');
    modal.setAttribute('aria-hidden','true');
  }
  function setTab(range){
    activeRange = range;
    document.querySelectorAll('.od-tab').forEach(b => b.classList.toggle('active', b.dataset.range === range));
  }

  async function loadMenuChart(){
    if (!activeUrl || activeUrl === '#') return;
    const url = activeUrl + (activeUrl.includes('?') ? '&' : '?') + 'range=' + encodeURIComponent(activeRange);

    const res = await fetch(url, { headers: { 'Accept': 'application/json' }});
    const data = await res.json();

    const labels = data.labels || [];
    const revenue = data.revenue || [];

    if (titleEl) titleEl.textContent = 'Grafik Penjualan ' + (data.produk?.nama || '');
    if (subEl)   subEl.textContent   = (activeRange === '7d' ? '7 hari terakhir' :
                                       activeRange === '30d' ? '30 hari terakhir' : '12 bulan terakhir');

    if (!menuChart && menuCanvas && window.Chart) {
      menuChart = new Chart(menuCanvas.getContext('2d'), {
        type: 'line',
        data: {
          labels,
          datasets: [{
            label: 'Omset',
            data: revenue,
            borderWidth: 4,
            pointRadius: 0,
            tension: 0.35,
            fill: true,
            borderColor: '#ff7a1a',
            backgroundColor: 'rgba(255,122,26,.14)'
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: { legend: { display: false }},
          scales: {
            y: { ticks: { callback: (v)=> shortID(v) }, grid: { color: 'rgba(0,0,0,.06)' } },
            x: { grid: { display: false } }
          }
        }
      });
    } else if (menuChart) {
      menuChart.data.labels = labels;
      menuChart.data.datasets[0].data = revenue;
      menuChart.update();
    }
  }

  document.addEventListener('click', (e) => {
    const item = e.target.closest('.od-topitem');
    if (!item) return;

    activeUrl = item.dataset.salesUrl || '';
    setTab('7d');
    openModal();
    loadMenuChart().catch(console.error);
  });

  document.querySelectorAll('.od-tab').forEach(btn => {
    btn.addEventListener('click', () => {
      setTab(btn.dataset.range);
      loadMenuChart().catch(console.error);
    });
  });

  closeBtn?.addEventListener('click', closeModal);
  modal?.addEventListener('click', (e) => { if (e.target === modal) closeModal(); });
  document.addEventListener('keydown', (e) => { if (e.key === 'Escape' && modal?.classList.contains('show')) closeModal(); });

  // ===== Review search + filter (rating rendah <=2)
  const search = document.getElementById('reviewSearch');
  const list   = document.getElementById('reviewList');
  const filterBtn = document.getElementById('reviewFilterBtn');
  let filterLow = false;

  function applyReviewFilter(){
    const key = (search?.value || '').toLowerCase().trim();
    list?.querySelectorAll('.review-item').forEach(it => {
      const hay = (it.dataset.search || '');
      const rating = Number(it.dataset.rating || 0);
      const passText = !key || hay.includes(key);
      const passRate = !filterLow || rating <= 2;
      it.style.display = (passText && passRate) ? '' : 'none';
    });
  }

  search?.addEventListener('input', applyReviewFilter);
  filterBtn?.addEventListener('click', () => {
    filterLow = !filterLow;
    filterBtn.classList.toggle('is-active', filterLow);
    applyReviewFilter();
  });
})();
</script>
@endpush
