{{-- resources/views/admin/auth/register.blade.php --}}
@extends('admin.layouts.app')
@section('title','Register Admin — Golden Spice')

@push('styles')
<style>
  .auth-wrapper{
    max-width:520px;
    margin:40px auto 60px;
    padding:24px 20px 28px;
    background:#ffffff;
    border-radius:20px;
    box-shadow:0 18px 50px rgba(0,0,0,.12);
  }
  .auth-title{
    font-weight:800;
    font-size:26px;
    margin-bottom:6px;
  }
  .auth-sub{
    font-size:13px;
    color:#777;
    margin-bottom:20px;
  }

  .field{ margin-bottom:14px; }
  .field label{
    display:block;
    font-size:13px;
    font-weight:700;
    margin-bottom:4px;
  }
  .field input{
    width:100%;
    border-radius:12px;
    border:1px solid #e0e0e0;
    padding:10px 12px;
    font-size:14px;
  }
  .field small{
    display:block;
    margin-top:4px;
    font-size:11px;
    color:#777;
  }
  .error-text{
    font-size:12px;
    color:#d32f2f;
    margin-top:2px;
  }

  .btn-primary{
    width:100%;
    border:0;
    border-radius:999px;
    padding:10px 16px;
    font-weight:800;
    background:linear-gradient(135deg,#ff3b3b,#ff7a1a);
    color:#fff;
    cursor:pointer;
    margin-top:8px;
  }

  /* password + eye icon sama kayak login */
  .password-group{
    display:flex;
    align-items:center;
    border-radius:12px;
    border:1px solid #e0e0e0;
    background:#fff;
    overflow:hidden;
  }
  .password-group input{
    border:0;
    border-radius:0;
    flex:1;
    padding:10px 12px;
    font-size:14px;
    outline:none;
  }
  .password-group .toggle-eye{
    width:44px;
    min-width:44px;
    height:100%;
    border:0;
    border-left:1px solid #eee;
    background:transparent;
    display:flex;
    align-items:center;
    justify-content:center;
    cursor:pointer;
  }
  .password-group .material-symbols-rounded{
    font-size:20px;
    color:#888;
  }

  /* meter password */
  .pw-meter-wrap{
    margin-top:6px;
  }
  .pw-meter-bar{
    height:4px;
    border-radius:999px;
    background:#f2f2f2;
    overflow:hidden;
    margin-bottom:6px;
  }
  .pw-meter-bar > span{
    display:block;
    height:100%;
    width:0%;
    background:#f97373;
    transition:width .18s ease, background .18s ease;
  }
  .pw-meter-status{
    font-size:11px;
    font-weight:600;
    color:#666;
  }
  .pw-meter-status--weak{ color:#e11d48; }
  .pw-meter-status--medium{ color:#f97316; }
  .pw-meter-status--strong{ color:#16a34a; }
  .pw-meter-status--very-strong{ color:#15803d; }

  .pw-tags{
    display:flex;
    flex-wrap:wrap;
    gap:6px;
    margin-top:6px;
  }
  .pw-tag{
    font-size:11px;
    padding:3px 8px;
    border-radius:999px;
    border:1px solid #e5e5e5;
    background:#fafafa;
    color:#555;
  }
  .pw-tag--ok{
    border-color:#a7f3d0;
    background:#ecfdf5;
    color:#047857;
  }

  /* CAPTCHA sama bentuk dengan login admin */
  .captcha-row{
    display:flex;
    align-items:stretch;
    gap:10px;
    margin-top:4px;
  }
  .captcha-pill{
    display:flex;
    align-items:center;
    gap:6px;
    padding:8px 14px;
    border-radius:16px;
    background:linear-gradient(135deg,#ff3b3b,#ff7a1a);
    color:#fff;
    font-weight:700;
    font-size:13px;
    letter-spacing:.15px;
    white-space:nowrap;
  }
  .captcha-pill .material-symbols-rounded{
    font-size:18px;
    opacity:.9;
  }
  .captcha-pill span:not(.material-symbols-rounded){
    font-size:12px;
    font-variant-numeric:tabular-nums;
  }
  .captcha-answer{
    flex:1;
  }
  .captcha-answer input{
    width:100%;
    height:100%;
    border-radius:12px;
    border:1px solid #e0e0e0;
    padding:10px 12px;
    font-size:13px;
  }
  .captcha-refresh{
    width:44px;
    border-radius:12px;
    border:1px solid #ffd0b0;
    background:#fff;
    display:flex;
    align-items:center;
    justify-content:center;
    cursor:pointer;
  }
  .captcha-refresh .material-symbols-rounded{
    font-size:20px;
    color:#ff5a2a;
  }

  .auth-foot{
    margin-top:14px;
    font-size:13px;
    display:flex;
    justify-content:space-between;
    gap:8px;
    flex-wrap:wrap;
  }
  .auth-foot a{
    font-weight:600;
  }
  .auth-foot .primary-link{
    color:#ef4444;
  }
  .auth-foot .secondary-link{
    color:#ef4444;
  }
</style>
@endpush

@section('content')
<div class="auth-wrapper">
  <h1 class="auth-title">Buat Akun</h1>
  <p class="auth-sub">Daftar admin/kasir untuk mengakses panel kasir Golden Spice.</p>

  <form method="POST" action="{{ route('admin.register.store') }}">
    @csrf

    {{-- NAMA --}}
    <div class="field">
      <label for="nama">Nama Lengkap</label>
      <input id="nama" type="text" name="nama"
             value="{{ old('nama') }}" required autofocus>
      @error('nama')
        <div class="error-text">{{ $message }}</div>
      @enderror
    </div>

    {{-- EMAIL --}}
    <div class="field">
      <label for="email">Email</label>
      <input id="email" type="email" name="email"
             value="{{ old('email') }}" required>
      @error('email')
        <div class="error-text">{{ $message }}</div>
      @enderror
    </div>

    {{-- PASSWORD + METER --}}
    <div class="field">
      <label for="password">Password</label>
      <div class="password-group" data-password-wrapper>
        <input id="password" type="password" name="password"
               autocomplete="new-password" required
               data-password-input>
        <button type="button" class="toggle-eye" data-toggle-password>
          <span class="material-symbols-rounded">visibility</span>
        </button>
      </div>

      <div class="pw-meter-wrap" data-pw-meter>
        <div class="pw-meter-bar">
          <span data-pw-bar></span>
        </div>
        <div class="pw-meter-status" data-pw-status>Keamanan password: -</div>
        <div class="pw-tags">
          <span class="pw-tag" data-pw-tag="length">≥ 8 karakter</span>
          <span class="pw-tag" data-pw-tag="case">Huruf besar & kecil</span>
          <span class="pw-tag" data-pw-tag="digit">Angka</span>
          <span class="pw-tag" data-pw-tag="symbol">Simbol</span>
        </div>
      </div>

      @error('password')
        <div class="error-text">{{ $message }}</div>
      @enderror
    </div>

    {{-- CONFIRM --}}
    <div class="field">
      <label for="password_confirmation">Konfirmasi Password</label>
      <div class="password-group" data-password-wrapper>
        <input id="password_confirmation" type="password"
               name="password_confirmation" required
               data-password-input>
        <button type="button" class="toggle-eye" data-toggle-password>
          <span class="material-symbols-rounded">visibility</span>
        </button>
      </div>
      <small>Ketik ulang password di atas.</small>
    </div>

    {{-- CAPTCHA --}}
    <div class="field">
      <label for="captcha">Verifikasi</label>
      <div class="captcha-row">
        <div class="captcha-pill">
          <span class="material-symbols-rounded">edit</span>
          <span>{{ $captcha['a'] ?? '?' }} + {{ $captcha['b'] ?? '?' }} = ?</span>
        </div>

        <div class="captcha-answer">
          <input id="captcha" type="number" name="captcha"
                 placeholder="Jawaban" required>
        </div>

        <button type="button" class="captcha-refresh"
                onclick="window.location.reload()">
          <span class="material-symbols-rounded">refresh</span>
        </button>
      </div>

      @error('captcha')
        <div class="error-text">{{ $message }}</div>
      @enderror
    </div>

    <button type="submit" class="btn-primary">Daftar</button>
  </form>

  <div class="auth-foot">
    <span>Sudah punya akun admin?
      <a href="{{ route('admin.login') }}" class="primary-link">Masuk</a>
    </span>
    <a href="{{ route('password.request') }}" class="secondary-link">
      Forgot Password?
    </a>
  </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  // toggle show/hide password (dua field)
  document.querySelectorAll('[data-toggle-password]').forEach(btn => {
    btn.addEventListener('click', () => {
      const wrap  = btn.closest('[data-password-wrapper]');
      const input = wrap ? wrap.querySelector('[data-password-input]') : null;
      if (!input) return;

      const isPw = input.type === 'password';
      input.type = isPw ? 'text' : 'password';

      const icon = btn.querySelector('.material-symbols-rounded');
      if (icon) icon.textContent = isPw ? 'visibility_off' : 'visibility';
    });
  });

  // password strength meter
  const pwInput  = document.querySelector('#password');
  const barEl    = document.querySelector('[data-pw-bar]');
  const statusEl = document.querySelector('[data-pw-status]');
  const tagEls   = {
    length: document.querySelector('[data-pw-tag="length"]'),
    case  : document.querySelector('[data-pw-tag="case"]'),
    digit : document.querySelector('[data-pw-tag="digit"]'),
    symbol: document.querySelector('[data-pw-tag="symbol"]'),
  };

  function resetMeter() {
    if (barEl) {
      barEl.style.width = '0%';
      barEl.style.background = '#f97373';
    }
    if (statusEl) {
      statusEl.textContent = 'Keamanan password: -';
      statusEl.className = 'pw-meter-status';
    }
    Object.values(tagEls).forEach(el => {
      if (el) el.classList.remove('pw-tag--ok');
    });
  }

  function evaluatePassword(value){
    // kalau kosong: bar diam, label "-", chip reset
    if (!value || !value.length) {
      resetMeter();
      return;
    }

    const checks = {
      length: value.length >= 8,
      case  : /[a-z]/.test(value) && /[A-Z]/.test(value),
      digit : /\d/.test(value),
      symbol: /[^A-Za-z0-9]/.test(value),
    };
    let score = Object.values(checks).filter(Boolean).length;

    // update chips
    Object.entries(checks).forEach(([k,ok]) => {
      if(!tagEls[k]) return;
      tagEls[k].classList.toggle('pw-tag--ok', ok);
    });

    // default: belum ada indikator terpenuhi → bar tetap 0, label "-"
    if (score === 0) {
      resetMeter();
      return;
    }

    // mulai gerak kalau minimal 1 indikator terpenuhi
    let width = 0, label = 'Keamanan password: -', cls = '';
    switch(score){
      case 1:
        width = 25;
        label = 'Keamanan password: Low';
        cls = 'pw-meter-status--weak';
        break;
      case 2:
        width = 50;
        label = 'Keamanan password: Medium';
        cls = 'pw-meter-status--medium';
        break;
      case 3:
        width = 75;
        label = 'Keamanan password: Strong';
        cls = 'pw-meter-status--strong';
        break;
      case 4:
        width = 100;
        label = 'Keamanan password: Very Strong';
        cls = 'pw-meter-status--very-strong';
        break;
    }

    if(barEl){
      barEl.style.width = width + '%';
      if(score === 1) barEl.style.background = '#f97373';
      else if(score === 2) barEl.style.background = '#fb923c';
      else barEl.style.background = '#22c55e';
    }

    if(statusEl){
      statusEl.textContent = label;
      statusEl.className = 'pw-meter-status ' + cls;
    }
  }

  if(pwInput){
    pwInput.addEventListener('input', e => evaluatePassword(e.target.value));
    // initial (kalau old('password') kebawa pas validasi gagal)
    evaluatePassword(pwInput.value || '');
  }
});
</script>
@endpush
