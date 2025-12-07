{{-- resources/views/pages/pages/order-status.blade.php --}}
@extends('layouts.app')
@section('title','Status Pesanan')

@php
    use Illuminate\Support\Carbon;

    $rupiah    = fn($n) => 'Rp '.number_format((int)$n,0,',','.');
    $orderId   = $order->id_pesanan ?? $order->getKey();
    $metode    = $order->metode_pembayaran ?? 'cod';
    $rawStatus = $order->status_pesanan ?? 'menunggu_pembayaran';

    // ====== Pipeline timeline (dipakai user) ======
    $pipeline = [
        [
            'key'   => 'received',
            'label' => 'Pesanan dibuat',
            'desc'  => 'Pesanan berhasil dibuat di sistem.',
            'time'  => $order->tanggal_pesanan ?? $order->created_at,
        ],
        [
            'key'   => 'paid',
            'label' => $metode === 'qris'
                ? 'Menunggu konfirmasi kasir'
                : 'Pesanan dikonfirmasi kasir',
            'desc'  => $metode === 'qris'
                ? 'Kasir sedang memeriksa pembayaranmu.'
                : 'Kasir memeriksa dan mengkonfirmasi pesananmu.',
            'time'  => $order->waktu_konfirmasi ?? null,
        ],
        [
            'key'   => 'kitchen',
            'label' => 'Proses dapur',
            'desc'  => 'Tim dapur menyiapkan pesanan.',
            'time'  => $order->waktu_diproses ?? null,
        ],
        [
            'key'   => 'assigned',
            'label' => 'Driver ditugaskan',
            'desc'  => 'Driver menuju lokasi toko.',
            'time'  => $order->waktu_driver_dapat ?? null,
        ],
        [
            'key'   => 'picked',
            'label' => 'Pesanan siap diambil',
            'desc'  => 'Pesanan selesai disiapkan dan diambil driver.',
            'time'  => null,
        ],
        [
            'key'   => 'delivering',
            'label' => 'Dalam perjalanan',
            'desc'  => 'Driver mengantar pesanan ke alamatmu.',
            'time'  => null,
        ],
    ];

    // Mapping status_pesanan -> step aktif
    $mapStatusToStep = [
        'menunggu_pembayaran'      => 0,
        'menunggu_konfirmasi_toko' => 1,
        'diproses_dapur'           => 2,
        'diantar'                  => 5,
        'selesai'                  => 5,
        'dibatalkan'               => 0,

        'dikirim'                  => 5,
        'diproses'                 => 2,
    ];

    $step       = $mapStatusToStep[$rawStatus] ?? 0;
    $isFinished = $rawStatus === 'selesai';

    // Label status chips
    $statusLabelMap = [
        'menunggu_pembayaran'      => 'Menunggu pembayaran',
        'menunggu_konfirmasi_toko' => 'Menunggu konfirmasi kasir',
        'diproses_dapur'           => 'Sedang diproses',
        'diantar'                  => 'Sedang diantar',
        'selesai'                  => 'Selesai',
        'dibatalkan'               => 'Dibatalkan',
    ];

    $statusChip = $statusLabelMap[$rawStatus] ?? ucfirst(str_replace('_',' ', $rawStatus));

    // Dummy merchant & customer
    $merchant = [
        'name' => 'GOLDEN SPICE',
        'addr' => 'Cabang 1 • Jl. Sawah No. 25',
        'phone'=> '021-123 456',
    ];

    $authUser = auth()->user();

    $customer = [
        'name'  => $authUser->nama ?? $order->nama_pelanggan ?? 'Pelanggan',
        'phone' => $authUser->nomor_telepon ?? $order->nomor_telepon ?? '-',
    ];

    // Data ulasan
    $review      = $order->ulasan ?? null;
    $canReview   = $rawStatus === 'selesai';
    $reviewRoute = route('orders.review.submit', ['id' => $orderId]);
@endphp

@section('content')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>

<style>
  body{background:#fdf6ef}
  .os-wrap{max-width:880px;margin:0 auto;padding:18px 14px}
  .os-back{display:inline-flex;align-items:center;gap:8px;font-weight:900;margin:6px 0 12px;text-decoration:none;color:#111}
  .os-card{background:#fff;border-radius:16px;box-shadow:0 8px 24px rgba(0,0,0,.06);padding:14px 14px;margin-bottom:12px}
  .os-head{display:flex;justify-content:space-between;align-items:center;margin-bottom:8px}
  .chip{font-size:12px;background:#e8fff1;border:1px solid #b7f0cf;color:#0a8a3c;padding:6px 10px;border-radius:999px;font-weight:800}

  /* timeline */
  .tl{margin-top:4px}
  .tl-item{display:flex;align-items:flex-start;margin-bottom:12px}
  .tl-dot{width:18px;height:18px;border-radius:999px;display:grid;place-items:center;font-size:11px;margin-top:3px}
  .dot-done{background:#e8fff1;color:#0a8a3c}
  .dot-now{background:#fff3cd;color:#b45309;border:1px solid #fde68a}
  .dot-not{background:#eee;color:#bbb}
  .tl-title{font-weight:900;margin:0}
  .tl-desc{font-size:12px;color:#666;margin-top:2px}
  .tl-time{font-size:11px;color:#888;margin-top:4px}

  /* cards kecil */
  .mini{display:flex;gap:10px;align-items:flex-start}
  .mini-ava{width:36px;height:36px;border-radius:999px;background:#ff5043;color:#fff;display:grid;place-items:center;font-weight:900}
  .mini-title{margin:0;font-weight:900}
  .mini-desc{font-size:12px;color:#666}
  .act{margin-left:auto;background:#ffedf0;border:0;color:#d0143b;border-radius:999px;width:32px;height:32px;cursor:pointer;display:inline-flex;align-items:center;justify-content:center}

  /* tombol chat khusus */
  .chat-pill{
      margin-left:auto;
      display:flex;
      flex-direction:column;
      align-items:center;
      justify-content:center;
      text-decoration:none;
      font-size:11px;
      color:#e11d48;
  }
  .chat-pill-icon{
      width:26px;
      height:26px;
      border-radius:999px;
      background:#ffeef2;
      display:flex;
      align-items:center;
      justify-content:center;
      margin-bottom:2px;
      font-size:13px;
  }

  /* map */
  .map{height:180px;border-radius:12px;overflow:hidden}
  #map{height:100%;width:100%;background:#ddd}
  .map-meta{display:flex;justify-content:space-between;font-size:12px;margin-bottom:6px}
  .badge-green{font-size:11px;background:#e8fff1;border-radius:999px;padding:4px 10px;font-weight:700;color:#0a8a3c}

  /* items */
  .item{display:flex;align-items:center;gap:10px;padding:8px 0;border-bottom:1px dashed #f1e0d0}
  .item:last-child{border-bottom:none}
  .thumb{width:56px;height:56px;border-radius:14px;object-fit:cover;background:#f3f3f3}
  .it-title{margin:0;font-weight:700}
  .it-note{font-size:12px;color:#777}
  .it-price{margin-left:auto;font-weight:700}

  /* summary */
  .sum-row{display:flex;justify-content:space-between;padding:6px 0}
  .sum-total{border-top:1px dashed #eee;margin-top:6px;padding-top:10px;font-weight:900}

  /* review card */
  .rv-form{margin-top:4px}
  .rv-form-row{display:flex;flex-direction:column;font-size:13px;margin-bottom:8px}
  .rv-input{
      border-radius:10px;
      border:1px solid #e5e5e5;
      padding:8px 10px;
      font-size:13px;
      font-family:inherit;
      resize:vertical;
  }
  .rv-input:focus{
      outline:none;
      border-color:#ff5043;
      box-shadow:0 0 0 1px rgba(255,80,67,.15);
  }
  .rv-btn{
      margin-top:6px;
      border:none;
      border-radius:999px;
      background:#ff5043;
      color:#fff;
      font-size:13px;
      font-weight:700;
      padding:8px 16px;
      cursor:pointer;
  }
  .rv-btn:hover{filter:brightness(1.05);}

  /* rating bintang */
  .rv-stars{
      display:flex;
      flex-direction:row-reverse;
      gap:4px;
      font-size:24px;
      cursor:pointer;
      margin-top:4px;
  }
  .rv-stars input{
      display:none;
  }
  .rv-stars label{
      color:#e5e7eb;
      cursor:pointer;
      transition:transform .08s ease,color .08s ease;
  }
  .rv-stars label:hover,
  .rv-stars label:hover ~ label{
      color:#fbbf24;
      transform:scale(1.05);
  }
  .rv-stars input:checked ~ label{
      color:#f59e0b;
  }
</style>

<div class="os-wrap">
  <a href="{{ route('menu') }}" class="os-back">← KEMBALI KE MENU</a>

  {{-- Info QRIS khusus HALAMAN USER --}}
  @if ($metode === 'qris')
      @if ($rawStatus === 'menunggu_pembayaran')
          <div class="os-card" style="margin-bottom:12px;background:#fff8e1;">
              <div style="font-size:13px;">
                  <strong>Menunggu pembayaran QRIS</strong><br>
                  Silakan scan kode QR dan selesaikan pembayaran. Setelah itu tekan tombol
                  <em>"Saya sudah bayar"</em> di halaman QR.
              </div>
          </div>
      @elseif ($rawStatus === 'menunggu_konfirmasi_toko')
          <div class="os-card" style="margin-bottom:12px;background:#e3f2fd;">
              <div style="font-size:13px;">
                  <strong>Menunggu konfirmasi kasir</strong><br>
                  Pembayaran QRIS kamu sudah terkirim. Kasir sedang memeriksa transaksi.
              </div>
          </div>
      @elseif ($rawStatus === 'diproses_dapur')
          <div class="os-card" style="margin-bottom:12px;background:#e8fff1;">
              <div style="font-size:13px;">
                  <strong>Pembayaran terverifikasi</strong><br>
                  Kasir sudah mengkonfirmasi pembayaranmu. Pesanan sedang disiapkan di dapur.
              </div>
          </div>
      @endif
  @endif

  {{-- STATUS PESANAN --}}
  <div class="os-card">
    <div class="os-head">
      <div>
        <div style="font-weight:900">Status Pesanan</div>
        <div style="font-size:12px;color:#666">
          Dibuat pada {{ Carbon::parse($order->tanggal_pesanan ?? $order->created_at)->format('d M Y • H:i') }}
        </div>
        <div style="font-size:11px;color:#999;margin-top:2px;">
          ID Pesanan: #{{ $orderId }} &middot; Metode: {{ strtoupper($metode) }}
        </div>
      </div>
      <span class="chip">{{ $statusChip }}</span>
    </div>

    <div class="tl">
      @foreach($pipeline as $i => $st)
        @php
            if ($isFinished) {
                $state = 'done';
            } else {
                $state = $i < $step ? 'done' : ($i === $step ? 'now' : 'not');
            }
            $time  = $st['time'] ? Carbon::parse($st['time'])->format('H:i') : null;
        @endphp
        <div class="tl-item">
          <div class="tl-dot dot-{{ $state }}">
            {{ $state === 'done' ? '✓' : ($state === 'now' ? '•' : '') }}
          </div>
          <div style="margin-left:22px">
            <p class="tl-title">{{ $st['label'] }}</p>
            <div class="tl-desc">{{ $st['desc'] }}</div>
            <div class="tl-time">{{ $time ?? '—' }}</div>
          </div>
        </div>
      @endforeach
    </div>
  </div>

  {{-- Merchant --}}
  <div class="os-card mini">
    <div class="mini-ava">S</div>
    <div>
      <p class="mini-title">{{ $merchant['name'] }}</p>
      <div class="mini-desc">{{ $merchant['addr'] }}</div>
      <div class="mini-desc">Telp: {{ $merchant['phone'] }}</div>
    </div>
    <button class="act">☎</button>
  </div>

  {{-- Customer + tombol Chat --}}
  <div class="os-card mini">
    <div class="mini-ava" style="background:#111">U</div>
    <div>
      <p class="mini-title">{{ $customer['name'] }}</p>
      <div class="mini-desc">Telepon: {{ $customer['phone'] }}</div>
    </div>

    <a href="{{ route('orders.chat', ['id' => $orderId]) }}" class="chat-pill">
        <span class="chat-pill-icon">✉</span>
        <span>Chat</span>
    </a>
  </div>

  {{-- Lokasi Driver --}}
  <div class="os-card">
    <div style="font-weight:900;margin-bottom:6px">Lokasi Driver</div>
    <div class="map-meta">
      <span class="badge-green">1.4 km dari alamatmu</span>
      <span style="color:#888">Estimasi tiba 8–12 menit</span>
    </div>
    <div class="map">
      <div id="map" aria-label="driver-location-map"></div>
    </div>
  </div>

  {{-- Detail Pesanan --}}
  <div class="os-card">
    <div style="font-weight:900;margin-bottom:6px">Pesananmu</div>
    @forelse($items as $it)
      <div class="item">
        @if(!empty($it->url_gambar))
          <img class="thumb" src="{{ asset($it->url_gambar) }}" alt="{{ $it->nama_produk }}">
        @else
          <div class="thumb"></div>
        @endif
        <div>
          <p class="it-title">{{ $it->nama_produk }}</p>
          <div class="it-note">Qty {{ $it->jumlah }} • @ {{ $rupiah($it->harga) }}</div>
          @if(!empty($it->catatan))
            <div class="it-note">• {{ $it->catatan }}</div>
          @endif
        </div>
        <div class="it-price">{{ $rupiah($it->subtotal) }}</div>
      </div>
    @empty
      <p style="font-size:13px;color:#777;">Tidak ada item di pesanan ini.</p>
    @endforelse
  </div>

  {{-- Ringkasan Pembayaran --}}
  <div class="os-card">
    <div style="font-weight:900;margin-bottom:6px">Ringkasan Pembayaran</div>
    <div class="sum-row">
        <span>Harga produk</span>
        <span>{{ $rupiah($order->biaya_produk) }}</span>
    </div>
    <div class="sum-row">
        <span>Biaya penanganan & pengiriman</span>
        <span>{{ $rupiah($order->biaya_ongkir) }}</span>
    </div>
    <div class="sum-row sum-total">
        <span>Total pembayaran</span>
        <span>{{ $rupiah($order->total_pembayaran) }}</span>
    </div>
  </div>

  {{-- Ulasan Pesanan --}}
  @if($canReview)
      <div class="os-card">
        <div style="font-weight:900;margin-bottom:6px">
            {{ $review ? 'Ulasan kamu' : 'Berikan ulasan untuk pesanan ini' }}
        </div>

        @if($review)
            <div style="font-size:13px;margin-bottom:8px;">
                <strong>Rating sekarang:</strong>
                @for($i = 1; $i <= 5; $i++)
                    <span style="color:{{ $i <= ($review->rating ?? 0) ? '#f59e0b' : '#e5e7eb' }}">★</span>
                @endfor
                <span style="font-size:12px;color:#777;margin-left:4px;">
                    {{ $review->rating }}/5
                </span>
            </div>

            @if($review->komentar)
                <div style="font-size:13px;color:#444;line-height:1.6;margin-bottom:8px;">
                    {{ $review->komentar }}
                </div>
            @endif

            <div style="margin-top:2px;font-size:12px;color:#999;margin-bottom:6px;">
                Kamu bisa mengubah rating atau komentar di bawah ini.
            </div>

            {{-- Form update ulasan --}}
            <form method="POST" action="{{ $reviewRoute }}" class="rv-form">
                @csrf
                <div class="rv-form-row">
                    <label style="font-size:13px;font-weight:600;">Ubah rating</label>
                    <div class="rv-stars">
                        @for($i = 5; $i >= 1; $i--)
                            <input
                                type="radio"
                                id="rating-edit-{{ $i }}"
                                name="rating"
                                value="{{ $i }}"
                                {{ (int)old('rating', $review->rating ?? 0) === $i ? 'checked' : '' }}
                            >
                            <label for="rating-edit-{{ $i }}">★</label>
                        @endfor
                    </div>
                </div>

                <div class="rv-form-row">
                    <label for="komentar" style="font-size:13px;font-weight:600;">Komentar (opsional)</label>
                    <textarea id="komentar" name="komentar" rows="3" class="rv-input" placeholder="Ceritakan pengalamanmu dengan pesanan ini">{{ old('komentar', $review->komentar ?? '') }}</textarea>
                </div>

                <button type="submit" class="rv-btn">
                    Simpan perubahan
                </button>
            </form>
        @else
            @if($errors->any())
                <div style="background:#fef2f2;border:1px solid #fecaca;padding:8px 10px;border-radius:10px;font-size:12px;color:#b91c1c;margin-bottom:8px;">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ $reviewRoute }}" class="rv-form">
                @csrf

                <div class="rv-form-row">
                    <label style="font-size:13px;font-weight:600;">Rating</label>
                    <div class="rv-stars">
                        @for($i = 5; $i >= 1; $i--)
                            <input
                                type="radio"
                                id="rating-new-{{ $i }}"
                                name="rating"
                                value="{{ $i }}"
                                {{ (int)old('rating') === $i ? 'checked' : '' }}
                            >
                            <label for="rating-new-{{ $i }}">★</label>
                        @endfor
                    </div>
                </div>

                <div class="rv-form-row">
                    <label for="komentar" style="font-size:13px;font-weight:600;">Komentar (opsional)</label>
                    <textarea id="komentar" name="komentar" rows="3" class="rv-input" placeholder="Ceritakan pengalamanmu dengan pesanan ini">{{ old('komentar') }}</textarea>
                </div>

                <button type="submit" class="rv-btn">
                    Kirim Ulasan
                </button>
            </form>
        @endif
      </div>
  @endif
</div>

<script>
  // Map kecil untuk lokasi driver (dummy: fallback Jakarta)
  const lat = {{ json_encode($order->lat ?? -6.200) }};
  const lng = {{ json_encode($order->lng ?? 106.816) }};

  const m = L.map('map', {
      zoomControl: false,
      dragging: false,
      scrollWheelZoom: false
  }).setView([lat, lng], 12);

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution:'&copy; OpenStreetMap'
  }).addTo(m);

  L.marker([lat, lng]).addTo(m);
</script>
@endsection
