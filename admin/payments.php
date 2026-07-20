<?php
require_once '../includes/db.php';
session_start();
if (empty($_SESSION['admin'])) { header('Location: login.php'); exit; }

// Verify payments
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    if ($_POST['action'] === 'verify') {
        $pdo->prepare("UPDATE payments SET status = 'verified' WHERE id = ?")->execute([$id]);
    } elseif ($_POST['action'] === 'reject') {
        $pdo->prepare("UPDATE payments SET status = 'rejected' WHERE id = ?")->execute([$id]);
    }
}

$payments = $pdo->query("SELECT p.*, r.customer_id, c.fullname FROM payments p JOIN reservations r ON p.reservation_id = r.id JOIN customers c ON r.customer_id = c.id ORDER BY p.created_at DESC")->fetchAll();
?>
<!doctype html>
<html><head><meta charset="utf-8"><title>Payments</title><link rel="stylesheet" href="../css/style.css"></head><body>
<header class="admin-header"><a href="dashboard.php">Dashboard</a> | <a href="payments.php">Payments</a></header>
<main class="container">
  <h2>Payments</h2>
  <table class="table">
    <thead><tr><th>ID</th><th>Reservation</th><th>Customer</th><th>Amount</th><th>Status</th><th>Proof</th><th>Action</th></tr></thead>
    <tbody>
      <?php foreach ($payments as $p): ?>
        <tr>
          <td><?=intval($p['id'])?></td>
          <td><?=intval($p['reservation_id'])?></td>
          <td><?=htmlspecialchars($p['fullname'])?></td>
          <td>₱<?=number_format($p['amount'],2)?></td>
          <td><?=htmlspecialchars($p['status'])?></td>
          <td><?php if ($p['proof']): ?><a href="../<?=htmlspecialchars($p['proof'])?>" target="_blank">View</a><?php else: ?>-<?php endif; ?></td>
          <td>
            <form method="post" style="display:inline">
              <input type="hidden" name="id" value="<?=intval($p['id'])?>">
              <button name="action" value="verify">Verify</button>
              <button name="action" value="reject">Reject</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</main>
</body></html>
