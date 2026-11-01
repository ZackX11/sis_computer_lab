<?php
require_once 'functions.php';
require_login('Faculty');
$stmt = $pdo->prepare('SELECT First_Name, Last_Name, Email, Designation FROM faculty WHERE Faculty_ID = ?');
$stmt->execute([$_SESSION['user_id']]);
$person = $stmt->fetch() ?: [];
$name = trim(($person['First_Name'] ?? '') . ' ' . ($person['Last_Name'] ?? ''));
if ($name === '') $name = $_SESSION['username'];
?>
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Faculty dashboard | SIS</title><link rel="stylesheet" href="style.css"></head>
<body><main class="dashboard">
<header class="dashboard-header"><div><div class="eyebrow">FACULTY WORKSPACE</div><h1>Welcome, <?= e($name) ?></h1><p class="subtext">Your faculty workspace is ready.</p></div><a class="btn logout-btn" href="logout.php">Logout ↗</a></header>
<section class="profile-strip"><div class="avatar"><?= e(strtoupper(substr($name, 0, 1))) ?></div><div><strong><?= e($name) ?></strong><span><?= e($_SESSION['user_id']) ?> · Faculty<?= !empty($person['Designation']) ? ' · ' . e($person['Designation']) : '' ?></span></div><span class="status-pill">Authenticated</span></section>
<section class="dashboard-content">
<div class="dashboard-card"><div class="card-icon">▤</div><h3>Faculty Profile</h3><p>Review your faculty identity and registered details.</p><span class="card-tag">PROFILE</span></div>
<div class="dashboard-card"><div class="card-icon">✉</div><h3>Contact Information</h3><p><?= e($person['Email'] ?? 'Email not available in master record') ?></p><span class="card-tag">CONTACT</span></div>
<div class="dashboard-card"><div class="card-icon">⌁</div><h3>Account Security</h3><p>Your account is protected by password, CAPTCHA and OTP verification.</p><span class="card-tag">SECURITY</span></div>
</section><div class="footer">Student Information System · NIT Silchar</div></main></body></html>
