<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Resort Booking System</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="stylesheet" href="css/style.css">
  <script defer src="js/script.js"></script>
</head>
<body>
<header class="site-header">
  <div class="container">
    <a class="logo" href="index.php">My Resort</a>
    <nav class="nav">
      <a href="index.php">Home</a>
      <a href="rooms.php">Rooms</a>
      <a href="cottages.php">Cottages</a>
      <a href="gallery.php">Gallery</a>
      <a href="booking.php">Book</a>
      <?php if (!empty($_SESSION['customer'])): ?>
        <a href="profile.php">Profile</a>
        <a href="logout.php">Logout</a>
      <?php else: ?>
        <a href="login.php">Login</a>
        <a href="register.php">Register</a>
      <?php endif; ?>
      <a href="admin/login.php">Admin</a>
    </nav>
  </div>
</header>
