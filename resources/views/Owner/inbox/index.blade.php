@extends('owner.layout')

@section('title', 'Review Pelanggan — Owner')

@push('styles')
<style>
    .oi-page{}

    .oi-header{
        margin-bottom:18px;
    }
    .oi-title{
        font-family:'Koulen',system-ui;
        letter-spacing:.04em;
        font-size:26px;
        margin-bottom:4px;
    }
    .oi-sub{
        font-size:13px;
        color:#777;
    }

    /* ===== TOP STATS ===== */
    .oi-stats-row{
        display:grid;
        grid-template-columns:repeat(3,minmax(0,1fr));
        gap:14px;
        margin-bottom:16px;
    }
    .oi-card{
        background:#fff;
        border-radius:18px;
        padding:14px 16px;
        box-shadow:0 6px 18px rgba(0,0,0,.06);
    }
    .oi-card-head{
        display:flex;
        align-items:flex-start;
        justify-content:space-between;
        margin-bottom:8px;
    }
    .oi-card-icon{
        width:32px;
        height:32px;
        border-radius:12px;
        background:#fff7f0;
        color:#f97316;
        display:grid;
        place-items:center;
        font-family:'Material Symbols Rounded';
        font-size:20px;
    }
    .oi-card-icon.green{
        background:#ecfdf3;
        color:#16a34a;
    }
    .oi-card-icon.red{
        background:#fef2f2;
        color:#ef4444;
    }
    .oi-chip{
        font-size:11px;
        padding:2px 6px;
        border-radius:999px;
        background:#ecfdf3;
        color:#16a34a;
        font-weight:600;
    }
    .oi-card-title{
        font-size:12px;
        color:#777;
        margin-bottom:4px;
    }
    .oi-card-value{
        font-size:20px;
        font-weight:700;
    }
    .oi-card-caption{
        font-size:11px;
        color:#999;
        margin-top:4px;
    }

    /* ===== CHARTS ===== */
    .oi-charts-row{
        display:grid;
        grid-template-columns:2fr 1.2fr;
        gap:14px;
        margin-bottom:18px;
    }
    .oi-card-title-lg{
        font-size:13px;
        font-weight:600;
        margin-bottom:2px;
    }
    .oi-card-sub{
        font-size:11px;
        color:#999;
        margin-bottom:8px;
    }

    /* bar chart */
    .oi-bar-chart{
        display:flex;
        align-items:flex-end;
        gap:10px;
        height:180px;
        padding-top:8px;
    }
    .oi-bar{
        flex:1;
        border-radius:6px 6px 0 0;
        background:#e5e7eb;
        position:relative;
        overflow:hidden;
    }
    .oi-bar-fill{
        position:absolute;
        bottom:0;
        left:0;
        right:0;
        border-radius:6px 6px 0 0;
        background:#3b82f6;
    }
    .oi-bar-label{
        position:absolute;
        bottom:-18px;
        left:50%;
        transform:translateX(-50%);
        font-size:11px;
        color:#666;
        white-space:nowrap;
    }

    /* pie */
    .oi-pie-placeholder{
        height:180px;
        display:flex;
        align-items:center;
        justify-content:center;
    }
    .oi-pie{
        width:140px;
        height:140px;
        border-radius:999px;
        position:relative;
    }
    .oi-pie::after{
        content:'';
        position:absolute;
        inset:28px;
        border-radius:999px;
        background:#fff;
    }
    .oi-legend{
        margin-top:10px;
        display:flex;
        gap:12px;
        flex-wrap:wrap;
        font-size:11px;
    }
    .oi-legend-item{
        display:flex;
        align-items:center;
        gap:4px;
    }
    .oi-dot{
        width:8px;
        height:8px;
        border-radius:999px;
        background:#22c55e;
    }
    .oi-dot.neutral{ background:#f97316; }
    .oi-dot.negative{ background:#ef4444; }

    /* ===== LIST CARD ===== */
    .oi-list-card{
        background:#fff;
        border-radius:18px;
        padding:16px 18px;
        box-shadow:0 6px 18px rgba(0,0,0,.06);
    }
    .oi-list-head{
        display:flex;
        justify-content:space-between;
        align-items:center;
        margin-bottom:12px;
        font-size:13px;
    }
    .oi-list-head-count{
        font-weight:600;
    }

    .oi-range-pills{
        display:inline-flex;
        background:#f4f4f6;
        border-radius:999px;
        padding:3px;
        gap:2px;
    }
    .oi-range-pill{
        border-radius:999px;
        padding:4px 10px;
        font-size:11px;
        border:none;
        background:transparent;
        cursor:pointer;
        color:#555;
    }
    .oi-range-pill.is-active{
        background:#1d4ed8;
        color:#fff;
        font-weight:600;
    }

    .oi-search-row{
        margin-bottom:10px;
    }
    .oi-search-inline{
        display:flex;
        align-items:center;
        gap:8px;
        background:#f9fafb;
        border-radius:999px;
        padding:8px 12px;
        border:1px solid #e5e7eb;
    }
    .oi-search-inline span.icon{
        font-family:'Material Symbols Rounded';
        font-size:18px;
        color:#999;
    }
    .oi-search-inline input{
        border:none;
        outline:none;
        background:transparent;
        width:100%;
        font-size:13px;
    }

    .oi-filters{
        display:flex;
        gap:8px;
        margin-bottom:12px;
        flex-wrap:wrap;
        font-size:12px;
    }
    .oi-filter-pill{
        border-radius:999px;
        border:1px solid #e5e7eb;
        padding:4px 10px;
        background:#f9fafb;
        cursor:pointer;
        text-decoration:none;
        color:#444;
    }
    .oi-filter-pill.is-active{
        background:#ffc107;
        border-color:#fbbf24;
        font-weight:600;
    }

    /* ===== REVIEW ITEM ===== */
    .oi-review{
        display:grid;
        grid-template-columns:48px minmax(0,1fr) 40px;
        gap:10px;
        padding:10px 0;
        border-bottom:1px solid #f1f59;
        font-size:13px;
    }
    .oi-review:last-child{
        border-bottom:none;
    }
    .oi-avatar{
        width:40px;
        height:40px;
        border-radius:999px;
        background:#e5e7eb;
        display:grid;
        place-items:center;
        font-size:16px;
        font-weight:600;
        color:#4b5563;
    }
    .oi-review-header{
        display:flex;
        justify-content:space-between;
        align-items:flex-start;
        gap:8px;
        margin-bottom:2px;
    }
    .oi-name{
        font-weight:600;
    }
    .oi-time{
        font-size:11px;
        color:#9ca3af;
    }
    .oi-rating{
        font-size:11px;
        color:#f59e0b;
        margin-bottom:2px;
    }
    .oi-stars{
        letter-spacing:1px;
        margin-right:4px;
    }
    .oi-review-text{
        font-size:13px;
        color:#4b5563;
        margin-bottom:4px;
    }
    .oi-order{
        font-size:11px;
        color:#9ca3af;
    }
    .oi-flag{
        font-family:'Material Symbols Rounded';
        font-size:18px;
        color:#9ca3af;
        cursor:pointer;
    }
    .oi-review-footer{
        margin-top:4px;
        display:flex;
        justify-content:space-between;
        align-items:center;
        font-size:11px;
    }
    .oi-tag-urgent{
        color:#ef4444;
        font-weight:600;
    }
    .oi-link-reply{
        color:#2563eb;
        cursor:pointer;
    }
    .oi-empty{
        font-size:13px;
        color:#6b7280;
        padding:12px 0 4px;
    }

    @media (max-width:960px){
        .oi-stats-row,
        .oi-charts-row{
            grid-template-columns:1fr;
        }
        .oi-review{
            grid-template-columns:40px minmax(0,1fr);
        }
        .oi-flag{
            display:none;
        }
    }
</style>
@endpush

@section('content')
@php
    /** @var \Illuminate\Support\Collection|\App\Models\Ulasan[]|\Illuminate\Pagination\LengthAwarePaginator $reviews */

    // Data untuk bar chart (pakai data yang lagi ditampilkan)
    $ratingBuckets = [
        5 => $reviews->where('rating', 5)->count(),
        4 => $reviews->where('rating', 4)->count(),
        3 => $reviews->where('rating', 3)->count(),
        2 => $reviews->where('rating', 2)->count(),
        1 => $reviews->where('rating', 1)->count(),
    ];
    $maxBucket = max($ratingBuckets) ?: 1;

    // Data untuk pie chart
    $positive  = $reviews->where('rating', '>=', 4)->count();
    $neutral   = $reviews->where('rating', 3)->count();
    $negative  = $reviews->where('rating', '<=', 2)->count();
    $totalSent = max(1, $positive + $neutral + $negative);

    $posDeg = $totalSent ? ($positive / $totalSent) * 360 : 0;
    $neuDeg = $totalSent ? ($neutral  / $totalSent) * 360 : 0;
    $negDeg = 360 - $posDeg - $neuDeg; // sisanya buat jaga rounding
@endphp

<div class="oi-page">
    {{-- HEADER --}}
    <div class="oi-header">
        <div class="oi-title">Review Pelanggan</div>
        <div class="oi-sub">Lihat ringkasan rating dan tanggapi ulasan pelanggan.</div>
    </div>

    {{-- TOP STATS --}}
    <div class="oi-stats-row">
        <div class="oi-card">
            <div class="oi-card-head">
                <div class="oi-card-icon">star</div>
                <div class="oi-chip">+0,0%</div>
            </div>
            <div class="oi-card-title">Rata-rata Rating</div>
            <div class="oi-card-value">{{ number_format($avgRating, 1, ',', '.') }}</div>
            <div class="oi-card-caption">Sepanjang Waktu</div>
        </div>

        <div class="oi-card">
            <div class="oi-card-head">
                <div class="oi-card-icon green">chat</div>
                <div class="oi-chip">+0,0%</div>
            </div>
            <div class="oi-card-title">Total Ulasan</div>
            <div class="oi-card-value">{{ number_format($totalReviews, 0, ',', '.') }}</div>
            <div class="oi-card-caption">Sepanjang Waktu</div>
        </div>

        <div class="oi-card">
            <div class="oi-card-head">
                <div class="oi-card-icon red">warning</div>
                <div class="oi-chip">+0,0%</div>
            </div>
            <div class="oi-card-title">Total Respon Urgen</div>
            <div class="oi-card-value">{{ number_format($followUpCount, 0, ',', '.') }}</div>
            <div class="oi-card-caption">Butuh Respon</div>
        </div>
    </div>

    {{-- CHARTS --}}
    <div class="oi-charts-row">
        {{-- Bar chart rating --}}
        <div class="oi-card">
            <div class="oi-card-title-lg">Customer Reviews Chart</div>
            <div class="oi-card-sub">Sepanjang Waktu</div>

            <div class="oi-bar-chart">
                @foreach([5,4,3,2,1] as $star)
                    @php
                        $value  = $ratingBuckets[$star];
                        $height = max(8, ($value / $maxBucket) * 100);
                    @endphp
                    <div class="oi-bar">
                        <div class="oi-bar-fill" style="height: {{ $height }}%;"></div>
                        <div class="oi-bar-label">{{ $star }} Stars</div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Pie chart sentiment --}}
        <div class="oi-card">
            <div class="oi-card-title-lg">Analisis Sentimen</div>
            <div class="oi-card-sub">Sepanjang Waktu</div>

            <div class="oi-pie-placeholder">
                <div
                    class="oi-pie"
                    style="background:
                        conic-gradient(
                            #22c55e 0 {{ $posDeg }}deg,
                            #f97316 {{ $posDeg }}deg {{ $posDeg + $neuDeg }}deg,
                            #ef4444 {{ $posDeg + $neuDeg }}deg 360deg
                        );"
                ></div>
            </div>

            <div class="oi-legend">
                <div class="oi-legend-item">
                    <span class="oi-dot"></span>
                    <span>Positive ({{ round($positive / $totalSent * 100) }}%)</span>
                </div>
                <div class="oi-legend-item">
                    <span class="oi-dot neutral"></span>
                    <span>Neutral ({{ round($neutral / $totalSent * 100) }}%)</span>
                </div>
                <div class="oi-legend-item">
                    <span class="oi-dot negative"></span>
                    <span>Negative ({{ round($negative / $totalSent * 100) }}%)</span>
                </div>
            </div>
        </div>
    </div>

    {{-- LIST ULASAN --}}
    <div class="oi-list-card">
        <div class="oi-list-head">
            <div class="oi-list-head-count">
                Semua Kritik Pelanggan ({{ number_format($totalReviews, 0, ',', '.') }})
            </div>

            <div class="oi-range-pills">
                <button class="oi-range-pill is-active" type="button">7 Hari</button>
                <button class="oi-range-pill" type="button">30 Hari</button>
                <button class="oi-range-pill" type="button">1 Tahun</button>
            </div>
        </div>

        {{-- Search --}}
        <div class="oi-search-row">
            <form method="GET" action="{{ route('owner.inbox.index') }}">
                <div class="oi-search-inline">
                    <span class="icon">search</span>
                    <input type="text"
                           name="q"
                           placeholder="Cari ulasan..."
                           value="{{ $search }}">
                </div>
            </form>
        </div>

        {{-- Filter rating --}}
        <div class="oi-filters">
            @php $currentRating = $ratingFilter; @endphp

            <a href="{{ route('owner.inbox.index', ['q' => $search]) }}"
               class="oi-filter-pill {{ $currentRating ? '' : 'is-active' }}">
                ⭐ Semua
            </a>

            @for($r = 5; $r >= 1; $r--)
                <a href="{{ route('owner.inbox.index', ['rating' => $r, 'q' => $search]) }}"
                   class="oi-filter-pill {{ (string)$currentRating === (string)$r ? 'is-active' : '' }}">
                    {{ $r }} ⭐
                </a>
            @endfor

            <span style="flex:1"></span>

            <button type="button" class="oi-filter-pill">
                Semua Urgensi
            </button>
        </div>

        {{-- Items --}}
        @forelse($reviews as $review)
            @php
                $user   = $review->pengguna;
                $order  = $review->pesanan;
                $rating = (int) $review->rating;
                $urgent = $rating <= 3;

                $initial = $user?->nama
                    ? mb_strtoupper(mb_substr($user->nama, 0, 1))
                    : 'G';

                $starsFull  = str_repeat('★', $rating);
                $starsEmpty = str_repeat('☆', max(0, 5 - $rating));
            @endphp

            <div class="oi-review">
                <div class="oi-avatar">
                    {{ $initial }}
                </div>

                <div>
                    <div class="oi-review-header">
                        <div>
                            <div class="oi-name">{{ $user->nama ?? 'Pelanggan' }}</div>
                            <div class="oi-time">{{ optional($review->created_at)->diffForHumans() }}</div>
                        </div>

                        <div class="oi-rating">
                            <span class="oi-stars">{{ $starsFull }}{{ $starsEmpty }}</span>
                            {{ $rating }} Bintang
                        </div>
                    </div>

                    <div class="oi-review-text">
                        {{ $review->komentar }}
                    </div>

                    <div class="oi-order">
                        Pesanan:
                        @if($order)
                            #{{ $order->id_pesanan }}
                        @else
                            -
                        @endif
                    </div>

                    <div class="oi-review-footer">
                        <div>
                            @if($urgent)
                                <span class="oi-tag-urgent">Urgent Reply</span>
                            @endif
                        </div>
                        <div class="oi-link-reply">Reply</div>
                    </div>
                </div>

                <div class="oi-flag">flag</div>
            </div>
        @empty
            <div class="oi-empty">
                Belum ada ulasan untuk ditampilkan.
            </div>
        @endforelse
    </div>
</div>
@endsection
