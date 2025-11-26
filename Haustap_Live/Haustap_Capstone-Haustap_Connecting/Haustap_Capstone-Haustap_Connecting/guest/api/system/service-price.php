<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../_db.php';
$pdo = haustap_db_conn();
if (!$pdo) { echo json_encode(['success'=>false,'message'=>'DB unavailable']); exit; }
ensure_services_table($pdo);

$name = isset($_GET['name']) ? trim($_GET['name']) : '';
$category = isset($_GET['category']) ? trim($_GET['category']) : '';
if ($name === '') { echo json_encode(['success'=>false,'message'=>'name required']); exit; }

function qLike($s){ return '%' . str_replace(['%','_'],['\%','\_'],$s) . '%'; }

try {
  // 1) Exact (case-insensitive)
  $stmt = $pdo->prepare('SELECT id, name, category, price, duration_minutes FROM services WHERE LOWER(name) = LOWER(?) LIMIT 1');
  $stmt->execute([$name]);
  $row = $stmt->fetch();
  if (!$row) {
    // 2) LIKE by full string
    $stmt = $pdo->prepare('SELECT id, name, category, price, duration_minutes FROM services WHERE name LIKE ?' . ($category ? ' AND category = ?' : '') . ' ORDER BY price DESC LIMIT 1');
    $params = [$name];
    $params[0] = qLike($params[0]);
    if ($category) $params[] = $category;
    $stmt->execute($params);
    $row = $stmt->fetch();
  }
  if (!$row) {
    // 3) Tokenized LIKE (match any token in name)
    $tokens = preg_split('/\s+|-|,/', $name);
    $tokens = array_filter(array_map('trim', $tokens));
    if ($tokens) {
      $sql = 'SELECT id, name, category, price, duration_minutes FROM services WHERE ';
      $conds = [];
      $params = [];
      foreach ($tokens as $t) { $conds[] = 'name LIKE ?'; $params[] = qLike($t); }
      if ($category) { $sql .= '(' . implode(' OR ', $conds) . ') AND category = ?'; $params[] = $category; }
      else { $sql .= implode(' OR ', $conds); }
      $sql .= ' ORDER BY price DESC LIMIT 1';
      $stmt = $pdo->prepare($sql);
      $stmt->execute($params);
      $row = $stmt->fetch();
    }
  }
  if ($row) { echo json_encode(['success'=>true,'data'=>$row]); }
  else { echo json_encode(['success'=>false,'message'=>'service not found']); }
} catch (Throwable $e) {
  echo json_encode(['success'=>false,'message'=>'query failed']);
}