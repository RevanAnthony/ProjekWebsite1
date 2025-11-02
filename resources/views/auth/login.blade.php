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
  </style>
@endpush

@section('content')
<section class="auth-hero">
  <div class="auth-wrap">
    <div class="auth-card">
      {{-- LEFT: image --}}
      <div class="auth-media">
        <img src="{{ asset('images/login-bowl.jpg') }}" alt="Golden Spice Rice Bowl">
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
            <input class="input" id="password" type="password" name="password" placeholder="••••••••" required>
          </div>

          <div class="row-mini">
            <label class="remember">
              <input type="checkbox" name="remember" value="1"> Remember my information
            </label>
            <a class="forgot" href="{{ route('password.request', [], false) }}">Forgot Password?</a>
          </div>

          <button type="submit" class="btn-submit">Login</button>

          <div class="or"><span>OR</span></div>

          <div class="oauths">
            {{-- Google: selalu tampil --}}
            <a class="btn-oauth" href="{{ route('auth.google.redirect') }}">
              <span class="material-icons-outlined">google</span> Login with Google
            </a>

            {{-- Facebook: hanya tampil jika rute ada (agar tidak error ketika belum diset) --}}
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
