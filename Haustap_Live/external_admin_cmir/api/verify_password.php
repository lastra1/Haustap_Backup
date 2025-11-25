<?php
// Simple password verification endpoint for admin area
if (session_status() !== PHP_SESSION_ACTIVE) session_start();

header('Content-Type: application/json');

// Basic request logging for debugging - do not log raw passwords
$logFile = dirname(__DIR__, 3) . '/storage/logs/php-error.log';
@error_log("[verify_password] request method=" . ($_SERVER['REQUEST_METHOD'] ?? '') . " remote_addr=" . ($_SERVER['REMOTE_ADDR'] ?? 'unknown') . " has_session=" . (isset($_SESSION['admin_logged_in']) ? '1' : '0') . "\n", 3, $logFile);

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    http_response_code(401);
    echo json_encode(['ok' => false, 'error' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'Method not allowed']);
    exit;
}

$pw = isset($_POST['password']) ? $_POST['password'] : '';
@error_log("[verify_password] password_present=" . ($pw === '' ? '0' : '1') . " email_session=" . (isset($_SESSION['admin_email']) ? $_SESSION['admin_email'] : 'none') . "\n", 3, $logFile);
if ($pw === '') {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Password required']);
    exit;
}

$dataDir = dirname(__DIR__, 3) . '/storage/data';
$adminsFile = $dataDir . '/admins.json';
$ok = false;
$email = isset($_SESSION['admin_email']) ? $_SESSION['admin_email'] : 'admin@haustap.local';

if (is_file($adminsFile)) {
    $decoded = json_decode(@file_get_contents($adminsFile), true);
    if (is_array($decoded)) {
        foreach ($decoded as $a) {
            if (isset($a['email']) && $a['email'] === $email && isset($a['password'])) {
                if (password_verify($pw, $a['password'])) { $ok = true; break; }
            }
        }
    }
}

@error_log("[verify_password] lookup_result=" . ($ok ? 'match' : 'no-match') . "\n", 3, $logFile);

// fallback dev credential
if (!$ok && $pw === 'Admin123!') $ok = true;

echo json_encode(['ok' => $ok]);
exit;

?>
