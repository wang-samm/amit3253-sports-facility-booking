<?php
require '../config.php';
require '../auth.php';
require_admin();
$_SESSION['users_csrf'] ??= bin2hex(random_bytes(32));

$flashError = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_error']);

$myId = current_user_id();
$roleOrder = ($_GET['role_order'] ?? 'desc') === 'asc' ? 'asc' : 'desc';
$orderSql = $roleOrder === 'asc' ? 'ASC' : 'DESC';
$users = $conn->query("SELECT id, name, email, is_admin, created_at FROM users ORDER BY is_admin $orderSql, name ASC")->fetch_all(MYSQLI_ASSOC);

$pageTitle = 'Manage Users';
require 'partials/header.php';
?>
<h1>Users</h1>
<p>Grant or revoke admin access, or remove an account.</p>
<p><a class="btn btn-small" href="user_create.php">+ Add Admin</a></p>
<?php if ($flashError): ?><p class="alert alert-error"><?= htmlspecialchars($flashError) ?></p><?php endif; ?>
<table>
<tr><th>Name</th><th>Email</th><th><a class="role-sort" href="users.php?role_order=<?= $roleOrder === 'asc' ? 'desc' : 'asc' ?>" title="Toggle user/admin order" aria-label="Sort roles <?= $roleOrder === 'asc' ? 'admins first' : 'users first' ?>">Role <?= $roleOrder === 'asc' ? '&#9650;' : '&#9660;' ?></a></th><th>Joined</th><th>Actions</th></tr>
<?php foreach ($users as $u): ?>
<tr>
<td><a href="user_view.php?id=<?= (int)$u['id'] ?>"><?= htmlspecialchars($u['name']) ?></a></td>
<td><?= htmlspecialchars($u['email']) ?></td>
<td><?= $u['email'] === 'admin@example.com' ? '<span class="badge badge-accent">System Admin</span>' : ($u['is_admin'] ? '<span class="badge badge-neutral">Admin</span>' : 'User') ?></td>
<td><?= htmlspecialchars(date('d M Y', strtotime($u['created_at']))) ?></td>
<td>
<?php if ($u['email'] === 'admin@example.com'): ?>
<span class="stat-label">Protected system account</span>
<?php elseif ((int)$u['id'] === (int)$myId): ?>
<span class="stat-label">(you)</span>
<?php else: ?>
<form action="user_toggle_admin.php" method="post" style="display:inline" onsubmit="return confirm('<?= $u['is_admin'] ? 'Remove admin access from ' : 'Grant admin access to ' ?><?= htmlspecialchars($u['name']) ?>?');">
<input type="hidden" name="id" value="<?= (int)$u['id'] ?>">
<input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['users_csrf']) ?>">
<button type="submit" class="btn btn-secondary btn-small"><?= $u['is_admin'] ? 'Revoke Admin' : 'Make Admin' ?></button>
</form>
<form action="user_delete.php" method="post" style="display:inline" onsubmit="return confirm('Delete this user and all of their bookings and testimonials? This cannot be undone.');">
<input type="hidden" name="id" value="<?= (int)$u['id'] ?>">
<input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['users_csrf']) ?>">
<button type="submit" class="btn-small btn-danger">Delete</button>
</form>
<?php endif; ?>
</td>
</tr>
<?php endforeach; ?>
</table>
<?php require 'partials/footer.php'; ?>
