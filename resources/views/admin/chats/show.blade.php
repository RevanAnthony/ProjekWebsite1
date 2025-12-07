{{-- resources/views/admin/chats/show.blade.php --}}
@extends('admin.layouts.panel')

@section('title', 'Detail Chat — Golden Spice')
@section('page-title', 'Chat Pesanan')
@section('page-subtitle', 'Respon cepat pertanyaan pelanggan.')

@push('styles')
<style>
  .gs-chat-page{
    display:flex;
    flex-direction:column;
    height:calc(100vh - 120px);
  }
  .gs-chat-header{
    display:flex;
    justify-content:space-between;
    align-items:flex-start;
    margin-bottom:10px;
  }
  .gs-chat-header-left{
    font-size:13px;
  }
  .gs-chat-header-right{
    text-align:right;
    font-size:12px;
    color:#777;
  }
  .gs-chat-header strong{
    font-size:15px;
  }
  .gs-chat-main-card{
    background:#fff;
    border-radius:18px;
    box-shadow:0 8px 22px rgba(0,0,0,.06);
    border:1px solid #eee;
    display:flex;
    flex-direction:column;
    flex:1;
  }
  .gs-chat-body{
    flex:1;
    padding:14px 16px;
    overflow-y:auto;
    background:#fafafa;
  }
  .gs-chat-footer{
    border-top:1px solid #eee;
    padding:10px 12px;
    background:#fff;
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
  .gs-chat-bubble{
    max-width:70%;
    border-radius:18px;
    padding:8px 10px;
    font-size:13px;
    line-height:1.4;
    word-wrap:break-word;
  }
  .gs-chat-message.is-mine .gs-chat-bubble{
    background:#ff5043;
    color:#fff;
    border-bottom-right-radius:4px;
  }
  .gs-chat-message.is-other .gs-chat-bubble{
    background:#fff;
    border:1px solid #eee;
    border-bottom-left-radius:4px;
  }
  .gs-chat-meta{
    display:flex;
    justify-content:space-between;
    font-size:11px;
    margin-bottom:2px;
    opacity:.85;
  }
  .gs-chat-text{
    white-space:pre-wrap;
  }
  .gs-chat-input-wrap{
    display:flex;
    gap:8px;
  }
  .gs-chat-input-wrap textarea{
    flex:1;
    resize:none;
    border-radius:12px;
    border:1px solid #ddd;
    padding:8px 12px;
    font-size:13px;
    max-height:80px;
  }
  .gs-chat-input-wrap button{
    border-radius:999px;
    border:0;
    padding:0 16px;
    font-size:13px;
    font-weight:700;
    background:#ff5043;
    color:#fff;
    cursor:pointer;
    white-space:nowrap;
  }
  .gs-chat-order-meta{
    font-size:12px;
    color:#555;
  }
  .gs-chat-order-meta span{
    display:inline-block;
    margin-right:8px;
  }
  .gs-chat-back{
    font-size:12px;
    text-decoration:none;
    color:#ff5043;
  }
</style>
@endpush

@section('content')
@php
  $order   = $chat->order;
  $orderId = optional($order)->id_pesanan ?? $chat->id_chat;
  $customerName = optional(optional($order)->pengguna)->nama ?? 'Pelanggan';
  $status = $order->status_pesanan ?? '-';
  $statusLbl = $status !== '-' ? ucwords(str_replace('_',' ', $status)) : '-';
  $tanggalLabel = $order && $order->tanggal_pesanan
      ? \Carbon\Carbon::parse($order->tanggal_pesanan)->format('d M Y · H:i')
      : '-';
  $currentUserId = auth()->id();
@endphp

<div class="gs-chat-page">
  <div class="gs-chat-header">
    <div class="gs-chat-header-left">
      <strong>Pesanan #{{ $orderId }}</strong><br>
      <span>{{ $customerName }}</span><br>
      <div class="gs-chat-order-meta">
        <span>Status: {{ $statusLbl }}</span>
        <span>Tanggal: {{ $tanggalLabel }}</span>
      </div>
    </div>
    <div class="gs-chat-header-right">
      <a href="{{ route('admin.chats.index') }}" class="gs-chat-back">&larr; Kembali ke daftar chat</a>
    </div>
  </div>

  <div class="gs-chat-main-card">
    <div class="gs-chat-body" id="gs-admin-chat-body">
      @if ($messages->isEmpty())
        <div style="font-size:13px;color:#999;margin-top:20px;text-align:center;">
          Belum ada pesan. Mulai chat dengan pelanggan jika perlu konfirmasi detail pesanan.
        </div>
      @else
        @foreach ($messages as $message)
          @php
            $isMine = $message->id_pengguna === $currentUserId;
            $senderLabel = $isMine
              ? 'Kamu'
              : (($message->sender_role === 'admin') ? 'Kasir' : ($message->sender->nama ?? 'Pelanggan'));
          @endphp
          <div class="gs-chat-message {{ $isMine ? 'is-mine' : 'is-other' }}">
            <div class="gs-chat-bubble">
              <div class="gs-chat-meta">
                <span>{{ $senderLabel }}</span>
                <span>{{ optional($message->created_at)->format('d M H:i') }}</span>
              </div>
              <div class="gs-chat-text">
                {!! nl2br(e($message->isi_pesan)) !!}
              </div>
            </div>
          </div>
        @endforeach
      @endif
    </div>

    <div class="gs-chat-footer">
      <form method="POST" action="{{ route('admin.chats.send', ['chat' => $chat->id_chat]) }}">
        @csrf
        <div class="gs-chat-input-wrap">
          <textarea
            name="message"
            rows="1"
            placeholder="Tulis pesan ke pelanggan..."
            required
          >{{ old('message') }}</textarea>
          <button type="submit">Kirim</button>
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
    var box = document.getElementById('gs-admin-chat-body');
    if (box) {
      box.scrollTop = box.scrollHeight;
    }
  });
</script>
@endpush
