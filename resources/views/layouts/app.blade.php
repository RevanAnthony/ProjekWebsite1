<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title','Golden Spice')</title>

  <!-- Fonts & Icons -->
  <link href="https://fonts.googleapis.com/css2?family=Koulen&family=Questrial&family=Hind:wght@700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:wght@400;600&display=swap">

  <!-- CSS utama (cache-buster) -->
  <link rel="stylesheet" href="{{ asset('css/golden.css') }}?v={{ filemtime(public_path('css/golden.css')) }}">
  @stack('styles')
</head>
<body>

  <!-- HEADER -->
  <header class="gs-header">
    <div class="gs-container hdr">
      <!-- Brand kiri -->
      <a class="brand" href="{{ auth()->check() ? route('home') : route('login') }}">GOLDEN SPICE</a>

      <!-- Nav (tampil hanya saat login) -->
      @auth
        <nav class="nav" id="mainNav">
          <a href="{{ route('home')    }}" class="{{ request()->routeIs('home')    ? 'active' : '' }}">Beranda</a>
          <a href="{{ route('menu')    }}" class="{{ request()->routeIs('menu')    ? 'active' : '' }}">Menu</a>
          <a href="{{ route('contact') }}" class="{{ request()->routeIs('contact') ? 'active' : '' }}">Contact</a>
          <!-- nav-pill disisipkan via JS -->
        </nav>
      @endauth

      <!-- Kanan: user / auth -->
      <div class="nav-right">
        @guest
          <a href="{{ route('login')    }}" class="btn-link  {{ request()->routeIs('login')    ? 'active' : '' }}">Login</a>
          <a href="{{ route('register') }}" class="btn-pill  {{ request()->routeIs('register') ? 'active' : '' }}">Daftar</a>
        @endguest

        @auth
          <div class="user">
            <button type="button" class="user-btn" id="userBtn">
              <span class="material-symbols-rounded">account_circle</span>
              <span>{{ Str::of(auth()->user()->nama ?? auth()->user()->email)->limit(18) }}</span>
              <span class="material-symbols-rounded chevron">expand_more</span>
            </button>

            <div class="user-menu" id="userMenu" aria-hidden="true">
              <div class="user-meta">
                <div class="avatar">{{ strtoupper(mb_substr(auth()->user()->nama ?? 'U',0,1)) }}</div>
                <div>
                  <div><strong>{{ auth()->user()->nama ?? 'User' }}</strong></div>
                  <div class="small">{{ auth()->user()->email }}</div>
                </div>
              </div>
              <form id="logoutForm" method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="logout" type="submit">
                  <span class="material-symbols-rounded">logout</span> Keluar
                </button>
              </form>
            </div>
          </div>
        @endauth
      </div>
    </div>
  </header>

  {{-- Overlay gelap --}}
<div class="cart-overlay"></div>

{{-- Drawer kanan --}}
<aside class="cart-drawer" id="cartDrawer">
  <div class="cart-head">
    <div>
      <div class="cart-brand">GOLDEN SPICE</div>
      <div class="cart-sub">Your Order (<span id="cartCount">0</span>)</div>
    </div>
    <button type="button" class="cart-close" aria-label="Tutup" data-cart-close>
      ✕
    </button>
  </div>

  <div class="cart-body">
    <div class="cart-list" id="cartItems">
      {{-- Contoh 1 item (silakan render dinamis) --}}
      {{-- 
      <div class="cart-item">
        <img class="cart-thumb" src="{{ asset('img/sample.jpg') }}" alt="">
        <div>
          <div class="cart-name">Nasi Ayam Sambal Bawang</div>
          <div class="cart-qtyrow">
            <button class="qty-btn" data-dec>-</button>
            <span class="qty-val">1</span>
            <button class="qty-btn" data-inc>+</button>
          </div>
        </div>
        <div class="cart-price">Rp 25.000</div>
      </div>
      --}}
    </div>
  </div>

  <div class="cart-foot">
    <div class="cart-total">
      <span>Total Price</span>
      <span id="cartTotal">Rp 0</span>
    </div>
    <button class="cart-cta" type="button">Review Order</button>
  </div>
</aside>


  <!-- KONTEN -->
  <main>
    @yield('content')
  </main>

  <!-- FOOTER -->
  <footer class="gs-footer">
    <div class="gs-container footer-bottom">
      <p class="muted">© {{ date('Y') }} 9old3n Spice. All Rights Reserved.</p>
      <div class="socials"><span class="dot"></span><span class="dot"></span><span class="dot"></span></div>
    </div>
  </footer>

  <!-- JS (nav-pill, ripple, reveal, & user menu) -->
  <script src="{{ asset('js/golden.js') }}?v={{ filemtime(public_path('js/golden.js')) }}"></script>
  @stack('scripts')
</body>
</html>
