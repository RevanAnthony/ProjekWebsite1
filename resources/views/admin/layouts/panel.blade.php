@php use Illuminate\Support\Str; @endphp
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title','Kasir Panel — Golden Spice')</title>

    {{-- Fonts & Icons --}}
    <link href="https://fonts.googleapis.com/css2?family=Koulen&family=Questrial&family=Hind:wght@700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:wght@400;600&display=swap">

    <style>
      :root{
        --gs-red:#d41616;
        --gs-bg:#f5f6f9;
        --gs-border:#e5e7eb;
      }
      *{box-sizing:border-box;}
      body{
        margin:0;
        font-family:'Questrial', system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        background:var(--gs-bg);
      }

      .admin-shell{
        display:flex;
        min-height:100vh;
      }

      /* SIDEBAR */
      .admin-sidebar{
        width:260px;
        background:#ffffff;
        border-right:1px solid var(--gs-border);
        display:flex;
        flex-direction:column;
      }
      .admin-sidebar-header{
        padding:18px 20px 14px;
        border-bottom:1px solid var(--gs-border);
        display:flex;
        align-items:center;
        gap:10px;
      }
      .admin-logo-circle{
        width:36px;
        height:36px;
        border-radius:999px;
        background:var(--gs-red);
        display:flex;
        align-items:center;
        justify-content:center;
        color:#fff;
        font-weight:800;
        font-size:18px;
      }
      .admin-brand-title{
        font-family:'Koulen', system-ui;
        font-size:20px;
        line-height:1;
      }
      .admin-brand-sub{
        font-size:11px;
        color:#999;
      }
      .admin-sidebar-outlet{
        padding:10px 20px 14px;
        font-size:11px;
        color:#666;
        border-bottom:1px solid var(--gs-border);
      }

      .admin-nav{
        padding:10px 8px;
        display:flex;
        flex-direction:column;
        gap:4px;
      }
      .admin-nav a{
        display:flex;
        align-items:center;
        gap:10px;
        padding:9px 12px;
        margin:2px 4px;
        border-radius:12px;
        color:#444;
        text-decoration:none;
        font-size:14px;
      }
      .admin-nav a .material-symbols-rounded{
        font-size:18px;
        color:#999;
      }
      .admin-nav a.is-active{
        background:rgba(212,22,22,0.08);
        color:var(--gs-red);
        font-weight:700;
      }
      .admin-nav a.is-active .material-symbols-rounded{
        color:var(--gs-red);
      }

      /* MAIN AREA */
      .admin-main{
        flex:1;
        display:flex;
        flex-direction:column;
      }
      .admin-topbar{
        height:60px;
        background:var(--gs-red);
        color:#fff;
        display:flex;
        align-items:center;
        justify-content:space-between;
        padding:0 24px;
      }
      .admin-topbar-left{
        font-size:13px;
        letter-spacing:.05em;
      }
      .admin-topbar-left span{
        font-weight:800;
      }
      .admin-user-box{
        display:flex;
        align-items:center;
        gap:10px;
        font-size:13px;
      }
      .admin-user-avatar{
        width:30px;
        height:30px;
        border-radius:999px;
        background:#fff;
        color:var(--gs-red);
        display:flex;
        align-items:center;
        justify-content:center;
        font-weight:800;
      }
      .admin-user-meta small{
        display:block;
        font-size:11px;
        opacity:.8;
      }
      .admin-logout-btn{
        border:0;
        background:rgba(255,255,255,.12);
        color:#fff;
        border-radius:999px;
        padding:4px 10px;
        font-size:11px;
        font-weight:600;
        cursor:pointer;
        margin-left:8px;
      }

      .admin-content-wrap{
        flex:1;
        padding:22px 28px 28px;
        overflow:auto;
      }

      .admin-page-title{
        font-size:22px;
        font-weight:800;
        margin:0 0 4px;
      }
      .admin-page-sub{
        font-size:13px;
        color:#777;
        margin:0 0 18px;
      }

      .flash-message{
        font-size:13px;
        padding:8px 12px;
        border-radius:10px;
        margin-bottom:14px;
        background:#ecfdf5;
        color:#047857;
      }
    </style>

    @stack('styles')
</head>
<body>
<div class="admin-shell">
  {{-- SIDEBAR --}}
  <aside class="admin-sidebar">
    <div class="admin-sidebar-header">
      <div class="admin-logo-circle">GS</div>
      <div>
        <div class="admin-brand-title">Golden Spice</div>
        <div class="admin-brand-sub">Kasir / Admin Panel</div>
      </div>
    </div>
    <div class="admin-sidebar-outlet">
      <strong>GOLDEN SPICE</strong><br>
      Cabang 1 · Jl. Marchell, no. 20
    </div>

    <nav class="admin-nav">
      <a href="{{ route('admin.menu.index') }}"
         class="{{ request()->routeIs('admin.menu.*') ? 'is-active' : '' }}">
        <span class="material-symbols-rounded">restaurant_menu</span>
        <span>Menu</span>
      </a>

      <a href="{{ route('admin.orders.index') }}"
         class="{{ request()->routeIs('admin.orders.*') ? 'is-active' : '' }}">
        <span class="material-symbols-rounded">receipt_long</span>
        <span>Pesanan</span>
      </a>

      <a href="{{ route('admin.chats.index') }}"
         class="{{ request()->routeIs('admin.chats.*') ? 'is-active' : '' }}">
        <span class="material-symbols-rounded">chat</span>
        <span>Chats</span>
      </a>
    </nav>
  </aside>

  {{-- MAIN --}}
  <div class="admin-main">
    <header class="admin-topbar">
      <div class="admin-topbar-left">
        <span>GOLDEN SPICE</span> · Kasir Panel
      </div>

      <div class="admin-user-box">
        <div class="admin-user-meta">
          <div>{{ Str::of(auth()->user()->nama ?? auth()->user()->email)->limit(18) }}</div>
          <small>Admin kasir</small>
        </div>
        <div class="admin-user-avatar">
          {{ strtoupper(mb_substr(auth()->user()->nama ?? 'A',0,1)) }}
        </div>

        <form method="POST" action="{{ route('admin.logout') }}">
          @csrf
          <button type="submit" class="admin-logout-btn">Logout</button>
        </form>
      </div>
    </header>

    <div class="admin-content-wrap">
      {{-- FLASH MESSAGE GLOBAL (opsional) --}}
      @if(session('success'))
        <div class="flash-message">{{ session('success') }}</div>
      @endif

      {{-- PAGE TITLE & SUBTITLE --}}
      @hasSection('page-title')
        <h1 class="admin-page-title">@yield('page-title')</h1>
      @endif

      @hasSection('page-subtitle')
        <p class="admin-page-sub">@yield('page-subtitle')</p>
      @endif

      @yield('content')
    </div>
  </div>
</div>

@stack('scripts')
</body>
</html>
