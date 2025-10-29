@extends('layouts.app')

@section('title','Golden Spice — Beranda')

@section('content')
  {{-- HERO --}}
  <section class="hero" style="--hero:url('{{ asset('images/images-header.png') }}')">
    <div class="hero-overlay"></div>
    <div class="gs-container hero-inner">
      <h1 class="hero-title">STANDAR EMAS RICE BOWL PEDAS!</h1>
      <p class="hero-sub">
        Nikmati perpaduan sempurna nasi hangat, ayam krispi juicy, dan saus spesial
        yang dibuat khusus untuk para pencari sensasi pedas.
      </p>
      <a href="{{ route('about') }}" class="gs-btn gs-btn--lg">
  LIHAT MENU & PESAN SEKARANG
</a>

    </div>
  </section>

  {{-- KENAPA HARUS --}}
  <section class="section">
    <div class="gs-container">
      {{-- >> warna merah --}}
      <h2 class="section-title red">KENAPA HARUS GOLDEN SPICE?</h2>
      <p class="section-sub">Tiga alasan utama mengapa rice bowl kami akan menjadi favoritmu!</p>

      <div class="grid-3">
        <article class="feature-card">
          <div class="feature-icon">🌶️</div>
          <h3 class="feature-title">SAUS JUARA BUATAN SENDIRI</h3>
          <p class="muted">Dari Sambal Geprek yang nendang sampai Saus Mentai creamy, semua saus kami dibuat dari bahan segar untuk rasa yang autentik.</p>
        </article>
        <article class="feature-card">
          <div class="feature-icon">🍃</div>
          <h3 class="feature-title">KUALITAS TANPA KOMPROMI</h3>
          <p class="muted">Potongan ayam terbaik digoreng krispi di luar dan tetap lembut di dalam, disajikan dengan telur mata sapi sempurna.</p>
        </article>
        <article class="feature-card">
          <div class="feature-icon">⚡</div>
          <h3 class="feature-title">CEPAT DAN PRAKTIS</h3>
          <p class="muted">Solusi makan siang di kantor atau makan malam praktis di rumah. Tinggal pesan, kami antar panas-panas ke depan pintu Anda!</p>
        </article>
      </div>
    </div>
  </section>

  {{-- MENU / PRODUK --}}
  <section id="menu" class="section alt">
    <div class="gs-container">
      {{-- >> merah + ikon api kiri/kanan --}}
      <h2 class="section-title red with-flames">KUALITAS TANPA TANDING!</h2>
      <p class="section-sub">Menjamin agar tiap gorengan ayam terasa krispi, enak dan lembut di dalam.</p>

      <div class="grid-3">
        {{-- atur level pedas: heat-1 | heat-2 | heat-3 --}}
        <article class="menu-card heat-3">
          <img src="{{ asset('images/menu-sambal-bawang-foto.jpg') }}" alt="Sambal Bawang" class="menu-img">
          <h4 class="menu-name">SAMBAL BAWANG</h4>
          <p class="muted">Ayam krispi geprek dengan sambal bawang khas Golden Spice yang pedasnya nendang dan bikin nagih.</p>
        </article>

        <article class="menu-card heat-2">
          <img src="{{ asset('images/menu-sambal-ijo-foto.jpg') }}" alt="Sambal Ijo" class="menu-img">
          <h4 class="menu-name">SAMBAL IJO</h4>
          <p class="muted">Ayam krispi disajikan dengan sambal ijo segar khas Nusantara — pedasnya pas, aromanya menggoda.</p>
        </article>

        <article class="menu-card heat-1">
          <img src="{{ asset('images/menu-spicy-mayo-foto.jpg') }}" alt="Spicy Mayo" class="menu-img">
          <h4 class="menu-name">SPICY MAYO</h4>
          <p class="muted">Saus mentai ala Jepang yang creamy dan gurih di-torch untuk memberikan aroma smokey yang khas.</p>
        </article>
      </div>

      <div class="center mt-24">
        <a href="{{ route('about') }}" class="link-arrow">
  LIHAT MENU >>>
</a>

      </div>
    </div>
  </section>

{{-- STORY --}}
  <section class="section">
    <div class="gs-container story-grid">
      <img src="{{ asset('images/story-foto.png') }}" alt="Golden Spice Cup" class="story-img">
      <div class="story-copy">
        {{-- >> merah + 45px --}}
        <h2 class="section-title red fs-45">STORY BEHIND IT...</h2>
        <p class="muted">Golden Spice lahir dari kecintaan kami pada makanan pedas yang proper, enak, dan berkualitas. Kami percaya bahwa makanan cepat saji tidak harus membosankan.</p>
        <p class="muted">Nama Golden Spice kami pilih karena kami memiliki standar emas dalam setiap hal: pemilihan bahan baku, proses masak yang higienis, hingga penyajian yang menggugah selera.</p>
        <p class="muted">Misi kami sederhana: menyajikan kelezatan pedas dalam setiap suapan signature rice bowl untuk menemani hari-harimu. 🌶️✨</p>
      </div>
    </div>
  </section>
@endsection
