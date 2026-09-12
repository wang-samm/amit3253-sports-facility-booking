<?php
require '../config.php';
require '../auth.php';
require '../helpers.php';
require_admin();

$id = (int) ($_GET['id'] ?? 0);
if ($id < 1) { http_response_code(404); exit('User not found.'); }
$stmt = $conn->prepare('SELECT id, name, email, id_number, faculty, date_of_birth, profile_image_url, is_admin, created_at FROM users WHERE id = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();
if (!$user) { http_response_code(404); exit('User not found.'); }

$pageTitle = 'User Profile';
require 'partials/header.php';
?>
<div class="form-card" style="max-width:640px;">
<h1><?= htmlspecialchars($user['name']) ?></h1>
<?php if ($user['profile_image_url']): ?><p><img class="user-detail-photo" src="<?= htmlspecialchars(entity_image_url(['image_url' => $user['profile_image_url']])) ?>" alt="Profile photo"></p><?php endif; ?>
<dl class="user-details">
<dt>Email</dt><dd><?= htmlspecialchars($user['email']) ?></dd>
<dt>Role</dt><dd><?= $user['email'] === 'admin@example.com' ? 'System Admin' : ($user['is_admin'] ? 'Admin' : 'User') ?></dd>
<dt>Student / Staff ID</dt><dd><?= htmlspecialchars($user['id_number'] ?: 'Not provided') ?></dd>
<dt>Faculty / Centre</dt><dd><?= htmlspecialchars($user['faculty'] ?: 'Not provided') ?></dd>
<dt>Date of Birth</dt><dd><?= htmlspecialchars($user['date_of_birth'] ?: 'Not provided') ?></dd>
<dt>Registered</dt><dd><?= htmlspecialchars(date('d M Y, g:i A', strtotime($user['created_at']))) ?></dd>
</dl>
<p><a class="btn btn-secondary btn-small" href="users.php">Back to Users</a></p>
</div>
<?php require 'partials/footer.php'; ?>
