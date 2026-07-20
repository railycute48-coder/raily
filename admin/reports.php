<?php
require_once '../includes/db.php';
session_start();
if (empty($_SESSION['admin'])) { header('Location: login.php'); exit; }

// Simple reports: monthly income
$rows = $pdo->query("SELECT DATE_FORMAT(created_at, '%Y-%m') as ym, SUM(amount) as total FROM payments WHERE status = 'verified' GROUP BY ym ORDER BY ym DESC LIMIT 12")->fetchAll();
?>
<!doctype html>
<html><head><meta charset="utf-8"><title>Reports</title><link rel="stylesheet" href="../css/style.css"></head><body>
<header class="admin-header"><a href="dashboard.php">Dashboard</a> | <a href="reports.php">Reports</a></header>
<main class="container">
  <h2>Reports</h2>
  <h3>Monthly Verified Income</h3>
  <table class="table">
    <thead><tr><th>Month</th><th>Total</th></tr></thead>
    <tbody>
      <?php foreach ($rows as $r): ?>
        <tr><td><?=htmlspecialchars($r['ym'])?></td><td>₱<?=number_format($r['total'],2)?></td></tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</main>
</body></html>
