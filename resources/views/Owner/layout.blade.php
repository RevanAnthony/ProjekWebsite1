<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>@yield('title','Owner Panel — Golden Spice')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Fonts & Icons --}}
    <link href="https://fonts.googleapis.com/css2?family=Koulen&family=Questrial&display=swap" rel="stylesheet">
    <link rel="stylesheet"
          href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:wght@400;600&display=swap">

    {{-- CSS front-end utama (opsional, biar warna & font konsisten) --}}
    <link rel="stylesheet" href="{{ asset('css/golden.css') }}">

    <style>
        body{
            margin:0;
            background:#f4f4f6;
            font-family:'Questrial',system-ui,-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;
            color:#222;
        }

        .owner-layout{
            display:grid;
            grid-template-columns:260px minmax(0,1fr);
            min-height:100vh;
        }

        .owner-sidebar{
            background:#ffffff;
            border-right:1px solid #eee;
            padding:24px 20px;
            display:flex;
            flex-direction:column;
            gap:32px;
        }

        .owner-brand{
            display:flex;
            align-items:center;
            gap:12px;
            font-weight:800;
        }
        .owner-brand-logo{
            width:40px;height:40px;border-radius:999px;
            background:#D50505;display:grid;place-items:center;color:#fff;font-weight:800;
        }

        .owner-nav{
            display:flex;
            flex-direction:column;
            gap:8px;
            margin-top:8px;
        }
        .owner-nav a{
            display:flex;
            align-items:center;
            gap:10px;
            padding:8px 12px;
            border-radius:12px;
            color:#444;
            text-decoration:none;
            font-size:14px;
        }
        .owner-nav a span.icon{
            font-family:'Material Symbols Rounded';
            font-size:20px;
        }
        .owner-nav a.active{
            background:#ffe6e6;
            color:#D50505;
            font-weight:600;
        }

        .owner-main{
            display:flex;
            flex-direction:column;
            background:#f4f4f6;
        }

        .owner-topbar{
            background:#ffffff;
            border-bottom:1px solid #eee;
            padding:16px 24px;
            display:flex;
            align-items:center;
            justify-content:space-between;
        }
        .owner-top-left{
            font-size:14px;
        }
        .owner-top-right{
            display:flex;
            align-items:center;
            gap:16px;
        }

        .owner-user-chip{
            display:flex;
            align-items:center;
            gap:8px;
            padding:6px 12px;
            border-radius:999px;
            background:#f5f5f5;
            font-size:13px;
        }
        .owner-user-avatar{
            width:30px;height:30px;border-radius:999px;
            background:#D50505;color:#fff;display:grid;place-items:center;font-weight:700;
        }

        /* WRAPPER KONTEN UTAMA */
        .owner-content{
            width:100%;
            max-width:1120px;      /* batas lebar konten */
            margin:24px auto 40px; /* auto = center */
            padding:0 24px;
        }

        .badge-role{
            font-size:10px;
            text-transform:uppercase;
            letter-spacing:.08em;
            color:#999;
        }

        .btn-logout{
            border:none;
            background:none;
            color:#D50505;
            font-size:14px;
            cursor:pointer;
            display:flex;
            align-items:center;
            gap:4px;
        }

        /* ====================
           KOMPONEN HALAMAN MENU
           ==================== */
        .owner-page-header{
            display:flex;
            justify-content:space-between;
            align-items:center;
            margin-bottom:16px;
        }
        .owner-page-header h1{
            font-family:'Koulen',system-ui;
            letter-spacing:.04em;
            font-size:28px;
        }
        .owner-search-form{
            width:280px;
        }
        .owner-search-input{
            display:flex;
            align-items:center;
            gap:8px;
            border-radius:999px;
            background:#fff;
            padding:6px 12px;
            border:1px solid #eee;
        }
        .owner-search-input input{
            border:none;
            outline:none;
            background:transparent;
            width:100%;
            font-size:14px;
        }
        .owner-search-input span{
            font-family:'Material Symbols Rounded';
            font-size:20px;
            color:#999;
        }

        .owner-menu-tabs{
            display:flex;
            gap:10px;
            margin-bottom:16px;
        }
        .owner-menu-tab{
            padding:10px 18px;
            border-radius:999px;
            background:#fff;
            border:1px solid #eee;
            font-size:13px;
            cursor:pointer;
            text-decoration:none;
            color:#444;
            display:flex;
            align-items:center;
            gap:8px;
        }
        .owner-menu-tab.is-active{
            background:#D50505;
            color:#fff;
            border-color:#D50505;
        }
        .owner-menu-tab .icon{
            font-family:'Material Symbols Rounded';
            font-size:18px;
        }

        .owner-menu-list{
            margin-top:8px;
        }
        .owner-menu-row{
            display:grid;
            grid-template-columns:96px minmax(0,1.5fr) 120px minmax(0,2fr) 110px 120px;
            gap:12px;
            padding:12px 16px;
            background:#fff;
            border-radius:18px;
            border:1px solid #ffd0d0;
            margin-bottom:10px;
            align-items:center;
        }
        .owner-menu-img{
            width:80px;
            height:80px;
            border-radius:16px;
            overflow:hidden;
            background:#f5f5f5;
        }
        .owner-menu-img img{
            width:100%;
            height:100%;
            object-fit:cover;
        }
        .owner-menu-title{
            font-weight:600;
            margin-bottom:4px;
        }
        .owner-menu-cat{
            font-size:12px;
            color:#999;
        }
        .owner-menu-price{
            font-weight:700;
        }
        .owner-menu-desc{
            font-size:13px;
            color:#555;
        }
        .owner-menu-code{
            font-size:13px;
            color:#666;
        }
        .owner-menu-actions{
            display:flex;
            align-items:center;
            gap:8px;
        }
        .btn-icon{
            width:36px;
            height:36px;
            border-radius:12px;
            border:none;
            display:grid;
            place-items:center;
            cursor:pointer;
            background:#fff;
            box-shadow:0 4px 10px rgba(0,0,0,.05);
        }
        .btn-icon span{
            font-family:'Material Symbols Rounded';
            font-size:20px;
        }
        .btn-icon.edit{ color:#ff7b00; }
        .btn-icon.delete{ color:#e11d48; }

        .owner-empty{
            padding:32px 16px;
            text-align:center;
            font-size:14px;
            color:#777;
        }

        .alert{
            padding:10px 14px;
            border-radius:12px;
            background:#e8fff0;
            border:1px solid #b7f0cc;
            font-size:13px;
            margin-bottom:12px;
            color:#166534;
        }
    </style>

    @stack('styles')
</head>
<body>
<div class="owner-layout">
    <aside class="owner-sidebar">
        <div class="owner-brand">
            <div class="owner-brand-logo">G</div>
            <div>
                <div>GOLDEN SPICE</div>
                <div style="font-size:11px;color:#999;">Cabang 1 | Jl. Marchall no. 20</div>
            </div>
        </div>

        @php
            use Illuminate\Support\Facades\Route;
        @endphp

        <nav class="owner-nav">
            <a href="{{ route('owner.dashboard') }}"
               class="{{ Route::is('owner.dashboard') ? 'active' : '' }}">
                <span class="icon">space_dashboard</span>
                <span>Dashboard</span>
            </a>

            <a href="{{ route('owner.menu.index') }}"
               class="{{ Route::is('owner.menu.*') ? 'active' : '' }}">
                <span class="icon">restaurant_menu</span>
                <span>Menu</span>
            </a>

            <a href="{{ route('owner.inbox.index') }}"
               class="{{ Route::is('owner.inbox.*') ? 'active' : '' }}">
                <span class="icon">mail</span>
                <span>Inbox</span>
            </a>
        </nav>
    </aside>

    <div class="owner-main">
        <header class="owner-topbar">
            <div class="owner-top-left">
                <div style="font-size:13px;color:#444;">GOLDEN SPICE</div>
                <div style="font-size:11px;color:#999;">Jl. Marchall no. 20</div>
            </div>

            <div class="owner-top-right">
                @php $owner = auth('owner')->user(); @endphp

                <div class="owner-user-chip">
                    <div class="owner-user-avatar">
                        {{ strtoupper(substr($owner->nama ?? 'O', 0, 1)) }}
                    </div>
                    <div>
                        <div style="font-size:13px;">{{ $owner->nama ?? 'Owner' }}</div>
                        <div class="badge-role">Owner</div>
                    </div>
                </div>

                <form action="{{ route('owner.logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn-logout">
                        <span class="material-symbols-rounded">logout</span> Logout
                    </button>
                </form>
            </div>
        </header>

        <main class="owner-content">
            @if(session('success'))
                <div class="alert">{{ session('success') }}</div>
            @endif

            @yield('content')
        </main>
    </div>
</div>

@stack('scripts')
</body>
</html>
