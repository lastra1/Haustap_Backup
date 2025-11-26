<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../_db.php';

$pdo = haustap_db_conn();
if (!$pdo) { http_response_code(500); echo json_encode(['success'=>false,'message'=>'DB unavailable']); exit; }
ensure_holds_table($pdo); ensure_capacity_table($pdo);

$raw = file_get_contents('php://input');
$input = json_decode($raw, true); if (!$input) $input = $_POST;

$user_key = isset($input['user_key']) ? trim($input['user_key']) : '';
$date = isset($input['date']) ? trim($input['date']) : '';
$time = isset($input['time']) ? trim($input['time']) : '';
$service = isset($input['service']) ? trim($input['service']) : null;
$address = isset($input['address']) ? trim($input['address']) : null;

if ($user_key === '' || $date === '' || $time === '') { http_response_code(422); echo json_encode(['success'=>false,'message'=>'user_key, date, time required']); exit; }

$id = uniqid('hold_', true);
$expires = date('c', time() + 10*60);

try {
  $stmt = $pdo->prepare('INSERT INTO booking_holds (id, user_key, date, time, service_code, address, expires_at, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
  $stmt->execute([$id, $user_key, $date, $time, $service, $address, $expires, date('c')]);
  echo json_encode(['success'=>true,'data'=>['id'=>$id,'expires_at'=>$expires]]);
} catch (Throwable $e) {
  http_response_code(500); echo json_encode(['success'=>false,'message'=>'Failed to create hold']);
}