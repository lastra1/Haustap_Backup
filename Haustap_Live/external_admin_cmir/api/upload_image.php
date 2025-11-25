<?php
// Simple image upload endpoint for admin_profile avatar
// Use output buffering and suppress display of PHP warnings so we always return valid JSON
ob_start();
ini_set('display_errors', '0');
error_reporting(E_ALL);

function respond($data, $code = 200) {
    if (!headers_sent()) http_response_code($code);
    // capture any buffered output (warnings/HTML) and log it for debugging, then clear buffer
    $buf = '';
    if (ob_get_length()) {
        $buf = ob_get_contents();
    }
    if ($buf !== null && trim($buf) !== '') {
        // attempt to write to storage/logs/upload_image.log
        $logDir = dirname(__DIR__) . '/../../storage/logs';
        if (!is_dir($logDir)) @mkdir($logDir, 0755, true);
        $logFile = $logDir . '/upload_image.log';
        @file_put_contents($logFile, date('[Y-m-d H:i:s] ') . $buf . PHP_EOL, FILE_APPEND);
    }
    if (ob_get_length()) ob_clean();
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

if (session_status() !== PHP_SESSION_ACTIVE) session_start();

// lightweight debug log for incoming uploads
$logDir = dirname(__DIR__) . '/../../storage/logs';
if (!is_dir($logDir)) @mkdir($logDir, 0755, true);
$debugFile = $logDir . '/upload_image_debug.log';
@file_put_contents($debugFile, date('[Y-m-d H:i:s]') . " ENTRY -- method=" . ($_SERVER['REQUEST_METHOD'] ?? '') . "\n", FILE_APPEND);

// Also write a small debug summary in storage/data so it's easier to view
$dataLogDir = dirname(__DIR__) . '/../../storage/data';
if (!is_dir($dataLogDir)) @mkdir($dataLogDir, 0755, true);
$summaryFile = $dataLogDir . '/upload_debug_summary.txt';
@file_put_contents($summaryFile, date('[Y-m-d H:i:s]') . " ENTRY\nREQUEST_METHOD=" . ($_SERVER['REQUEST_METHOD'] ?? '') . "\nFILES_COUNT=" . (isset($_FILES) ? count($_FILES) : 0) . "\n", FILE_APPEND);

// Basic auth guard: ensure admin logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    respond(['ok' => false, 'error' => 'Unauthorized'], 401);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respond(['ok' => false, 'error' => 'Method not allowed'], 405);
}

if (!isset($_FILES['avatar']) || $_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
    respond(['ok' => false, 'error' => 'No file uploaded or upload error'], 400);
}

$file = $_FILES['avatar'];
$maxBytes = 3 * 1024 * 1024; // 3MB
$allowed = ['image/jpeg' => '.jpg', 'image/png' => '.png'];

if ($file['size'] > $maxBytes) {
    respond(['ok' => false, 'error' => 'File too large (max 3 MB)'], 400);
}

$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime = finfo_file($finfo, $file['tmp_name']);
finfo_close($finfo);

if (!isset($allowed[$mime])) {
    respond(['ok' => false, 'error' => 'Invalid file type'], 400);
}

// build storage path: repo_root/storage/uploads/admins
$uploadDir = dirname(__DIR__) . '/../../storage/uploads/admins';
if (!is_dir($uploadDir)) {
    if (!@mkdir($uploadDir, 0755, true)) {
        respond(['ok' => false, 'error' => 'Failed to create upload directory'], 500);
    }
}

$ext = $allowed[$mime];
$filename = uniqid('admin_', true) . $ext;
$dest = $uploadDir . DIRECTORY_SEPARATOR . $filename;

if (!move_uploaded_file($file['tmp_name'], $dest)) {
    respond(['ok' => false, 'error' => 'Failed to move uploaded file'], 500);
}

// Return URL relative to web root. Assumes web server serves repo root so /storage/... is reachable.
$url = '/storage/uploads/admins/' . $filename;

// Optionally: update session or admin record to remember avatar path. For now, just return URL.
// Persist avatar path for this admin in storage/data/admins.json
$dataDir = dirname(__DIR__) . '/../../storage/data';
$adminsFile = $dataDir . '/admins.json';
if (!is_dir($dataDir)) {
    @mkdir($dataDir, 0755, true);
}

$admins = [];
if (is_file($adminsFile)) {
    $contents = @file_get_contents($adminsFile);
    $decoded = json_decode($contents, true);
    if (is_array($decoded)) $admins = $decoded;
}

$adminEmail = isset($_SESSION['admin_email']) ? $_SESSION['admin_email'] : null;
if ($adminEmail) {
    // find by email or create new
    $found = false;
    foreach ($admins as &$a) {
        if (isset($a['email']) && $a['email'] === $adminEmail) {
            $a['avatar'] = $url;
            $found = true;
            break;
        }
    }
    unset($a);
    if (!$found) {
        $admins[] = ['email' => $adminEmail, 'avatar' => $url];
    }
    @file_put_contents($adminsFile, json_encode($admins, JSON_PRETTY_PRINT));
    // also store in session for immediate use
    $_SESSION['admin_avatar'] = $url;
}
respond(['ok' => true, 'url' => $url], 200);
