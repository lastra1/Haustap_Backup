<?php
// Simple auth gate for admin pages
// Include this file early in pages to enforce login

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$loggedIn = isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;

if (!$loggedIn) {
    $loginPath = 'login.php';
    if (!headers_sent()) {
        header('Location: ' . $loginPath);
        exit;
    } else {
        echo '<script>window.location.href = ' . json_encode($loginPath) . ';</script>';
        exit;
    }
}
?>
