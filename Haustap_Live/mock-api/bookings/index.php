<?php
// Mock Bookings API for local development
// Endpoints:
// - GET  /mock-api/bookings                     -> list bookings
// - POST /mock-api/bookings                     -> create booking
// - POST /mock-api/bookings/{id}/cancel         -> cancel booking
// - POST /mock-api/bookings/{id}/status         -> update status
// - POST /mock-api/bookings/{id}/rate           -> set rating
// - POST /mock-api/bookings/{id}/return         -> request a return
// - GET  /mock-api/bookings/returns             -> list return requests

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'OPTIONS') {
  echo json_encode(['success' => true]);
  exit;
}

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$storeDir = __DIR__ . '/../_data';
$bookingsFile = $storeDir . '/bookings.json';
$returnsFile  = $storeDir . '/returns.json';

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

function read_json(){
  $raw = file_get_contents('php://input');
  $data = json_decode($raw, true);
  return is_array($data) ? $data : [];
}

function path_segments_after_base($base){
  $uri = $_SERVER['REQUEST_URI'] ?? '';
  $qpos = strpos($uri, '?'); if ($qpos !== false) { $uri = substr($uri, 0, $qpos); }
  // Normalize multiple slashes
  $uri = preg_replace('/\/+/', '/', $uri);
  if ($base && strpos($uri, $base) === 0) {
    $rest = substr($uri, strlen($base));
  } else {
    $rest = $uri;
  }
  $rest = trim($rest, '/');
  if ($rest === '') return [];
  return explode('/', $rest);
}

$segments = path_segments_after_base('/mock-api/bookings');

// --- List bookings ---
if ($method === 'GET' && count($segments) === 0) {
  $items = load_store($bookingsFile);
  echo json_encode(['success' => true, 'data' => $items]);
  exit;
}

// --- Create booking ---
if ($method === 'POST' && count($segments) === 0) {
  $payload = read_json();
  $items = load_store($bookingsFile);
  // Compute next id
  $maxId = 0; foreach ($items as $it) { $iid = (int)($it['id'] ?? 0); if ($iid > $maxId) $maxId = $iid; }
  $id = $maxId + 1;
  $nowMs = round(microtime(true) * 1000);
  $booking = [
    'id' => $id,
    'provider_id' => isset($payload['provider_id']) ? (int)$payload['provider_id'] : null,
    'service_name' => isset($payload['service_name']) ? (string)$payload['service_name'] : null,
    'scheduled_date' => isset($payload['scheduled_date']) ? (string)$payload['scheduled_date'] : null,
    'scheduled_time' => isset($payload['scheduled_time']) ? (string)$payload['scheduled_time'] : null,
    'address' => isset($payload['address']) ? (string)$payload['address'] : null,
    'lat' => isset($payload['lat']) ? $payload['lat'] : null,
    'lng' => isset($payload['lng']) ? $payload['lng'] : null,
    'price' => isset($payload['price']) ? $payload['price'] : null,
    'notes' => isset($payload['notes']) ? (string)$payload['notes'] : null,
    'status' => 'pending',
    'created_at' => $nowMs,
  ];
  $items[] = $booking;
  save_store($bookingsFile, $items);
  echo json_encode(['success' => true, 'id' => $id, 'data' => ['id' => $id, 'booking' => $booking]]);
  exit;
}

// --- Cancel booking ---
if ($method === 'POST' && count($segments) === 2 && is_numeric($segments[0]) && $segments[1] === 'cancel') {
  $id = (int)$segments[0];
  $payload = read_json();
  $reason = isset($payload['reason']) ? (string)$payload['reason'] : null;
  $nowMs = round(microtime(true) * 1000);
  $items = load_store($bookingsFile);
  $found = false;
  foreach ($items as &$it) {
    if ((int)($it['id'] ?? 0) === $id) {
      $it['status'] = 'cancelled';
      if ($reason !== null && $reason !== '') { $it['cancellation_reason'] = $reason; }
      $it['cancelled_at'] = $nowMs;
      $found = true; break;
    }
  }
  unset($it);
  if (!$found) { http_response_code(404); echo json_encode(['success'=>false,'message'=>'Booking not found']); exit; }
  save_store($bookingsFile, $items);
  echo json_encode(['success' => true]);
  exit;
}

// --- Update status ---
if ($method === 'POST' && count($segments) === 2 && is_numeric($segments[0]) && $segments[1] === 'status') {
  $id = (int)$segments[0];
  $payload = read_json();
  $newStatus = strtolower((string)($payload['status'] ?? ''));
  if ($newStatus === '') { http_response_code(422); echo json_encode(['success'=>false,'message'=>'status required']); exit; }
  $items = load_store($bookingsFile);
  $found = false;
  foreach ($items as &$it) {
    if ((int)($it['id'] ?? 0) === $id) { $it['status'] = $newStatus; $found = true; break; }
  }
  unset($it);
  if (!$found) { http_response_code(404); echo json_encode(['success'=>false,'message'=>'Booking not found']); exit; }
  save_store($bookingsFile, $items);
  echo json_encode(['success' => true]);
  exit;
}

// --- Rate booking ---
if ($method === 'POST' && count($segments) === 2 && is_numeric($segments[0]) && $segments[1] === 'rate') {
  $id = (int)$segments[0];
  $payload = read_json();
  $rating = isset($payload['rating']) ? (int)$payload['rating'] : null;
  if (!$rating || $rating < 1 || $rating > 5) { http_response_code(422); echo json_encode(['success'=>false,'message'=>'rating must be 1-5']); exit; }
  $items = load_store($bookingsFile);
  $found = false;
  foreach ($items as &$it) {
    if ((int)($it['id'] ?? 0) === $id) { $it['rating'] = $rating; $found = true; break; }
  }
  unset($it);
  if (!$found) { http_response_code(404); echo json_encode(['success'=>false,'message'=>'Booking not found']); exit; }
  save_store($bookingsFile, $items);
  echo json_encode(['success' => true]);
  exit;
}

// --- Request return ---
if ($method === 'POST' && count($segments) === 2 && is_numeric($segments[0]) && $segments[1] === 'return') {
  $id = (int)$segments[0];
  $payload = read_json();
  $issues = isset($payload['issues']) && is_array($payload['issues']) ? $payload['issues'] : [];
  $notes = isset($payload['notes']) ? (string)$payload['notes'] : '';
  $recs = load_store($returnsFile);
  $recs[] = [ 'booking_id' => $id, 'issues' => $issues, 'notes' => $notes, 'ts' => round(microtime(true) * 1000) ];
  save_store($returnsFile, $recs);
  echo json_encode(['success' => true]);
  exit;
}

// --- List returns ---
if ($method === 'GET' && count($segments) === 1 && $segments[0] === 'returns') {
  $recs = load_store($returnsFile);
  echo json_encode(['success' => true, 'data' => $recs]);
  exit;
}

http_response_code(404);
echo json_encode(['success' => false, 'message' => 'Not Found']);
?>
