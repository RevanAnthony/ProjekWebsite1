
<?php include 'header.php'; ?>
<section class="py-5">
  <div class="container text-center">
    <i class="bi bi-check-circle text-success display-4"></i>
    <h2 class="mt-3">Terima kasih!</h2>
    <p>Pesananmu berhasil dibuat. Nomor pesanan: <strong>#<?= (int)($_GET['id'] ?? 0) ?></strong>.</p>
    <a href="index.php" class="btn btn-danger">Kembali ke Beranda</a>
  </div>
</section>
<?php include 'footer.php'; ?>
