
<?php require_once __DIR__ . '/config.php'; ?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Golden Spice</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link href="<?= $BASE_URL ?>/assets/css/style.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-danger fixed-top shadow-sm">
  <div class="container">
    <a class="navbar-brand fw-bold" href="<?= $BASE_URL ?>/index.php">GOLDEN SPICE</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarsExample07" aria-controls="navbarsExample07" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarsExample07">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item"><a class="nav-link" href="<?= $BASE_URL ?>/index.php">Beranda</a></li>
        <li class="nav-item"><a class="nav-link" href="<?= $BASE_URL ?>/menu.php">Menu</a></li>
        <li class="nav-item"><a class="nav-link" href="<?= $BASE_URL ?>/about.php">Tentang Kami</a></li>
        <li class="nav-item"><a class="nav-link" href="<?= $BASE_URL ?>/contact.php">Kontak</a></li>
      </ul>
      <div class="d-flex gap-3">
        <a class="btn btn-light" href="<?= $BASE_URL ?>/cart.php"><i class="bi bi-bag"></i> <span class="badge bg-danger"><?= cart_count(); ?></span></a>
        <a class="btn btn-outline-light" href="<?= $BASE_URL ?>/admin/login.php"><i class="bi bi-person-lock"></i> Admin</a>
      </div>
    </div>
  </div>
</nav>
<main class="pt-5"> <!-- space for fixed nav -->
