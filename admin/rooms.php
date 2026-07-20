<?php
require_once '../includes/db.php';
session_start();
if (empty($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}

// Rooms CRUD: create, edit, delete
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['create_room'])) {
        $name = $_POST['name']; $desc = $_POST['description']; $price = $_POST['price']; $cap = intval($_POST['capacity']);
        $stmt = $pdo->prepare("INSERT INTO rooms (name, description, price, capacity) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $desc, $price, $cap]);
    } elseif (isset($_POST['delete']) && isset($_POST['id'])) {
        $id = intval($_POST['id']);
        $pdo->prepare("DELETE FROM rooms WHERE id = ?")->execute([$id]);
    }
}

$rooms = $pdo->query("SELECT * FROM rooms ORDER BY id DESC")->fetchAll();
?>
<!doctype html>
<html><head><meta charset="utf-8"><title>Manage Rooms</title><link rel="stylesheet" href="../css/style.css"></head><body>
<header class="admin-header"><a href="dashboard.php">Dashboard</a> | <a href="rooms.php">Rooms</a></header>
<main class="container">
  <h2>Rooms</h2>
  <form method="post" style="margin-bottom:16px">
    <label>Name <input name="name" required></label>
    <label>Description <input name="description"></label>
    <label>Price <input name="price" required></label>
    <label>Capacity <input name="capacity" type="number" value="2"></label>
    <button name="create_room" type="submit">Create Room</button>
  </form>
  <table class="table">
    <thead><tr><th>ID</th><th>Name</th><th>Price</th><th>Capacity</th><th>Action</th></tr></thead>
    <tbody>
      <?php foreach ($rooms as $r): ?>
        <tr>
          <td><?=intval($r['id'])?></td>
          <td><?=htmlspecialchars($r['name'])?></td>
          <td>₱<?=number_format($r['price'],2)?></td>
          <td><?=intval($r['capacity'])?></td>
          <td>
            <form method="post" style="display:inline">
              <input type="hidden" name="id" value="<?=intval($r['id'])?>">
              <button name="delete" value="1">Delete</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</main>
</body></html>
