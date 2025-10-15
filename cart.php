
<?php require 'header.php';

// Handle actions
if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

if (isset($_GET['inc'])) {
  $id=(int)$_GET['inc']; $_SESSION['cart'][$id] = ($_SESSION['cart'][$id] ?? 0) + 1;
  header("Location: cart.php"); exit;
} elseif (isset($_GET['dec'])) {
  $id=(int)$_GET['dec']; if (isset($_SESSION['cart'][$id])) { $_SESSION['cart'][$id]--; if ($_SESSION['cart'][$id]<=0) unset($_SESSION['cart'][$id]); }
  header("Location: cart.php"); exit;
} elseif (isset($_GET['del'])) {
  $id=(int)$_GET['del']; unset($_SESSION['cart'][$id]);
  header("Location: cart.php"); exit;
}

$items = [];
$total = 0;
if ($_SESSION['cart']) {
  $ids = implode(',', array_map('intval', array_keys($_SESSION['cart'])));
  $rows = db()->query("SELECT * FROM products WHERE id IN ($ids)")->fetchAll();
  foreach ($rows as $r) {
    $qty = $_SESSION['cart'][$r['id']];
    $sub = $qty * $r['price'];
    $total += $sub;
    $items[] = ['row'=>$r, 'qty'=>$qty, 'sub'=>$sub];
  }
}
?>
<section class="py-5">
  <div class="container">
    <h2 class="section-title mb-4">Keranjang</h2>
    <?php if (!$items): ?>
      <div class="alert alert-info">Keranjang masih kosong. <a href="menu.php">Belanja sekarang »</a></div>
    <?php else: ?>
    <div class="table-responsive">
      <table class="table align-middle">
        <thead><tr><th>Produk</th><th>Harga</th><th>Qty</th><th>Subtotal</th><th></th></tr></thead>
        <tbody>
          <?php foreach ($items as $i): $p=$i['row']; ?>
          <tr>
            <td>
              <div class="d-flex align-items-center">
                <img src="assets/img/<?= esc($p['image'] ?: 'placeholder.jpg') ?>" class="rounded me-3" width="70">
                <div>
                  <div class="fw-semibold"><?= esc($p['name']) ?></div>
                  <small class="text-muted"><?= esc(mb_strimwidth($p['description'],0,60,'…','UTF-8')) ?></small>
                </div>
              </div>
            </td>
            <td>Rp<?= number_format($p['price'],0,',','.') ?></td>
            <td>
              <div class="btn-group" role="group">
                <a href="cart.php?dec=<?= (int)$p['id'] ?>" class="btn btn-outline-danger">-</a>
                <span class="btn btn-light disabled"><?= (int)$i['qty'] ?></span>
                <a href="cart.php?inc=<?= (int)$p['id'] ?>" class="btn btn-outline-danger">+</a>
              </div>
            </td>
            <td>Rp<?= number_format($i['sub'],0,',','.') ?></td>
            <td><a href="cart.php?del=<?= (int)$p['id'] ?>" class="btn btn-sm btn-link text-danger">Hapus</a></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <div class="d-flex justify-content-end">
      <div class="card p-4 shadow-sm" style="min-width:320px">
        <div class="d-flex justify-content-between mb-3">
          <span class="fw-semibold">Total</span>
          <span class="fw-bold">Rp<?= number_format($total,0,',','.') ?></span>
        </div>
        <a href="checkout.php" class="btn btn-danger w-100">Lanjut ke Checkout</a>
      </div>
    </div>
    <?php endif; ?>
  </div>
</section>
<?php include 'footer.php'; ?>
