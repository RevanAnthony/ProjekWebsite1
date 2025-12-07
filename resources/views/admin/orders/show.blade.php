{{-- resources/views/admin/orders/show.blade.php --}}
@extends('admin.layouts.panel')

@php
    use Illuminate\Support\Carbon;

    $rupiah = fn($n) => 'Rp '.number_format((int) $n, 0, ',', '.');

    /** @var \App\Models\Pesanan $order */
    $orderId   = $order->id_pesanan ?? $order->getKey();
    $status    = $order->status_pesanan ?? 'menunggu_pembayaran';
    $metode    = strtolower($order->metode_pembayaran ?? 'cod');
    $isCod     = $metode === 'cod';

    // ==========================
    // Label status utama (chip)
    // ==========================
    if ($isCod) {
        $statusLabelMap = [
            'menunggu_pembayaran'      => 'Menunggu Konfirmasi Pesanan',
            'menunggu_konfirmasi_toko' => 'Menunggu Konfirmasi Toko',
            'diproses_dapur'           => 'Diproses Dapur',
            'diantar'                  => 'Diantar',
            'selesai'                  => 'Selesai',
            'dibatalkan'               => 'Dibatalkan',
        ];
    } else {
        $statusLabelMap = [
            'menunggu_pembayaran'      => 'Menunggu Pembayaran',
            'menunggu_konfirmasi_toko' => 'Menunggu Konfirmasi Toko',
            'diproses_dapur'           => 'Diproses Dapur',
            'diantar'                  => 'Diantar',
            'selesai'                  => 'Selesai',
            'dibatalkan'               => 'Dibatalkan',
        ];
    }

    $statusLabel = $statusLabelMap[$status] ?? ucwords(str_replace('_', ' ', $status));

    // ==========================
    // Timeline (admin)
    // ==========================
    // 0: waiting 1: waiting-confirm 2: cooking 3: delivery 4: done 5: canceled (khusus)
    $timelineSteps = [
        [
            'key'   => 'waiting',
            'label' => $isCod ? 'Menunggu Konfirmasi Pesanan' : 'Menunggu Pembayaran',
            'desc'  => $isCod
                ? 'Order dibuat, menunggu konfirmasi dari kasir.'
                : 'Order dibuat, menunggu pembayaran dari pelanggan.',
        ],
        [
            'key'   => 'waiting_confirm',
            'label' => 'Menunggu Konfirmasi Toko',
            'desc'  => $isCod
                ? 'Pelanggan sudah membuat pesanan, menunggu toko memproses.'
                : 'Pelanggan mengklaim sudah bayar, menunggu cek kasir.',
        ],
        [
            'key'   => 'cooking',
            'label' => 'Diproses Dapur',
            'desc'  => 'Dapur sedang menyiapkan pesanan.',
        ],
        [
            'key'   => 'delivery',
            'label' => 'Diantar',
            'desc'  => 'Driver sedang mengantar pesanan ke pelanggan.',
        ],
        [
            'key'   => 'done',
            'label' => 'Selesai',
            'desc'  => 'Pesanan sudah diterima pelanggan.',
        ],
        [
            'key'   => 'cancel',
            'label' => 'Dibatalkan',
            'desc'  => 'Pesanan dibatalkan oleh kasir/pelanggan.',
        ],
    ];

    // Map status_pesanan -> index step
    $statusToStep = [
        'menunggu_pembayaran'      => 0,
        'menunggu_konfirmasi_toko' => 1,
        'diproses_dapur'           => 2,
        'diantar'                  => 3,
        'selesai'                  => 4,
        'dibatalkan'               => 5,
    ];

    $activeStep = $statusToStep[$status] ?? 0;
@endphp

@section('title', "Order #{$orderId} — Kasir Panel")
@section('page-title', "Order #{$orderId}")
@section('page-subtitle', 'Detail pesanan dan status terkini.')

@push('styles')
<style>
  .order-layout{
    display:grid;
    grid-template-columns: minmax(0, 2fr) minmax(260px, 1fr);
    gap:18px;
    align-items:flex-start;
  }

  .card{
    background:#fff;
    border-radius:18px;
    box-shadow:0 8px 24px rgba(0,0,0,.06);
    padding:18px 20px;
  }

  .order-header{
    display:flex;
    justify-content:space-between;
    align-items:flex-start;
    margin-bottom:14px;
  }
  .order-title{
    font-size:18px;
    font-weight:800;
    margin:0 0 4px;
  }
  .order-meta{
    font-size:12px;
    color:#777;
    margin:0;
  }
  .badge-status{
    display:inline-flex;
    align-items:center;
    padding:4px 10px;
    border-radius:999px;
    font-size:11px;
    font-weight:700;
  }
  .badge-status--pending{
    background:#fff3cd;
    color:#8a6d3b;
  }
  .badge-status--cooking{
    background:#e3f2fd;
    color:#1565c0;
  }
  .badge-status--delivery{
    background:#e8f5e9;
    color:#2e7d32;
  }
  .badge-status--done{
    background:#e8f5e9;
    color:#2e7d32;
  }
  .badge-status--cancel{
    background:#ffebee;
    color:#c62828;
  }

  .order-info-grid{
    display:grid;
    grid-template-columns:repeat(2,minmax(0,1fr));
    gap:10px 40px;
    font-size:12px;
    margin-bottom:16px;
  }
  .info-label{
    font-size:11px;
    text-transform:uppercase;
    letter-spacing:.04em;
    color:#999;
  }
  .info-value{
    font-size:13px;
    font-weight:700;
  }

  table.order-items{
    width:100%;
    border-collapse:collapse;
    margin-top:8px;
    font-size:12px;
  }
  table.order-items th,
  table.order-items td{
    padding:6px 4px;
  }
  table.order-items thead th{
    font-size:11px;
    text-transform:uppercase;
    letter-spacing:.04em;
    color:#999;
    border-bottom:1px solid #eee;
  }
  table.order-items tbody td{
    border-bottom:1px solid #f3f3f3;
  }
  table.order-items tfoot td{
    font-weight:800;
    border-top:1px solid #eee;
    padding-top:8px;
  }

  .order-actions{
    display:flex;
    flex-wrap:wrap;
    gap:8px;
    margin-top:16px;
  }
  .btn-main{
    border-radius:999px;
    border:0;
    padding:8px 16px;
    font-size:12px;
    font-weight:800;
    background:#ff5043;
    color:#fff;
    cursor:pointer;
  }
  .btn-ghost{
    border-radius:999px;
    border:1px solid #ddd;
    padding:7px 14px;
    font-size:12px;
    font-weight:700;
    background:#fff;
    cursor:pointer;
  }
  .btn-danger{
    border-radius:999px;
    border:0;
    padding:7px 14px;
    font-size:12px;
    font-weight:700;
    background:#ffebee;
    color:#c62828;
    cursor:pointer;
  }

  /* Timeline */
  .timeline-head{
    font-weight:800;
    margin:0 0 4px;
  }
  .timeline-sub{
    font-size:12px;
    color:#777;
    margin:0 0 10px;
  }
  .timeline-list{
    list-style:none;
    padding:0;
    margin:0;
  }
  .timeline-item{
    display:flex;
    align-items:flex-start;
    gap:8px;
    padding:6px 0;
  }
  .tl-dot{
    width:12px;
    height:12px;
    border-radius:999px;
    margin-top:4px;
    border:2px solid #ddd;
    background:#fff;
  }
  .tl-dot--active{
    background:#ff5043;
    border-color:#ff5043;
  }
  .tl-dot--done{
    background:#2ecc71;
    border-color:#2ecc71;
  }
  .tl-label{
    font-size:13px;
    font-weight:700;
  }
  .tl-desc{
    font-size:11px;
    color:#777;
  }
</style>
@endpush

@section('content')
<div class="order-layout">
  {{-- LEFT: Ringkasan order + items --}}
  <div class="card">
    @php
        // kelas chip status
        $statusClass = 'badge-status';
        if (in_array($status, ['menunggu_pembayaran','menunggu_konfirmasi_toko'])) {
            $statusClass .= ' badge-status--pending';
        } elseif ($status === 'diproses_dapur') {
            $statusClass .= ' badge-status--cooking';
        } elseif ($status === 'diantar') {
            $statusClass .= ' badge-status--delivery';
        } elseif ($status === 'selesai') {
            $statusClass .= ' badge-status--done';
        } elseif ($status === 'dibatalkan') {
            $statusClass .= ' badge-status--cancel';
        }
    @endphp

    <div class="order-header">
      <div>
        <h3 class="order-title">Order #{{ $orderId }}</h3>
        <p class="order-meta">
          Tanggal Pesanan<br>
          <strong>
            {{ $order->tanggal_pesanan
                ? Carbon::parse($order->tanggal_pesanan)->format('d M Y · H:i')
                : '-' }}
          </strong>
        </p>
      </div>
      <span class="{{ $statusClass }}">
        {{ $statusLabel }}
      </span>
    </div>

    <div class="order-info-grid">
      <div>
        <div class="info-label">Metode Pembayaran</div>
        <div class="info-value">{{ strtoupper($metode) }}</div>
      </div>
      <div>
        <div class="info-label">Metode Pengambilan</div>
        <div class="info-value">{{ ucfirst($order->metode_pengambilan ?? '-') }}</div>
      </div>
      <div>
        <div class="info-label">Pelanggan</div>
        <div class="info-value">
          {{ optional($order->pengguna)->nama ?? 'Pelanggan' }}
        </div>
      </div>
      <div>
        <div class="info-label">Kontak Pelanggan</div>
        <div class="info-value">
          {{ optional($order->pengguna)->email ?? '-' }}
        </div>
      </div>
      <div>
        <div class="info-label">Total Pembayaran</div>
        <div class="info-value">{{ $rupiah($order->total_pembayaran) }}</div>
      </div>
    </div>

    <div style="margin-top:4px;font-size:12px;font-weight:700;">Item Pesanan</div>

    <table class="order-items">
      <thead>
        <tr>
          <th align="left">Produk</th>
          <th align="center" width="40">Qty</th>
          <th align="right" width="90">Harga</th>
          <th align="right" width="100">Subtotal</th>
        </tr>
      </thead>
      <tbody>
        @foreach($order->items as $detail)
          <tr>
            <td>
              {{ optional($detail->produk)->nama_produk ?? $detail->nama_produk ?? 'Produk' }}
            </td>
            <td align="center">{{ $detail->jumlah }}</td>
            <td align="right">{{ $rupiah($detail->harga) }}</td>
            <td align="right">{{ $rupiah($detail->subtotal) }}</td>
          </tr>
        @endforeach
      </tbody>
      <tfoot>
        <tr>
          <td colspan="3" align="right">Total</td>
          <td align="right">{{ $rupiah($order->total_pembayaran) }}</td>
        </tr>
      </tfoot>
    </table>

    {{-- ACTIONS (ADMIN/KASIR) --}}
    <div class="order-actions">
      {{-- Tombol utama berubah sesuai status --}}
      @if (in_array($status, ['menunggu_pembayaran','menunggu_konfirmasi_toko']))
        <form method="POST" action="{{ route('admin.orders.update-status', $orderId) }}">
          @csrf
          <input type="hidden" name="action" value="to_cooking">
          <button type="submit" class="btn-main">
            {{ $isCod ? 'Konfirmasi Pesanan & Kirim ke Dapur' : 'Konfirmasi Pembayaran & Kirim ke Dapur' }}
          </button>
        </form>
      @elseif ($status === 'diproses_dapur')
        <form method="POST" action="{{ route('admin.orders.update-status', $orderId) }}">
          @csrf
          <input type="hidden" name="action" value="to_delivery">
          <button type="submit" class="btn-main">
            Tandai Siap Diantar
          </button>
        </form>
      @elseif ($status === 'diantar')
        <form method="POST" action="{{ route('admin.orders.update-status', $orderId) }}">
          @csrf
          <input type="hidden" name="action" value="finish">
          <button type="submit" class="btn-main">
            Tandai Selesai
          </button>
        </form>
      @endif

      {{-- Batalkan pesanan (selain sudah selesai / dibatalkan) --}}
      @if (!in_array($status, ['selesai','dibatalkan']))
        <form method="POST" action="{{ route('admin.orders.update-status', $orderId) }}">
          @csrf
          <input type="hidden" name="action" value="cancel">
          <button type="submit" class="btn-danger">
            Batalkan Pesanan
          </button>
        </form>
      @endif

      {{-- Kembali ke daftar --}}
      <a href="{{ route('admin.orders.index') }}" class="btn-ghost">
        Kembali ke daftar pesanan
      </a>
    </div>
  </div>

  {{-- RIGHT: Timeline status --}}
  <div class="card">
    <h4 class="timeline-head">Status Timeline</h4>
    <p class="timeline-sub">Pantau progres pesanan ini.</p>

    <ul class="timeline-list">
      @foreach($timelineSteps as $idx => $step)
        @php
            if ($activeStep === 5 && $idx < 5) {
                // kalau dibatalkan, step 0-4 dianggap "done" abu/merah ringan
                $state = 'done';
            } else {
                $state = $idx < $activeStep ? 'done' : ($idx === $activeStep ? 'active' : 'idle');
            }
        @endphp
        <li class="timeline-item">
          <span class="tl-dot
            {{ $state === 'done' ? ' tl-dot--done' : '' }}
            {{ $state === 'active' ? ' tl-dot--active' : '' }}">
          </span>
          <div>
            <div class="tl-label">
              {{ $step['label'] }}
            </div>
            <div class="tl-desc">
              {{ $step['desc'] }}
            </div>
          </div>
        </li>
      @endforeach
    </ul>
  </div>
</div>
@endsection
