
<?php require 'header.php';

// Load cart
if (!isset($_SESSION['cart']) || !$_SESSION['cart']) {
  header("Location: menu.php"); exit;
}

$errors = [];
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $name = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $notes = trim($_POST['notes'] ?? '');

    if ($name==='' || $phone==='' || $address==='') { $errors[] = "Nama, telepon, dan alamat wajib diisi."; }

    // Rebuild items & total
    $items = []; $total = 0;
    $ids = implode(',', array_map('intval', array_keys($_SESSION['cart'])));
    $rows = db()->query("SELECT * FROM products WHERE id IN ($ids)")->fetchAll();
    foreach ($rows as $r) {
      $qty = $_SESSION['cart'][$r['id']];
      $sub = $qty * $r['price'];
      $total += $sub;
      $items[] = ['row'=>$r, 'qty'=>$qty, 'sub'=>$sub];
    }

    if (!$errors) {
      $pdo = db();
      $pdo->beginTransaction();
      $stmt = $pdo->prepare("INSERT INTO orders (customer_name, phone, address, notes, total, status) VALUES (?,?,?,?,?, 'baru')");
      $stmt->execute([$name,$phone,$address,$notes,$total]);
      $order_id = $pdo->lastInsertId();

      $ins = $pdo->prepare("INSERT INTO order_items (order_id, product_id, qty, price) VALUES (?,?,?,?)");
      foreach ($items as $i) {
        $p = $i['row']; $ins->execute([$order_id, $p['id'], $i['qty'], $p['price']]);
      }
      $pdo->commit();
      $_SESSION['cart'] = [];
      header("Location: thanks.php?id=".$order_id);
      exit;
    }
}

?>
<section class="py-5">
  <div class="container">
    <h2 class="section-title mb-4">Checkout</h2>
    <?php if ($errors): ?>
      <div class="alert alert-danger">
        <ul class="m-0">
          <?php foreach ($errors as $e): ?><li><?= esc($e) ?></li><?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>
    <form method="post" class="row g-4">
      <div class="col-md-6">
        <div class="card p-4 shadow-sm">
          <div class="mb-3">
            <label class="form-label">Nama Lengkap</label>
            <input type="text" name="name" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">No. Telepon/WA</label>
            <input type="text" name="phone" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Alamat</label>
            <textarea name="address" class="form-control" rows="4" required></textarea>
          </div>
          <div class="mb-3">
            <label class="form-label">Catatan</label>
            <textarea name="notes" class="form-control" rows="3"></textarea>
          </div>
          <button class="btn btn-danger">Buat Pesanan</button>
        </div>
      </div>
      <div class="col-md-6">
        <div class="card p-4 shadow-sm">
          <h6 class="fw-bold mb-3">Ringkasan Pesanan</h6>
          <ul class="list-group list-group-flush">
            <?php
              $ids = implode(',', array_map('intval', array_keys($_SESSION['cart'])));
              $rows = db()->query("SELECT * FROM products WHERE id IN ($ids)")->fetchAll();
              $total=0;
              foreach ($rows as $r):
                $qty = $_SESSION['cart'][$r['id']];
                $sub = $qty * $r['price']; $total += $sub;
            ?>
            <li class="list-group-item d-flex justify-content-between align-items-center">
              <span><?= esc($r['name']) ?> × <?= (int)$qty ?></span>
              <span>Rp<?= number_format($sub,0,',','.') ?></span>
            </li>
            <?php endforeach; ?>
            <li class="list-group-item d-flex justify-content-between align-items-center fw-bold">
              <span>Total</span>
              <span>Rp<?= number_format($total,0,',','.') ?></span>
            </li>
          </ul>
        </div>
      </div>
    </form>
  </div>
</section>
<?php include 'footer.php'; ?>
