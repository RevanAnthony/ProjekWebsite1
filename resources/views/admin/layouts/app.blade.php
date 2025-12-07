{{-- resources/views/admin/layouts/app.blade.php --}}
@php
    use Illuminate\Support\Str;
@endphp
<!doctype html>
<html lang="id">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title','Kasir Panel — Golden Spice')</title>

    {{-- Fonts & Icons --}}
    <link href="https://fonts.googleapis.com/css2?family=Koulen&...estrial&family=Hind:wght@700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:wght@400;600&display=swap">

    {{-- CSS utama --}}
    <link rel="stylesheet" href="{{ asset('css/golden.css') }}?v={{ @filemtime(public_path('css/golden.css')) }}">

    @stack('styles')

    <style>
      body{
        margin:0;
        background:#f3f4f6;
        color:#111827;
        font-family:system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;
      }

      /* ===== HEADER ADMIN ===== */
      .admin-header{
        position:sticky;
        top:0;
        z-index:50;
        background:#b40015; /* lebih gelap dari header user */
        color:#fff;
        box-shadow:0 4px 16px rgba(0,0,0,.25);
      }
      .admin-header-inner{
        max-width:1080px;
        margin:0 auto;
        padding:10px 18px;
        display:flex;
        align-items:center;
        justify-content:space-between;
        gap:16px;
      }
      .admin-brand{
        display:flex;
        align-items:center;
        gap:10px;
        font-family:"Koulen",system-ui,sans-serif;
        letter-spacing:.04em;
        font-size:20px;
      }
      .admin-brand-badge{
        width:32px;
        height:32px;
        border-radius:999px;
        background:#fff;
        color:#b40015;
        display:flex;
        align-items:center;
        justify-content:center;
        font-weight:800;
        font-size:16px;
        box-shadow:0 6px 18px rgba(0,0,0,.35);
      }
      .admin-tagline{
        font-size:11px;
        opacity:.85;
        margin-top:-2px;
      }

      .admin-user{
        display:flex;
        align-items:center;
        gap:10px;
        font-size:12px;
      }
      .admin-user .avatar{
        width:28px;
        height:28px;
        border-radius:999px;
        background:rgba(255,255,255,.1);
        display:flex;
        align-items:center;
        justify-content:center;
        font-weight:700;
      }
      .admin-user form{
        margin:0;
      }

      /* ===== MAIN & FOOTER ===== */
      .admin-main{
        max-width:1080px;
        margin:28px auto 80px;
        padding:0 16px;
      }
      .admin-footer{
        padding:12px 16px 18px;
        border-top:1px solid #e5e7eb;
        text-align:center;
        font-size:12px;
        color:#777;
      }
    </style>
  </head>
  <body>
    @php
        // anggap admin kalau sudah login dan rolenya 'admin'
        $admin = auth()->check() && (auth()->user()->role ?? null) === 'admin'
            ? auth()->user()
            : null;
    @endphp

    {{-- Header admin HANYA muncul di prefix kasir, bukan di /login user --}}
    @if(request()->is('gs-kasir-panel-x01*'))
      <header class="admin-header">
        <div class="admin-header-inner">
          <div class="admin-brand">
            <span class="admin-brand-badge">GS</span>
            <div>
              <div>GOLDEN SPICE</div>
              <div class="admin-tagline">Kasir / Admin Panel</div>
            </div>
          </div>

          {{-- Box user cuma kalau benar-benar admin --}}
          @if($admin)
            <div class="admin-user">
              <div class="avatar">
                {{ strtoupper(mb_substr($admin->nama ?? 'A', 0, 1)) }}
              </div>
              <div>
                <div style="font-weight:700;">
                  {{ Str::of($admin->nama ?? $admin->email)->limit(18) }}
                </div>
                <div style="font-size:11px;opacity:.8;">
                  Admin kasir
                </div>
              </div>
              <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button
                  type="submit"
                  style="border:0;background:#fff;color:#b40015;border-radius:999px;padding:4px 10px;font-size:11px;font-weight:700;cursor:pointer;"
                >
                  Logout
                </button>
              </form>
            </div>
          @endif
        </div>
      </header>
    @endif

    <main class="admin-main">
      @yield('content')
    </main>

    <footer class="admin-footer">
      © {{ date('Y') }} Golden Spice — Kasir Panel.
    </footer>

    @stack('scripts')
  </body>
</html>
