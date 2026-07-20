<?php
require_once 'includes/db.php';
include 'includes/header.php';

$stmt = $pdo->query("SELECT * FROM rooms ORDER BY id");
$rooms = $stmt->fetchAll();
?>
<main class="container">
  <h2>Rooms</h2>
  <div class="grid">
    <?php foreach ($rooms as $r): ?>
      <article class="card">
        <h3><?=htmlspecialchars($r['name'])?></h3>
        <p><?=nl2br(htmlspecialchars($r['description']))?></p>
        <p>Capacity: <?=intval($r['capacity'])?> | Price: ₱<?=number_format($r['price'],2)?></p>
        <a class="button" href="booking.php?room_id=<?=intval($r['id'])?>">Book this room</a>
      </article>
    <?php endforeach; ?>
  </div>
</main>
<?php include 'includes/footer.php'; ?>
