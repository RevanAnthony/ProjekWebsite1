{{-- resources/views/admin/chats/index.blade.php --}}
@extends('admin.layouts.panel')

@section('title', 'Chat Pelanggan — Golden Spice')
@section('page-title', 'Chats')
@section('page-subtitle', 'Balas chat pelanggan dengan cepat.')

@push('styles')
<style>
  .ac-shell{
    display:flex;
    min-height:540px;
    background:#fff;
    border-radius:18px;
    box-shadow:0 12px 28px rgba(0,0,0,.06);
    overflow:hidden;
    border:1px solid #eee;
  }

  /* LIST CHAT KIRI */
  .ac-list{
    width:32%;
    border-right:1px solid #eee;
    display:flex;
    flex-direction:column;
  }
  .ac-list-header{
    padding:14px 16px 10px;
    border-bottom:1px solid #f3f3f3;
  }
  .ac-list-title{
    font-size:16px;
    font-weight:800;
    margin:0 0 2px;
  }
  .ac-list-sub{
    font-size:11px;
    color:#777;
  }
  .ac-search{
    margin-top:8px;
  }
  .ac-search input{
    width:100%;
    border-radius:999px;
    border:1px solid #e0e0e0;
    font-size:12px;
    padding:7px 30px 7px 10px;
    outline:none;
    background:#fafafa;
  }

  .ac-list-body{
    flex:1;
    overflow-y:auto;
    background:#fafafa;
  }

  .ac-item{
    display:flex;
    align-items:center;
    padding:10px 12px;
    border-bottom:1px solid #f1f1f1;
    cursor:pointer;
    text-decoration:none;
    color:inherit;
  }
  .ac-item:hover{
    background:#f0f4ff;
  }
  .ac-item.active{
    background:#e3ebff;
  }

  .ac-avatar{
    width:32px;
    height:32px;
    border-radius:999px;
    background:#ff5043;
    color:#fff;
    display:grid;
    place-items:center;
    font-weight:800;
    font-size:14px;
    margin-right:10px;
  }
  .ac-item-main{
    flex:1;
    min-width:0;
  }
  .ac-name{
    font-size:13px;
    font-weight:700;
    margin-bottom:2px;
    white-space:nowrap;
    overflow:hidden;
    text-overflow:ellipsis;
  }
  .ac-preview{
    font-size:11px;
    color:#777;
    white-space:nowrap;
    overflow:hidden;
    text-overflow:ellipsis;
  }
  .ac-right{
    display:flex;
    flex-direction:column;
    align-items:flex-end;
    font-size:11px;
    color:#999;
    gap:4px;
  }
  .ac-unread-dot{
    width:14px;
    height:14px;
    border-radius:999px;
    background:#ff5043;
    color:#fff;
    font-size:9px;
    display:grid;
    place-items:center;
    font-weight:800;
  }
  .ac-empty{
    padding:20px;
    font-size:12px;
    color:#777;
  }

  /* PANEL CHAT KANAN */
  .ac-chat{
    flex:1;
    display:flex;
    flex-direction:column;
    background:#f8f8f8;
  }
  .ac-chat-header{
    padding:14px 18px;
    background:#fff;
    border-bottom:1px solid #eee;
    display:flex;
    align-items:center;
    gap:10px;
  }
  .ac-chat-header-main{
    flex:1;
  }
  .ac-chat-name{
    font-size:14px;
    font-weight:800;
    margin:0 0 2px;
  }
  .ac-chat-order{
    font-size:11px;
    color:#777;
  }
  .ac-chat-order strong{
    color:#ff5043;
  }
  .ac-chat-btn-order{
    border-radius:999px;
    border:1px solid #ff5043;
    color:#ff5043;
    font-size:11px;
    padding:6px 12px;
    background:#fff;
    font-weight:700;
    text-decoration:none;
  }

  .ac-unread-banner{
    font-size:11px;
    color:#777;
    text-align:center;
    padding:4px 0;
    background:#f0f0f0;
  }

  .ac-chat-body{
    flex:1;
    padding:14px 18px;
    overflow-y:auto;
  }

  .ac-msg-row{
    display:flex;
    margin-bottom:8px;
  }
  .ac-msg-row.me{
    justify-content:flex-end;
  }
  .ac-msg-row.other{
    justify-content:flex-start;
  }
  .ac-msg-bubble{
    max-width:70%;
    border-radius:16px;
    padding:7px 10px;
    font-size:12px;
    line-height:1.4;
    word-wrap:break-word;
  }
  .ac-msg-row.me .ac-msg-bubble{
    background:#ff5043;
    color:#fff;
    border-bottom-right-radius:4px;
  }
  .ac-msg-row.other .ac-msg-bubble{
    background:#fff;
    border:1px solid #eee;
    border-bottom-left-radius:4px;
  }
  .ac-msg-meta{
    font-size:10px;
    color:#eee;
    margin-top:2px;
    text-align:right;
  }
  .ac-msg-row.other .ac-msg-meta{
    color:#999;
  }

  .ac-chat-footer{
    padding:10px 18px;
    background:#fff;
    border-top:1px solid #eee;
  }
  .ac-input-wrap{
    display:flex;
    gap:10px;
    align-items:flex-end;
  }
  .ac-input-wrap textarea{
    flex:1;
    resize:none;
    border-radius:999px;
    border:1px solid #ddd;
    padding:8px 14px;
    font-size:12px;
    max-height:80px;
    outline:none;
  }
  .ac-input-wrap textarea:focus{
    border-color:#ff5043;
    box-shadow:0 0 0 1px rgba(255,80,67,.15);
  }
  .ac-input-wrap button{
    border-radius:999px;
    border:0;
    padding:0 16px;
    font-size:12px;
    font-weight:800;
    background:#ff5043;
    color:#fff;
    cursor:pointer;
    white-space:nowrap;
  }

  @media (max-width:900px){
    .ac-shell{flex-direction:column;}
    .ac-list{width:100%;max-height:260px;}
  }
</style>
@endpush

@section('content')
@php use Illuminate\Support\Str; @endphp

<div class="ac-shell">
  {{-- LIST CHAT KIRI --}}
  <div class="ac-list">
    <div class="ac-list-header">
      <p class="ac-list-title">Chat Pelanggan</p>
      <p class="ac-list-sub">Lihat semua chat pelanggan dan balas dengan cepat.</p>
      <div class="ac-search">
        {{-- Search belum aktif, cuma layout --}}
        <input type="text" placeholder="Cari nama atau pesanan..." disabled>
      </div>
    </div>

    <div class="ac-list-body">
      @if($chats->isEmpty())
        <div class="ac-empty">
          Belum ada chat dari pelanggan.
        </div>
      @else
        @foreach($chats as $chat)
          @php
            $order       = $chat->order;
            $customer    = optional(optional($order)->pengguna)->nama ?? 'Pelanggan';
            $lastMessage = $chat->messages->first(); // sudah diurut desc pada controller
            $preview     = $lastMessage ? Str::limit($lastMessage->isi_pesan, 40) : 'Belum ada pesan';
            $timeLabel   = $lastMessage && $lastMessage->created_at
              ? $lastMessage->created_at->format('H:i')
              : '';
            $unread      = $unreadCounts[$chat->id_chat] ?? 0;
            $isActive    = $activeChat && $activeChat->id_chat === $chat->id_chat;
            $initial     = mb_substr($customer, 0, 1);
          @endphp
          <a href="{{ route('admin.chats.show', ['chat' => $chat->id_chat]) }}"
             class="ac-item {{ $isActive ? 'active' : '' }}"
          >
            <div class="ac-avatar">{{ strtoupper($initial) }}</div>
            <div class="ac-item-main">
              <div class="ac-name">{{ $customer }}</div>
              <div class="ac-preview">{{ $preview }}</div>
            </div>
            <div class="ac-right">
              @if($timeLabel)
                <span>{{ $timeLabel }}</span>
              @endif
              @if($unread > 0)
                <div class="ac-unread-dot">{{ $unread }}</div>
              @endif
            </div>
          </a>
        @endforeach
      @endif
    </div>
  </div>

  {{-- PANEL CHAT KANAN --}}
  <div class="ac-chat">
    @if(!$activeChat)
      <div class="ac-chat-body">
        <p class="ac-empty">
          Belum ada chat yang aktif. Tunggu sampai pelanggan mengirim pesan, atau buka chat dari halaman pesanan.
        </p>
      </div>
    @else
      @php
        $order   = $activeChat->order;
        $customer= optional(optional($order)->pengguna)->nama ?? 'Pelanggan';
        $initial = mb_substr($customer, 0, 1);
        $orderId = optional($order)->id_pesanan;
        $unreadForActive = $unreadCounts[$activeChat->id_chat] ?? 0;

        $orderLine = '';
        if ($order) {
            // Aman meskipun kolom nggak ada, paling cuma kosong
            $orderLine = trim(($order->alamat ?? $order->alamat_lengkap ?? '') . ' ' . ($order->catatan ?? ''));
        }
      @endphp

      <div class="ac-chat-header">
        <div class="ac-avatar">{{ strtoupper($initial) }}</div>
        <div class="ac-chat-header-main">
          <p class="ac-chat-name">{{ $customer }}</p>
          @if($orderId)
            <div class="ac-chat-order">
              <strong>Pesanan #{{ $orderId }}</strong>
              @if($orderLine)
                &nbsp;&mdash;&nbsp; {{ $orderLine }}
              @endif
            </div>
          @endif
        </div>
        @if($orderId)
          <a href="{{ route('admin.orders.show', ['order' => $orderId]) }}"
             class="ac-chat-btn-order" target="_blank">
            Lihat Pesanan
          </a>
        @endif
      </div>

      @if($unreadForActive > 0)
        <div class="ac-unread-banner">
          {{ $unreadForActive }} pesan baru dari pelanggan.
        </div>
      @endif

      <div class="ac-chat-body" id="ac-chat-body">
        @if($messages->isEmpty())
          <p class="ac-empty">Belum ada pesan. Kamu bisa menyapa pelanggan terlebih dahulu.</p>
        @else
          @foreach($messages as $message)
            @php
              $isMine = $message->sender_role === 'admin';
            @endphp
            <div class="ac-msg-row {{ $isMine ? 'me' : 'other' }}">
              <div class="ac-msg-bubble">
                {!! nl2br(e($message->isi_pesan)) !!}
                <div class="ac-msg-meta">
                  {{ optional($message->created_at)->format('H:i') }}
                </div>
              </div>
            </div>
          @endforeach
        @endif
      </div>

      <div class="ac-chat-footer">
        <form method="POST" action="{{ route('admin.chats.send', ['chat' => $activeChat->id_chat]) }}">
          @csrf
          <div class="ac-input-wrap">
            <textarea
              name="message"
              rows="1"
              placeholder="Ketik pesan ke pelanggan..."
              required
            >{{ old('message') }}</textarea>
            <button type="submit">Kirim</button>
          </div>
          @error('message')
            <p style="color:#d41616;font-size:11px;margin-top:4px;">{{ $message }}</p>
          @enderror
        </form>
      </div>
    @endif
  </div>
</div>
@endsection

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function () {
    var box = document.getElementById('ac-chat-body');
    if (box) {
      box.scrollTop = box.scrollHeight;
    }
  });
</script>
@endpush
