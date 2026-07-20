<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function require_login() {
    if (empty($_SESSION['customer'])) {
        header('Location: login.php');
        exit;
    }
}

function current_user() {
    return $_SESSION['customer'] ?? null;
}
