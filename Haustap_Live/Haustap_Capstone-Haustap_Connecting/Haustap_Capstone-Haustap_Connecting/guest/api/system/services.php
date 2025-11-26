<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../_db.php';
$pdo = haustap_db_conn();
if (!$pdo) { echo json_encode(['success'=>false,'message'=>'DB unavailable']); exit; }
try {
  $stmt = $pdo->query('SHOW TABLES LIKE "services"');
  $has = (bool) $stmt->fetchColumn();
  if (!$has) { echo json_encode(['success'=>true,'count'=>0,'tables'=>['services'=>false],'data'=>[]]); exit; }
  $rows = $pdo->query('SELECT id, name, category, price, duration_minutes FROM services ORDER BY name')->fetchAll();
  echo json_encode(['success'=>true,'count'=>count($rows),'tables'=>['services'=>true],'data'=>$rows]);
} catch (Throwable $e) {
  echo json_encode(['success'=>false,'message'=>'Query failed']);
}