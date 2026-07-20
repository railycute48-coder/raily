<?php
require_once '../includes/db.php';
session_start();
if (empty($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}

// handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    if ($_POST['action'] === 'confirm') {
        $pdo->prepare("UPDATE reservations SET status = 'confirmed' WHERE id = ?")->execute([$id]);
    } elseif ($_POST['action'] === 'reject') {
        $pdo->prepare("UPDATE reservations SET status = 'rejected' WHERE id = ?")->execute([$id]);
    } elseif ($_POST['action'] === 'cancel') {
        $pdo->prepare("UPDATE reservations SET status = 'cancelled' WHERE id = ?")->execute([$id]);
    }
}

$stmt = $pdo->query("SELECT r.*, c.fullname FROM reservations r JOIN customers c ON r.customer_id = c.id ORDER BY r.created_at DESC");
$res = $stmt->fetchAll();
?>
<!doctype html>
<html><head><meta charset="utf-8"><title>Reservations</title><link rel="stylesheet" href="../css/style.css"></head><body>
<header class="admin-header"><a href="dashboard.php">Dashboard</a> | <a href="reservations.php">Reservations</a></header>
<main class="container">
  <h2>Reservations</h2>
  <table class="table">
    <thead><tr><th>ID</th><th>Customer</th><th>Unit</th><th>Dates</th><th>Total</th><th>Status</th><th>Payment Proof</th><th>Action</th></tr></thead>
    <tbody>
      <?php foreach ($res as $r): ?>
        <tr>
          <td><?=intval($r['id'])?></td>
          <td><?=htmlspecialchars($r['fullname'])?></td>
          <td>
            <?php if ($r['room_id']): $s = $pdo->prepare("SELECT name FROM rooms WHERE id=?"); $s->execute([$r['room_id']]); echo htmlspecialchars($s->fetchColumn()); ?>
            <?php elseif ($r['cottage_id']): $s = $pdo->prepare("SELECT name FROM cottages WHERE id=?"); $s->execute([$r['cottage_id']]); echo htmlspecialchars($s->fetchColumn()); endif; ?>
          </td>
          <td><?=htmlspecialchars($r['check_in'])?> to <?=htmlspecialchars($r['check_out'])?></td>
          <td>₱<?=number_format($r['total_price'],2)?></td>
          <td><?=htmlspecialchars($r['status'])?></td>
          <td>
            <?php if ($r['payment_proof']): ?>
              <a href="../<?=htmlspecialchars($r['payment_proof'])?>" target="_blank">View</a>
            <?php else: ?>
              -
            <?php endif; ?>
          </td>
          <td>
            <form method="post" style="display:inline">
              <input type="hidden" name="id" value="<?=intval($r['id'])?>">
              <button name="action" value="confirm">Confirm</button>
              <button name="action" value="reject">Reject</button>
              <button name="action" value="cancel">Cancel</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</main>
</body></html>
