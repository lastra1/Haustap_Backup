<?php
// Redirect only the root path to the public homepage.
// If your web server rewrites unknown routes to index.php,
// delegate handling to the project router instead of forcing a homepage redirect.

$uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';

// Root path: send users to the guest homepage
if ($uri === '/' || $uri === '/index.php') {
  header('Location: /guest/homepage.php', true, 302);
  exit;
}

// For non-root requests (e.g., /services/outdoor), use router to resolve aliases or files
require __DIR__ . '/router.php';
