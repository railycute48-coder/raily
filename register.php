<?php
require_once 'includes/db.php';
include 'includes/header.php';

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = trim($_POST['fullname'] ?? '');
    $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $pw2 = $_POST['password_confirm'] ?? '';

    if (!$fullname) $errors[] = "Full name is required.";
    if (!$email) $errors[] = "Valid email is required.";
    if (strlen($password) < 6) $errors[] = "Password must be at least 6 characters.";
    if ($password !== $pw2) $errors[] = "Passwords do not match.";

    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT id FROM customers WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $errors[] = "Email already registered.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO customers (fullname, email, password, phone) VALUES (?, ?, ?, ?)");
            $stmt->execute([$fullname, $email, $hash, $phone]);
            header('Location: login.php?registered=1');
            exit;
        }
    }
}
?>
<?php include 'includes/header.php'; ?>
<main class="container narrow">
  <h2>Register</h2>
  <?php if ($errors): ?>
    <div class="error"><?=implode('<br>', array_map('htmlspecialchars', $errors))?></div>
  <?php endif; ?>
  <form method="post" action="register.php">
    <label>Full name <input name="fullname" value="<?=htmlspecialchars($_POST['fullname'] ?? '')?>"></label>
    <label>Email <input name="email" value="<?=htmlspecialchars($_POST['email'] ?? '')?>"></label>
    <label>Phone <input name="phone" value="<?=htmlspecialchars($_POST['phone'] ?? '')?>"></label>
    <label>Password <input type="password" name="password"></label>
    <label>Confirm Password <input type="password" name="password_confirm"></label>
    <button type="submit">Register</button>
  </form>
</main>
<?php include 'includes/footer.php'; ?>
