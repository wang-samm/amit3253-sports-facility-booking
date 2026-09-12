<?php
require '../config.php';
require '../auth.php';
require_admin();
$_SESSION['reply_csrf'] ??= bin2hex(random_bytes(32));
$error = $_SESSION['reply_error'] ?? '';
unset($_SESSION['reply_error']);
$rows = $conn->query('SELECT t.*, u.name AS user_name, f.facility_name FROM testimonials t JOIN users u ON u.id = t.user_id JOIN facilities f ON f.id = t.facility_id ORDER BY t.created_at DESC')->fetch_all(MYSQLI_ASSOC);
$pageTitle = 'Testimonials';
require 'partials/header.php';
?>
<h1>Testimonials</h1>
<?php if ($error): ?><p class="alert alert-error"><?= htmlspecialchars($error) ?></p><?php endif; ?>
<?php if (!$rows): ?><div class="empty-state"><p>No comments yet.</p></div><?php else: ?>
<div class="testimonial-list">
<?php foreach ($rows as $t): ?>
<div class="testimonial-card">
<span class="badge badge-accent"><?= htmlspecialchars($t['facility_name']) ?></span>
<div class="testimonial-stars"><?= str_repeat('&#9733;', (int)$t['rating']) ?></div>
<p><?= nl2br(htmlspecialchars($t['comment'])) ?></p>
<div class="testimonial-meta"><span><?= htmlspecialchars($t['user_name']) ?> &middot; <?= htmlspecialchars(date('d M Y', strtotime($t['created_at']))) ?></span>
<form method="post" action="testimonial_delete.php"><input type="hidden" name="id" value="<?= (int)$t['id'] ?>"><button class="btn-small btn-danger">Delete</button></form></div>
<?php if ($t['admin_reply'] !== null): ?><div class="admin-reply"><strong>Reply from <?= htmlspecialchars($t['admin_reply_name'] ?? 'Admin') ?></strong><p><?= nl2br(htmlspecialchars($t['admin_reply'])) ?></p><small><?= htmlspecialchars(date('d M Y', strtotime($t['admin_replied_at']))) ?></small></div><?php endif; ?>
<form method="post" action="testimonial_reply.php" class="testimonial-reply-form">
<input type="hidden" name="id" value="<?= (int)$t['id'] ?>">
<input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['reply_csrf']) ?>">
<label><?= $t['admin_reply'] === null ? 'Reply to testimonial' : 'Edit your admin reply' ?>
<textarea name="reply" rows="3" maxlength="1000" required><?= htmlspecialchars($t['admin_reply'] ?? '') ?></textarea></label>
<button class="btn btn-small" type="submit"><?= $t['admin_reply'] === null ? 'Post Reply' : 'Update Reply' ?></button>
</form>
</div>
<?php endforeach; ?>
</div>
<?php endif; ?>
<?php require 'partials/footer.php'; ?>
