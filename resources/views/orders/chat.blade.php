{{-- resources/views/orders/chat.blade.php --}}
@extends('layouts.app')

@section('title', 'Chat — Golden Spice')

@push('styles')
<style>
  body{background:#fdf6ef;}

  .gs-chat-shell{
    max-width:960px;
    margin:20px auto 40px;
    padding:0 16px;
  }

  .gs-chat-panel{
    background:#fff;
    border-radius:24px;
    box-shadow:0 14px 30px rgba(0,0,0,.08);
    border:1px solid #f0e6de;
    display:flex;
    flex-direction:column;
    height:calc(100vh - 170px);
    min-height:440px;
  }

  /* HEADER DALAM KARTU */
  .gs-chat-topbar{
    display:flex;
    align-items:center;
    padding:14px 16px 8px;
    border-bottom:1px solid #f3ece6;
  }
  .gs-chat-back{
    margin-right:10px;
    text-decoration:none;
    font-size:18px;
    line-height:1;
    color:#ff5043;
    display:inline-flex;
    align-items:center;
    justify-content:center;
    width:28px;
    height:28px;
    border-radius:999px;
    background:rgba(255,80,67,.06);
  }
  .gs-chat-profile{
    display:flex;
    align-items:center;
    gap:10px;
    flex:1;
  }
  .gs-chat-avatar{
    width:32px;
    height:32px;
    border-radius:999px;
    background:#ff5043;
    color:#fff;
    display:grid;
    place-items:center;
    font-size:15px;
    font-weight:800;
  }
  .gs-chat-name{
    font-size:14px;
    font-weight:800;
    margin-bottom:2px;
  }
  .gs-chat-meta{
    font-size:11px;
    color:#777;
    display:flex;
    align-items:center;
    gap:6px;
  }
  .gs-dot-online{
    width:8px;
    height:8px;
    border-radius:999px;
    background:#2ecc71;
  }
  .gs-chat-status-chip{
    font-size:11px;
    border-radius:999px;
    padding:4px 9px;
    background:#fff5e3;
    color:#c46a00;
    font-weight:700;
    margin-left:auto;
  }

  /* BANNER KECIL DI BAWAH HEADER */
  .gs-chat-banner{
    padding:6px 16px 10px;
    border-bottom:1px solid #f3ece6;
    font-size:12px;
    color:#555;
  }
  .gs-chat-banner strong{
    display:block;
    margin-bottom:2px;
  }

  /* BADAN CHAT */
  .gs-chat-body{
    flex:1;
    padding:10px 16px 12px;
    overflow-y:auto;
    background:#faf7f2;
  }

  .gs-chat-day-label{
    text-align:center;
    font-size:11px;
    color:#999;
    margin:10px 0;
  }

  .gs-chat-message{
    display:flex;
    margin-bottom:8px;
  }
  .gs-chat-message.is-mine{
    justify-content:flex-end;
  }
  .gs-chat-message.is-other{
    justify-content:flex-start;
  }

  .gs-chat-bubble-wrap{
    max-width:74%;
  }

  .gs-chat-bubble{
    border-radius:18px;
    padding:8px 11px;
    font-size:13px;
    line-height:1.45;
    word-wrap:break-word;
  }
  .gs-chat-message.is-mine .gs-chat-bubble{
    background:#ff5043;
    color:#fff;
    border-bottom-right-radius:4px;
  }
  .gs-chat-message.is-other .gs-chat-bubble{
    background:#ffffff;
    border:1px solid #f0e6de;
    border-bottom-left-radius:4px;
    color:#333;
  }

  .gs-chat-meta-line{
    font-size:11px;
    color:#999;
    margin-top:2px;
  }

  .gs-chat-sender{
    font-weight:700;
    margin-right:6px;
  }

  /* PESAN PEMBUKA (SYSTEM) */
  .gs-chat-message.is-system .gs-chat-bubble{
    border:1px solid #c3e6cb;
    background:#f4fff7;
    color:#2f5f38;
  }
  .gs-chat-message.is-system .gs-chat-meta-line{
    color:#8ca792;
  }

  /* FOOTER INPUT */
  .gs-chat-footer{
    border-top:1px solid #f0e6de;
    padding:10px 14px;
    background:#fff;
  }
  .gs-chat-input-wrap{
    display:flex;
    gap:10px;
    align-items:flex-end;
  }
  .gs-chat-input-wrap textarea{
    flex:1;
    resize:none;
    border-radius:999px;
    border:1px solid #ddd;
    padding:9px 14px;
    font-size:13px;
    max-height:90px;
    outline:none;
  }
  .gs-chat-input-wrap textarea:focus{
    border-color:#ff5043;
    box-shadow:0 0 0 1px rgba(255,80,67,.2);
  }
  .gs-chat-input-wrap button{
    border-radius:999px;
    border:0;
    padding:0 18px;
    font-size:13px;
    font-weight:800;
    background:#ff5043;
    color:#fff;
    cursor:pointer;
    white-space:nowrap;
  }
  .gs-chat-helper{
    font-size:11px;
    color:#999;
    margin-top:4px;
    text-align:right;
  }

  @media (max-width:768px){
    .gs-chat-panel{
      height:calc(100vh - 130px);
      border-radius:18px;
    }
    .gs-chat-bubble-wrap{max-width:82%;}
  }
</style>
@endpush

@section('content')
@php
  $orderId      = $order->id_pesanan ?? $order->getKey();
  $statusLbl    = ucwords(str_replace('_',' ', $order->status_pesanan ?? ''));
  $createdLabel = $order->tanggal_pesanan || $order->created_at
      ? \Illuminate\Support\Carbon::parse($order->tanggal_pesanan ?? $order->created_at)->format('d M Y · H:i')
      : null;
  $currentUserId = auth()->id();
  $currentName   = auth()->user()->nama ?? 'Kak';
@endphp

<div class="gs-chat-shell">
  <div class="gs-chat-panel">

    {{-- HEADER DALAM KARTU, MIRIP GRAB --}}
    <div class="gs-chat-topbar">
      <a href="{{ route('orders.show', ['id' => $orderId]) }}" class="gs-chat-back" title="Kembali">
        ←
      </a>

      <div class="gs-chat-profile">
        <div class="gs-chat-avatar">G</div>
        <div>
          <div class="gs-chat-name">Pusat Bantuan Golden Spice</div>
          <div class="gs-chat-meta">
            <span class="gs-dot-online"></span>
            <span>Kasir online</span>
          </div>
        </div>
      </div>

      @if($statusLbl)
        <span class="gs-chat-status-chip">{{ $statusLbl }}</span>
      @endif
    </div>

    <div class="gs-chat-banner">
      @if($createdLabel)
        <strong>Pesanan dibuat {{ $createdLabel }}</strong>
      @endif
      <span>Kalau ada kendala dengan pesanan ini, tuliskan saja di chat. Kasir akan membantu secepatnya.</span>
    </div>

    {{-- BADAN CHAT --}}
    <div class="gs-chat-body" id="gs-chat-body">

      {{-- Tanggal label di tengah, biar berasa timeline --}}
      @if($createdLabel)
        <div class="gs-chat-day-label">
          {{ $createdLabel }}
        </div>
      @endif

      {{-- Kalau BELUM ADA pesan, tampilkan 2 pesan pembuka ala Grab --}}
      @if ($messages->isEmpty())
        <div class="gs-chat-message is-other is-system">
          <div class="gs-chat-bubble-wrap">
            <div class="gs-chat-bubble">
              Hai {{ $currentName }}, terima kasih ya. Tulis kendalamu di sini supaya tim kasir Golden Spice bisa bantu cek pesananmu lebih cepat. 😊
            </div>
            <div class="gs-chat-meta-line">
              <span class="gs-chat-sender">Golden Spice</span> · beberapa detik yang lalu
            </div>
          </div>
        </div>

        <div class="gs-chat-message is-other is-system">
          <div class="gs-chat-bubble-wrap">
            <div class="gs-chat-bubble">
              Demi kenyamanan bareng-bareng, hindari kata-kata kasar ya. Tim kami akan tetap berusaha bantu sebaik mungkin. 🙏
            </div>
            <div class="gs-chat-meta-line">
              <span class="gs-chat-sender">Golden Spice</span>
            </div>
          </div>
        </div>
      @else
        {{-- Kalau SUDAH ADA pesan, langsung tampilkan riwayat chat --}}
        @foreach ($messages as $message)
          @php
            $isMine = $message->id_pengguna === $currentUserId;
            $senderLabel = $isMine
              ? 'Kamu'
              : (($message->sender_role === 'admin') ? 'Kasir Golden Spice' : ($message->sender->nama ?? 'Pelanggan'));
          @endphp
          <div class="gs-chat-message {{ $isMine ? 'is-mine' : 'is-other' }}">
            <div class="gs-chat-bubble-wrap">
              <div class="gs-chat-bubble">
                {!! nl2br(e($message->isi_pesan)) !!}
              </div>
              <div class="gs-chat-meta-line">
                <span class="gs-chat-sender">{{ $senderLabel }}</span>
                <span>{{ optional($message->created_at)->format('H:i') }}</span>
              </div>
            </div>
          </div>
        @endforeach
      @endif
    </div>

    {{-- FOOTER INPUT --}}
    <div class="gs-chat-footer">
      <form method="POST" action="{{ route('orders.chat.send', ['id' => $orderId]) }}">
        @csrf
        <div class="gs-chat-input-wrap">
          <textarea
            name="message"
            rows="1"
            placeholder="Tulis pesan ke kasir..."
            required
          >{{ old('message') }}</textarea>
          <button type="submit">Kirim</button>
        </div>
        <div class="gs-chat-helper">
          Balasan dari kasir akan muncul di atas.
        </div>
        @error('message')
          <p style="color:#d41616;font-size:11px;margin-top:4px;">{{ $message }}</p>
        @enderror
      </form>
    </div>

  </div>
</div>
@endsection

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function () {
    var box = document.getElementById('gs-chat-body');
    if (box) {
      box.scrollTop = box.scrollHeight;
    }
  });
</script>
@endpush
