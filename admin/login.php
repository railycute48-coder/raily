<?php
require_once '../includes/db.php';
session_start();
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ?");
    $stmt->execute([$username]);
    $admin = $stmt->fetch();
    if ($admin && password_verify($password, $admin['password'])) {
        unset($admin['password']);
        $_SESSION['admin'] = $admin;
        header('Location: dashboard.php');
        exit;
    } else {
        $errors[] = "Invalid admin credentials.";
    }
}
?>
<!doctype html>
<html><head><meta charset="utf-8"><title>Admin Login</title><link rel="stylesheet" href="../css/style.css"></head><body>
<div class="container narrow">
  <h2>Admin Login</h2>
  <?php if ($errors): ?><div class="error"><?=htmlspecialchars($errors[0])?></div><?php endif; ?>
  <form method="post">
    <label>Username <input name="username"></label>
    <label>Password <input type="password" name="password"></label>
    <button type="submit">Login</button>
  </form>
</div>
</body></html>
