
<?php require_once __DIR__ . '/_admin_common.php'; ensure_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $id = (int)($_POST['id'] ?? 0);
  $title = trim($_POST['title'] ?? '');
  $description = trim($_POST['description'] ?? '');
  $icon = trim($_POST['icon'] ?? 'bi-trophy');
  if ($id) {
    db()->prepare("UPDATE reasons SET title=?, description=?, icon=? WHERE id=?")->execute([$title,$description,$icon,$id]);
  } else {
    db()->prepare("INSERT INTO reasons (title, description, icon) VALUES (?,?,?)")->execute([$title,$description,$icon]);
  }
  header("Location: reasons.php"); exit;
}
if (isset($_GET['del'])) { db()->prepare("DELETE FROM reasons WHERE id=?")->execute([(int)$_GET['del']]); header("Location: reasons.php"); exit; }
$rows = db()->query("SELECT * FROM reasons ORDER BY id DESC")->fetchAll();
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Reasons - Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Reasons (Why Us)</h3>
    <div><a class="btn btn-secondary" href="index.php">← Dashboard</a></div>
  </div>
  <form class="card p-3 mb-4" method="post">
    <div class="row g-2 align-items-end">
      <div class="col-md-3"><label class="form-label">Judul</label><input name="title" class="form-control" required></div>
      <div class="col-md-6"><label class="form-label">Deskripsi</label><input name="description" class="form-control" required></div>
      <div class="col-md-2"><label class="form-label">Icon (Bootstrap)</label><input name="icon" class="form-control" placeholder="bi-fire"></div>
      <div class="col-md-1"><button class="btn btn-danger w-100">Tambah</button></div>
    </div>
  </form>

  <div class="table-responsive">
    <table class="table table-striped align-middle">
      <thead><tr><th>ID</th><th>Icon</th><th>Judul</th><th>Deskripsi</th><th></th></tr></thead>
      <tbody>
        <?php foreach ($rows as $r): ?>
          <tr>
            <td><?= (int)$r['id'] ?></td>
            <td><i class="bi <?= esc($r['icon']) ?>"></i> <code><?= esc($r['icon']) ?></code></td>
            <td><?= esc($r['title']) ?></td>
            <td><?= esc($r['description']) ?></td>
            <td class="text-end">
              <a href="reasons.php?del=<?= (int)$r['id'] ?>" class="text-danger btn btn-sm btn-link" onclick="return confirm('Hapus reason ini?')">Hapus</a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
</body>
</html>
