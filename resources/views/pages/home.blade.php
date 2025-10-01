@extends('layouts.app')
@section('title','Chlorofil — Natural Glow')

@section('content')

{{-- ======================= HERO ======================= --}}
<section class="hero py-5 py-md-6 border-bottom">
  <div class="container-xxl">
    <div class="row align-items-center gy-5">
      <div class="col-lg-6">
        <span class="chip mb-3">Sains Hijau</span>
        <h1 class="display-5 fw-800 section-title">
          Kekuatan Klorofil <span class="text-success">untuk Kulit Sehat</span>
        </h1>
        <p class="lead-hero mt-3">
          Rangkaian perawatan kulit berbasis copper chlorophyllin yang menenangkan,
          kaya antioksidan, dan ramah lingkungan.
        </p>
        <div class="d-flex gap-2 mt-4">
          <a href="{{ route('about') }}" class="btn btn-brand btn-lg px-4">Lihat Profil</a>
          <a href="{{ route('contact') }}" class="btn btn-outline-brand btn-lg px-4">Konsultasi Gratis</a>
        </div>
        <div class="d-flex gap-3 mt-4 text-muted small">
          <div><i class="bi bi-shield-check text-success me-1"></i> Dermatologically Friendly</div>
          <div><i class="bi bi-recycle text-success me-1"></i> Eco Packaging</div>
        </div>
      </div>

      <div class="col-lg-6">
        {{-- Hero visual (Unsplash) --}}
        <div class="ratio ratio-4x3 rounded-4 border"
             style="background:#eafff5 url('https://images.unsplash.com/photo-1505577058444-a3dab90d4253?auto=format&fit=crop&w=1400&q=80') center/cover">
        </div>
      </div>
    </div>
  </div>
</section>

{{-- ======================= FEATURES + STATS ======================= --}}
<section class="py-5">
  <div class="container-xxl">
    <div class="row g-4">
      <div class="col-md-4">
        <div class="card card-feature h-100">
          <div class="card-body">
            <div class="d-flex align-items-center mb-2">
              <i class="bi bi-droplet-half text-success fs-4 me-2"></i>
              <h5 class="mb-0">Bahan Aktif Hijau</h5>
            </div>
            <p class="mb-0 text-muted">Copper chlorophyllin untuk menenangkan & menjaga barrier.</p>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card card-feature h-100">
          <div class="card-body">
            <div class="d-flex align-items-center mb-2">
              <i class="bi bi-heart text-success fs-4 me-2"></i>
              <h5 class="mb-0">Aman Harian</h5>
            </div>
            <p class="mb-0 text-muted">Bebas paraben & pewangi keras; ramah kulit sensitif.</p>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card card-feature h-100">
          <div class="card-body">
            <div class="d-flex align-items-center mb-2">
              <i class="bi bi-recycle text-success fs-4 me-2"></i>
              <h5 class="mb-0">Berkelanjutan</h5>
            </div>
            <p class="mb-0 text-muted">Kemasan mudah didaur ulang & proses efisien energi.</p>
          </div>
        </div>
      </div>
    </div>

    <div class="row text-center mt-5 g-3">
      <div class="col-md-4">
        <div class="stat">
          <h3 class="text-success"><span data-count="98" data-suffix="%">0%</span></h3>
          <div class="small text-muted">Kulit terasa lebih tenang*</div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="stat">
          <h3 class="text-success"><span data-count="120000">0</span></h3>
          <div class="small text-muted">Botol terjual 12 bulan terakhir</div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="stat">
          <h3 class="text-success"><span data-count="88" data-suffix="/100">0</span></h3>
          <div class="small text-muted">Rerata ulasan pelanggan (88/100)</div>
        </div>
      </div>
      <div class="small text-muted mt-2">*Self-reported pengguna rutin 4 minggu.</div>
    </div>
  </div>
</section>

{{-- ======================= PRODUCTS (Local images) ======================= --}}
<section class="py-5 border-top">
  <div class="container-xxl">
    <div class="d-flex align-items-center justify-content-between mb-3">
      <h3 class="section-title">Produk Unggulan</h3>
      <a class="link-brand" href="{{ route('about') }}">Lihat detail brand →</a>
    </div>

    <div class="row g-4">
      <div class="col-md-4">
        <div class="card h-100 border-0 shadow-sm">
          <img class="card-img-top object-fit-cover" src="{{ asset('images/cleanser.jpeg') }}"
               alt="Chlorofil Cleanser - pembersih wajah lembut" width="1200" height="800" loading="lazy">
          <div class="card-body">
            <h5 class="fw-semibold">Chlorofil Cleanser</h5>
            <p class="text-muted mb-0">Pembersih lembut dengan chlorophyll.</p>
          </div>
        </div>
      </div>

      <div class="col-md-4">
        <div class="card h-100 border-0 shadow-sm">
          <img class="card-img-top object-fit-cover" src="{{ asset('images/serum.jpeg') }}"
               alt="Chlorofil Serum dengan pipet/dropper" width="1200" height="800" loading="lazy">
          <div class="card-body">
            <h5 class="fw-semibold">Chlorofil Serum</h5>
            <p class="text-muted mb-0">Serum pencerah & antioksidan.</p>
          </div>
        </div>
      </div>

      <div class="col-md-4">
        <div class="card h-100 border-0 shadow-sm">
          <img class="card-img-top object-fit-cover" src="{{ asset('images/gel.jpeg') }}"
               alt="Gel Moisturizer tekstur ringan" width="1200" height="800" loading="lazy">
          <div class="card-body">
            <h5 class="fw-semibold">Gel Moisturizer</h5>
            <p class="text-muted mb-0">Pelembap ringan berbasis chlorophyll.</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
@endsection