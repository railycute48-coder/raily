<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';
include 'includes/header.php';

require_login();
$user = current_user();

// handle upload of payment proof for a reservation
$messages = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['payment_proof']) && isset($_POST['reservation_id'])) {
    $res_id = intval($_POST['reservation_id']);
    if ($_FILES['payment_proof']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['payment_proof']['name'], PATHINFO_EXTENSION);
        $filename = 'uploads/payment_' . time() . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
        if (!is_dir('uploads')) mkdir('uploads', 0755, true);
        if (!move_uploaded_file($_FILES['payment_proof']['tmp_name'], $filename)) {
            $messages[] = "Failed to save uploaded file.";
        } else {
            $stmt = $pdo->prepare("UPDATE reservations SET payment_proof = ? WHERE id = ? AND customer_id = ?");
            $stmt->execute([$filename, $res_id, $user['id']]);
            $stmt2 = $pdo->prepare("SELECT total_price FROM reservations WHERE id = ?");
            $stmt2->execute([$res_id]);
            $total = $stmt2->fetchColumn() ?: 0;
            $stmt = $pdo->prepare("INSERT INTO payments (reservation_id, amount, method, proof) VALUES (?, ?, ?, ?)");
            $stmt->execute([$res_id, $total, 'bank_transfer', $filename]);
            $messages[] = "Payment proof uploaded, pending verification by admin.";
        }
    } else {
        $messages[] = "No file uploaded or upload error.";
    }
}

// fetch reservations
$stmt = $pdo->prepare("SELECT r.*, c.fullname FROM reservations r JOIN customers c ON r.customer_id = c.id WHERE r.customer_id = ? ORDER BY r.created_at DESC");
$stmt->execute([$user['id']]);
$reservations = $stmt->fetchAll();
?>
<?php include 'includes/header.php'; ?>
<main class="container">
  <h2>Profile: <?=htmlspecialchars($user['fullname'])?></h2>
  <?php foreach ($messages as $m): ?>
    <div class="success"><?=htmlspecialchars($m)?></div>
  <?php endforeach; ?>

  <h3>Your Reservations</h3>
  <table class="table">
    <thead><tr><th>ID</th><th>Unit</th><th>Check-in</th><th>Check-out</th><th>Total</th><th>Status</th><th>Action</th></tr></thead>
    <tbody>
      <?php foreach ($reservations as $r): ?>
        <tr>
          <td><?=intval($r['id'])?></td>
          <td>
            <?php
              if ($r['room_id']) {
                $s = $pdo->prepare("SELECT name FROM rooms WHERE id = ?");
                $s->execute([$r['room_id']]);
                echo htmlspecialchars($s->fetchColumn());
              } elseif ($r['cottage_id']) {
                $s = $pdo->prepare("SELECT name FROM cottages WHERE id = ?");
                $s->execute([$r['cottage_id']]);
                echo htmlspecialchars($s->fetchColumn());
              }
            ?>
          </td>
          <td><?=htmlspecialchars($r['check_in'])?></td>
          <td><?=htmlspecialchars($r['check_out'])?></td>
          <td>₱<?=number_format($r['total_price'],2)?></td>
          <td><?=htmlspecialchars($r['status'])?></td>
          <td>
            <?php if (!$r['payment_proof']): ?>
            <form method="post" enctype="multipart/form-data" style="display:inline">
              <input type="hidden" name="reservation_id" value="<?=intval($r['id'])?>">
              <input type="file" name="payment_proof" required>
              <button type="submit">Upload Proof</button>
            </form>
            <?php else: ?>
              <span>Proof uploaded</span>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</main>
<?php include 'includes/footer.php'; ?>
