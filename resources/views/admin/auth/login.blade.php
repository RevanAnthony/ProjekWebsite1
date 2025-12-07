{{-- resources/views/admin/auth/login.blade.php --}}
@extends('admin.layouts.app')
@section('title','Login Admin — Golden Spice')

@push('styles')
<style>
  .auth-wrapper{
    max-width:480px;
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
    margin-top:10px;
  }

  /* password + eye icon */
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

  /* CAPTCHA */
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

  /* remember me */
  .remember-row{
    display:flex;
    align-items:center;
    gap:8px;
    margin-top:12px;
    font-size:13px;
    color:#444;
    line-height:1.4;
  }
  .remember-row input[type="checkbox"]{
    width:18px;
    height:18px;
    margin:0;
    flex:0 0 18px;
    accent-color:#ff5a2a;
  }
  .remember-row span{
    margin:0;
    font-weight:400;
    cursor:pointer;
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
  <h1 class="auth-title">Login Admin / Kasir</h1>
  <p class="auth-sub">Masuk ke panel kasir Golden Spice.</p>

  {{-- NOTIF ERROR GLOBAL (opsional) --}}
  @if ($errors->has('email') && !$errors->has('password') && !$errors->has('captcha'))
    {{-- kalau mau bikin box error global, taruh di sini --}}
  @endif

  <form method="POST" action="{{ route('admin.login.attempt') }}">
    @csrf

    {{-- EMAIL --}}
    <div class="field">
      <label for="email">Email</label>
      <input id="email"
             type="email"
             name="email"
             value="{{ old('email') }}"
             required
             autofocus>
      @error('email')
        <div class="error-text">{{ $message }}</div>
      @enderror
    </div>

    {{-- PASSWORD --}}
    <div class="field">
      <label for="password">Password</label>
      <div class="password-group" data-password-wrapper>
        <input id="password"
               type="password"
               name="password"
               autocomplete="current-password"
               required
               data-password-input>
        <button type="button"
                class="toggle-eye"
                data-toggle-password>
          <span class="material-symbols-rounded">visibility</span>
        </button>
      </div>
      @error('password')
        <div class="error-text">{{ $message }}</div>
      @enderror
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
          <input id="captcha"
                 type="number"
                 name="captcha"
                 placeholder="Jawaban"
                 value="{{ old('captcha') }}"
                 required>
        </div>

        <button type="button"
                class="captcha-refresh"
                onclick="window.location.reload()">
          <span class="material-symbols-rounded">refresh</span>
        </button>
      </div>

      @error('captcha')
        <div class="error-text">{{ $message }}</div>
      @enderror
    </div>

    {{-- REMEMBER --}}
    <div class="field" style="margin-top:6px;">
      <label class="remember-row">
        <input type="checkbox"
               id="remember"
               name="remember"
               value="1"
               {{ old('remember') ? 'checked' : '' }}>
        <span>Ingat saya di perangkat ini</span>
      </label>
    </div>

    <button type="submit" class="btn-primary">Masuk</button>
  </form>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
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
});
</script>
@endpush
