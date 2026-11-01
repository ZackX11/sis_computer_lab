<?php
require_once 'functions.php';
if (empty($_SESSION['pending_user_id']) || empty($_SESSION['otp_hash'])) redirect('login.php');
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $otp = trim($_POST['otp'] ?? '');
    if (time() > ($_SESSION['otp_expires'] ?? 0)) {
        $error = 'OTP expired. Please sign in again.';
        unset($_SESSION['pending_user_id'], $_SESSION['pending_type'], $_SESSION['pending_username'], $_SESSION['otp_hash'], $_SESSION['demo_otp']);
    } elseif (!preg_match('/^\d{6}$/', $otp) || !hash_equals($_SESSION['otp_hash'], hash('sha256', $otp))) {
        $_SESSION['otp_attempts'] = ($_SESSION['otp_attempts'] ?? 0) + 1;
        if ($_SESSION['otp_attempts'] >= 3) {
            unset($_SESSION['pending_user_id'], $_SESSION['pending_type'], $_SESSION['pending_username'], $_SESSION['otp_hash'], $_SESSION['demo_otp']);
            $error = 'Three incorrect attempts. You have been redirected to the home page.';
            $_SESSION['otp_locked_message'] = $error;
            redirect('index.php');
        }
        $remaining = 3 - $_SESSION['otp_attempts'];
        $error = "Incorrect OTP. $remaining attempt(s) remaining.";
    } else {
        $userId = $_SESSION['pending_user_id'];
        $type = $_SESSION['pending_type'];
        $pdo->prepare('UPDATE login SET Last_Login = NOW() WHERE User_ID = ?')->execute([$userId]);
        session_regenerate_id(true);
        $_SESSION['authenticated'] = true;
        $_SESSION['user_id'] = $userId;
        $_SESSION['stakeholder_type'] = $type;
        $_SESSION['username'] = $_SESSION['pending_username'];
        unset($_SESSION['pending_user_id'], $_SESSION['pending_type'], $_SESSION['pending_username'], $_SESSION['otp_hash'], $_SESSION['demo_otp'], $_SESSION['otp_attempts'], $_SESSION['otp_expires']);
        redirect($type === 'Student' ? 'student_dashboard.php' : 'faculty_dashboard.php');
    }
}
?>
<!DOCTYPE html><html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>OTP verification | SIS</title><link rel="stylesheet" href="style.css">
</head><body><main class="auth-shell compact">
    <a class="back-link" href="login.php">← Back to login</a>
    <div class="brand-mark small">SIS<span>.</span></div>
    <div class="eyebrow">TWO-STEP VERIFICATION</div>
    <h1>Verify it's you</h1>
    <p class="subtext">Enter the six-digit one-time password to finish signing in.</p>
    <?php if ($error): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?>
    <?php if (DEMO_MODE && isset($_SESSION['demo_otp'])): ?>
        <div class="demo-otp"><span>DEMO MODE · YOUR OTP</span><strong><?= e($_SESSION['demo_otp']) ?></strong></div>
    <?php endif; ?>
    <form method="post">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <label for="otp">6-digit OTP</label>
        <input class="otp-input" id="otp" name="otp" inputmode="numeric" maxlength="6" pattern="[0-9]{6}" placeholder="••••••" autocomplete="one-time-code" required autofocus>
        <p class="hint">OTP is valid for 5 minutes. Maximum 3 incorrect attempts.</p>
        <button type="submit" class="btn">Verify and sign in →</button>
    </form>
</main></body></html>
