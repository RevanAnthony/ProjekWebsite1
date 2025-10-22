<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title','Golden Spice')</title>

  {{-- Google Fonts --}}
  <link href="https://fonts.googleapis.com/css2?family=Koulen&family=Questrial&family=Hind:wght@500;600;700&display=swap" rel="stylesheet">

  {{-- CSS utama --}}
  <link rel="stylesheet" href="{{ asset('css/golden.css') }}?v={{ filemtime(public_path('css/golden.css')) }}">
  @stack('styles')
</head>
<body>
  {{-- Topbar / Navbar --}}
  <header class="gs-header">
    <div class="gs-container">
      <a class="brand" href="{{ route('home') }}">GOLDEN SPICE</a>
      <nav class="nav">
        <a href="{{ route('home') }}"  class="{{ request()->routeIs('home') ? 'active' : '' }}">Beranda</a>
        <a href="{{ route('about') }}" class="{{ request()->routeIs('about') ? 'active' : '' }}">Menu</a>
        <a href="{{ route('contact') }}" class="{{ request()->routeIs('contact') ? 'active' : '' }}">Contact</a>
      </nav>
    </div>
  </header>

  {{-- Konten halaman --}}
  <main>
    @yield('content')
  </main>

  {{-- Footer --}}
  <footer class="gs-footer">
    <div class="gs-container footer-grid">
      <div>
        <h3 class="footer-title">GOLDEN SPICE</h3>
        <p class="muted">Address: jl.</p>
        <p class="muted">Operating hours<br>Weekday: 00.00 - 00.00<br>Weekend: 00.00 - 00.00</p>
        <p class="muted">Telephone: 0811</p>
        <p class="muted">E-mail: <a href="mailto:goldspice@gmail.com">goldspice@gmail.com</a></p>
      </div>
      <div>
        <h3 class="footer-title">SERVICES</h3>
        <ul class="footer-list">
          <li>Dine-in</li>
          <li>Delivery</li>
          <li>Take Away</li>
          <li>Catering</li>
        </ul>
      </div>
      <div>
        <h3 class="footer-title">INFO</h3>
        <ul class="footer-list">
          <li><a href="{{ route('contact') }}">Contact Us</a></li>
          <li><a href="{{ route('about') }}">Menu</a></li>
          <li><a href="#">FAQ</a></li>
        </ul>
      </div>
    </div>
    <div class="gs-container footer-bottom">
      <div class="socials">
        <span class="dot" aria-hidden="true"></span>
        <span class="dot" aria-hidden="true"></span>
        <span class="dot" aria-hidden="true"></span>
        <span class="dot" aria-hidden="true"></span>
      </div>
      <p class="muted">© 2025 9old3n Spice. All Rights Reserved.</p>
    </div>
  </footer>

  {{-- JS interaksi/hover --}}
  <script defer src="{{ asset('js/golden.js') }}?v={{ filemtime(public_path('js/golden.js')) }}"></script>
  @stack('scripts')
</body>
</html>
