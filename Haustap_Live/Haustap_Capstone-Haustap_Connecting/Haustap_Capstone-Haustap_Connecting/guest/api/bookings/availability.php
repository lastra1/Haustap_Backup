<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../_db.php';

$dateStr = isset($_GET['date']) ? $_GET['date'] : null;
$service = isset($_GET['service']) ? trim($_GET['service']) : null;
$zone = isset($_GET['zone']) ? trim($_GET['zone']) : null;
try { $d = $dateStr ? new DateTime($dateStr) : new DateTime(); } catch (Throwable $e) { http_response_code(422); echo json_encode(['success'=>false,'message'=>'Invalid date']); exit; }

$pdo = haustap_db_conn();
if ($pdo) { ensure_blackout_table($pdo); ensure_capacity_table($pdo); }

$dayOfWeek = (int) $d->format('w');
$dateYmd = $d->format('Y-m-d');

$blackout = false;
if ($pdo) {
  try {
    $stmt = $pdo->prepare('SELECT 1 FROM blackout_dates WHERE date = ? AND (service_code IS NULL OR service_code = ? ) LIMIT 1');
    $stmt->execute([$dateYmd, $service]);
    $blackout = (bool) $stmt->fetchColumn();
  } catch (Throwable $e) { $blackout = false; }
}

$available = ($dayOfWeek !== 0 && $dayOfWeek !== 6) && !$blackout;
$slots = [];
if ($available) {
  if ($pdo) {
    try {
      $stmt = $pdo->prepare('SELECT slot, capacity_total, capacity_used FROM capacity_calendar WHERE date = ? AND (zone IS NULL OR zone = ?) AND (service_code IS NULL OR service_code = ?)');
      $stmt->execute([$dateYmd, $zone, $service]);
      $rows = $stmt->fetchAll();
      if ($rows && count($rows)) {
        foreach ($rows as $r) { if ((int)$r['capacity_total'] > (int)$r['capacity_used']) $slots[] = $r['slot']; }
      }
    } catch (Throwable $e) {}
  }
  if (!count($slots)) {
    $start = new DateTime($dateYmd.' 08:00:00');
    $end = new DateTime($dateYmd.' 20:00:00');
    $cursor = clone $start;
    while ($cursor <= $end) { $slots[] = $cursor->format('g:i A'); $cursor->modify('+30 minutes'); }
  }
}
echo json_encode(['success'=>true,'available'=>$available,'slots'=>$slots]);

