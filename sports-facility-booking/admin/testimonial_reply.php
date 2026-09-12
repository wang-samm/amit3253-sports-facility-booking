<?php
require '../config.php';
require '../auth.php';
require_admin();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit; }
if (empty($_SESSION['reply_csrf']) || !hash_equals($_SESSION['reply_csrf'], (string) ($_POST['csrf'] ?? ''))) {
    $_SESSION['reply_error'] = 'Please refresh the page and try again.';
} else {
    $id = (int) ($_POST['id'] ?? 0);
    $reply = trim($_POST['reply'] ?? '');
    if ($id < 1 || $reply === '' || strlen($reply) > 1000) {
        $_SESSION['reply_error'] = 'Enter a reply of at most 1000 characters.';
    } else {
        $adminId = (int) current_user_id();
        $stmt = $conn->prepare('SELECT name FROM users WHERE id = ? AND is_admin = 1');
        $stmt->bind_param('i', $adminId);
        $stmt->execute();
        $admin = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        if (!$admin) { http_response_code(403); exit('Admins only.'); }
        $stmt = $conn->prepare('UPDATE testimonials SET admin_reply = ?, admin_reply_name = ?, admin_replied_at = CURRENT_TIMESTAMP WHERE id = ?');
        $stmt->bind_param('ssi', $reply, $admin['name'], $id);
        $stmt->execute();
        if ($stmt->affected_rows === 0) $_SESSION['reply_error'] = 'Testimonial not found.';
        $stmt->close();
    }
}
header('Location: testimonials.php');
exit;
