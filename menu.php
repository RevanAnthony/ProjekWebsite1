
<?php require 'header.php';

// Add to cart via GET (simple)
if (isset($_GET['add'])) {
  $id = (int)$_GET['add'];
  if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
  $_SESSION['cart'][$id] = ($_SESSION['cart'][$id] ?? 0) + 1;
  header("Location: menu.php");
  exit;
}

$cat = isset($_GET['cat']) ? (int)$_GET['cat'] : 0;
$cats = db()->query("SELECT * FROM categories ORDER BY name")->fetchAll();

$sql = "SELECT p.*, c.name AS cat_name FROM products p LEFT JOIN categories c ON c.id=p.category_id";
$params = [];
if ($cat) { $sql .= " WHERE p.category_id=?"; $params[]=$cat; }
$sql .= " ORDER BY p.id DESC";
$stmt = db()->prepare($sql); $stmt->execute($params);
$products = $stmt->fetchAll();
?>
<section class="py-5">
  <div class="container">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
      <h2 class="section-title m-0">Menu</h2>
      <form class="d-flex" method="get">
        <select class="form-select me-2" name="cat" onchange="this.form.submit()">
          <option value="0">Semua Kategori</option>
          <?php foreach ($cats as $c): ?>
            <option value="<?= $c['id'] ?>" <?= $cat==$c['id']?'selected':'' ?>><?= esc($c['name']) ?></option>
          <?php endforeach; ?>
        </select>
        <noscript><button class="btn btn-danger">Filter</button></noscript>
      </form>
    </div>

    <div class="row g-4">
      <?php foreach ($products as $p): ?>
      <div class="col-md-3">
        <div class="card h-100 border-0 shadow-sm">
          <img src="assets/img/<?= esc($p['image'] ?: 'placeholder.jpg') ?>" class="card-img-top" alt="<?= esc($p['name']) ?>">
          <div class="card-body d-flex flex-column">
            <h5 class="card-title fw-bold"><?= esc($p['name']) ?></h5>
            <p class="card-text small text-muted"><?= esc(mb_strimwidth($p['description'],0,120,'…','UTF-8')) ?></p>
            <div class="mt-auto d-flex align-items-center justify-content-between">
              <span class="fw-semibold">Rp<?= number_format($p['price'],0,',','.') ?></span>
              <a href="menu.php?add=<?= (int)$p['id'] ?>" class="btn btn-danger"><i class="bi bi-plus"></i> Keranjang</a>
            </div>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php include 'footer.php'; ?>
