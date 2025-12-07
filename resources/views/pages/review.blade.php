@extends('layouts.app')
@section('title','Review Order — Golden Spice')

@push('styles')
<style>
  .rv-wrap{max-width:960px;margin:0 auto}
  .rv-list{display:flex;flex-direction:column;gap:14px;margin:18px 0 22px}
  .rv-item{display:grid;grid-template-columns:56px 1fr auto auto;gap:12px;align-items:center;padding:12px;border:1px solid #eee;border-radius:14px;background:#fff}
  .rv-thumb{width:56px;height:56px;border-radius:10px;object-fit:cover;background:#f6f6f6}
  .rv-name{font-weight:700}
  .rv-qty{font-weight:700;opacity:.8}
  .rv-price{font-weight:800}
  .rv-total{display:flex;justify-content:space-between;padding:14px 16px;border:2px dashed #eee;border-radius:14px;background:#fff}
  .rv-actions{display:flex;gap:12px;justify-content:flex-end;margin-top:16px}
  .empty{padding:32px;border-radius:16px;background:#fff;border:1px solid #eee}
</style>
@endpush

@section('content')
<section class="section">
  <div class="gs-container rv-wrap">
    <h1 class="section-title red">Review Order</h1>

    @php
      // Ambil items langsung dari $cart yang dikirim controller
      $items = optional($cart)->items ?? collect();

      $fmt = function ($n) {
        return 'Rp '.number_format((int)$n, 0, ',', '.');
      };

      $subtotalOf = function ($i) {
        $harga = (int)optional($i->produk)->harga ?? 0;
        $qty   = (int)$i->jumlah;
        return (int)($i->subtotal ?? ($qty * $harga));
      };

      $total = $items->sum(fn($i) => $subtotalOf($i));
    @endphp

    @if($items->count())
      <ul class="rv-list">
        @foreach($items as $i)
          @php
            $p    = $i->produk;
            $img  = $p?->url_gambar ? asset($p->url_gambar) : asset('images/placeholder.jpg');
            $nama = $p?->nama_produk ?? 'Produk';
            $qty  = (int)$i->jumlah;
            $line = $subtotalOf($i);
          @endphp
          <li class="rv-item">
            <img class="rv-thumb" src="{{ $img }}" alt="{{ $nama }}">
            <div class="rv-name">{{ $nama }}</div>
            <div class="rv-qty">× {{ $qty }}</div>
            <div class="rv-price">{{ $fmt($line) }}</div>
          </li>
        @endforeach
      </ul>

      <div class="rv-total">
        <span>Total</span>
        <strong>{{ $fmt($total) }}</strong>
      </div>

      <div class="rv-actions">
        <a href="{{ url('/menu') }}" class="gs-btn gs-btn--ghost">Kembali ke Menu</a>
        <a href="{{ url('/checkout') }}" class="gs-btn">Lanjut Checkout</a>
      </div>
    @else
      <div class="empty">
        Keranjangmu masih kosong. <a href="{{ url('/menu') }}" class="link">Tambah item dulu</a>.
      </div>
    @endif
  </div>
</section>
@endsection
