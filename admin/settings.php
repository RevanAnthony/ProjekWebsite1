
<?php require_once __DIR__ . '/_admin_common.php'; ensure_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  foreach (['hero_title','hero_sub','hero_cta'] as $k) {
    $v = $_POST[$k] ?? '';
    $stmt = db()->prepare("INSERT INTO settings (skey,svalue) VALUES (?,?) ON DUPLICATE KEY UPDATE svalue=VALUES(svalue)");
    $stmt->execute([$k, $v]);
  }
  $msg = "Tersimpan.";
}
$hero_title = get_setting('hero_title','');
$hero_sub = get_setting('hero_sub','');
$hero_cta = get_setting('hero_cta','');
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Settings - Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Settings</h3>
    <div><a class="btn btn-secondary" href="index.php">← Dashboard</a></div>
  </div>
  <?php if (!empty($msg)): ?><div class="alert alert-success"><?= esc($msg) ?></div><?php endif; ?>
  <form method="post" class="card p-4 shadow-sm">
    <h6 class="fw-bold mb-3">Hero Section</h6>
    <div class="mb-3"><label class="form-label">Judul</label><input name="hero_title" class="form-control" value="<?= esc($hero_title) ?>"></div>
    <div class="mb-3"><label class="form-label">Subjudul</label><textarea name="hero_sub" rows="3" class="form-control"><?= esc($hero_sub) ?></textarea></div>
    <div class="mb-3"><label class="form-label">Teks Tombol</label><input name="hero_cta" class="form-control" value="<?= esc($hero_cta) ?>"></div>
    <button class="btn btn-danger">Simpan</button>
  </form>
</div>
</body>
</html>
