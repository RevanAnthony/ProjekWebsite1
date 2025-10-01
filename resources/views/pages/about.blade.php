@extends('layouts.app')
@section('title','Tentang Kami — Chlorofil')

@section('content')
<section class="hero py-5 border-bottom">
  <div class="container-xxl">
    <span class="chip mb-2">Company Profile</span>
    <h1 class="section-title display-6">Sains Hijau untuk Kulit Sehat</h1>
    <p class="lead-hero">
      Kami memformulasikan skincare berbasis <em>copper chlorophyllin</em> yang menenangkan, kaya antioksidan,
      dan berfokus pada keberlanjutan. Misi kami: membantu kulit kembali tenang & cerah secara bertahap.
    </p>

    <div class="row text-center g-3 mt-3">
      <div class="col-md-4"><div class="stat"><h3 class="text-success"><span data-count="98" data-suffix="%">0%</span></h3><div class="small text-muted">Kulit terasa lebih kalem*</div></div></div>
      <div class="col-md-4"><div class="stat"><h3 class="text-success"><span data-count="120000">0</span></h3><div class="small text-muted">Botol terjual 12 bulan</div></div></div>
      <div class="col-md-4"><div class="stat"><h3 class="text-success"><span data-count="88" data-suffix="/100">0</span></h3><div class="small text-muted">Rerata ulasan (88/100)</div></div></div>
      <div class="small text-muted mt-2">*Self-reported pengguna rutin 4 minggu.</div>
    </div>
  </div>
</section>

<section class="py-5">
  <div class="container-xxl">
    <div class="row g-4">
      <div class="col-md-6">
        <div class="p-4 border rounded-4 h-100">
          <h5 class="fw-bold mb-2">Visi</h5>
          <p class="mb-0 text-muted">Menjadi brand skincare hijau yang dipercaya untuk menenangkan kulit sensitif dengan hasil konsisten & ramah lingkungan.</p>
        </div>
      </div>
      <div class="col-md-6">
        <div class="p-4 border rounded-4 h-100">
          <h5 class="fw-bold mb-2">Misi</h5>
          <ul class="mb-0 text-muted">
            <li>Formulasi fokus pada <em>calming</em> & antioksidan chlorophyllin.</li>
            <li>Meminimalkan iritan & memilih kemasan mudah didaur ulang.</li>
            <li>Edukasi perawatan kulit yang sederhana namun efektif.</li>
          </ul>
        </div>
      </div>
    </div>

    <div class="row g-4 mt-1">
      <div class="col-lg-6">
        <h5 class="fw-bold mb-3">Sejarah Singkat</h5>
        <div class="border rounded-4 p-4">
          <ol class="timeline list-unstyled m-0">
            <li class="mb-3">
              <strong>2021</strong> — Riset bahan aktif hijau & uji iritasi kulit sensitif.
            </li>
            <li class="mb-3">
              <strong>2022</strong> — Meluncurkan cleanser & gel moisturizer generasi pertama.
            </li>
            <li class="mb-0">
              <strong>2024–sekarang</strong> — Penguatan supply chain berkelanjutan & edukasi konsumen.
            </li>
          </ol>
        </div>
      </div>
      <div class="col-lg-6">
        <h5 class="fw-bold mb-3">Nilai & Keunggulan</h5>
        <div class="row g-3">
          <div class="col-sm-12">
            <div class="card card-feature h-100">
              <div class="card-body">
                <div class="d-flex align-items-center mb-2"><i class="bi bi-droplet-half text-success fs-4 me-2"></i><h6 class="mb-0">Bahan Aktif Hijau</h6></div>
                <p class="mb-0 text-muted">Copper chlorophyllin menenangkan & menjaga barrier.</p>
              </div>
            </div>
          </div>
          <div class="col-sm-12">
            <div class="card card-feature h-100">
              <div class="card-body">
                <div class="d-flex align-items-center mb-2"><i class="bi bi-heart text-success fs-4 me-2"></i><h6 class="mb-0">Aman untuk Harian</h6></div>
                <p class="mb-0 text-muted">Bebas paraben & pewangi keras; ramah kulit sensitif.</p>
              </div>
            </div>
          </div>
          <div class="col-sm-12">
            <div class="card card-feature h-100">
              <div class="card-body">
                <div class="d-flex align-items-center mb-2"><i class="bi bi-recycle text-success fs-4 me-2"></i><h6 class="mb-0">Berkelanjutan</h6></div>
                <p class="mb-0 text-muted">Kemasan mudah didaur ulang & proses efisien energi.</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="mt-5">
      <h5 class="fw-bold mb-3">Struktur Organisasi</h5>
      <div class="row g-3">
        <div class="col-md-3">
          <div class="border rounded-4 p-3 h-100">
            <div class="small text-muted">CEO</div>
            <div class="fw-semibold">Chief Executive Officer</div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="border rounded-4 p-3 h-100">
            <div class="small text-muted">R&D Lead</div>
            <div class="fw-semibold">Formulation & QA</div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="border rounded-4 p-3 h-100">
            <div class="small text-muted">Ops</div>
            <div class="fw-semibold">Supply Chain & Production</div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="border rounded-4 p-3 h-100">
            <div class="small text-muted">Growth</div>
            <div class="fw-semibold">Brand & Customer Success</div>
          </div>
        </div>
      </div>
    </div>

    <div class="mt-5">
      <h5 class="fw-bold mb-3">Pertanyaan Umum</h5>
      <div class="accordion" id="faq">
        <div class="accordion-item">
          <h2 class="accordion-header"><button class="accordion-button" data-bs-toggle="collapse" data-bs-target="#f1">Apakah aman untuk kulit sensitif?</button></h2>
          <div id="f1" class="accordion-collapse collapse show" data-bs-parent="#faq">
            <div class="accordion-body">Ya. Formulasi difokuskan untuk <em>calming</em>, bebas paraben & pewangi keras.</div>
          </div>
        </div>
        <div class="accordion-item">
          <h2 class="accordion-header"><button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#f2">Apakah produk mengandung alkohol?</button></h2>
          <div id="f2" class="accordion-collapse collapse" data-bs-parent="#faq">
            <div class="accordion-body">Kami menghindari alkohol yang berpotensi mengiritasi; setiap batch melalui QA.</div>
          </div>
        </div>
      </div>
    </div>

  </div>
</section>
@endsection
