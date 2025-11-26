<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../_db.php';

function read_json($file) {
  if (!file_exists($file)) return [];
  $raw = file_get_contents($file);
  if ($raw === false || $raw === '') return [];
  $data = json_decode($raw, true);
  return is_array($data) ? $data : [];
}

function write_json($file, $data) {
  $dir = dirname($file);
  if (!is_dir($dir)) @mkdir($dir, 0777, true);
  file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT));
}

$input = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $raw = file_get_contents('php://input');
  $input = json_decode($raw, true);
  if (!$input) $input = $_POST;
} else {
  http_response_code(405);
  echo json_encode(['success' => false, 'message' => 'Method not allowed']);
  exit;
}

$address = isset($input['address']) ? trim($input['address']) : '';
$lat = isset($input['lat']) ? floatval($input['lat']) : null;
$lng = isset($input['lng']) ? floatval($input['lng']) : null;
$house_type = isset($input['house_type']) ? trim($input['house_type']) : '';
$service_name = isset($input['service_name']) ? trim($input['service_name']) : '';
$cleaning_type = isset($input['cleaning_type']) ? trim($input['cleaning_type']) : '';
$user_key = isset($input['user_key']) ? trim($input['user_key']) : '';

if ($user_key === '') {
  http_response_code(401);
  echo json_encode(['success' => false, 'message' => 'Authentication required']);
  exit;
}

if ($address === '' && ($lat === null || $lng === null) && $house_type === '') {
  http_response_code(422);
  echo json_encode(['success' => false, 'message' => 'Address or coordinates or house_type required']);
  exit;
}

$record = [
  'id' => uniqid('loc_', true),
  'address' => $address,
  'lat' => $lat,
  'lng' => $lng,
  'house_type' => $house_type,
  'service_name' => $service_name,
  'cleaning_type' => $cleaning_type,
  'user_key' => $user_key,
  'created_at' => date('c')
];

$pdo = haustap_db_conn();
if ($pdo && ensure_locations_table($pdo)) {
  try {
    $stmt = $pdo->prepare("INSERT INTO booking_locations (id, address, lat, lng, house_type, service_name, cleaning_type, user_key, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
      $record['id'], $record['address'], $record['lat'], $record['lng'], $record['house_type'], $record['service_name'], $record['cleaning_type'], $record['user_key'], $record['created_at']
    ]);
    echo json_encode(['success' => true, 'data' => $record, 'store' => 'mysql']);
    exit;
  } catch (Throwable $e) {
    // Fall through to JSON file store
  }
}

// Fallback: store in local JSON file
$store = __DIR__ . '/../data/locations.json';
$all = read_json($store);
$all[] = $record;
write_json($store, $all);

echo json_encode(['success' => true, 'data' => $record, 'store' => 'file']);