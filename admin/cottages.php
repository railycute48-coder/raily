<?php
require_once '../includes/db.php';
session_start();
if (empty($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}

// Cottages CRUD
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['create_cottage'])) {
        $name = $_POST['name']; $desc = $_POST['description']; $price = $_POST['price']; $cap = intval($_POST['capacity']);
        $stmt = $pdo->prepare("INSERT INTO cottages (name, description, price, capacity) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $desc, $price, $cap]);
    } elseif (isset($_POST['delete']) && isset($_POST['id'])) {
        $id = intval($_POST['id']);
        $pdo->prepare("DELETE FROM cottages WHERE id = ?")->execute([$id]);
    }
}

$cottages = $pdo->query("SELECT * FROM cottages ORDER BY id DESC")->fetchAll();
?>
<!doctype html>
<html><head><meta charset="utf-8"><title>Manage Cottages</title><link rel="stylesheet" href="../css/style.css"></head><body>
<header class="admin-header"><a href="dashboard.php">Dashboard</a> | <a href="cottages.php">Cottages</a></header>
<main class="container">
  <h2>Cottages</h2>
  <form method="post" style="margin-bottom:16px">
    <label>Name <input name="name" required></label>
    <label>Description <input name="description"></label>
    <label>Price <input name="price" required></label>
    <label>Capacity <input name="capacity" type="number" value="4"></label>
    <button name="create_cottage" type="submit">Create Cottage</button>
  </form>
  <table class="table">
    <thead><tr><th>ID</th><th>Name</th><th>Price</th><th>Capacity</th><th>Action</th></tr></thead>
    <tbody>
      <?php foreach ($cottages as $c): ?>
        <tr>
          <td><?=intval($c['id'])?></td>
          <td><?=htmlspecialchars($c['name'])?></td>
          <td>₱<?=number_format($c['price'],2)?></td>
          <td><?=intval($c['capacity'])?></td>
          <td>
            <form method="post" style="display:inline">
              <input type="hidden" name="id" value="<?=intval($c['id'])?>">
              <button name="delete" value="1">Delete</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</main>
</body></html>
