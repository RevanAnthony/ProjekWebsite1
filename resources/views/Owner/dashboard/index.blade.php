@extends('owner.layout')

@section('title','Dashboard — Owner')

@push('styles')
<style>
  .stat-row{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:14px;margin-bottom:18px}
  .stat-card{background:#fff;border-radius:18px;padding:14px 16px;border:1px solid #f0f0f0;box-shadow:0 8px 24px rgba(0,0,0,.03);font-size:13px}
  .stat-label{color:#777;font-size:12px;margin-bottom:6px}
  .stat-value{font-size:18px;font-weight:800}
  .stat-sub{font-size:11px;color:#999;margin-top:4px}

  .grid-main{display:grid;grid-template-columns:minmax(0,2.1fr) minmax(0,1.3fr);gap:16px;margin-bottom:16px}
  .card{background:#fff;border-radius:18px;padding:16px 18px;border:1px solid #f0f0f0;box-shadow:0 8px 24px rgba(0,0,0,.03);font-size:13px}
  .card h3{margin:0 0 8px;font-size:15px}
  .card-sub{font-size:11px;color:#999;margin-bottom:10px}

  .top-menu-list{margin-top:8px}
  .top-menu-item{display:flex;align-items:center;justify-content:space-between;padding:6px 0;border-bottom:1px dashed #f1f1f1;font-size:13px}
  .top-menu-item:last-child{border-bottom:none}
  .badge-rank{width:26px;height:26px;border-radius:9px;display:grid;place-items:center;font-size:13px;font-weight:700;}
  .badge-rank-1{background:#f97316;color:#fff}
  .badge-rank-2{background:#fb923c;color:#fff}
  .badge-rank-3{background:#fdba74;color:#fff}
  .badge-rank-other{background:#e5e7eb;color:#111}

  .review-item{display:flex;gap:10px;padding:8px 0;border-bottom:1px dashed #f2f2f2}
  .review-item:last-child{border-bottom:none}
  .avatar{width:32px;height:32px;border-radius:999px;background:#f3f4f6;display:grid;place-items:center;font-size:13px;font-weight:700}
  .rating-stars{color:#facc15;font-size:13px;margin-bottom:3px}

  .grid-bottom{display:grid;grid-template-columns:minmax(0,1.6fr) minmax(0,1.4fr);gap:16px}
  .finance-row{display:flex;justify-content:space-between;margin-bottom:6px;font-size:13px}
  .finance-row span.label{color:#666}
  .finance-row span.neg{color:#b91c1c}
  .finance-row span.pos{color:#16a34a;font-weight:600}
  .profit-badge{margin-top:8px;padding:6px 10px;border-radius:999px;background:#ecfdf5;font-size:12px;color:#15803d;display:inline-block}
</style>
@endpush

@section('content')
  <div class="owner-page-header">
      <h1>Dashboard Overview</h1>
  </div>

  @php
      $fmtRp = function(int $v){
          return 'Rp '.number_format($v,0,',','.');
      };
      $fmtNum = function($v){
          return number_format($v,0,',','.');
      };
      $avgRatingRounded = number_format($avgRating,1,',','.');
  @endphp

  {{-- STAT CARDS --}}
  <div class="stat-row">
      <div class="stat-card">
          <div class="stat-label">Total Penjualan</div>
          <div class="stat-value">{{ $fmtRp($totalSales) }}</div>
          <div class="stat-sub">Bulan ini</div>
      </div>
      <div class="stat-card">
          <div class="stat-label">Total Transaksi</div>
          <div class="stat-value">{{ $fmtNum($totalTransactions) }}</div>
          <div class="stat-sub">Bulan ini</div>
      </div>
      <div class="stat-card">
          <div class="stat-label">Menu Terjual</div>
          <div class="stat-value">{{ $fmtNum($totalItemsSold) }}</div>
          <div class="stat-sub">Item bulan ini</div>
      </div>
      <div class="stat-card">
          <div class="stat-label">Rata-rata Nilai</div>
          <div class="stat-value">{{ $avgRatingRounded }} <span style="font-size:14px;">/ 5</span></div>
          <div class="stat-sub">Berdasarkan ulasan</div>
      </div>
  </div>

  {{-- GRAFIK + MENU TERLARIS --}}
  <div class="grid-main">
      <div class="card">
          <h3>Grafik Penjualan</h3>
          <div class="card-sub">7 hari terakhir</div>
          <canvas id="salesChart" height="120"></canvas>
      </div>

      <div class="card">
          <h3>Menu Terlaris</h3>
          <div class="card-sub">Top 5 item</div>
          <div class="top-menu-list">
              @forelse($topMenus as $index => $item)
                  @php
                      $p = $item->produk;
                      $rank = $index + 1;
                      $rankClass = $rank === 1 ? 'badge-rank-1' : ($rank === 2 ? 'badge-rank-2' : ($rank === 3 ? 'badge-rank-3' : 'badge-rank-other'));
                  @endphp
                  <div class="top-menu-item">
                      <div style="display:flex;align-items:center;gap:10px;">
                          <div class="badge-rank {{ $rankClass }}">{{ $rank }}</div>
                          <div>
                              <div style="font-weight:600;">{{ $p->nama_produk ?? 'Produk' }}</div>
                              <div style="font-size:11px;color:#999;">Terjual {{ (int)$item->total_qty }}x</div>
                          </div>
                      </div>
                      <div style="font-size:13px;color:#666;">
                          {{ $p ? $fmtRp($p->harga) : '' }}
                      </div>
                  </div>
              @empty
                  <div style="font-size:13px;color:#777;">Belum ada data penjualan.</div>
              @endforelse
          </div>
      </div>
  </div>

  {{-- REVIEW & RINGKASAN KEUANGAN --}}
  <div class="grid-bottom">
      <div class="card">
          <h3>Kritik Pelanggan Terbaru</h3>
          <div class="card-sub">3 ulasan terakhir</div>

          @forelse($latestReviews as $rev)
              @php
                  $userName = $rev->pengguna->nama ?? 'Pelanggan';
                  $initial  = mb_substr($userName,0,1);
                  $stars    = str_repeat('★', (int)$rev->rating).str_repeat('☆', max(0,5-(int)$rev->rating));
              @endphp
              <div class="review-item">
                  <div class="avatar">{{ $initial }}</div>
                  <div>
                      <div style="font-weight:600;font-size:13px;">{{ $userName }}</div>
                      <div class="rating-stars">{{ $stars }}</div>
                      <div style="font-size:13px;color:#444;">{{ $rev->komentar }}</div>
                      <div style="font-size:11px;color:#9ca3af;margin-top:4px;">
                          {{ optional($rev->created_at)->diffForHumans() }}
                      </div>
                  </div>
              </div>
          @empty
              <div style="font-size:13px;color:#777;">Belum ada ulasan pelanggan.</div>
          @endforelse
      </div>

      <div class="card">
          <h3>Ringkasan Keuangan</h3>
          <div class="card-sub">Perkiraan bulan ini</div>

          <div class="finance-row">
              <span class="label">Biaya Bahan Baku</span>
              <span class="neg">-{{ $fmtRp($cogsEstimate) }}</span>
          </div>
          <div class="finance-row">
              <span class="label">Biaya Operasional</span>
              <span class="neg">-{{ $fmtRp($operational) }}</span>
          </div>
          <div class="finance-row">
              <span class="label">Total Biaya</span>
              <span class="neg">-{{ $fmtRp($totalCost) }}</span>
          </div>
          <div class="finance-row" style="margin-top:10px;border-top:1px dashed #e5e7eb;padding-top:8px;">
              <span class="label">Total Pendapatan</span>
              <span class="pos">{{ $fmtRp($totalRevenue) }}</span>
          </div>
          <div class="finance-row">
              <span class="label">Laba Bersih</span>
              <span class="pos">{{ $fmtRp($netProfit) }}</span>
          </div>

          <span class="profit-badge">
              Margin Keuntungan: {{ $totalRevenue > 0 ? number_format($netProfit * 100 / max($totalRevenue,1), 1, ',', '.') : 0 }}%
          </span>
      </div>
  </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  const ctx = document.getElementById('salesChart').getContext('2d');
  const chartLabels = {!! json_encode($chartLabels) !!};
  const chartValues = {!! json_encode($chartValues) !!};

  new Chart(ctx, {
      type: 'line',
      data: {
          labels: chartLabels,
          datasets: [{
              label: 'Penjualan (Rp)',
              data: chartValues,
              tension: 0.35,
              fill: true,
              borderWidth: 2
          }]
      },
      options: {
          responsive: true,
          plugins: {legend: {display:false}},
          scales: {
              y: {
                  ticks: {callback: value => value.toLocaleString('id-ID')}
              }
          }
      }
  });
</script>
@endpush
