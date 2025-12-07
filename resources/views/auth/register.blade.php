{{-- resources/views/auth/register.blade.php --}}
@extends('layouts.app')
@section('title','Daftar — Golden Spice')

@push('styles')
  <link href="https://fonts.googleapis.com/css2?family=Koulen&family=Questrial&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
  <style>
    :root{ --red:#D50505; --bg:#faf6f2; --card:#ffffff; --muted:#6b6b6b; }
    .auth-hero{background:var(--bg); min-height:calc(100vh - 64px); display:flex; align-items:center; padding:40px 0;}
    .auth-wrap{width:min(1120px,92%); margin-inline:auto;}
    .auth-card{display:grid; grid-template-columns: 460px 1fr; gap:34px; background:#fff; border-radius:28px; box-shadow:0 24px 60px rgba(0,0,0,.12); overflow:hidden;}
    .auth-media img{display:block; width:100%; height:100%; object-fit:cover;}
    .auth-body{padding:34px;}
    .auth-title{font-family:"Koulen", cursive; font-size:42px; line-height:1; margin:0 0 8px; color:#2b2b2b}
    .auth-sub{margin:0 0 20px; color:var(--muted); font-size:14px}
    .field{margin:16px 0 10px}
    .label{font-weight:800; margin-bottom:6px; display:block; color:#2b2b2b}
    .input{width:100%; padding:14px; border-radius:12px; border:1px solid #e7e2dc; background:#fff; outline:none}
    .input:focus{border-color:#f89f9f; box-shadow:0 0 0 4px rgba(213,5,5,.10)}
    .btn-submit{width:100%; border:0; cursor:pointer; padding:14px 18px; border-radius:12px; margin-top:14px; color:#fff; font-weight:800; background:linear-gradient(135deg,#ff3b3b 0%, #ff7a1a 100%);}
    .errors{background:#ffe9e9; border:1px solid #ffc6c6; color:#7b0000; padding:10px 12px; border-radius:12px; margin-bottom:10px;}
    @media (max-width:980px){ .auth-card{grid-template-columns:1fr} .auth-media{display:none} }

    /* === Password strength & match === */
.pw-meter{ margin-top:8px }
.pw-meter .bar{ height:8px; border-radius:999px; background:#eee; overflow:hidden }
.pw-meter .fill{ height:100%; width:0%; transition:width .25s ease; background:#ff7a7a }
.pw-hints{ display:flex; gap:8px; flex-wrap:wrap; margin-top:6px; font-size:12px; color:#666 }
.pw-hints .chip{ padding:4px 8px; border-radius:999px; background:#f3f3f3 }
.pw-hints .chip.ok{ background:#e8fff0; color:#0e6b35 }
.pw-hints .chip.bad{ background:#fff1f1; color:#7b0000 }

.match-note{ font-size:12px; margin-top:6px }
.match-note.ok{ color:#0e6b35 }
.match-note.bad{ color:#b00000 }

.input.ok{ border-color:#b8e5c4; box-shadow:0 0 0 4px rgba(16,185,129,.12) }
.input.bad{ border-color:#ffb0b0; box-shadow:0 0 0 4px rgba(213,5,5,.10) }

.btn-submit[disabled]{ opacity:.6; cursor:not-allowed; filter:grayscale(.1) }

  </style>
@endpush

@section('content')
<section class="auth-hero">
  <div class="auth-wrap">
    <div class="auth-card">
      <div class="auth-media">
        <img src="{{ asset('images/login-register.png') }}" alt="Golden Spice"
             loading="lazy" style="display:block;width:100%;height:100%;object-fit:cover">
      </div>

    
      <div class="auth-body">
        <h1 class="auth-title">Buat Akun</h1>
        <p class="auth-sub">Daftar untuk mulai pesan menu favoritmu.</p>

        @if ($errors->any())
          <div class="errors">
            @foreach ($errors->all() as $e)
              <div>• {{ $e }}</div>
            @endforeach
          </div>
        @endif

        @if ($errors->any())
          ...
        @endif

        @php
  [$rc1,$rc2] = [random_int(1,9), random_int(1,9)];
  session(['captcha.register' => $rc1 + $rc2]);
@endphp

        <form method="POST" action="{{ route('register.store') }}">
          @csrf

          <div class="field">
            <label class="label" for="nama">Nama</label>
            <input id="nama" name="nama" type="text" class="input" value="{{ old('nama') }}" required autofocus>
          </div>

          <div class="field">
            <label class="label" for="email">Email</label>
            <input id="email" name="email" type="email" class="input" value="{{ old('email') }}" required>
          </div>

          {{-- Password + eye --}}
          {{-- Password + strength --}}
<div class="field">
  <label class="label" for="password">Password</label>
  <div class="gs-input">
    <input id="password" name="password" type="password"
           class="input gs-with-eye" required autocomplete="new-password">
    <button type="button" class="gs-eye-btn" data-password-toggle="#password"
            aria-label="Show password" aria-pressed="false">
      <span class="material-icons-outlined" data-eye>visibility</span>
    </button>
  </div>

  <div class="pw-meter" id="pwMeter">
    <div class="bar"><div class="fill"></div></div>
    <div class="pw-hints">
      <span class="chip bad" data-crit="len">≥ 8 karakter</span>
      <span class="chip bad" data-crit="mix">Huruf besar & kecil</span>
      <span class="chip bad" data-crit="num">Angka</span>
      <span class="chip bad" data-crit="sym">Simbol</span>
    </div>
  </div>
</div>


          {{-- Konfirmasi + match --}}
<div class="field">
  <label class="label" for="password_confirmation">Konfirmasi Password</label>
  <div class="gs-input">
    <input id="password_confirmation" name="password_confirmation" type="password"
           class="input gs-with-eye" required autocomplete="new-password">
    <button type="button" class="gs-eye-btn" data-password-toggle="#password_confirmation"
            aria-label="Show password" aria-pressed="false">
      <span class="material-icons-outlined" data-eye>visibility</span>
    </button>
  </div>
  <div class="match-note" id="pwMatch">Ketik ulang password di atas.</div>
</div>

          <div class="field">
  <label class="label">Verifikasi</label>
  <div class="captcha-wrap">
    <div class="captcha-box" id="captcha-register" aria-label="Captcha">
      <span class="captcha-pepper">🌶</span>
      <span class="captcha-num">{{ $rc1 }}</span>
      <span class="captcha-op">+ </span>
      <span class="captcha-num">{{ $rc2 }}</span>
      <span class="captcha-op">=</span>
      <span class="captcha-q">?</span>
    </div>

    <input class="input captcha-input" type="number" inputmode="numeric"
           name="captcha" placeholder="Jawaban" required>

    <a href="{{ url()->current() }}#captcha-register" class="captcha-refresh" aria-label="Ganti soal">
      <span class="material-icons-outlined">refresh</span>
    </a>
  </div>

  @error('captcha')
    <div class="errors" style="margin-top:8px">{{ $message }}</div>
  @enderror
</div>

          <button type="submit" class="btn-submit">Daftar</button>

          <div style="display:flex;justify-content:space-between;align-items:center;margin-top:12px;color:#666">
            <span>Sudah punya akun? <a href="{{ route('login') }}" style="color:#D50505;font-weight:700">Masuk</a></span>
            <a href="{{ route('password.request') }}" class="forgot" style="color:#D50505;font-weight:700">Forgot Password?</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</section>
@endsection
