<?php $currentPage = basename($_SERVER['PHP_SELF']); $navPhoto = current_profile_photo($conn); ?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<script>
(function () {
    var saved = localStorage.getItem('theme');
    if (saved === 'dark' || saved === 'light') {
        document.documentElement.setAttribute('data-theme', saved);
    }
})();
</script>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($pageTitle ?? 'Admin') ?></title>
<link rel="icon" type="image/png" href="../assets/favicon.png">
<link rel="stylesheet" href="../style.css?v=<?= @filemtime(__DIR__ . '/../../style.css') ?>">
</head>
<body>
<nav class="navbar admin-navbar">
<a class="brand" href="facilities.php"><img src="../assets/tarumt-logo.png" alt="TAR UMT" class="brand-logo">Admin &middot; Sports Booking</a>
<div class="nav-links">
<a href="facilities.php" class="<?= in_array($currentPage, ['facilities.php','facility_create.php','facility_edit.php']) ? 'active' : '' ?>">Facilities</a>
<a href="courts.php" class="<?= $currentPage === 'courts.php' ? 'active' : '' ?>">Courts</a>
<a href="bookings.php" class="<?= $currentPage === 'bookings.php' ? 'active' : '' ?>">Bookings</a>
<a href="closures.php" class="<?= $currentPage === 'closures.php' ? 'active' : '' ?>">Closures</a>
<a href="testimonials.php" class="<?= $currentPage === 'testimonials.php' ? 'active' : '' ?>">Testimonials</a>
<a href="messages.php" class="<?= $currentPage === 'messages.php' ? 'active' : '' ?>">Messages</a>
<a href="users.php" class="<?= $currentPage === 'users.php' ? 'active' : '' ?>">Users</a>
<a href="account.php" class="admin-account-link <?= $currentPage === 'account.php' ? 'active' : '' ?>"><?php if ($navPhoto): ?><img class="user-avatar" src="<?= htmlspecialchars($navPhoto) ?>" alt="My profile photo"><?php else: ?><span class="user-avatar" aria-hidden="true">&#128100;</span><?php endif; ?> My Profile</a>
<a href="../logout.php">Logout</a>
<button id="theme-toggle" class="theme-toggle" type="button" aria-label="Toggle dark mode">&#9728;</button>
</div>
</nav>
<main class="container">
