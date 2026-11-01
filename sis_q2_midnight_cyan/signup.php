<?php
require_once 'functions.php';
$errors = [];
$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $type = $_POST['stakeholder_type'] ?? '';
    $userId = trim($_POST['user_id'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if (!in_array($type, ['Student', 'Faculty'], true)) $errors[] = 'Select Student or Faculty.';
    if ($username === '' || strlen($username) < 3) $errors[] = 'Enter a username with at least 3 characters.';
    $errors = array_merge($errors, user_id_errors($userId, $username, $password), password_errors($password));
    if ($password !== $confirm) $errors[] = 'Password and confirmation do not match.';

    if (!$errors) {
        $masterTable = $type === 'Student' ? 'student' : 'faculty';
        $idColumn = $type === 'Student' ? 'Student_ID' : 'Faculty_ID';
        $check = $pdo->prepare("SELECT `$idColumn` FROM `$masterTable` WHERE `$idColumn` = ?");
        $check->execute([$userId]);
        if (!$check->fetch()) {
            $errors[] = "$type ID was not found in the SIS master table. Ask the administrator to add your record first.";
        } else {
            $exists = $pdo->prepare('SELECT Login_ID FROM login WHERE User_ID = ?');
            $exists->execute([$userId]);
            if ($exists->fetch()) {
                $errors[] = 'An account already exists for this User ID.';
            } else {
                $salt = bin2hex(random_bytes(16));
                $digest = hash('sha256', $salt . $password);
                $insert = $pdo->prepare('INSERT INTO login (User_ID, Stakeholder_Type, Username, Password_Hash, Random_salt) VALUES (?, ?, ?, ?, ?)');
                $insert->execute([$userId, $type, $username, $digest, $salt]);
                $success = 'Registration successful. You can now sign in.';
            }
        }
    }
}
?>
<!DOCTYPE html><html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Create account | SIS</title><link rel="stylesheet" href="style.css">
</head><body>
<main class="auth-shell">
    <a class="back-link" href="index.php">← Home</a>
    <div class="brand-mark small">SIS<span>.</span></div>
    <div class="eyebrow">NEW ACCOUNT</div>
    <h1>Create your account</h1><p class="subtext">Register with your institute-issued ID.</p>
    <?php if ($success): ?><div class="alert success"><?= e($success) ?></div><?php endif; ?>
    <?php if ($errors): ?><div class="alert error"><strong>Please correct the following:</strong><ul><?php foreach ($errors as $error): ?><li><?= e($error) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
    <form method="post" id="signupForm" novalidate>
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <label for="stakeholder_type">Stakeholder type</label>
        <select id="stakeholder_type" name="stakeholder_type" required>
            <option value="">Select your role</option>
            <option value="Student" <?= (($_POST['stakeholder_type'] ?? '') === 'Student') ? 'selected' : '' ?>>Student</option>
            <option value="Faculty" <?= (($_POST['stakeholder_type'] ?? '') === 'Faculty') ? 'selected' : '' ?>>Faculty</option>
        </select>
        <label for="user_id">Student ID / Faculty ID</label>
        <input id="user_id" name="user_id" value="<?= e($_POST['user_id'] ?? '') ?>" placeholder="Enter institute ID" required>
        <label for="username">Username</label>
        <input id="username" name="username" value="<?= e($_POST['username'] ?? '') ?>" placeholder="Choose a username" required>
        <label for="password">Password</label>
        <input type="password" id="password" name="password" placeholder="Minimum 10 characters" required>
        <div class="hint">Use uppercase, lowercase, a number and a special character. No spaces.</div>
        <label for="confirm_password">Confirm password</label>
        <input type="password" id="confirm_password" name="confirm_password" placeholder="Re-enter password" required>
        <button type="submit" class="btn">Create account <span>→</span></button>
    </form>
    <p class="bottom-note">Already registered? <a href="login.php">Sign in</a></p>
</main>
<script src="script.js"></script>
</body></html>
