<?php
require_once '../includes/db.php';
session_start();
if (empty($_SESSION['admin'])) { header('Location: login.php'); exit; }

$customers = $pdo->query("SELECT * FROM customers ORDER BY id DESC")->fetchAll();
?>
<!doctype html>
<html><head><meta charset="utf-8"><title>Customers</title><link rel="stylesheet" href="../css/style.css"></head><body>
<header class="admin-header"><a href="dashboard.php">Dashboard</a> | <a href="customers.php">Customers</a></header>
<main class="container">
  <h2>Customers</h2>
  <table class="table">
    <thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Phone</th><th>Joined</th></tr></thead>
    <tbody>
      <?php foreach ($customers as $c): ?>
        <tr>
          <td><?=intval($c['id'])?></td>
          <td><?=htmlspecialchars($c['fullname'])?></td>
          <td><?=htmlspecialchars($c['email'])?></td>
          <td><?=htmlspecialchars($c['phone'])?></td>
          <td><?=htmlspecialchars($c['created_at'])?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</main>
</body></html>
