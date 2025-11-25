<?php
// Mock Bookings API for local preview and UI development
// Supports:
// - GET    /mock-api/bookings                      (list)
// - POST   /mock-api/bookings                      (create -> pending)
// - GET    /mock-api/bookings/{id}                 (show)
// - POST   /mock-api/bookings/{id}/status          (provider accept/complete)
// - POST   /mock-api/bookings/{id}/cancel          (client cancel if pending)
// - POST   /mock-api/bookings/{id}/rate            (client rate if completed)
// - POST   /mock-api/bookings/{id}/return          (client request return if completed)

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
  echo json_encode(['success' => true]);
  exit;
}

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$storeDir = __DIR__ . '/../_data';
$storeFile = $storeDir . '/bookings.json';
// Separate store for return requests
$returnsStoreFile = $storeDir . '/returns.json';

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

function read_json() {
  $raw = file_get_contents('php://input');
  if ($raw === false) return null;
  $data = json_decode($raw, true);
  return is_array($data) ? $data : null;
}

function path_segments_after_base($basePrefix) {
  $uri = urldecode(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH));
  $rel = substr($uri, strlen($basePrefix));
  if ($rel === false) { $rel = ''; }
  $rel = trim($rel, '/');
  if ($rel === '') return [];
  return array_values(array_filter(explode('/', $rel), 'strlen'));
}

function find_booking_index(&$items, $id) {
  foreach ($items as $idx => $it) {
    if ((int)($it['id'] ?? 0) === (int)$id) return $idx;
  }
  return -1;
}

function normalize_status($s) {
  $s = strtolower((string)($s ?? ''));
  if ($s === 'canceled') return 'cancelled';
  if ($s === 'in_progress' || $s === 'inprogress' || $s === 'accepted' || $s === 'started') return 'ongoing';
  if ($s === 'done' || $s === 'finish' || $s === 'finished' || $s === 'complete') return 'completed';
  if (!$s) return 'pending';
  return $s;
}

function can_transition($current, $next) {
  $current = normalize_status($current);
  $next = normalize_status($next);
  // Allowed path: pending -> ongoing -> completed
  if ($current === 'pending' && $next === 'ongoing') return true;
  if ($current === 'ongoing' && $next === 'completed') return true;
  // Idempotent allow setting same status
  if ($current === $next) return true;
  return false;
}

$segments = path_segments_after_base('/mock-api/bookings');

// Routing
if ($method === 'GET' && count($segments) === 0) {
  $items = load_store($storeFile);
  echo json_encode(['success' => true, 'data' => $items]);
  exit;
}

// GET /bookings/returns — list all return requests, enriched with minimal booking info
if ($method === 'GET' && count($segments) === 1 && $segments[0] === 'returns') {
  // Load returns
  $returns = [];
  if (is_file($returnsStoreFile)) {
    $raw = file_get_contents($returnsStoreFile);
    $data = json_decode($raw, true);
    if (is_array($data)) { $returns = $data; }
  }
  // Enrich with booking summary
  $items = load_store($storeFile);
  $byId = [];
  foreach ($items as $it) { $byId[(int)($it['id'] ?? 0)] = $it; }
  $out = array_map(function($r) use ($byId) {
    $bid = (int)($r['booking_id'] ?? 0);
    $b = isset($byId[$bid]) ? $byId[$bid] : null;
    $summary = null;
    if ($b) {
      $summary = [
        'id' => (int)($b['id'] ?? $bid),
        'service_name' => (string)($b['service_name'] ?? ''),
        'scheduled_date' => $b['scheduled_date'] ?? null,
        'scheduled_time' => $b['scheduled_time'] ?? null,
        'address' => $b['address'] ?? null,
        // Use numeric price from booking to display TOTAL on Return tab
        'price' => isset($b['price']) ? (float)$b['price'] : null,
        'provider_id' => isset($b['provider_id']) ? (int)$b['provider_id'] : null,
        'status' => (string)($b['status'] ?? ''),
      ];
    }
    $r['booking'] = $summary;
    return $r;
  }, $returns);
  echo json_encode(['success' => true, 'data' => $out]);
  exit;
}

if ($method === 'GET' && count($segments) === 1 && is_numeric($segments[0])) {
  $items = load_store($storeFile);
  $idx = find_booking_index($items, (int)$segments[0]);
  if ($idx < 0) { http_response_code(404); echo json_encode(['success'=>false,'message'=>'Not Found']); exit; }
  echo json_encode(['success' => true, 'data' => $items[$idx]]);
  exit;
}

if ($method === 'POST' && count($segments) === 0) {
  $payload = read_json();
  if (!$payload) { http_response_code(400); echo json_encode(['success'=>false,'message'=>'Invalid JSON']); exit; }

  $items = load_store($storeFile);
  $nextId = 1;
  if ($items) {
    $ids = array_map(function($x){ return (int)($x['id'] ?? 0); }, $items);
    $nextId = max($ids) + 1;
  }

  $booking = [
    'id' => $nextId,
    'provider_id' => (int)($payload['provider_id'] ?? 0),
    'service_name' => (string)($payload['service_name'] ?? 'General Service'),
    'scheduled_date' => $payload['scheduled_date'] ?? null,
    'scheduled_time' => $payload['scheduled_time'] ?? null,
    'address' => $payload['address'] ?? null,
    'status' => 'pending',
    'notes' => (string)($payload['notes'] ?? ''),
    'price' => isset($payload['price']) ? (float)$payload['price'] : null,
    'rating' => null,
    'rated_at' => null,
    'completed_at' => null,
    'cancelled_at' => null,
  ];

  $items[] = $booking;
  save_store($storeFile, $items);

  http_response_code(201);
  echo json_encode(['success' => true, 'id' => $booking['id'], 'data' => $booking]);
  exit;
}

// POST /bookings/{id}/status
if ($method === 'POST' && count($segments) === 2 && is_numeric($segments[0]) && $segments[1] === 'status') {
  $payload = read_json();
  $status = normalize_status($payload['status'] ?? '');
  if (!$status || !in_array($status, ['pending','ongoing','completed','cancelled'], true)) {
    http_response_code(422);
    echo json_encode(['success'=>false,'message'=>'Invalid status']);
    exit;
  }

  $items = load_store($storeFile);
  $id = (int)$segments[0];
  $idx = find_booking_index($items, $id);
  if ($idx < 0) { http_response_code(404); echo json_encode(['success'=>false,'message'=>'Not Found']); exit; }

  $current = normalize_status($items[$idx]['status'] ?? 'pending');
  if (!can_transition($current, $status)) {
    http_response_code(422);
    echo json_encode(['success'=>false,'message'=>'Transition not allowed', 'current'=>$current, 'requested'=>$status]);
    exit;
  }

  $items[$idx]['status'] = $status;
  if ($status === 'completed') { $items[$idx]['completed_at'] = date('c'); }
  save_store($storeFile, $items);
  // Auto-create chat conversation when booking is accepted/ongoing
  if ($status === 'ongoing') {
    $chatStoreFile = __DIR__ . '/../_data/chat.json';
    // Lightweight chat store helpers
    $chatStore = [];
    if (is_file($chatStoreFile)) {
      $raw = file_get_contents($chatStoreFile);
      $data = json_decode($raw, true);
      if (is_array($data)) { $chatStore = $data; }
    }
    if (!isset($chatStore['conversations'])) $chatStore['conversations'] = [];
    // Ensure a conversation exists with key 'booking_<id>'
    $exists = false;
    foreach ($chatStore['conversations'] as $c) {
      if ((int)($c['booking_id'] ?? 0) === (int)$id) { $exists = true; break; }
    }
    if (!$exists) {
      $chatStore['conversations'][] = [
        'id' => 'booking_' . $id,
        'booking_id' => (int)$id,
        'client_id' => null,
        'provider_id' => (int)($items[$idx]['provider_id'] ?? 0),
        'opened_at' => round(microtime(true) * 1000)
      ];
      $dir = dirname($chatStoreFile);
      if (!is_dir($dir)) { @mkdir($dir, 0777, true); }
      file_put_contents($chatStoreFile, json_encode($chatStore, JSON_PRETTY_PRINT));
    }
  }
  echo json_encode(['success'=>true,'data'=>$items[$idx]]);
  exit;
}

// POST /bookings/{id}/cancel (only if current is pending)
if ($method === 'POST' && count($segments) === 2 && is_numeric($segments[0]) && $segments[1] === 'cancel') {
  $items = load_store($storeFile);
  $id = (int)$segments[0];
  $idx = find_booking_index($items, $id);
  if ($idx < 0) { http_response_code(404); echo json_encode(['success'=>false,'message'=>'Not Found']); exit; }

  $current = normalize_status($items[$idx]['status'] ?? 'pending');
  if (in_array($current, ['completed','cancelled'], true)) {
    http_response_code(422);
    echo json_encode(['success'=>false,'message'=>'Cannot cancel completed/cancelled booking']);
    exit;
  }
  if ($current !== 'pending') {
    http_response_code(422);
    echo json_encode(['success'=>false,'message'=>'Only pending bookings can be cancelled']);
    exit;
  }

  $items[$idx]['status'] = 'cancelled';
  $items[$idx]['cancelled_at'] = date('c');
  save_store($storeFile, $items);
  echo json_encode(['success'=>true,'data'=>$items[$idx]]);
  exit;
}

// POST /bookings/{id}/rate (only if completed)
if ($method === 'POST' && count($segments) === 2 && is_numeric($segments[0]) && $segments[1] === 'rate') {
  $payload = read_json();
  $rating = isset($payload['rating']) ? (int)$payload['rating'] : null;
  if (!$rating || $rating < 1 || $rating > 5) {
    http_response_code(422);
    echo json_encode(['success'=>false,'message'=>'Invalid rating']);
    exit;
  }

  $items = load_store($storeFile);
  $id = (int)$segments[0];
  $idx = find_booking_index($items, $id);
  if ($idx < 0) { http_response_code(404); echo json_encode(['success'=>false,'message'=>'Not Found']); exit; }

  $current = normalize_status($items[$idx]['status'] ?? 'pending');
  if ($current !== 'completed') {
    http_response_code(422);
    echo json_encode(['success'=>false,'message'=>'Only completed bookings can be rated']);
    exit;
  }

  $items[$idx]['rating'] = $rating;
  $items[$idx]['rated_at'] = date('c');
  save_store($storeFile, $items);
  echo json_encode(['success'=>true,'data'=>$items[$idx]]);
  exit;
}

// POST /bookings/{id}/return (only if completed)
if ($method === 'POST' && count($segments) === 2 && is_numeric($segments[0]) && $segments[1] === 'return') {
  $payload = read_json();
  $issues = isset($payload['issues']) && is_array($payload['issues']) ? array_values(array_filter($payload['issues'], 'strlen')) : [];
  $notes = isset($payload['notes']) ? trim((string)$payload['notes']) : '';
  if (count($issues) === 0) {
    http_response_code(422);
    echo json_encode(['success'=>false,'message'=>'At least one issue is required']);
    exit;
  }

  $items = load_store($storeFile);
  $id = (int)$segments[0];
  $idx = find_booking_index($items, $id);
  if ($idx < 0) { http_response_code(404); echo json_encode(['success'=>false,'message'=>'Not Found']); exit; }

  $current = normalize_status($items[$idx]['status'] ?? 'pending');
  if ($current !== 'completed') {
    http_response_code(422);
    echo json_encode(['success'=>false,'message'=>'Only completed bookings can request a return']);
    exit;
  }

  // Determine fee based on completion time (free <= 24h, else 300)
  $completedAt = isset($items[$idx]['completed_at']) ? strtotime($items[$idx]['completed_at']) : null;
  $now = time();
  $free = false;
  if ($completedAt) {
    $diff = $now - $completedAt;
    $free = ($diff <= 24 * 60 * 60);
  }
  $fee = $free ? 0 : 300;

  // Load existing return requests store
  $returns = [];
  if (is_file($returnsStoreFile)) {
    $raw = file_get_contents($returnsStoreFile);
    $data = json_decode($raw, true);
    if (is_array($data)) { $returns = $data; }
  }
  $nextReturnId = 1;
  if ($returns) {
    $ids = array_map(function($x){ return (int)($x['id'] ?? 0); }, $returns);
    $nextReturnId = max($ids) + 1;
  }

  $record = [
    'id' => $nextReturnId,
    'booking_id' => $id,
    'issues' => $issues,
    'notes' => $notes,
    'status' => 'pending',
    'created_at' => date('c'),
    'fee' => $fee,
    'approved_at' => null,
    'declined_at' => null,
  ];

  $returns[] = $record;
  $dir = dirname($returnsStoreFile);
  if (!is_dir($dir)) { @mkdir($dir, 0777, true); }
  file_put_contents($returnsStoreFile, json_encode($returns, JSON_PRETTY_PRINT));

  // Optional: mark booking with last_return_id for UI convenience
  $items[$idx]['last_return_id'] = $record['id'];
  save_store($storeFile, $items);

  echo json_encode(['success'=>true, 'data'=>$record]);
  exit;
}

http_response_code(405);
echo json_encode(['success' => false, 'message' => 'Method Not Allowed']);
?>
