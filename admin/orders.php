
<?php require_once __DIR__ . '/_admin_common.php'; ensure_login();

if (isset($_POST['status']) && isset($_POST['id'])) {
  db()->prepare("UPDATE orders SET status=? WHERE id=?")->execute([$_POST['status'], (int)$_POST['id']]);
  header("Location: orders.php?id=".(int)$_POST['id']); exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Pesanan - Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Pesanan</h3>
    <div><a class="btn btn-secondary" href="index.php">← Dashboard</a></div>
  </div>

  <div class="row g-4">
    <div class="col-md-5">
      <div class="card p-3">
        <h6 class="fw-bold">Daftar Pesanan</h6>
        <div class="list-group">
          <?php foreach (db()->query("SELECT * FROM orders ORDER BY id DESC") as $o): ?>
            <a class="list-group-item list-group-item-action <?= $id==$o['id']?'active':'' ?>" href="orders.php?id=<?= (int)$o['id'] ?>">
              #<?= (int)$o['id'] ?> - <?= esc($o['customer_name']) ?> - Rp<?= number_format($o['total'],0,',','.') ?>
            </a>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
    <div class="col-md-7">
      <?php if ($id): 
        $order = db()->prepare("SELECT * FROM orders WHERE id=?"); $order->execute([$id]); $order=$order->fetch();
      ?>
      <div class="card p-4">
        <div class="d-flex justify-content-between mb-3">
          <h6 class="fw-bold">Detail Pesanan #<?= (int)$order['id'] ?></h6>
          <form method="post" class="d-flex gap-2">
            <input type="hidden" name="id" value="<?= (int)$order['id'] ?>">
            <select name="status" class="form-select">
              <?php foreach (['baru','diproses','selesai','batal'] as $s): ?>
                <option value="<?= $s ?>" <?= $order['status']==$s?'selected':'' ?>><?= ucfirst($s) ?></option>
              <?php endforeach; ?>
            </select>
            <button class="btn btn-danger">Ubah</button>
          </form>
        </div>
        <div class="mb-2"><strong>Nama:</strong> <?= esc($order['customer_name']) ?></div>
        <div class="mb-2"><strong>Telepon:</strong> <?= esc($order['phone']) ?></div>
        <div class="mb-2"><strong>Alamat:</strong> <?= esc($order['address']) ?></div>
        <div class="mb-3"><strong>Catatan:</strong> <?= esc($order['notes']) ?></div>
        <div class="table-responsive">
          <table class="table">
            <thead><tr><th>Produk</th><th>Qty</th><th>Harga</th><th>Subtotal</th></tr></thead>
            <tbody>
              <?php
                $items = db()->prepare("SELECT oi.*, p.name FROM order_items oi JOIN products p ON p.id=oi.product_id WHERE oi.order_id=?");
                $items->execute([$id]);
                foreach ($items as $it): ?>
                  <tr>
                    <td><?= esc($it['name']) ?></td>
                    <td><?= (int)$it['qty'] ?></td>
                    <td>Rp<?= number_format($it['price'],0,',','.') ?></td>
                    <td>Rp<?= number_format($it['qty']*$it['price'],0,',','.') ?></td>
                  </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
        <div class="text-end fw-bold">Total: Rp<?= number_format($order['total'],0,',','.') ?></div>
      </div>
      <?php else: ?>
        <div class="alert alert-info">Pilih pesanan untuk melihat detail.</div>
      <?php endif; ?>
    </div>
  </div>
</div>
</body>
</html>
