<?php
require 'config.php';
require 'auth.php';

$pageTitle = 'About';
require 'partials/header.php';
?>
<div class="page-header">
<h1>About This Platform</h1>
<p>A cloud-based way to reserve TAR UMT sports facilities.</p>
</div>

<section>
<h2>Our Mission</h2>
<p>The platform replaces manual forms and conflicting spreadsheets with live court and time-slot availability. Students can reserve facilities while administrators manage courts, closures and all bookings centrally.</p>
</section>

<section>
<h2>How It Works</h2>
<div class="card-grid">
<div class="card">
<div class="card-icon">&#128197;</div>
<h3>1. Browse Facilities</h3>
<p>Compare sports, locations, courts and hourly fees.</p>
</div>
<div class="card">
<div class="card-icon">&#127903;</div>
<h3>2. Select a Slot</h3>
<p>Use the live schedule to choose an available court and time.</p>
</div>
<div class="card">
<div class="card-icon">&#9989;</div>
<h3>3. Manage Bookings</h3>
<p>Review, update or cancel a future reservation from your homepage.</p>
</div>
</div>
</section>

<section>
<h2>Who Runs This</h2>
<p>This platform is a sample project built for the AMIT3253 Cloud Computing for Business
capstone assignment, demonstrating a scalable facility-booking system deployed on AWS.</p>
</section>
<?php require 'partials/footer.php'; ?>
