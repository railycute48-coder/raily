<?php
require_once '../includes/db.php';
session_start();
if (empty($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}
$admin = $_SESSION['admin'];

$total_res = $pdo->query("SELECT COUNT(*) FROM reservations")->fetchColumn();
$pending = $pdo->query("SELECT COUNT(*) FROM reservations WHERE status = 'pending'")->fetchColumn();
$confirmed = $pdo->query("SELECT COUNT(*) FROM reservations WHERE status = 'confirmed'")->fetchColumn();
?>
<!doctype html>
<html><head><meta charset="utf-8"><title>Admin Dashboard</title><link rel="stylesheet" href="../css/style.css"></head><body>
<header class="admin-header"><a href="dashboard.php">Dashboard</a> | <a href="rooms.php">Rooms</a> | <a href="reservations.php">Reservations</a> | <a href="customers.php">Customers</a> | <a href="payments.php">Payments</a> | <a href="reports.php">Reports</a> | <a href="settings.php">Settings</a> | <a href="logout.php">Logout</a></header>
<main class="container">
  <h2>Admin Dashboard</h2>
  <div class="cards">
    <div class="card">Total Reservations: <?=intval($total_res)?></div>
    <div class="card">Pending: <?=intval($pending)?></div>
    <div class="card">Confirmed: <?=intval($confirmed)?></div>
  </div>
</main>
</body></html>
