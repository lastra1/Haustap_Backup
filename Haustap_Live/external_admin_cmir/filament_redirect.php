<?php
require_once __DIR__ . '/includes/auth.php';
// Simple SSO bridge: redirects to Laravel backend to establish a session
// Adjust the session key below to match your actual admin login variable

$email = isset($_SESSION['admin_email']) ? $_SESSION['admin_email'] : 'admin@haustap.local';
$ts = time();
$secret = 'dev-sso-secret'; // keep in sync with backend ADMIN_SSO_SECRET
$sig = hash_hmac('sha256', $email . '|' . $ts, $secret);

$backend = 'http://localhost:8001';
$redirect = '/admin';

$url = $backend . '/sso/admin?email=' . urlencode($email)
    . '&ts=' . $ts
    . '&sig=' . $sig
    . '&redirect=' . urlencode($redirect);

header('Location: ' . $url);
exit;
