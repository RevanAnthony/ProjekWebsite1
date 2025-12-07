{{-- resources/views/auth/login.blade.php --}}
@extends('layouts.app')
@section('title','Login — Golden Spice')

@push('styles')
  <link href="https://fonts.googleapis.com/css2?family=Koulen&family=Questrial&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
  <style>
    :root{
      --red:#D50505;
      --bg:#faf6f2;
      --card:#ffffff;
      --muted:#6b6b6b;
      --radius:20px;
    }
    .auth-hero{
      background: radial-gradient(1200px 600px at 30% -10%, #ffb2b2 0%, transparent 40%),
                  radial-gradient(900px 500px at 90% 110%, #ffc7a2 0%, transparent 50%),
                  var(--bg);
      min-height: calc(100vh - 64px);
      display:flex; align-items:center; padding:40px 0;
    }
    .auth-wrap{ width:min(1120px, 92%); margin-inline:auto; }
    .auth-card{
      display:grid; grid-template-columns: 460px 1fr; gap:34px;
      background:var(--card); border-radius:28px; box-shadow:0 24px 60px rgba(0,0,0,.12);
      overflow:hidden;
    }
    .auth-media{ background:#fff; }
    .auth-media img{ display:block; width:100%; height:100%; object-fit:cover; }
    .auth-body{ padding:34px 34px 28px; position:relative; }
    .auth-brand{ position:absolute; right:24px; top:20px; font-weight:800; color:#5b0000 }
    .auth-title{ font-family:"Koulen", cursive; font-size:42px; line-height:1; margin:0 0 8px; color:#2b2b2b }
    .auth-sub{ margin:0 0 20px; color:var(--muted); font-size:14px }

    /* form */
    .field{ margin:16px 0 10px }
    .label{ font-weight:800; margin-bottom:6px; display:block; color:#2b2b2b }
    .input{
      width:100%; padding:14px 14px; border-radius:12px; border:1px solid #e7e2dc;
      background:#fff; font:inherit; outline:none; transition:border-color .18s ease, box-shadow .18s ease;
    }
    .input:focus{ border-color:#f89f9f; box-shadow:0 0 0 4px rgba(213,5,5,.10) }

    .row-mini{ display:flex; align-items:center; justify-content:space-between; gap:10px; margin-top:8px }
    .remember{ display:flex; gap:8px; align-items:center; color:#3a3a3a; font-size:14px }
    .forgot{ font-size:14px; color:var(--red) }
    .btn-submit{
      width:100%; border:0; cursor:pointer; padding:14px 18px; border-radius:12px; margin-top:14px;
      color:#fff; font-weight:800; letter-spacing:.3px;
      background:linear-gradient(135deg,#ff3b3b 0%, #ff7a1a 100%);
      box-shadow:0 10px 22px rgba(255,61,61,.3), 0 3px 10px rgba(0,0,0,.06);
      transition: transform .15s ease, box-shadow .15s ease, filter .18s ease;
    }
    .btn-submit:hover{ transform: translateY(-1px); filter:saturate(1.05) }
    .or{ display:flex; align-items:center; gap:12px; margin:14px 0 }
    .or::before,.or::after{ content:""; height:1px; flex:1; background:#eee }
    .or span{ color:#888; font-size:12px; letter-spacing:.25px }
    .oauths{ display:flex; gap:10px; flex-wrap:wrap }
    .btn-oauth{
      flex:1 1 210px; display:flex; align-items:center; justify-content:center; gap:8px;
      padding:12px 14px; border-radius:12px; border:1px solid #e7e2dc; background:#fff; cursor:pointer;
      font-weight:700; color:#222;
    }
    .btn-oauth:hover{ border-color:#d9d2cc; box-shadow:0 4px 12px rgba(0,0,0,.06) }
    .btn-oauth .material-icons-outlined{ font-size:20px }

    .errors{ background:#ffe9e9; border:1px solid #ffc6c6; color:#7b0000; padding:10px 12px; border-radius:12px; margin-bottom:10px; }
    .status{ background:#e8fff0; border:1px solid #c8f0d6; color:#0e6b35; padding:10px 12px; border-radius:12px; margin-bottom:10px; }

    @media (max-width: 980px){
      .auth-card{ grid-template-columns:1fr }
      .auth-media{ display:none }
    }

    /* == Password eye (di dalam input) == */
    .gs-input { position: relative; }
    .gs-with-eye { padding-right: 44px !important; }
    .gs-eye-btn{
      position:absolute;
      right:12px; top:50%; transform:translateY(-50%);
      width:32px; height:32px; display:grid; place-items:center;
      border:0; background:transparent; cursor:pointer; line-height:0;
      z-index:3; color:#9a9a9a; opacity:.9;
    }
    .gs-eye-btn:hover{ color:#555; opacity:1; }
    .gs-eye-off{ display:none; }
    .gs-eye-btn[aria-pressed="true"] .gs-eye{ display:none; }
    .gs-eye-btn[aria-pressed="true"] .gs-eye-off{ display:inline; }
  </style>
@endpush

@section('content')
<section class="auth-hero">
  <div class="auth-wrap">
    <div class="auth-card">
      {{-- LEFT: image --}}
      <div class="auth-media">
        <img src="{{ asset('images/login-register.png') }}" alt="Golden Spice"
             loading="lazy" style="display:block;width:100%;height:100%;object-fit:cover">
      </div>

      {{-- RIGHT: form --}}
      <div class="auth-body">
        <div class="auth-brand">9old3n Spice</div>
        <h1 class="auth-title">Hey,<br>Welcome Back!</h1>
        <p class="auth-sub">We are very happy to see you back!</p>

        {{-- flash status / errors --}}
        @if (session('status'))
          <div class="status">{{ session('status') }}</div>
        @endif
        @if ($errors->any())
          <div class="errors">
            @foreach ($errors->all() as $err)
              <div>• {{ $err }}</div>
            @endforeach
          </div>
        @endif

        <form method="POST" action="{{ route('login.attempt') }}">
          @csrf

          <div class="field">
            <label class="label" for="email">Email</label>
            <input class="input" id="email" type="email" name="email" value="{{ old('email') }}" placeholder="commitcommunity@gmail.com" required autofocus>
          </div>

          <div class="field">
            <label class="label" for="password">Password</label>
            <div class="gs-input">
              <input class="input gs-with-eye" id="password" type="password" name="password" placeholder="••••••••" required>
              <button
                type="button"
                class="gs-eye-btn"
                data-password-toggle="#password"
                aria-label="Show password"
                aria-pressed="false"
              >
                <span class="material-icons-outlined gs-eye">visibility</span>
                <span class="material-icons-outlined gs-eye-off">visibility_off</span>
              </button>
            </div>
          </div>

          <div class="row-mini">
            <label class="remember">
              <input type="checkbox" name="remember" value="1" {{ old('remember') ? 'checked' : '' }}> Remember my information
            </label>
            <a class="forgot" href="{{ route('password.request', [], false) }}">Forgot Password?</a>
          </div>

          {{-- Captcha: pakai style lama dari golden.css --}}
          <div class="field">
            <label class="label">Verifikasi</label>
            <div class="captcha-wrap">
              <div class="captcha-box" id="captcha-login" aria-label="Captcha">
                <span class="captcha-pepper">🌶</span>
                <span class="captcha-num">{{ $c1 ?? 0 }}</span>
                <span class="captcha-op">+</span>
                <span class="captcha-num">{{ $c2 ?? 0 }}</span>
                <span class="captcha-op">=</span>
                <span class="captcha-q">?</span>
              </div>

              <input class="input captcha-input" type="number" inputmode="numeric"
                     name="captcha" placeholder="Jawaban" required
                     value="{{ old('captcha') }}" autocomplete="off">

              <a href="{{ url()->current() }}#captcha-login" class="captcha-refresh" aria-label="Ganti soal">
                <span class="material-icons-outlined">refresh</span>
              </a>
            </div>

            @error('captcha')
              <div class="errors" style="margin-top:8px">{{ $message }}</div>
            @enderror
          </div>

          <button type="submit" class="btn-submit">Login</button>

          <div class="or"><span>OR</span></div>

          <div class="oauths">
            {{-- Google: selalu tampil --}}
            <a class="btn-oauth btn-google" href="{{ route('auth.google.redirect') }}">
              <svg class="g-icon" viewBox="0 0 533.5 544.3" width="18" height="18" role="img" aria-hidden="true">
                <path fill="#4285F4" d="M533.5 278.4c0-18.5-1.6-36.3-4.7-53.6H272v101.5h146.9c-6.3 34-25 62.9-53.3 82.2v68h86.1c50.3-46.4 81.8-114.8 81.8-198.1z"/>
                <path fill="#34A853" d="M272 544.3c72.3 0 132.9-23.9 177.2-64.8l-86.1-68c-23.9 16-54.4 25.5-91.1 25.5-69.9 0-129.2-47.2-150.4-110.5H33.6v69.7C77.7 488.7 168.2 544.3 272 544.3z"/>
                <path fill="#FBBC04" d="M121.6 326.5c-10-29.9-10-62.1 0-92l.1-.4V164.4H33.6c-33.8 67.6-33.8 148.2 0 215.8l88-53.7z"/>
                <path fill="#EA4335" d="M272 107.7c39.3-.6 77.1 13.8 106.2 40.7l79.3-79.3C402.7 24 340.7 0 272 0 168.2 0 77.7 55.6 33.6 139l88 69.6C142.7 154.9 202 107.7 272 107.7z"/>
              </svg>
              <span class="btn-text">Login with Google</span>
            </a>

            {{-- Facebook: hanya tampil jika rute ada --}}
            @if (Route::has('auth.facebook.redirect'))
              <a class="btn-oauth" href="{{ route('auth.facebook.redirect') }}">
                <span class="material-icons-outlined">facebook</span> Login with Facebook
              </a>
            @endif
          </div>
        </form>
      </div>
    </div>
  </div>
</section>
@endsection
