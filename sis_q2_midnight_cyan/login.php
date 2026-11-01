<?php
require_once 'functions.php';
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $userId = trim($_POST['user_id'] ?? '');
    $password = $_POST['password'] ?? '';
    $captcha = strtoupper(trim($_POST['captcha'] ?? ''));
    $expected = strtoupper($_SESSION['captcha'] ?? '');
    unset($_SESSION['captcha']);

    if ($expected === '' || !hash_equals($expected, $captcha)) {
        $errors[] = 'CAPTCHA is incorrect. Please try again.';
    } else {
        $stmt = $pdo->prepare('SELECT * FROM login WHERE User_ID = ?');
        $stmt->execute([$userId]);
        $account = $stmt->fetch();
        if (!$account || !hash_equals($account['Password_Hash'], hash('sha256', $account['Random_salt'] . $password))) {
            $errors[] = 'Invalid User ID or password.';
        } else {
            $otp = (string) random_int(100000, 999999);
            $_SESSION['pending_user_id'] = $account['User_ID'];
            $_SESSION['pending_type'] = $account['Stakeholder_Type'];
            $_SESSION['pending_username'] = $account['Username'];
            $_SESSION['otp_hash'] = hash('sha256', $otp);
            $_SESSION['otp_attempts'] = 0;
            $_SESSION['otp_expires'] = time() + 300;
            if (DEMO_MODE) $_SESSION['demo_otp'] = $otp;
            redirect('verify_otp.php');
        }
    }
}
?>
<!DOCTYPE html><html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Sign in | SIS</title><link rel="stylesheet" href="style.css">
</head><body>
<main class="auth-shell">
    <a class="back-link" href="index.php">← Home</a>
    <div class="brand-mark small">SIS<span>.</span></div>
    <div class="eyebrow">WELCOME BACK</div>
    <h1>Sign in to SIS</h1><p class="subtext">Enter your credentials to continue.</p>
    <?php if ($errors): ?><div class="alert error"><?php foreach ($errors as $error): ?><div><?= e($error) ?></div><?php endforeach; ?></div><?php endif; ?>
    <form method="post">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <label for="user_id">User ID</label>
        <input id="user_id" name="user_id" value="<?= e($_POST['user_id'] ?? '') ?>" placeholder="Student ID / Faculty ID" autocomplete="username" required>
        <label for="password">Password</label>
        <input type="password" id="password" name="password" placeholder="Enter your password" autocomplete="current-password" required>
        <label for="captcha">Security check</label>
        <div class="captcha-row">
            <img id="captchaImage" class="captcha-image" src="captcha.php" alt="CAPTCHA image">
            <button type="button" class="refresh-btn" id="refreshCaptcha" aria-label="Refresh CAPTCHA">↻</button>
        </div>
        <input id="captcha" name="captcha" placeholder="Type the characters shown above" autocomplete="off" required>
        <button type="submit" class="btn">Continue to verification <span>→</span></button>
    </form>
    <p class="bottom-note">New to the portal? <a href="signup.php">Create an account</a></p>
</main>
<script src="script.js"></script>
</body></html>
