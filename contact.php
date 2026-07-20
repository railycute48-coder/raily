<?php
require_once 'includes/db.php';
include 'includes/header.php';

$sent = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');
    if ($name && $email && $message) {
        // In production: store in DB or send email. Here we'll save to a simple file.
        $entry = "[".date('Y-m-d H:i:s')."] $name <$email>\n$message\n----\n";
        file_put_contents('database/contacts.txt', $entry, FILE_APPEND | LOCK_EX);
        $sent = true;
    }
}
?>
<main class="container narrow">
  <h2>Contact Us</h2>
  <?php if ($sent): ?><div class="success">Message sent. We'll get back to you shortly.</div><?php endif; ?>
  <form method="post">
    <label>Name <input name="name" required></label>
    <label>Email <input name="email" type="email" required></label>
    <label>Message <textarea name="message" required></textarea></label>
    <button type="submit">Send</button>
  </form>
</main>
<?php include 'includes/footer.php'; ?>
