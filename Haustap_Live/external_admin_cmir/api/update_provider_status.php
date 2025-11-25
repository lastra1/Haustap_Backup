<?php
// Simple endpoint to update provider status or delete provider records stored in storage/data/providers.json
// Usage: POST id=<int>&action=<suspended|banned|delete>
header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];
if ($method !== 'POST') {
  http_response_code(405);
  echo json_encode(['success' => false, 'error' => 'Only POST allowed']);
  exit;
}

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$action = isset($_POST['action']) ? trim($_POST['action']) : '';
if (!$id || !$action) {
  http_response_code(400);
  echo json_encode(['success' => false, 'error' => 'Missing id or action']);
  exit;
}

$allowed = ['suspended','banned','delete'];
if (!in_array($action, $allowed, true)) {
  http_response_code(400);
  echo json_encode(['success' => false, 'error' => 'Invalid action']);
  exit;
}

// Resolve providers.json
$store = realpath(__DIR__ . '/../../../storage/data/providers.json');
// Try a few reasonable fallbacks in case this file is in a slightly different location
if (!$store || !is_file($store)) {
  $candidates = [
    __DIR__ . '/../../storage/data/providers.json',
    __DIR__ . '/../../../../storage/data/providers.json',
  ];
  foreach ($candidates as $cand) {
    if (is_file($cand)) { $store = $cand; break; }
  }
}

if (!$store || !is_file($store)) {
  http_response_code(500);
  echo json_encode(['success' => false, 'error' => 'Providers store not found']);
  exit;
}

$raw = @file_get_contents($store);
$items = json_decode($raw ?: '[]', true);
if (!is_array($items)) $items = [];

$found = false;
foreach ($items as $k => $it) {
  if (isset($it['id']) && (int)$it['id'] === $id) {
    $found = true;
    if ($action === 'delete') {
      array_splice($items, $k, 1);
    } else {
      // set status
      $items[$k]['status'] = $action;
      // also update modified timestamp
      $items[$k]['updated_at'] = date('c');
    }
    break;
  }
}

if (!$found) {
  http_response_code(404);
  echo json_encode(['success' => false, 'error' => 'Provider not found']);
  exit;
}

// save back
if (@file_put_contents($store, json_encode($items, JSON_PRETTY_PRINT)) === false) {
  http_response_code(500);
  echo json_encode(['success' => false, 'error' => 'Failed to save store']);
  exit;
}

// success
$res = ['success' => true];
if ($action !== 'delete') $res['status'] = $action;

echo json_encode($res);
exit;

?>
