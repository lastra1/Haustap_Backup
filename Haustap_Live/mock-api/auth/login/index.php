<?php
// Mock login endpoint to support local UI without the Laravel backend.
// Route: POST /mock-api/auth/login
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Methods: POST, OPTIONS');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
  http_response_code(204);
  exit;
}

if (strtoupper($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
  http_response_code(405);
  echo json_encode(['message' => 'Method Not Allowed']);
  exit;
}

// Read JSON body (fallback to form fields if needed)
$raw = file_get_contents('php://input');
$data = json_decode($raw, true);
if (!is_array($data)) { $data = []; }

$email = isset($data['email']) ? trim((string)$data['email']) : '';
$password = isset($data['password']) ? (string)$data['password'] : '';

// Fallback: if JSON parse failed or fields empty, try traditional form body
if ($email === '' || $password === '') {
  if (!empty($_POST)) {
    $email = $email !== '' ? $email : trim((string)($_POST['email'] ?? ''));
    $password = $password !== '' ? $password : (string)($_POST['password'] ?? '');
  } else {
    $urlData = [];
    parse_str($raw ?? '', $urlData);
    if (is_array($urlData)) {
      $email = $email !== '' ? $email : trim((string)($urlData['email'] ?? ''));
      $password = $password !== '' ? $password : (string)($urlData['password'] ?? '');
    }
  }
}

if ($email === '' || $password === '') {
  http_response_code(401);
  echo json_encode(['message' => 'Invalid credentials']);
  exit;
}

// Basic role inference for demo: emails containing 'admin' => admin; 'provider' => provider; otherwise client.
$roleName = 'client';
$lower = strtolower($email);
if (strpos($lower, 'admin') !== false) { $roleName = 'admin'; }
elseif (strpos($lower, 'provider') !== false) { $roleName = 'provider'; }

$user = [
  'id' => 1,
  'name' => null, // let UI prefer first+last name if already captured locally
  'email' => $email,
  'role' => [ 'name' => $roleName ],
];

echo json_encode([
  'success' => true,
  'token' => 'dev-token-' . substr(md5($email . '|' . microtime(true)), 0, 8),
  'user' => $user,
]);
exit;
