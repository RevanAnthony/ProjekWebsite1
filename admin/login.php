
<?php
require_once __DIR__ . '/../config.php';

// Auto create default admin if no users exist
$count = db()->query("SELECT COUNT(*) AS c FROM users")->fetch()['c'];
if ($count == 0) {
  $stmt = db()->prepare("INSERT INTO users (username, password_hash) VALUES (?,?)");
  $stmt->execute(['admin', password_hash('admin123', PASSWORD_BCRYPT)]);
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = trim($_POST['username'] ?? '');
  $password = trim($_POST['password'] ?? '');

  $stmt = db()->prepare("SELECT * FROM users WHERE username = ? LIMIT 1");
  $stmt->execute([$username]);
  $user = $stmt->fetch();
  if ($user && password_verify($password, $user['password_hash'])) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    header("Location: index.php");
    exit;
  } else {
    $error = "Username atau password salah";
  }
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login Admin - Golden Spice</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center" style="min-height:100vh">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-4">
        <div class="card shadow-sm">
          <div class="card-body p-4">
            <h5 class="fw-bold mb-3 text-center">Login Admin</h5>
            <?php if ($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>
            <form method="post">
              <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control" required>
              </div>
              <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
              </div>
              <button class="btn btn-danger w-100">Login</button>
              <a class="btn btn-link w-100 mt-2" href="../index.php">← Kembali ke situs</a>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
