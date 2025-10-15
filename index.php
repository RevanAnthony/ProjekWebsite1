
<?php include 'header.php'; ?>

<section class="hero d-flex align-items-center">
  <div class="container hero-content">
    <div class="row">
      <div class="col-lg-8">
        <p class="text-uppercase fw-semibold mb-2">Standar Emas Rice Bowl Pedas!</p>
        <h1 class="display-5 fw-extrabold mb-3 text-white"><?= esc(get_setting('hero_title','STANDAR EMAS RICE BOWL PEDAS!')) ?></h1>
        <p class="lead mb-4"><?= esc(get_setting('hero_sub','Nikmati perpaduan sempurna nasi hangat, ayam krispi, dan saus spesial yang dibuat khusus untuk para pecinta sensasi pedas.')) ?></p>
        <a href="<?= $BASE_URL ?>/menu.php" class="btn btn-lg btn-danger btn-cta"><?= esc(get_setting('hero_cta','Lihat Menu & Pesan Sekarang')) ?></a>
      </div>
    </div>
  </div>
</section>

<section class="py-5 bg-light">
  <div class="container">
    <h2 class="section-title text-center mb-1">Kenapa Harus Golden Spice?</h2>
    <p class="text-center text-muted mb-5">Tiga alasan utama mengapa rice bowl kami akan menjadi favoritmu!</p>
    <div class="row g-4">
      <?php
      $reasons = db()->query("SELECT * FROM reasons LIMIT 3")->fetchAll();
      foreach ($reasons as $r): ?>
      <div class="col-md-4">
        <div class="p-4 bg-white rounded-4 shadow-sm card-feature h-100">
          <div class="icon mb-3"><i class="bi <?= esc($r['icon']) ?> fs-4"></i></div>
          <h5 class="fw-bold mb-2"><?= esc($r['title']) ?></h5>
          <p class="text-muted mb-0"><?= esc($r['description']) ?></p>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="py-5">
  <div class="container">
    <div class="d-flex align-items-center justify-content-center gap-3 mb-3">
      <i class="bi bi-fire text-danger fs-3"></i>
      <h2 class="section-title text-center m-0">Kualitas Tanpa Tanding!</h2>
      <i class="bi bi-fire text-danger fs-3"></i>
    </div>
    <p class="text-center text-muted mb-4">Menjamin agar tiap gorengan ayam terasa krispi, enak, dan lembut di dalam.</p>
    <div class="row g-4">
      <?php
      $stmt = db()->query("SELECT * FROM products ORDER BY id ASC LIMIT 4");
      foreach ($stmt as $p): ?>
      <div class="col-md-3">
        <div class="card card-product border-0">
          <img class="img-fluid" src="assets/img/<?= esc($p['image'] ?: 'placeholder.jpg') ?>" alt="<?= esc($p['name']) ?>">
          <div class="card-body px-0">
            <h6 class="fw-bold"><?= esc($p['name']) ?></h6>
            <p class="small text-muted"><?= esc(mb_strimwidth($p['description'],0,80,'…','UTF-8')) ?></p>
            <div class="d-flex align-items-center justify-content-between">
              <span class="fw-semibold">Rp<?= number_format($p['price'],0,',','.') ?></span>
              <a href="menu.php?add=<?= (int)$p['id'] ?>" class="btn btn-sm btn-danger">Tambah</a>
            </div>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <div class="text-center mt-4">
      <a href="menu.php" class="btn btn-outline-danger">Lihat Menu »</a>
    </div>
  </div>
</section>

<section class="py-5 bg-light">
  <div class="container">
    <div class="row align-items-center g-4">
      <div class="col-md-5">
        <img class="img-fluid rounded-4 shadow" src="assets/img/sambal-bawang.jpg" alt="Story">
      </div>
      <div class="col-md-7">
        <h3 class="fw-bold text-danger">Story Behind It…</h3>
        <p>Golden Spice lahir dari kecintaan kami pada makanan pedas yang proper, enak, dan berkualitas. Karena percaya bahwa makanan cepat saji tidak harus membosankan.</p>
        <p>Mulai dari pemilihan bahan, bumbu, proses masak yang higienis, hingga penyajian yang menggugah selera—kami fokus di detail. Misi kami sederhana: menyajikan kelezatan pedas dalam setiap suapan signature rice bowl untuk menemani hari-harimu.</p>
      </div>
    </div>
  </div>
</section>

<?php include 'footer.php'; ?>
