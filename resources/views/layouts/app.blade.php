@php use Illuminate\Support\Str; @endphp
<!doctype html>
<html lang="id">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="app-auth" content="{{ auth()->check() ? '1' : '0' }}">
    <title>@yield('title', 'Golden Spice')</title>

    {{-- Fonts & Icons --}}
    <link href="https://fonts.googleapis.com/css2?family=Koulen&family=Questrial&family=Hind:wght@700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:wght@400;600&display=swap">

    {{-- CSS utama (cache-buster) --}}
    <link rel="stylesheet" href="{{ asset('css/golden.css') }}?v={{ @filemtime(public_path('css/golden.css')) }}">
    @stack('styles')
  </head>
  <body>

    {{-- =================== HEADER / NAV =================== --}}
    <header class="gs-header">
      <div class="gs-container hdr">
        <a class="brand" href="{{ auth()->check() ? route('home') : route('login') }}">GOLDEN SPICE</a>

        @auth
          <nav id="mainNav" class="nav">
            <a href="{{ route('home') }}"
               class="{{ request()->routeIs('home') ? 'active' : '' }}">Beranda</a>

            <a href="{{ route('menu') }}"
               class="{{ request()->routeIs('menu') ? 'active' : '' }}">Menu</a>

            <a href="{{ route('contact') }}"
               class="{{ request()->routeIs('contact') ? 'active' : '' }}">Contact</a>

            {{-- PENTING: selalu ke orders.index, bukan orders.show --}}
            <a href="{{ route('orders.index') }}"
               class="{{ request()->routeIs('orders.*') ? 'active' : '' }}">Order</a>

            <span class="nav-pill" id="navPill" aria-hidden="true"></span>
          </nav>
        @endauth

        <div class="nav-right">
          @guest
            {{-- NAV untuk Login/Daftar agar nav-pill bisa mengikuti --}}
            <nav id="mainNav" class="nav">
              <a href="{{ route('login') }}"
                 class="{{ request()->routeIs('login') ? 'active' : '' }}">Login</a>
              <a href="{{ route('register') }}"
                 class="{{ request()->routeIs('register') ? 'active' : '' }}">Daftar</a>
              <span class="nav-pill" aria-hidden="true"></span>
            </nav>
          @endguest

          @auth
            <div class="user">
              <button type="button"
                      class="user-btn"
                      id="userBtn"
                      aria-expanded="false"
                      aria-controls="userMenu">
                <span class="material-symbols-rounded">account_circle</span>
                <span>{{ Str::of(auth()->user()->nama ?? auth()->user()->email)->limit(18) }}</span>
                <span class="material-symbols-rounded chevron">expand_more</span>
              </button>

              <div class="user-menu" id="userMenu" aria-hidden="true">
                <div class="user-meta">
                  <div class="avatar">
                    {{ strtoupper(mb_substr(auth()->user()->nama ?? 'U', 0, 1)) }}
                  </div>
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

    {{-- =================== KONTEN =================== --}}
    <main>
      @yield('content')
    </main>

    {{-- =================== FOOTER =================== --}}
    <footer class="gs-footer">
      <div class="gs-container footer-bottom">
        <p class="muted">© {{ date('Y') }} 9old3n Spice. All Rights Reserved.</p>
        <div class="socials">
          <span class="dot"></span><span class="dot"></span><span class="dot"></span>
        </div>
      </div>
    </footer>

    {{-- ========== CART: FAB + Drawer (KANAN, hanya di halaman MENU) ========== --}}
    @auth
      @if (request()->routeIs('menu'))
        {{-- FAB --}}
        <button class="fab-cart" type="button" aria-label="Buka keranjang" data-cart-open>
          <svg width="26" height="26" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M7 6h14l-1.5 9.5a2 2 0 0 1-2 1.7H9.6a2 2 0 0 1-2-1.5L5.2 3.9H2"
                  stroke="white" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
            <circle cx="10" cy="21" r="1.6" fill="white"/>
            <circle cx="18" cy="21" r="1.6" fill="white"/>
          </svg>
          <span class="fab-badge" id="cartBadge">0</span>
        </button>

        {{-- Overlay & Drawer --}}
        <div class="cart-overlay" aria-hidden="true"></div>

        <aside class="cart-drawer" aria-hidden="true">
          <div class="cart-head">
            <div>
              <div class="cart-brand">GOLDEN SPICE</div>
              <div class="cart-sub">Your Order (<span id="cartCountHdr">0</span>)</div>
            </div>
            <button class="cart-close" data-cart-close aria-label="Tutup">&times;</button>
          </div>

          <div class="cart-body">
            <ul class="cart-list" id="cartList"></ul>
            <div class="cart-empty" id="cartEmpty">Keranjang masih kosong.</div>
          </div>

          <div class="cart-foot">
            <div class="cart-total">
              <span>Total</span>
              <strong id="cartTotal">Rp 0</strong>
            </div>

            <div class="cart-actions" style="display:flex; gap:10px;">
              <button id="cartClearBtn" type="button" class="btn-clear">Hapus semua</button>
              <a href="{{ route('payment.start') }}" class="cart-cta">Review Order</a>
            </div>
          </div>
        </aside>
      @endif
    @endauth>

    {{-- =================== JS =================== --}}
    <script src="{{ asset('js/golden.js') }}?v={{ @filemtime(public_path('js/golden.js')) }}" defer></script>
    @stack('scripts')
  </body>
</html>
