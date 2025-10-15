
<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'golden_spice');
define('DB_USER', 'root');
define('DB_PASS', '');

// Base URL if the project is in subfolder (e.g., /golden-spice). Update if needed.
$BASE_URL = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
if ($BASE_URL === '') { $BASE_URL = ''; }

function db() {
    static $pdo;
    if ($pdo === null) {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        } catch (PDOException $e) {
            die('Koneksi database gagal: ' . htmlspecialchars($e->getMessage()));
        }
    }
    return $pdo;
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function esc($str) { return htmlspecialchars($str, ENT_QUOTES, 'UTF-8'); }

function get_setting($key, $default='') {
    $stmt = db()->prepare("SELECT svalue FROM settings WHERE skey = ? LIMIT 1");
    $stmt->execute([$key]);
    $row = $stmt->fetch();
    return $row ? $row['svalue'] : $default;
}

function cart_count() {
    return isset($_SESSION['cart']) ? array_sum(array_values($_SESSION['cart'])) : 0;
}
?>
