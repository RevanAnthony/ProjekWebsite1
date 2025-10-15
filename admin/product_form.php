
<?php require_once __DIR__ . '/_admin_common.php'; ensure_login();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$product = ['id'=>0,'name'=>'','description'=>'','price'=>'','spicy_level'=>'','category_id'=>'','image'=>''];
if ($id) {
  $stmt = db()->prepare("SELECT * FROM products WHERE id=?");
  $stmt->execute([$id]); $product = $stmt->fetch();
}

$cats = db()->query("SELECT * FROM categories ORDER BY name")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = trim($_POST['name'] ?? '');
  $description = trim($_POST['description'] ?? '');
  $price = (float)($_POST['price'] ?? 0);
  $spicy = (int)($_POST['spicy_level'] ?? 0);
  $cat_id = (int)($_POST['category_id'] ?? 0);

  $imgname = $product['image'] ?? null;
  if (!empty($_FILES['image']['name'])) {
    $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
    $safe = uniqid('img_') . '.' . strtolower($ext);
    $target = __DIR__ . '/../assets/img/' . $safe;
    if (in_array(strtolower($ext), ['jpg','jpeg','png','gif'])) {
      move_uploaded_file($_FILES['image']['tmp_name'], $target);
      $imgname = $safe;
    }
  }

  if ($id) {
    $stmt = db()->prepare("UPDATE products SET category_id=?, name=?, description=?, price=?, spicy_level=?, image=?, updated_at=NOW() WHERE id=?");
    $stmt->execute([$cat_id, $name, $description, $price, $spicy, $imgname, $id]);
  } else {
    $stmt = db()->prepare("INSERT INTO products (category_id, name, description, price, spicy_level, image) VALUES (?,?,?,?,?,?)");
    $stmt->execute([$cat_id, $name, $description, $price, $spicy, $imgname]);
  }
  header("Location: products.php"); exit;
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= $id?'Edit':'Tambah' ?> Produk - Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3><?= $id?'Edit':'Tambah' ?> Produk</h3>
    <div><a href="products.php" class="btn btn-secondary">← Kembali</a></div>
  </div>

  <form method="post" enctype="multipart/form-data" class="card p-4 shadow-sm">
    <div class="row g-3">
      <div class="col-md-6">
        <label class="form-label">Nama Produk</label>
        <input type="text" name="name" class="form-control" value="<?= esc($product['name']) ?>" required>
      </div>
      <div class="col-md-3">
        <label class="form-label">Harga</label>
        <input type="number" name="price" class="form-control" value="<?= esc($product['price']) ?>" required>
      </div>
      <div class="col-md-3">
        <label class="form-label">Level Pedas (0-5)</label>
        <input type="number" name="spicy_level" min="0" max="5" class="form-control" value="<?= esc($product['spicy_level']) ?>">
      </div>
      <div class="col-md-4">
        <label class="form-label">Kategori</label>
        <select name="category_id" class="form-select">
          <?php foreach ($cats as $c): ?>
            <option value="<?= $c['id'] ?>" <?= $product['category_id']==$c['id']?'selected':'' ?>><?= esc($c['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-8">
        <label class="form-label">Gambar</label>
        <input type="file" name="image" class="form-control">
        <?php if ($product['image']): ?>
          <div class="mt-2"><img src="../assets/img/<?= esc($product['image']) ?>" width="120" class="rounded"></div>
        <?php endif; ?>
      </div>
      <div class="col-12">
        <label class="form-label">Deskripsi</label>
        <textarea name="description" class="form-control" rows="5"><?= esc($product['description']) ?></textarea>
      </div>
    </div>
    <div class="mt-3">
      <button class="btn btn-danger"><?= $id?'Simpan':'Tambah' ?></button>
    </div>
  </form>
</div>
</body>
</html>
