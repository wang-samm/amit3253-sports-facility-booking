<?php
session_start();

function current_user_id() {
    return $_SESSION['user_id'] ?? null;
}

function current_user_name() {
    return $_SESSION['user_name'] ?? null;
}

function current_user_is_admin() {
    return !empty($_SESSION['is_admin']);
}

function require_login() {
    if (!current_user_id()) {
        $prefix = str_contains($_SERVER['SCRIPT_NAME'] ?? '', '/admin/') ? '../' : '';
        header('Location: ' . $prefix . 'login.php');
        exit;
    }
}

function require_admin() {
    require_login();
    global $conn;
    $id = (int) current_user_id();
    $stmt = $conn->prepare('SELECT is_admin FROM users WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    $_SESSION['is_admin'] = (bool) ($user['is_admin'] ?? false);
    if (!$_SESSION['is_admin']) {
        http_response_code(403);
        die('Admins only.');
    }
}

// Look up the current photo, so a renewed photo appears without logging out.
function current_profile_photo($conn) {
    if (!current_user_id()) return null;
    $id = (int) current_user_id();
    $stmt = $conn->prepare('SELECT profile_image_url FROM users WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    if (empty($row['profile_image_url'])) return null;
    require_once __DIR__ . '/helpers.php';
    return entity_image_url(['image_url' => $row['profile_image_url']]);
}
