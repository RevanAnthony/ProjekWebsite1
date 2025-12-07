{{-- resources/views/orders/qris.blade.php --}}
@extends('layouts.app')

@section('title', 'Pembayaran QRIS')

@push('styles')
<style>
  .qris-page{
    max-width:960px;
    margin:40px auto 80px;
  }
  .qris-card{
    background:#fff;
    border-radius:24px;
    padding:24px 24px 28px;
    box-shadow:0 10px 30px rgba(15,15,15,.06);
  }
  .qris-header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:18px;
    gap:12px;
  }
  .qris-amount{
    font-weight:900;
    font-size:22px;
  }
  .qris-meta{
    font-size:13px;
    color:#777;
  }
  .qris-body{
    display:flex;
    gap:24px;
    flex-wrap:wrap;
    align-items:flex-start;
  }
  .qris-code{
    flex:0 0 260px;
    text-align:center;
  }
  .qris-code img{
    max-width:100%;
    border-radius:18px;
    border:6px solid #f6f6f6;
  }
  .qris-instruction{
    flex:1;
    font-size:14px;
  }
  .qris-instruction ol{
    padding-left:18px;
    margin:0 0 12px;
  }
  .qris-footer{
    margin-top:24px;
    display:flex;
    justify-content:space-between;
    gap:12px;
    flex-wrap:wrap;
    align-items:center;
  }
  .gs-btn-outline{
    border-radius:14px;
    border:1px solid #eee;
    padding:0 18px;
    height:48px;
    display:inline-flex;
    align-items:center;
    gap:8px;
    font-weight:700;
    background:#fff;
    cursor:pointer;
    text-decoration:none;
    color:#111;
  }

  /* copy style tombol utama biar konsisten */
  .gs-btn-primary{
    position:relative;
    display:inline-flex;
    align-items:center;
    justify-content:center;
    gap:.55rem;
    font-family:"Hind","Questrial",sans-serif;
    text-transform:uppercase;
    font-weight:800;
    letter-spacing:.3px;
    height:48px;
    padding:0 22px;
    border-radius:14px;
    border:0;
    cursor:pointer;
    user-select:none;
    background:linear-gradient(135deg,#ff3b3b 0%, #ff7a1a 100%);
    color:#fff;
    box-shadow:0 8px 18px rgba(255,61,61,.35), 0 2px 6px rgba(0,0,0,.08);
    transition:.18s;
    overflow:hidden;
  }
  .gs-btn-primary:active{
    transform:translateY(0) scale(.98);
  }
</style>
@endpush

@section('content')
@php
    // fallback kalau controller lupa kirim $qrisImageUrl
    $qrisImageUrl = $qrisImageUrl ?? asset('images/qris.jpg');
    $orderId = $pesanan->id_pesanan ?? $pesanan->getKey();
@endphp

<div class="qris-page">
  <div class="qris-card">
    <div class="qris-header">
      <div>
        <div style="font-weight:800;font-size:18px;">Bayar dengan QRIS</div>
        <div class="qris-meta">
          Order #{{ $orderId }} · Golden Spice
        </div>
      </div>
      <div class="qris-amount">
        Rp {{ number_format($pesanan->total_pembayaran,0,',','.') }}
      </div>
    </div>

    <div class="qris-body">
      <div class="qris-code">
        <img src="{{ $qrisImageUrl }}" alt="QRIS Code">
        <div class="qris-meta" style="margin-top:8px;">
          Scan QR ini dengan aplikasi pembayaran yang mendukung QRIS.
        </div>
      </div>
      <div class="qris-instruction">
        <strong>Panduan pembayaran:</strong>
        <ol>
          <li>Buka aplikasi e-wallet / mobile banking yang mendukung QRIS.</li>
          <li>Pilih menu <b>Scan QR</b> lalu arahkan ke QR di sebelah kiri.</li>
          <li>Pastikan nominal pembayaran sesuai:
            <b>Rp {{ number_format($pesanan->total_pembayaran,0,',','.') }}</b>.
          </li>
          <li>Konfirmasi pembayaran di aplikasi kamu.</li>
        </ol>
        <p>
         <p>
  Setelah pembayaran berhasil, klik tombol di bawah untuk memberi tahu kami
  bahwa kamu sudah bayar. Kasir akan mengecek pembayaran kamu secara manual
  sebelum pesanan diproses.
</p>

        </p>
      </div>
    </div>

    <div class="qris-footer">
      <a href="{{ route('menu') }}" class="gs-btn-outline">
        Kembali ke Menu
      </a>

      <form method="POST"
            action="{{ route('orders.qris.confirm', ['id' => $orderId]) }}">
        @csrf
        <button type="submit" class="gs-btn-primary">
          Saya sudah bayar
        </button>
      </form>
    </div>
  </div>
</div>
@endsection
