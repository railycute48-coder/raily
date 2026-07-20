<?php
require_once '../includes/db.php';
session_start();
if (empty($_SESSION['admin'])) { header('Location: login.php'); exit; }

$messages = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_password'])) {
    $new = $_POST['new_password'];
    if (strlen($new) >= 6) {
        $hash = password_hash($new, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE admins SET password = ? WHERE id = ?");
        $stmt->execute([$hash, $_SESSION['admin']['id']]);
        $messages[] = "Password updated.";
    } else {
        $messages[] = "Password must be at least 6 characters.";
    }
}
?>
<!doctype html>
<html><head><meta charset="utf-8"><title>Settings</title><link rel="stylesheet" href="../css/style.css"></head><body>
<header class="admin-header"><a href="dashboard.php">Dashboard</a> | <a href="settings.php">Settings</a></header>
<main class="container narrow">
  <h2>Settings</h2>
  <?php foreach ($messages as $m): ?><div class="success"><?=htmlspecialchars($m)?></div><?php endforeach; ?>
  <form method="post">
    <label>New Admin Password <input type="password" name="new_password" required></label>
    <button type="submit">Update Password</button>
  </form>
</main>
</body></html>
