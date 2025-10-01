@extends('layouts.app')
@section('title','Kontak — Chlorofil')

@section('content')
<section class="hero py-5 border-bottom">
  <div class="container-xxl">
    <h1 class="section-title">Hubungi Kami</h1>
    <p class="text-muted">Ada pertanyaan tentang produk? Tim kami siap membantu.</p>
  </div>
</section>

<section class="py-5">
  <div class="container-xxl">
    <div class="row g-4">
      <div class="col-lg-6">
        <div class="border rounded-4 p-4 h-100">
          <form class="needs-validation" novalidate>
            <div class="mb-3">
              <label class="form-label">Nama</label>
              <input type="text" class="form-control" required>
              <div class="invalid-feedback">Wajib diisi.</div>
            </div>
            <div class="mb-3">
              <label class="form-label">Email</label>
              <input type="email" class="form-control" required>
              <div class="invalid-feedback">Masukkan email yang valid.</div>
            </div>
            <div class="mb-3">
              <label class="form-label">Pesan</label>
              <textarea class="form-control" rows="5" required></textarea>
              <div class="invalid-feedback">Tulis pesan singkatmu.</div>
            </div>
            <button class="btn btn-brand">Kirim Pesan</button>
          </form>
        </div>
      </div>
      <div class="col-lg-6">
        <div class="border rounded-4 p-4 h-100">
          <h5 class="fw-bold mb-3">Kontak</h5>
          <ul class="list-unstyled text-muted">
            <li class="mb-2"><i class="bi bi-envelope text-success me-2"></i> hello@chlorofil.id</li>
            <li class="mb-2"><i class="bi bi-telephone text-success me-2"></i> +62 812-1234-5678</li>
            <li class="mb-3"><i class="bi bi-geo-alt text-success me-2"></i> Jl. Hijau No. 8, Jakarta</li>
          </ul>
          <div class="ratio ratio-21x9 rounded-3 overflow-hidden">
            <iframe src="https://www.openstreetmap.org/export/embed.html?bbox=106.79%2C-6.25%2C106.93%2C-6.10&layer=mapnik" loading="lazy"></iframe>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
@endsection
