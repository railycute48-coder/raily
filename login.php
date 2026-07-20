<?php
require_once 'includes/db.php';
include 'includes/header.php';

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM customers WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    if ($user && password_verify($password, $user['password'])) {
        session_start();
        unset($user['password']);
        $_SESSION['customer'] = $user;
        header('Location: index.php');
        exit;
    } else {
        $errors[] = "Invalid email or password.";
    }
}
?>
<?php include 'includes/header.php'; ?>
<main class="container narrow">
  <h2>Login</h2>
  <?php if (!empty($_GET['registered'])): ?>
    <div class="success">Registration successful. Please login.</div>
  <?php endif; ?>
  <?php if ($errors): ?>
    <div class="error"><?=htmlspecialchars($errors[0])?></div>
  <?php endif; ?>
  <form method="post" action="login.php">
    <label>Email <input name="email" value="<?=htmlspecialchars($_POST['email'] ?? '')?>"></label>
    <label>Password <input type="password" name="password"></label>
    <button type="submit">Login</button>
  </form>
</main>
<?php include 'includes/footer.php'; ?>
