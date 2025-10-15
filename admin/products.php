
<?php require_once __DIR__ . '/_admin_common.php'; ensure_login();

if (isset($_GET['del'])) {
  $id = (int)$_GET['del'];
  db()->prepare("DELETE FROM products WHERE id=?")->execute([$id]);
  header("Location: products.php"); exit;
}
$rows = db()->query("SELECT p.*, c.name AS cat_name FROM products p LEFT JOIN categories c ON c.id=p.category_id ORDER BY p.id DESC")->fetchAll();
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Produk - Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Produk</h3>
    <div>
      <a class="btn btn-secondary" href="index.php">← Dashboard</a>
      <a class="btn btn-danger" href="product_form.php">+ Tambah Produk</a>
    </div>
  </div>

  <div class="table-responsive">
    <table class="table table-striped align-middle">
      <thead><tr><th>ID</th><th>Gambar</th><th>Nama</th><th>Kategori</th><th>Harga</th><th>Pedas</th><th></th></tr></thead>
      <tbody>
        <?php foreach ($rows as $r): ?>
        <tr>
          <td><?= (int)$r['id'] ?></td>
          <td><img src="../assets/img/<?= esc($r['image'] ?: 'placeholder.jpg') ?>" width="70" class="rounded"></td>
          <td><?= esc($r['name']) ?></td>
          <td><?= esc($r['cat_name']) ?></td>
          <td>Rp<?= number_format($r['price'],0,',','.') ?></td>
          <td><?= (int)$r['spicy_level'] ?></td>
          <td class="text-end">
            <a href="product_form.php?id=<?= (int)$r['id'] ?>" class="btn btn-sm btn-outline-secondary">Edit</a>
            <a href="products.php?del=<?= (int)$r['id'] ?>" class="btn btn-sm btn-link text-danger" onclick="return confirm('Hapus produk?')">Hapus</a>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
</body>
</html>
