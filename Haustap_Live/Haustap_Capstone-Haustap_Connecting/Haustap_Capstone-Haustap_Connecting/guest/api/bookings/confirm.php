<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../_db.php';

$pdo = haustap_db_conn();
if (!$pdo) { http_response_code(500); echo json_encode(['success'=>false,'message'=>'DB unavailable']); exit; }
ensure_holds_table($pdo); ensure_capacity_table($pdo); ensure_bookings_table($pdo);

$raw = file_get_contents('php://input');
$input = json_decode($raw, true); if (!$input) $input = $_POST;

$hold_id = isset($input['hold_id']) ? trim($input['hold_id']) : '';
$user_key = isset($input['user_key']) ? trim($input['user_key']) : '';
if ($hold_id === '' || $user_key === '') { http_response_code(422); echo json_encode(['success'=>false,'message'=>'hold_id and user_key required']); exit; }

try {
  $stmt = $pdo->prepare('SELECT * FROM booking_holds WHERE id = ? AND user_key = ? LIMIT 1');
  $stmt->execute([$hold_id, $user_key]);
  $hold = $stmt->fetch();
  if (!$hold) { http_response_code(404); echo json_encode(['success'=>false,'message'=>'Hold not found']); exit; }
  if (strtotime($hold['expires_at']) < time()) { http_response_code(410); echo json_encode(['success'=>false,'message'=>'Hold expired']); exit; }

  $date = $hold['date']; $time = $hold['time']; $service = $hold['service_code'];
  $capOk = true;
  try {
    $stmt2 = $pdo->prepare('SELECT id, capacity_total, capacity_used FROM capacity_calendar WHERE date = ? AND slot = ? AND (service_code IS NULL OR service_code = ?) LIMIT 1');
    $stmt2->execute([$date, $time, $service]);
    $row = $stmt2->fetch();
    if ($row) {
      if ((int)$row['capacity_used'] >= (int)$row['capacity_total']) $capOk = false;
      else {
        $pdo->beginTransaction();
        $pdo->prepare('UPDATE capacity_calendar SET capacity_used = capacity_used + 1 WHERE id = ?')->execute([$row['id']]);
        $pdo->prepare('INSERT INTO bookings (id, user_key, date, time, service_code, address, created_at) VALUES (?, ?, ?, ?, ?, ?, ?)')
            ->execute([uniqid('bk_', true), $user_key, $date, $time, $service, $hold['address'], date('c')]);
        $pdo->prepare('DELETE FROM booking_holds WHERE id = ?')->execute([$hold_id]);
        $pdo->commit();
        echo json_encode(['success'=>true]);
        exit;
      }
    }
  } catch (Throwable $e) { $capOk = false; }
  if (!$capOk) { http_response_code(409); echo json_encode(['success'=>false,'message'=>'Slot unavailable']); }
} catch (Throwable $e) {
  http_response_code(500); echo json_encode(['success'=>false,'message'=>'Confirm failed']);
}