<?php
require '../config.php';
require '../auth.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_SESSION['users_csrf']) || !hash_equals($_SESSION['users_csrf'], (string) ($_POST['csrf'] ?? ''))) {
        $_SESSION['flash_error'] = 'Please refresh the page and try again.';
        header('Location: users.php');
        exit;
    }
    $id = (int)($_POST['id'] ?? 0);
    $myId = (int)current_user_id();

    if ($id < 1 || $id === $myId) {
        $_SESSION['flash_error'] = 'You cannot change your own admin status.';
    } else {
        $stmt = $conn->prepare("UPDATE users SET is_admin = NOT is_admin WHERE id = ? AND email <> 'admin@example.com'");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        if ($stmt->affected_rows === 0) $_SESSION['flash_error'] = 'The System Administrator cannot be changed.';
        $stmt->close();
    }
}

header('Location: users.php');
exit;
