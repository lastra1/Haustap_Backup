<?php
// Simple router: /services/{slug} → services.php
// Keeps existing UI by reusing services.php without modification.
// Optionally pass the slug into $_GET for services.php to consume if needed.
$slug = '';
if (isset($_SERVER['REQUEST_URI'])) {
    $parts = explode('/', trim($_SERVER['REQUEST_URI'], '/'));
    // e.g., ['services','cleaning']
    if (count($parts) >= 2 && strtolower($parts[0]) === 'services') {
        $slug = $parts[1];
        $_GET['category'] = $slug;
    }
}
require_once __DIR__ . '/../services.php';

