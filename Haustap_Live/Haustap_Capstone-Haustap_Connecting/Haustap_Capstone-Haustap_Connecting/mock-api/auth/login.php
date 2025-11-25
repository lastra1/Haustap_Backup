<?php
// Simple dev-only login stub to unblock UI while Laravel backend is unavailable.
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
$email = isset($data['email']) ? trim($data['email']) : '';
$password = isset($data['password']) ? $data['password'] : '';

// Very basic check: accept any non-empty credentials for dev
if ($email === '' || $password === '') {
  http_response_code(401);
  echo json_encode(['message' => 'Invalid credentials']);
  exit;
}

$displayName = null; // Avoid overriding local first/last name
$user = [
  'id' => 1,
  'name' => $displayName,
  'email' => $email,
];

echo json_encode([
  'success' => true,
  'token' => 'dev-token-123',
  'user' => $user,
]);
exit;