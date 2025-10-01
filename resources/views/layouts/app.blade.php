<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title','Chlorofil Skincare')</title>

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

  <!-- Bootstrap CSS & Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

  <style>
    :root{
      --brand-50:#ecfdf5; --brand-100:#d1fae5;
      --brand-600:#059669; --brand-700:#047857; --brand-800:#065f46;
      --ink:#0f172a; --muted:#475569; --ring:#cbd5e1;
    }
    html,body{height:100%}
    body{
      font-family:"Plus Jakarta Sans", system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, "Apple Color Emoji","Segoe UI Emoji";
      color:var(--ink);
      background:#fff;
      letter-spacing:.2px;
    }
    .navbar-brand{font-weight:800; letter-spacing:.2px}
    .btn-brand{
      --bs-btn-color:#fff; --bs-btn-bg:var(--brand-700); --bs-btn-border-color:var(--brand-700);
      --bs-btn-hover-bg:var(--brand-800); --bs-btn-hover-border-color:var(--brand-800);
      --bs-btn-focus-shadow-rgb:4,120,87;
    }
    .btn-outline-brand{
      --bs-btn-color:var(--brand-700); --bs-btn-border-color:var(--brand-700);
      --bs-btn-hover-bg:var(--brand-700); --bs-btn-hover-border-color:var(--brand-700);
      --bs-btn-hover-color:#fff;
    }
    .link-brand{color:var(--brand-700); text-decoration:none}
    .link-brand:hover{color:var(--brand-800); text-decoration:underline}

    .chip{display:inline-block; padding:.35rem .65rem; border-radius:999px; font-size:.78rem; background:var(--brand-50); color:var(--brand-800); font-weight:600}

    .hero{
      background:
        radial-gradient(1200px 400px at 10% -10%, #a7f3d0 0%, transparent 60%),
        radial-gradient(800px 300px at 90% 0%, #d1fae5 0%, transparent 60%),
        linear-gradient(180deg, #ffffff 0%, #f8fffb 100%);
    }
    .section-title{font-weight:800; letter-spacing:.2px}
    .lead-hero{font-size:1.125rem; color:var(--muted)}
    .card-feature{border:1px solid #e2e8f0; transition:all .2s ease}
    .card-feature:hover{transform:translateY(-4px); box-shadow:0 12px 28px rgba(2,48,38,.08)}

    .stat{border:1px solid #e2e8f0; border-radius:.8rem; padding:1rem}
    .stat h3{font-weight:800; margin:0}
    .footer{background:var(--brand-700); color:#ecfeff}
    .nav-link{font-weight:600}
    .nav-link.active, .nav-link:hover{color:var(--brand-700)!important}
    @media (min-width:1200px){ .container-xxl{max-width:1200px} }
  </style>
</head>
<body>

<nav class="navbar navbar-expand-lg bg-white sticky-top border-bottom">
  <div class="container-xxl">
    <a class="navbar-brand" href="{{ route('home') }}">Chlorofil</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div id="navMain" class="collapse navbar-collapse">
      <ul class="navbar-nav ms-auto mb-2 mb-lg-0 gap-lg-2">
        <li class="nav-item"><a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Home</a></li>
        <li class="nav-item"><a class="nav-link {{ request()->routeIs('about') ? 'active' : '' }}" href="{{ route('about') }}">Tentang Kami</a></li>
        <li class="nav-item"><a class="nav-link {{ request()->routeIs('contact') ? 'active' : '' }}" href="{{ route('contact') }}">Kontak</a></li>
      </ul>
    </div>
  </div>
</nav>

<main>@yield('content')</main>

<footer class="footer py-5 mt-5">
  <div class="container-xxl">
    <div class="row gy-4 align-items-center">
      <div class="col-lg-7">
        <h5 class="mb-1 fw-bold">Chlorofil Skincare</h5>
        <p class="mb-0">Perawatan kulit hijau berbasis chlorophyll. Aman, ringan, dan ramah lingkungan.</p>
      </div>
      <div class="col-lg-5 text-lg-end">
        <a href="{{ route('contact') }}" class="btn btn-light text-success fw-semibold">Hubungi Kami</a>
      </div>
    </div>
    <hr class="border-light-subtle my-4">
    <div class="d-flex justify-content-between small">
      <span>&copy; {{ date('Y') }} Chlorofil Skincare. All rights reserved.</span>
      <span class="opacity-75">Made by Kelompok APS</span>
    </div>
  </div>
</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
  // counter on view (mendukung desimal & suffix)
  const observer = new IntersectionObserver((entries)=>{
    entries.forEach(entry=>{
      if(!entry.isIntersecting) return;

      const el       = entry.target;
      const to       = parseFloat(el.dataset.count || '0');
      const decimals = parseInt(el.dataset.decimals || '0', 10);
      const factor   = Math.pow(10, decimals);

      let n    = 0;
      const end = Math.round(to * factor);
      const step = Math.max(1, Math.floor(end / 60)); // ~1 detik animasi

      const t = setInterval(()=>{
        n += step;
        if(n >= end){ n = end; clearInterval(t); }
        const val = (n / factor).toLocaleString('id-ID', {
          minimumFractionDigits: decimals, maximumFractionDigits: decimals
        });
        // jika to >= 1000 dan tidak pakai decimals, tambahkan '+'
        const plus = (decimals === 0 && to >= 1000) ? '+' : '';
        el.textContent = val + plus + (el.dataset.suffix || '');
      }, 16);

      observer.unobserve(el);
    });
  }, { threshold: .5 });

  document.querySelectorAll('[data-count]').forEach(el => observer.observe(el));
</script>

</body>
</html>
