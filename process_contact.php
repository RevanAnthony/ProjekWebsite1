
<?php require 'config.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = trim($_POST['name'] ?? '');
  $email = trim($_POST['email'] ?? '');
  $subject = trim($_POST['subject'] ?? '');
  $message = trim($_POST['message'] ?? '');
  if ($name && $email && $message) {
    $stmt = db()->prepare("INSERT INTO messages (name, email, subject, message) VALUES (?,?,?,?)");
    $stmt->execute([$name, $email, $subject, $message]);
  }
}
header("Location: contact.php?sent=1");
exit;
