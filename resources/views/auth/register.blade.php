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
  </style>
@endpush

@section('content')
<section class="auth-hero">
  <div class="auth-wrap">
    <div class="auth-card">
      <div class="auth-media">
        <img src="{{ asset('images/login-bowl.jpg') }}" alt="Golden Spice">
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

          <div class="field">
            <label class="label" for="password">Password</label>
            <input id="password" name="password" type="password" class="input" required>
          </div>

          <div class="field">
            <label class="label" for="password_confirmation">Konfirmasi Password</label>
            <input id="password_confirmation" name="password_confirmation" type="password" class="input" required>
          </div>

          <button type="submit" class="btn-submit">Daftar</button>

          <p style="margin-top:12px;color:#666">
            Sudah punya akun? <a href="{{ route('login') }}" style="color:#D50505;font-weight:700">Masuk</a>
          </p>
        </form>
      </div>
    </div>
  </div>
</section>
@endsection
