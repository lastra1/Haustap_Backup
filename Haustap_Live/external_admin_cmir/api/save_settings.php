<?php
// Persist system settings to storage/data/system-settings.json
// Requires an authenticated admin session
require_once __DIR__ . '/../includes/auth.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'Method not allowed']);
    exit;
}

$systemName = isset($_POST['system_name']) ? trim($_POST['system_name']) : '';
$contactEmail = isset($_POST['contact_email']) ? trim($_POST['contact_email']) : '';

if ($systemName === '') {
    http_response_code(422);
    echo json_encode(['ok' => false, 'error' => 'System name is required']);
    exit;
}
if ($contactEmail === '' || !filter_var($contactEmail, FILTER_VALIDATE_EMAIL)) {
    http_response_code(422);
    echo json_encode(['ok' => false, 'error' => 'Valid contact email is required']);
    exit;
}

$dir = realpath(__DIR__ . '/../../storage/data');
if ($dir === false) {
    $dir = __DIR__ . '/../../storage/data';
}
if (!is_dir($dir)) {
    @mkdir($dir, 0777, true);
}

$file = $dir . '/system-settings.json';
$data = [
    'system_name' => $systemName,
    'contact_email' => $contactEmail,
    'updated_at' => time(),
];

$tmp = $file . '.tmp';
$json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
if ($json === false) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'Failed to encode JSON']);
    exit;
}

if (file_put_contents($tmp, $json) === false) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'Failed to write settings']);
    exit;
}

// Atomic replace
if (!@rename($tmp, $file)) {
    @unlink($tmp);
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'Failed to update settings file']);
    exit;
}

echo json_encode(['ok' => true]);
exit;

