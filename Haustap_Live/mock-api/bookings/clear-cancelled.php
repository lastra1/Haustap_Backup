<?php
// Bulk clear cancelled bookings for mock API
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Methods: POST, OPTIONS');

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'OPTIONS') {
  echo json_encode(['success' => true]);
  exit;
}

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
  http_response_code(405);
  echo json_encode(['success' => false, 'message' => 'Method Not Allowed']);
  exit;
}

$storeDir = __DIR__ . '/../_data';
$storeFile = $storeDir . '/bookings.json';

function load_store($file) {
  if (is_file($file)) {
    $raw = file_get_contents($file);
    $data = json_decode($raw, true);
    if (is_array($data)) return $data;
  }
  return [];
}

function save_store($file, $data) {
  $dir = dirname($file);
  if (!is_dir($dir)) { @mkdir($dir, 0777, true); }
  file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT));
}

function normalize_status($s) {
  $s = strtolower((string)($s ?? ''));
  if ($s === 'canceled') return 'cancelled';
  if ($s === 'in_progress' || $s === 'inprogress' || $s === 'accepted' || $s === 'started') return 'ongoing';
  if ($s === 'done' || $s === 'finish' || $s === 'finished' || $s === 'complete') return 'completed';
  if (!$s) return 'pending';
  return $s;
}

$items = load_store($storeFile);
$before = count($items);
$remaining = array_values(array_filter($items, function($it) {
  $st = normalize_status($it['status'] ?? 'pending');
  return $st !== 'cancelled';
}));
save_store($storeFile, $remaining);
$cleared = $before - count($remaining);
echo json_encode(['success' => true, 'cleared' => $cleared, 'remaining' => count($remaining)]);
?>
