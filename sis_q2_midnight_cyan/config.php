<?php
declare(strict_types=1);

const DB_HOST = '127.0.0.1';
const DB_PORT = '3307'; // Change to 3306 if your XAMPP MySQL uses 3306.
const DB_NAME = 'student_information_system';
const DB_USER = 'root';
const DB_PASS = '';

const DEMO_MODE = true; // true: show OTP on verification page; false: configure an OTP delivery service.

if (session_status() !== PHP_SESSION_ACTIVE) {
    ini_set('session.use_strict_mode', '1');
    session_set_cookie_params([
        'httponly' => true,
        'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
        'samesite' => 'Lax'
    ]);
    session_start();
}
