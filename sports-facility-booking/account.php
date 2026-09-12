<?php
require 'config.php';
require 'auth.php';
require 'helpers.php';
require_login();

$uid = current_user_id();
$profileError = '';
$profileSuccess = '';
$passwordError = '';
$passwordSuccess = '';
$photoError = '';
$photoSuccess = '';
$_SESSION['photo_csrf'] ??= bin2hex(random_bytes(32));

$stmt = $conn->prepare('SELECT name, email, id_number, faculty, date_of_birth, profile_image_url FROM users WHERE id = ?');
$stmt->bind_param('i', $uid);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();
if (!$user) {
    http_response_code(403);
    exit('Account not found.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['form'] ?? '') === 'photo') {
    if (!hash_equals($_SESSION['photo_csrf'], (string) ($_POST['csrf'] ?? ''))) {
        $photoError = 'Please refresh the page and try again.';
    } elseif (empty($_FILES['photo']) || $_FILES['photo']['error'] === UPLOAD_ERR_NO_FILE) {
        $photoError = 'Choose a photo to upload.';
    } else {
        [$newPhoto, $uploadError] = handle_image_upload($_FILES['photo'], __DIR__ . '/uploads', 'profile');
        if ($uploadError) {
            $photoError = $uploadError;
        } else {
            $stmt = $conn->prepare('UPDATE users SET profile_image_url = ? WHERE id = ?');
            $stmt->bind_param('si', $newPhoto, $uid);
            if ($stmt->execute()) {
                $previous = $user['profile_image_url'];
                $user['profile_image_url'] = $newPhoto;
                if ($previous) delete_image_file($previous, __DIR__ . '/uploads');
                $photoSuccess = 'Profile photo updated.';
            } else {
                delete_image_file($newPhoto, __DIR__ . '/uploads');
                $photoError = 'Could not save your profile photo.';
            }
            $stmt->close();
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['form'] ?? '') === 'profile') {
    $name          = trim($_POST['name'] ?? '');
    $id_number     = trim($_POST['id_number'] ?? '');
    $faculty       = trim($_POST['faculty'] ?? '');
    $date_of_birth = trim($_POST['date_of_birth'] ?? '');

    if ($name === '' || $date_of_birth === '' || $id_number === '' || $faculty === '') {
        $profileError = 'All fields are required.';
    } else {
        $stmt = $conn->prepare('UPDATE users SET name = ?, id_number = ?, faculty = ?, date_of_birth = ? WHERE id = ?');
        $stmt->bind_param('ssssi', $name, $id_number, $faculty, $date_of_birth, $uid);
        $stmt->execute();
        $stmt->close();
        $_SESSION['user_name'] = $name;
        $user['name'] = $name;
        $user['id_number'] = $id_number;
        $user['faculty'] = $faculty;
        $user['date_of_birth'] = $date_of_birth;
        $profileSuccess = 'Profile updated.';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['form'] ?? '') === 'password') {
    $current = $_POST['current_password'];
    $new = $_POST['new_password'];
    $confirm = $_POST['confirm_password'];

    $stmt = $conn->prepare('SELECT password_hash FROM users WHERE id = ?');
    $stmt->bind_param('i', $uid);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!password_verify($current, $row['password_hash'])) {
        $passwordError = 'Current password is incorrect.';
    } elseif (strlen($new) < 6) {
        $passwordError = 'New password must be at least 6 characters.';
    } elseif ($new !== $confirm) {
        $passwordError = 'New passwords do not match.';
    } else {
        $newHash = password_hash($new, PASSWORD_DEFAULT);
        $stmt = $conn->prepare('UPDATE users SET password_hash = ? WHERE id = ?');
        $stmt->bind_param('si', $newHash, $uid);
        $stmt->execute();
        $stmt->close();
        $passwordSuccess = 'Password changed.';
    }
}

$pageTitle = 'My Account';
require 'partials/header.php';
?>
<div class="page-header">
<h1>My Account</h1>
<p>Manage your profile and password.</p>
<?php if (current_user_is_admin()): ?><p><a href="admin/users.php">Back to Admin</a></p><?php endif; ?>
</div>

<div class="form-card" style="margin-bottom:24px;">
<h2>Profile Photo</h2>
<?php if ($user['profile_image_url']): ?><p><img class="profile-photo" src="<?= htmlspecialchars(entity_image_url(['image_url' => $user['profile_image_url']])) ?>" alt="Your current profile photo"></p><?php else: ?><p>No profile photo yet.</p><?php endif; ?>
<?php if ($photoError): ?><p class="alert alert-error"><?= htmlspecialchars($photoError) ?></p><?php endif; ?>
<?php if ($photoSuccess): ?><p class="alert alert-success"><?= htmlspecialchars($photoSuccess) ?></p><?php endif; ?>
<form method="post" enctype="multipart/form-data">
<input type="hidden" name="form" value="photo">
<input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['photo_csrf']) ?>">
<label>Upload or replace photo <input type="file" name="photo" accept="image/jpeg,image/png,image/gif,image/webp" required></label>
<p>JPG, PNG, GIF or WebP, up to 5 MB.</p>
<button type="submit">Save Photo</button>
</form>
</div>

<div class="form-card" style="margin-bottom:24px;">
<h2>Profile</h2>
<?php if ($profileError): ?><p class="alert alert-error"><?= htmlspecialchars($profileError) ?></p><?php endif; ?>
<?php if ($profileSuccess): ?><p class="alert alert-success"><?= htmlspecialchars($profileSuccess) ?></p><?php endif; ?>
<form method="post">
<input type="hidden" name="form" value="profile">
<label>Full Name <span class="required-mark">*</span> <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required></label>
<label>Email <input type="email" value="<?= htmlspecialchars($user['email']) ?>" disabled></label>
<label>Student ID / Staff ID <span class="required-mark">*</span> <input type="text" name="id_number" value="<?= htmlspecialchars($user['id_number'] ?? '') ?>" required></label>
<label>Faculty <span class="required-mark">*</span>
<select name="faculty" required>
<option value="">-- Select Faculty / Centre --</option>
<?php foreach (tarumt_faculties() as $f): ?>
<option value="<?= htmlspecialchars($f) ?>" <?= ($user['faculty'] ?? '') === $f ? 'selected' : '' ?>><?= htmlspecialchars($f) ?></option>
<?php endforeach; ?>
</select>
</label>
<label>Date of Birth <span class="required-mark">*</span> <input type="date" name="date_of_birth" value="<?= htmlspecialchars($user['date_of_birth'] ?? '') ?>" required></label>
<button type="submit">Save Changes</button>
</form>
</div>

<div class="form-card">
<h2>Change Password</h2>
<?php if ($passwordError): ?><p class="alert alert-error"><?= htmlspecialchars($passwordError) ?></p><?php endif; ?>
<?php if ($passwordSuccess): ?><p class="alert alert-success"><?= htmlspecialchars($passwordSuccess) ?></p><?php endif; ?>
<form method="post">
<input type="hidden" name="form" value="password">
<label>Current Password <span class="required-mark">*</span>
<div class="password-field">
<input type="password" name="current_password" required>
<button type="button" class="password-toggle" tabindex="-1" aria-label="Show password"></button>
</div>
</label>
<label>New Password <span class="required-mark">*</span>
<div class="password-field">
<input type="password" name="new_password" required>
<button type="button" class="password-toggle" tabindex="-1" aria-label="Show password"></button>
</div>
</label>
<label>Confirm New Password <span class="required-mark">*</span>
<div class="password-field">
<input type="password" name="confirm_password" required>
<button type="button" class="password-toggle" tabindex="-1" aria-label="Show password"></button>
</div>
</label>
<button type="submit">Change Password</button>
</form>
</div>
<?php require 'partials/footer.php'; ?>
