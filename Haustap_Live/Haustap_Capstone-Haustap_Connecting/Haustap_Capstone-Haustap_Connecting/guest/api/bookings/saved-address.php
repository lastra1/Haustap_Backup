<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../_db.php';

$pdo = haustap_db_conn();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
  $userKey = isset($_GET['user_key']) ? trim($_GET['user_key']) : '';
  if ($userKey === '') { http_response_code(401); echo json_encode(['success' => false, 'message' => 'Authentication required']); exit; }
  if ($pdo && ensure_saved_addresses_table($pdo)) {
    try {
      $stmt = $pdo->prepare('SELECT * FROM saved_addresses WHERE user_key = ? ORDER BY created_at DESC LIMIT 1');
      $stmt->execute([$userKey]);
      $row = $stmt->fetch();
      echo json_encode(['success' => true, 'data' => $row ?: null, 'store' => 'mysql']);
      exit;
    } catch (Throwable $e) {}
  }
  echo json_encode(['success' => true, 'data' => null, 'store' => $pdo ? 'mysql' : 'file']);
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $raw = file_get_contents('php://input');
  $input = json_decode($raw, true);
  if (!$input) $input = $_POST;
  $sid = isset($input['sid']) ? trim($input['sid']) : '';
  $userKey = isset($input['user_key']) ? trim($input['user_key']) : '';
  $address = isset($input['address']) ? trim($input['address']) : '';
  if ($userKey === '' || $address === '') {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'user_key and address required']);
    exit;
  }
  $id = uniqid('addr_', true);
  if ($pdo && ensure_saved_addresses_table($pdo)) {
    try {
      $stmt = $pdo->prepare('INSERT INTO saved_addresses (id, session_id, user_key, address, created_at) VALUES (?, ?, ?, ?, ?)');
      $stmt->execute([$id, null, $userKey, $address, date('c')]);
      echo json_encode(['success' => true, 'data' => ['id' => $id, 'session_id' => $sid, 'user_key' => $userKey, 'address' => $address], 'store' => 'mysql']);
      exit;
    } catch (Throwable $e) {}
  }
  $file = __DIR__ . '/../data/saved_addresses.json';
  if (!is_dir(dirname($file))) @mkdir(dirname($file), 0777, true);
  $list = [];
  if (file_exists($file)) { $list = json_decode(@file_get_contents($file), true) ?: []; }
  $list[] = ['id' => $id, 'session_id' => null, 'user_key' => $userKey, 'address' => $address, 'created_at' => date('c')];
  @file_put_contents($file, json_encode($list, JSON_PRETTY_PRINT));
  echo json_encode(['success' => true, 'data' => ['id' => $id, 'session_id' => $sid, 'address' => $address], 'store' => 'file']);
  exit;
}

http_response_code(405);
echo json_encode(['success' => false, 'message' => 'Method not allowed']);