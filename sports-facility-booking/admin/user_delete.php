<?php
require '../config.php';
require '../auth.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_SESSION['users_csrf']) || !hash_equals($_SESSION['users_csrf'], (string) ($_POST['csrf'] ?? ''))) {
        $_SESSION['flash_error'] = 'Please refresh the page and try again.';
    } else {
        $id = (int) ($_POST['id'] ?? 0);
        if ($id < 1 || $id === (int) current_user_id()) {
            $_SESSION['flash_error'] = 'You cannot delete your own account.';
        } else {
            $stmt = $conn->prepare("DELETE FROM users WHERE id = ? AND email <> 'admin@example.com'");
            $stmt->bind_param('i', $id);
            $stmt->execute();
            if ($stmt->affected_rows === 0) $_SESSION['flash_error'] = 'The System Administrator cannot be deleted.';
            $stmt->close();
        }
    }
}

header('Location: users.php');
exit;
