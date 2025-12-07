{{-- resources/views/orders/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Order — Golden Spice')

@push('styles')
<style>
  .orders-page{
    max-width:980px;
    margin:24px auto 80px;
    padding:0 16px;
  }
  .orders-title{
    font-size:24px;
    font-weight:900;
    margin-bottom:4px;
  }
  .orders-sub{
    font-size:13px;
    color:#777;
    margin-bottom:18px;
  }
  .orders-section{
    margin-top:18px;
  }
  .orders-section h2{
    font-size:18px;
    font-weight:900;
    margin:0 0 10px 0;
  }
  .order-card{
    background:#fff;
    border-radius:18px;
    box-shadow:0 10px 26px rgba(0,0,0,.05);
    padding:14px 16px;
    margin-bottom:10px;
    display:flex;
    align-items:flex-start;
    gap:12px;
  }
  .order-left{
    flex:1;
  }
  .order-code{
    font-weight:800;
    font-size:15px;
    margin-bottom:2px;
  }
  .order-meta{
    font-size:12px;
    color:#777;
  }
  .order-total{
    font-weight:900;
    font-size:15px;
    white-space:nowrap;
  }
  .order-badge{
    display:inline-flex;
    align-items:center;
    padding:2px 8px;
    border-radius:999px;
    font-size:11px;
    font-weight:700;
    margin-top:4px;
  }
  .order-badge--qris{
    background:#e3f2fd;
    color:#0d47a1;
  }
  .order-badge--cod{
    background:#fff3e0;
    color:#e65100;
  }
  .order-status-pill{
    display:inline-flex;
    align-items:center;
    padding:2px 10px;
    border-radius:999px;
    font-size:11px;
    font-weight:700;
    margin-top:4px;
    background:#f5f5f5;
    color:#444;
  }
  .order-actions{
    display:flex;
    flex-direction:column;
    align-items:flex-end;
    gap:6px;
  }
  .gs-btn-small{
    border-radius:12px;
    border:1px solid #eee;
    padding:6px 12px;
    font-size:12px;
    font-weight:700;
    background:#fff;
    cursor:pointer;
    text-decoration:none;
    color:#111;
  }
  .orders-empty{
    background:#fff;
    border-radius:18px;
    padding:16px;
    box-shadow:0 10px 26px rgba(0,0,0,.03);
    font-size:13px;
    color:#666;
  }
  .orders-empty strong{
    display:block;
    margin-bottom:4px;
  }

  @media (max-width:768px){
    .order-card{
      flex-direction:column;
      align-items:flex-start;
    }
    .order-actions{
      width:100%;
      flex-direction:row;
      justify-content:space-between;
      align-items:center;
    }
    .order-total{
      font-size:14px;
    }
  }
</style>
@endpush

@section('content')
<div class="orders-page">
  <div class="orders-header">
    <div class="orders-title">Pesananmu</div>
    <div class="orders-sub">
      Lihat pesanan yang sedang berjalan dan riwayat pesanan sebelumnya.
    </div>
  </div>

  {{-- PESANAN AKTIF --}}
  <section class="orders-section">
    <h2>Pesanan Aktif</h2>

    @if ($activeOrders->isEmpty())
      <div class="orders-empty">
        <strong>Belum ada pesanan aktif.</strong>
        Kamu bisa mulai order baru dari halaman menu.
        <div style="margin-top:8px;">
          <a href="{{ route('menu') }}" class="gs-btn-small">Pergi ke Menu</a>
        </div>
      </div>
    @else
      @foreach ($activeOrders as $order)
        @php
          $orderId   = $order->id_pesanan ?? $order->getKey();
          $statusRaw = $order->status_pesanan ?? '';
          $statusLbl = ucwords(str_replace('_', ' ', $statusRaw));
          $pembayaran = strtoupper($order->metode_pembayaran ?? '-');
        @endphp
        <div class="order-card">
          <div class="order-left">
            <div class="order-code">Order #{{ $orderId }}</div>
            <div class="order-meta">
              {{ \Carbon\Carbon::parse($order->tanggal_pesanan)->format('d M Y · H:i') }}
            </div>
            <div class="order-meta">
              Metode Pembayaran:
              <strong>{{ $pembayaran }}</strong>
              @if ($order->metode_pembayaran === 'qris')
                <span class="order-badge order-badge--qris">QRIS</span>
              @elseif ($order->metode_pembayaran === 'cod')
                <span class="order-badge order-badge--cod">COD</span>
              @endif
            </div>
            <span class="order-status-pill">
              {{ $statusLbl }}
            </span>
          </div>
          <div class="order-actions">
            <div class="order-total">
              Rp {{ number_format($order->total_pembayaran,0,',','.') }}
            </div>
            <div>
              @if ($order->metode_pembayaran === 'qris'
                   && $order->status_pesanan === 'menunggu_pembayaran')
                <a href="{{ route('orders.qris', ['id' => $orderId]) }}"
                   class="gs-btn-small">Lanjutkan pembayaran</a>
              @endif
              <a href="{{ route('orders.show', ['id' => $orderId]) }}"
                 class="gs-btn-small">Lihat detail</a>
            </div>
          </div>
        </div>
      @endforeach
    @endif
  </section>

  {{-- RIWAYAT PESANAN --}}
  <section class="orders-section" style="margin-top:24px;">
    <h2>Riwayat Pesanan</h2>

    @if ($historyOrders->isEmpty())
      <div class="orders-empty">
        <strong>Belum ada riwayat pesanan.</strong>
        Pesanan yang sudah selesai atau dibatalkan akan muncul di sini.
      </div>
    @else
      @foreach ($historyOrders as $order)
        @php
          $orderId   = $order->id_pesanan ?? $order->getKey();
          $statusRaw = $order->status_pesanan ?? '';
          $statusLbl = ucwords(str_replace('_', ' ', $statusRaw));
        @endphp
        <div class="order-card">
          <div class="order-left">
            <div class="order-code">Order #{{ $orderId }}</div>
            <div class="order-meta">
              {{ \Carbon\Carbon::parse($order->tanggal_pesanan)->format('d M Y · H:i') }}
            </div>
            <div class="order-meta">
              Status: <strong>{{ $statusLbl }}</strong>
            </div>
          </div>
          <div class="order-actions">
            <div class="order-total">
              Rp {{ number_format($order->total_pembayaran,0,',','.') }}
            </div>
            <a href="{{ route('orders.show', ['id' => $orderId]) }}"
               class="gs-btn-small">Lihat detail</a>
          </div>
        </div>
      @endforeach
    @endif
  </section>
</div>
@endsection
