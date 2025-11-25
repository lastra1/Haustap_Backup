<?php
// OTP verify endpoint: validates code against stored record and clears it
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
  echo json_encode(['message' => 'OTP ID and code required']);
  exit;
}

$storePath = __DIR__ . '/../../../_data/otps.json';
$storeRaw = is_file($storePath) ? file_get_contents($storePath) : '{}';
$store = json_decode($storeRaw, true);
if (!is_array($store)) { $store = []; }
$record = $store[$otpId] ?? null;

if (!$record) {
  http_response_code(404);
  echo json_encode(['message' => 'OTP not found']);
  exit;
}

$expiresAt = (int) ($record['expires_at'] ?? 0);
if ($expiresAt > 0 && $expiresAt < time()) {
  http_response_code(400);
  echo json_encode(['message' => 'OTP expired']);
  exit;
}

$expected = (string) ($record['code'] ?? '');
if (!hash_equals($expected, $code)) {
  http_response_code(400);
  echo json_encode(['message' => 'Invalid OTP']);
  exit;
}

// Success: clear OTP from store to prevent reuse
unset($store[$otpId]);
@file_put_contents($storePath, json_encode($store, JSON_PRETTY_PRINT));

echo json_encode(['success' => true, 'message' => 'OTP verified']);
exit;

