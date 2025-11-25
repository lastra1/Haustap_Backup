<?php
// Destroy admin session and redirect to dashboard
session_start();

// Clear all session variables
$_SESSION = [];

// If sessions use cookies, invalidate the cookie
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
}

// Also clear any non-HttpOnly cookies commonly used on the frontend
setcookie('haustap_token', '', time() - 3600, '/');
setcookie('haustap_user', '', time() - 3600, '/');

// Finally destroy the session
session_destroy();

// Redirect to admin login page after logout
header('Location: login.php');
exit;
?>
