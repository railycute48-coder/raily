<?php
require_once 'includes/db.php';
include 'includes/header.php';

$stmt = $pdo->query("SELECT * FROM cottages ORDER BY id");
cottages = $stmt->fetchAll();
?>
<main class="container">
  <h2>Cottages</h2>
  <div class="grid">
    <?php foreach ($cottages as $c): ?>
      <article class="card">
        <h3><?=htmlspecialchars($c['name'])?></h3>
        <p><?=nl2br(htmlspecialchars($c['description']))?></p>
        <p>Capacity: <?=intval($c['capacity'])?> | Price: ₱<?=number_format($c['price'],2)?></p>
        <a class="button" href="booking.php?cottage_id=<?=intval($c['id'])?>">Book this cottage</a>
      </article>
    <?php endforeach; ?>
  </div>
</main>
<?php include 'includes/footer.php'; ?>
