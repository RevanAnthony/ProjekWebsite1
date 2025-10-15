
<?php require_once __DIR__ . '/_admin_common.php'; ensure_login(); ?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin Dashboard - Golden Spice</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-dark bg-dark mb-4">
  <div class="container">
    <a class="navbar-brand" href="index.php">Admin - Golden Spice</a>
    <div class="d-flex">
      <span class="navbar-text me-3">Halo, <?= esc($_SESSION['username']) ?></span>
      <a class="btn btn-outline-light" href="logout.php">Logout</a>
    </div>
  </div>
</nav>
<div class="container">
  <div class="row g-3">
    <div class="col-md-3"><a class="btn btn-danger w-100" href="products.php">Produk</a></div>
    <div class="col-md-3"><a class="btn btn-danger w-100" href="categories.php">Kategori</a></div>
    <div class="col-md-3"><a class="btn btn-danger w-100" href="reasons.php">Reasons</a></div>
    <div class="col-md-3"><a class="btn btn-danger w-100" href="orders.php">Pesanan</a></div>
    <div class="col-md-3"><a class="btn btn-outline-danger w-100" href="settings.php">Settings</a></div>
  </div>

  <hr class="my-4">

  <div class="row g-4">
    <div class="col-md-6">
      <div class="card p-4 shadow-sm">
        <h5 class="fw-bold mb-3">Statistik Singkat</h5>
        <?php
          $p = db()->query("SELECT COUNT(*) AS c FROM products")->fetch()['c'];
          $o = db()->query("SELECT COUNT(*) AS c FROM orders")->fetch()['c'];
          $m = db()->query("SELECT COUNT(*) AS c FROM messages")->fetch()['c'];
        ?>
        <ul class="list-group">
          <li class="list-group-item d-flex justify-content-between"><span>Total Produk</span><span class="fw-bold"><?= (int)$p ?></span></li>
          <li class="list-group-item d-flex justify-content-between"><span>Total Pesanan</span><span class="fw-bold"><?= (int)$o ?></span></li>
          <li class="list-group-item d-flex justify-content-between"><span>Pesan Masuk</span><span class="fw-bold"><?= (int)$m ?></span></li>
        </ul>
      </div>
    </div>
    <div class="col-md-6">
      <div class="card p-4 shadow-sm">
        <h5 class="fw-bold mb-3">Pesanan Terbaru</h5>
        <div class="table-responsive">
          <table class="table">
            <thead><tr><th>ID</th><th>Nama</th><th>Total</th><th>Status</th><th>Tanggal</th></tr></thead>
            <tbody>
              <?php foreach (db()->query("SELECT * FROM orders ORDER BY id DESC LIMIT 5") as $r): ?>
                <tr>
                  <td>#<?= (int)$r['id'] ?></td>
                  <td><?= esc($r['customer_name']) ?></td>
                  <td>Rp<?= number_format($r['total'],0,',','.') ?></td>
                  <td><?= esc($r['status']) ?></td>
                  <td><?= esc($r['created_at']) ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
</body>
</html>
