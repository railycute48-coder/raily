<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';
include 'includes/header.php';

require_login();
$user = current_user();

// show past reservations
$stmt = $pdo->prepare("SELECT r.*, c.fullname FROM reservations r JOIN customers c ON r.customer_id = c.id WHERE r.customer_id = ? ORDER BY r.created_at DESC");
$stmt->execute([$user['id']]);
$reservations = $stmt->fetchAll();
?>
<main class="container">
  <h2>Booking History</h2>
  <table class="table">
    <thead><tr><th>ID</th><th>Unit</th><th>Check-in</th><th>Check-out</th><th>Total</th><th>Status</th></tr></thead>
    <tbody>
      <?php foreach ($reservations as $r): ?>
        <tr>
          <td><?=intval($r['id'])?></td>
          <td>
            <?php
              if ($r['room_id']) {
                $s = $pdo->prepare("SELECT name FROM rooms WHERE id = ?"); $s->execute([$r['room_id']]); echo htmlspecialchars($s->fetchColumn());
              } elseif ($r['cottage_id']) {
                $s = $pdo->prepare("SELECT name FROM cottages WHERE id = ?"); $s->execute([$r['cottage_id']]); echo htmlspecialchars($s->fetchColumn());
              }
            ?>
          </td>
          <td><?=htmlspecialchars($r['check_in'])?></td>
          <td><?=htmlspecialchars($r['check_out'])?></td>
          <td>₱<?=number_format($r['total_price'],2)?></td>
          <td><?=htmlspecialchars($r['status'])?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</main>
<?php include 'includes/footer.php'; ?>
