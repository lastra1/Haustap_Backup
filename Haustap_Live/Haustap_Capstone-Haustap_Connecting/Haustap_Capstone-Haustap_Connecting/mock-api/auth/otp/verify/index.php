<?php
// Verify OTP against persisted mock store
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Methods: POST, OPTIONS');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
  http_response_code(204);
  exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  echo json_encode(['message' => 'Method Not Allowed']);
  exit;
}

$raw = file_get_contents('php://input');
$data = json_decode($raw, true) ?? [];
$otpId = trim($data['otpId'] ?? '');
$code  = trim($data['code'] ?? '');

if ($otpId === '' || $code === '') {
  http_response_code(422);
  echo json_encode(['message' => 'otpId and code required']);
  exit;
}

$storePath = __DIR__ . '/../../../_data/otps.json';
if (!file_exists($storePath)) {
  http_response_code(400);
  echo json_encode(['success' => false, 'message' => 'No OTP session found']);
  exit;
}

$storeRaw = file_get_contents($storePath);
$store = json_decode($storeRaw, true);
if (!is_array($store) || !isset($store[$otpId])) {
  http_response_code(400);
  echo json_encode(['success' => false, 'message' => 'Invalid or expired OTP']);
  exit;
}

$rec = $store[$otpId];
// Expire check
if (($rec['expires_at'] ?? 0) < time()) {
  unset($store[$otpId]);
  file_put_contents($storePath, json_encode($store, JSON_PRETTY_PRINT));
  http_response_code(400);
  echo json_encode(['success' => false, 'message' => 'OTP expired']);
  exit;
}

// Match code
if ($code !== ($rec['code'] ?? '')) {
  http_response_code(400);
  echo json_encode(['success' => false, 'message' => 'Invalid OTP']);
  exit;
}

// Success: clear it
unset($store[$otpId]);
file_put_contents($storePath, json_encode($store, JSON_PRETTY_PRINT));
echo json_encode(['success' => true]);
exit;