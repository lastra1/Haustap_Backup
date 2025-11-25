<?php
// Default landing page: redirect to Admin Login
// If already logged in, login.php will forward you to dashboard.php
header('Location: login.php');
exit;

