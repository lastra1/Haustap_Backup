<?php
// Mock Chat API for client-provider conversations tied to a booking
// Endpoints:
// - POST /mock-api/chat/open                 -> ensure conversation exists for booking_id
// - GET  /mock-api/chat/{booking_id}/messages?since=<epoch_ms>
// - POST /mock-api/chat/{booking_id}/messages

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
$storeFile = $storeDir . '/chat.json';

function load_store($file){
  if (is_file($file)) {
    $raw = file_get_contents($file);
    $data = json_decode($raw, true);
    if (is_array($data)) return $data;
  }
  return ['conversations'=>[], 'messages'=>[], 'next_msg_id'=>1];
}

function save_store($file, $data){
  $dir = dirname($file);
  if (!is_dir($dir)) { @mkdir($dir, 0777, true); }
  file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT));
}

function read_json(){
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

$segments = path_segments_after_base('/mock-api/chat');
$store = load_store($storeFile);

// Ensure conversation exists for booking_id
if ($method === 'POST' && count($segments) === 1 && $segments[0] === 'open') {
  $payload = read_json();
  $booking_id = (int)($payload['booking_id'] ?? 0);
  $client_id = isset($payload['client_id']) ? (int)$payload['client_id'] : null;
  $provider_id = isset($payload['provider_id']) ? (int)$payload['provider_id'] : null;
  if ($booking_id <= 0) { http_response_code(422); echo json_encode(['success'=>false,'message'=>'booking_id required']); exit; }
  $found = null;
  foreach ($store['conversations'] as $c) {
    if ((int)($c['booking_id'] ?? 0) === $booking_id) { $found = $c; break; }
  }
  if (!$found) {
    $found = [
      'id' => 'booking_' . $booking_id,
      'booking_id' => $booking_id,
      'client_id' => $client_id,
      'provider_id' => $provider_id,
      'opened_at' => round(microtime(true) * 1000)
    ];
    $store['conversations'][] = $found;
    save_store($storeFile, $store);
  }
  echo json_encode(['success'=>true, 'conversation'=>$found]);
  exit;
}

// GET messages for booking
if ($method === 'GET' && count($segments) === 2 && is_numeric($segments[0]) && $segments[1] === 'messages') {
  $booking_id = (int)$segments[0];
  $since = isset($_GET['since']) ? (int)$_GET['since'] : 0;
  $messages = array_values(array_filter($store['messages'], function($m) use ($booking_id, $since){
    if ((int)($m['booking_id'] ?? 0) !== $booking_id) return false;
    if ($since && (int)($m['ts'] ?? 0) <= $since) return false;
    return true;
  }));
  // sort ascending by ts
  usort($messages, function($a,$b){ return ((int)$a['ts']) <=> ((int)$b['ts']); });
  echo json_encode(['success'=>true, 'messages'=>$messages]);
  exit;
}

// POST a new message
if ($method === 'POST' && count($segments) === 2 && is_numeric($segments[0]) && $segments[1] === 'messages') {
  $booking_id = (int)$segments[0];
  $payload = read_json();
  $text = trim((string)($payload['text'] ?? ''));
  $sender = (string)($payload['sender'] ?? 'client'); // 'client' or 'provider'
  $sender_id = isset($payload['sender_id']) ? (int)$payload['sender_id'] : null;
  if ($text === '') { http_response_code(422); echo json_encode(['success'=>false,'message'=>'text required']); exit; }
  $msg = [
    'id' => $store['next_msg_id'],
    'booking_id' => $booking_id,
    'sender' => $sender,
    'sender_id' => $sender_id,
    'text' => $text,
    'ts' => round(microtime(true) * 1000)
  ];
  $store['messages'][] = $msg;
  $store['next_msg_id'] = $store['next_msg_id'] + 1;
  save_store($storeFile, $store);
  echo json_encode(['success'=>true, 'message'=>$msg]);
  exit;
}

http_response_code(405);
echo json_encode(['success'=>false, 'message'=>'Method Not Allowed']);
?>
