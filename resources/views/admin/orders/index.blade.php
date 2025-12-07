{{-- resources/views/admin/orders/index.blade.php --}}
@extends('admin.layouts.panel')

@section('title', 'Pesanan Kasir — Golden Spice')
@section('page-title', 'Pesanan Masuk')
@section('page-subtitle', 'Konfirmasi pembayaran dan tandai pesanan yang sudah selesai.')

@push('styles')
<style>
  .order-tabs{
    display:flex;
    gap:10px;
    margin-bottom:18px;
  }
  .order-tab{
    border-radius:999px;
    border:1px solid #e0e0e0;
    background:#fff;
    padding:6px 14px;
    font-size:13px;
    font-weight:700;
    cursor:pointer;
  }
  .order-tab.active{
    background:#ff5043;
    border-color:#ff5043;
    color:#fff;
  }

  .order-section-title{
    font-size:16px;
    font-weight:800;
    margin:4px 0 10px;
  }

  .admin-order-card{
    background:#fff;
    border-radius:18px;
    box-shadow:0 8px 22px rgba(0,0,0,.06);
    padding:14px 18px;
    display:flex;
    justify-content:space-between;
    gap:12px;
    margin-bottom:10px;
  }
  .co-left{ flex:1; }
  .co-code{
    font-weight:800;
    font-size:15px;
  }
  .co-meta{
    font-size:12px;
    color:#777;
  }
  .co-meta + .co-meta{ margin-top:2px; }
  .co-payment{
    font-size:12px;
    margin-top:4px;
  }
  .co-payment span{ font-weight:700; }

  .co-right{
    min-width:190px;
    text-align:right;
    display:flex;
    flex-direction:column;
    align-items:flex-end;
    gap:6px;
  }
  .co-total{
    font-weight:900;
    font-size:16px;
  }
  .co-status{
    display:inline-flex;
    align-items:center;
    padding:3px 10px;
    border-radius:999px;
    font-size:11px;
    font-weight:700;
    background:#f5f5f5;
    color:#444;
  }
  .co-status--waiting{
    background:#fff3e0;
    color:#e65100;
  }
  .co-status--done{
    background:#e8f5e9;
    color:#1b5e20;
  }
  .co-status--cancel{
    background:#ffebee;
    color:#b71c1c;
  }

  .co-actions{
    display:flex;
    flex-wrap:wrap;
    gap:6px;
    justify-content:flex-end;
  }
  .btn-ghost{
    border-radius:999px;
    border:1px solid #e0e0e0;
    padding:5px 12px;
    font-size:11px;
    font-weight:700;
    background:#fff;
    cursor:pointer;
  }
  .btn-main{
    border-radius:999px;
    border:0;
    padding:6px 14px;
    font-size:11px;
    font-weight:800;
    background:#ff5043;
    color:#fff;
    cursor:pointer;
  }
  .btn-danger{
    border-radius:999px;
    border:1px solid #ff5043;
    padding:5px 12px;
    font-size:11px;
    font-weight:700;
    background:#fff;
    color:#ff5043;
    cursor:pointer;
  }

  .order-empty{
    font-size:13px;
    color:#777;
    margin-top:4px;
  }

  @media(max-width:900px){
    .admin-order-card{
      flex-direction:column;
      align-items:flex-start;
    }
    .co-right{
      align-items:flex-start;
      text-align:left;
    }
    .co-actions{
      justify-content:flex-start;
    }
  }
</style>
@endpush

@section('content')
<div>
  <div class="order-tabs">
    <button type="button" class="order-tab active" data-tab="active">Pesanan Aktif</button>
    <button type="button" class="order-tab" data-tab="history">Riwayat</button>
  </div>

  {{-- ================= PESANAN AKTIF ================= --}}
  <section id="order-active">
    <h2 class="order-section-title">Pesanan Aktif</h2>

    @if ($activeOrders->isEmpty())
      <div class="order-empty">Belum ada pesanan aktif.</div>
    @else
      @foreach ($activeOrders as $order)
        @php
          $orderId       = $order->id_pesanan ?? $order->getKey();
          $status        = $order->status_pesanan ?? '';
          $statusLbl     = ucwords(str_replace('_',' ', $status));
          $statusClass   = 'co-status';

          if (in_array($status, ['menunggu_pembayaran','menunggu_konfirmasi_toko'])) {
              $statusClass .= ' co-status--waiting';
          } elseif ($status === 'selesai') {
              $statusClass .= ' co-status--done';
          } elseif ($status === 'dibatalkan') {
              $statusClass .= ' co-status--cancel';
          }

          $pembayaran    = strtoupper($order->metode_pembayaran ?? '-');
          $namaPelanggan = optional($order->pengguna)->nama ?? 'Pelanggan';
          $tanggalLabel  = $order->tanggal_pesanan
              ? \Carbon\Carbon::parse($order->tanggal_pesanan)->format('d M Y · H:i')
              : '-';
        @endphp

        <div class="admin-order-card">
          <div class="co-left">
            <div class="co-code">Order #{{ $orderId }}</div>
            <div class="co-meta">{{ $tanggalLabel }}</div>
            <div class="co-meta">{{ $namaPelanggan }}</div>
            <div class="co-payment">
              Metode Pembayaran: <span>{{ $pembayaran }}</span>
            </div>
          </div>

          <div class="co-right">
            <div class="co-total">
              Rp {{ number_format($order->total_pembayaran, 0, ',', '.') }}
            </div>

            <span class="{{ $statusClass }}">
              {{ $statusLbl }}
            </span>

            <div class="co-actions">
              {{-- Chat pesanan di panel kasir --}}
              <a href="{{ route('admin.orders.chat', ['order' => $orderId]) }}"
                 class="btn-ghost">
                Chat
              </a>

              {{-- Detail pesanan versi kasir (HALAMAN ADMIN) --}}
              <a href="{{ route('admin.orders.show', ['order' => $orderId]) }}"
                 class="btn-ghost" target="_blank">
                Lihat detail
              </a>

              {{-- Konfirmasi Pembayaran: dari menunggu_pembayaran/menunggu_konfirmasi_toko -> dapur --}}
              @if (in_array($status, ['menunggu_pembayaran','menunggu_konfirmasi_toko']))
                <form method="POST"
                      action="{{ route('admin.orders.update-status', ['order' => $orderId]) }}">
                  @csrf
                  <input type="hidden" name="action" value="to_cooking">
                  <button type="submit" class="btn-main">
                    Konfirmasi Pembayaran
                  </button>
                </form>
              @endif

              {{-- Tandai Selesai: dari diantar -> selesai --}}
              @if ($status === 'diantar')
                <form method="POST"
                      action="{{ route('admin.orders.update-status', ['order' => $orderId]) }}">
                  @csrf
                  <input type="hidden" name="action" value="finish">
                  <button type="submit" class="btn-ghost">
                    Tandai Selesai
                  </button>
                </form>
              @endif

              {{-- Batalkan: selama belum selesai/dibatalkan --}}
              @if (!in_array($status, ['selesai','dibatalkan']))
                <form method="POST"
                      action="{{ route('admin.orders.update-status', ['order' => $orderId]) }}">
                  @csrf
                  <input type="hidden" name="action" value="cancel">
                  <button type="submit" class="btn-danger">
                    Batalkan
                  </button>
                </form>
              @endif
            </div>
          </div>
        </div>
      @endforeach
    @endif
  </section>

  {{-- ================= RIWAYAT ================= --}}
  <section id="order-history" style="display:none;">
    <h2 class="order-section-title">Riwayat Pesanan</h2>

    @if ($historyOrders->isEmpty())
      <div class="order-empty">Belum ada riwayat pesanan.</div>
    @else
      @foreach ($historyOrders as $order)
        @php
          $orderId      = $order->id_pesanan ?? $order->getKey();
          $status       = $order->status_pesanan ?? '';
          $statusLbl    = ucwords(str_replace('_',' ', $status));
          $tanggalLabel = $order->tanggal_pesanan
              ? \Carbon\Carbon::parse($order->tanggal_pesanan)->format('d M Y · H:i')
              : '-';
        @endphp

        <div class="admin-order-card">
          <div class="co-left">
            <div class="co-code">Order #{{ $orderId }}</div>
            <div class="co-meta">{{ $tanggalLabel }}</div>
          </div>
          <div class="co-right">
            <div class="co-total">
              Rp {{ number_format($order->total_pembayaran, 0, ',', '.') }}
            </div>
            <span class="co-status">{{ $statusLbl }}</span>
          </div>
        </div>
      @endforeach
    @endif
  </section>
</div>
@endsection

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const tabs = document.querySelectorAll('.order-tab');
    const activeSection = document.getElementById('order-active');
    const historySection = document.getElementById('order-history');

    tabs.forEach(tab => {
      tab.addEventListener('click', () => {
        tabs.forEach(t => t.classList.remove('active'));
        tab.classList.add('active');

        if (tab.dataset.tab === 'history') {
          activeSection.style.display = 'none';
          historySection.style.display = 'block';
        } else {
          activeSection.style.display = 'block';
          historySection.style.display = 'none';
        }
      });
    });
  });
</script>
@endpush
