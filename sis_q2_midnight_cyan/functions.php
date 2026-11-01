<?php
require_once __DIR__ . '/db.php';

function e(?string $value): string {
    return htmlspecialchars($value ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf(): void {
    $token = $_POST['csrf_token'] ?? '';
    if (!is_string($token) || empty($_SESSION['csrf_token']) ||
        !hash_equals($_SESSION['csrf_token'], $token)) {
        http_response_code(419);
        exit('Invalid form token. Please go back and try again.');
    }
}

function redirect(string $page): never {
    header('Location: ' . $page);
    exit;
}

function password_errors(string $password): array {
    $errors = [];
    if (strlen($password) < 10) $errors[] = 'Password must contain at least 10 characters.';
    if (!preg_match('/[A-Z]/', $password)) $errors[] = 'Add at least one uppercase letter.';
    if (!preg_match('/[a-z]/', $password)) $errors[] = 'Add at least one lowercase letter.';
    if (!preg_match('/[0-9]/', $password)) $errors[] = 'Add at least one digit.';
    if (!preg_match('/[^A-Za-z0-9]/', $password)) $errors[] = 'Add at least one special character.';
    if (preg_match('/\s/', $password)) $errors[] = 'Spaces are not allowed in the password.';
    if (preg_match('/123|234|345|456|567|678|789|321|432|543|654|765|876|987|000|111|222|333|444|555|666|777|888|999/i', $password)) {
        $errors[] = 'Avoid increasing, decreasing or repeated number patterns.';
    }
    foreach (['password', 'iloveyou', 'welcome', 'admin', 'system', 'qwerty', 'india', 'root'] as $word) {
        if (stripos($password, $word) !== false) {
            $errors[] = 'Password contains a common or disallowed word.';
            break;
        }
    }
    return $errors;
}

function user_id_errors(string $userId, string $username, string $password): array {
    $errors = [];
    if (strlen($userId) < 10) $errors[] = 'User ID must contain at least 10 characters.';
    if (!preg_match('/[A-Z]/', $userId)) $errors[] = 'User ID needs an uppercase letter.';
    if (!preg_match('/[a-z]/', $userId)) $errors[] = 'User ID needs a lowercase letter.';
    if (!preg_match('/[0-9]/', $userId)) $errors[] = 'User ID needs a digit.';
    if (preg_match('/123|234|345|456|567|678|789|321|432|543|654|765|876|987|000|111|222|333|444|555|666|777|888|999/i', $userId)) {
        $errors[] = 'Avoid increasing, decreasing or repeated number patterns in User ID.';
    }
    foreach (['abc', 'xyz', 'system', 'admin', 'root', 'india', 'qwerty', 'password', 'welcome'] as $word) {
        if (stripos($userId, $word) !== false) {
            $errors[] = 'User ID contains a common or disallowed substring.';
            break;
        }
    }
    if ($username !== '' && (stripos($username, $userId) !== false || stripos($userId, $username) !== false)) {
        $errors[] = 'User ID and username must not contain one another.';
    }
    if ($password !== '' && (stripos($password, $userId) !== false || stripos($userId, $password) !== false)) {
        $errors[] = 'User ID and password must not contain one another.';
    }
    return $errors;
}

function require_login(string $type): void {
    if (empty($_SESSION['authenticated']) || ($_SESSION['stakeholder_type'] ?? '') !== $type) {
        redirect('login.php');
    }
}
