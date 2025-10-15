
<?php require_once __DIR__ . '/_admin_common.php'; ensure_login();

// Create/Update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
  $name = trim($_POST['name'] ?? '');
  if ($name !== '') {
    if ($id) {
      $stmt = db()->prepare("UPDATE categories SET name=? WHERE id=?");
      $stmt->execute([$name, $id]);
    } else {
      $stmt = db()->prepare("INSERT INTO categories (name) VALUES (?)");
      $stmt->execute([$name]);
    }
  }
  header("Location: categories.php"); exit;
}

// Delete
if (isset($_GET['del'])) {
  $id = (int)$_GET['del'];
  db()->prepare("DELETE FROM categories WHERE id=?")->execute([$id]);
  header("Location: categories.php"); exit;
}
$rows = db()->query("SELECT * FROM categories ORDER BY id DESC")->fetchAll();
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Kategori - Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4">
  <div class="d-flex justify-content-between mb-3">
    <h3>Kategori</h3>
    <div>
      <a href="index.php" class="btn btn-secondary">← Dashboard</a>
    </div>
  </div>

  <form class="row g-2 mb-4" method="post">
    <div class="col-auto">
      <input type="hidden" name="id" value="">
      <input type="text" name="name" class="form-control" placeholder="Nama kategori..." required>
    </div>
    <div class="col-auto">
      <button class="btn btn-danger">Tambah</button>
    </div>
  </form>

  <div class="table-responsive">
    <table class="table table-striped align-middle">
      <thead><tr><th>ID</th><th>Nama</th><th></th></tr></thead>
      <tbody>
        <?php foreach ($rows as $r): ?>
        <tr>
          <td><?= (int)$r['id'] ?></td>
          <td><?= esc($r['name']) ?></td>
          <td class="text-end">
            <a class="btn btn-sm btn-link text-danger" href="categories.php?del=<?= (int)$r['id'] ?>" onclick="return confirm('Hapus kategori ini?')">Hapus</a>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
</body>
</html>
