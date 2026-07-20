<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';
include 'includes/header.php';

require_login();
$user = current_user();

// Simple booking page: handled below
$room_id = isset($_GET['room_id']) ? intval($_GET['room_id']) : null;
cottage_id = isset($_GET['cottage_id']) ? intval($_GET['cottage_id']) : null;

$errors = [];
$success = null;

// Fetch selected item price
$item = null;
if ($room_id) {
    $stmt = $pdo->prepare("SELECT * FROM rooms WHERE id = ?");
    $stmt->execute([$room_id]);
    $item = $stmt->fetch();
}
if ($cottage_id) {
    $stmt = $pdo->prepare("SELECT * FROM cottages WHERE id = ?");
    $stmt->execute([$cottage_id]);
    $item = $stmt->fetch();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $check_in = $_POST['check_in'] ?? '';
    $check_out = $_POST['check_out'] ?? '';
    $guests = max(1, intval($_POST['guests'] ?? 1));
    $selected_room = intval($_POST['room_id'] ?? 0) ?: null;
    $selected_cottage = intval($_POST['cottage_id'] ?? 0) ?: null;

    if (!$check_in || !$check_out) $errors[] = "Check-in and check-out dates are required.";
    if (strtotime($check_in) >= strtotime($check_out)) $errors[] = "Check-out must be after check-in.";

    // determine price
    if ($selected_room) {
        $stmt = $pdo->prepare("SELECT price FROM rooms WHERE id = ?");
        $stmt->execute([$selected_room]);
        $row = $stmt->fetch();
        $price = $row ? $row['price'] : 0;
    } elseif ($selected_cottage) {
        $stmt = $pdo->prepare("SELECT price FROM cottages WHERE id = ?");
        $stmt->execute([$selected_cottage]);
        $row = $stmt->fetch();
        $price = $row ? $row['price'] : 0;
    } else {
        $errors[] = "Please select a room or cottage.";
        $price = 0;
    }

    if (empty($errors)) {
        // Calculate nights
        $nights = (strtotime($check_out) - strtotime($check_in)) / 86400;
        if ($nights < 1) $nights = 1;
        $total = $price * $nights;

        // Simple availability check
        if ($selected_room) {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM reservations WHERE room_id = ? AND status IN ('pending','confirmed') AND NOT (check_out <= ? OR check_in >= ?)");
            $stmt->execute([$selected_room, $check_in, $check_out]);
            $conflicts = $stmt->fetchColumn();
            if ($conflicts > 0) $errors[] = "Selected room is not available for these dates.";
        } elseif ($selected_cottage) {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM reservations WHERE cottage_id = ? AND status IN ('pending','confirmed') AND NOT (check_out <= ? OR check_in >= ?)");
            $stmt->execute([$selected_cottage, $check_in, $check_out]);
            $conflicts = $stmt->fetchColumn();
            if ($conflicts > 0) $errors[] = "Selected cottage is not available for these dates.";
        }

        if (empty($errors)) {
            $stmt = $pdo->prepare("INSERT INTO reservations (customer_id, room_id, cottage_id, check_in, check_out, guests, total_price) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$user['id'], $selected_room, $selected_cottage, $check_in, $check_out, $guests, $total]);
            $reservation_id = $pdo->lastInsertId();
            $success = "Reservation created with ID #$reservation_id. Please upload payment proof on your profile.";
        }
    }
}
?>
<main class="container narrow">
  <h2>Book <?= $item ? htmlspecialchars($item['name']) : 'a room/cottage' ?></h2>
  <?php if ($errors): ?>
    <div class="error"><?=implode('<br>', array_map('htmlspecialchars', $errors))?></div>
  <?php endif; ?>
  <?php if ($success): ?>
    <div class="success"><?=htmlspecialchars($success)?></div>
  <?php endif; ?>
  <form method="post" action="booking.php">
    <input type="hidden" name="room_id" value="<?=htmlspecialchars($room_id)?>">
    <input type="hidden" name="cottage_id" value="<?=htmlspecialchars($cottage_id)?>">
    <label>Check-in <input type="date" name="check_in" value="<?=htmlspecialchars($_POST['check_in'] ?? '')?>"></label>
    <label>Check-out <input type="date" name="check_out" value="<?=htmlspecialchars($_POST['check_out'] ?? '')?>"></label>
    <label>Guests <input type="number" name="guests" min="1" value="<?=htmlspecialchars($_POST['guests'] ?? 1)?>>"></label>
    <button type="submit">Reserve</button>
  </form>
</main>
<?php include 'includes/footer.php'; ?>
