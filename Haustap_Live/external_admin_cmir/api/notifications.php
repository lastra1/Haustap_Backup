<?php
require_once __DIR__ . '/../includes/auth.php';
// storage path (three levels up from api folder)
$storePath = realpath(__DIR__ . '/../../../storage/data/notifications.json');
if (!$storePath) $storePath = __DIR__ . '/../../../storage/data/notifications.json';

// ensure file exists
if (!is_file($storePath)) {
  file_put_contents($storePath, json_encode([]));
}

// safe read
$raw = @file_get_contents($storePath);
$items = json_decode($raw ?: '[]', true);
if (!is_array($items)) $items = [];

$method = $_SERVER['REQUEST_METHOD'];
header('Content-Type: application/json');

if ($method === 'GET') {
  // query params: unread=1, limit=5
  $unreadOnly = isset($_GET['unread']) && $_GET['unread'] == '1';
  $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 0;

  // sort by created_at desc
  usort($items, function($a, $b){
    $ta = strtotime($a['created_at'] ?? 0);
    $tb = strtotime($b['created_at'] ?? 0);
    return $tb <=> $ta;
  });

  $out = array_values(array_filter($items, function($it) use ($unreadOnly){
    if ($unreadOnly) return empty($it['read']);
    return true;
  }));

  if ($limit > 0) $out = array_slice($out, 0, $limit);
  echo json_encode(['ok' => true, 'data' => $out]);
  exit;
}

if ($method === 'POST') {
  // simple action based API via form or JSON
  $action = isset($_POST['action']) ? $_POST['action'] : null;
  // allow JSON body
  if (!$action) {
    $body = json_decode(file_get_contents('php://input'), true) ?: [];
    $action = $body['action'] ?? null;
    if (isset($body['id'])) $_POST['id'] = $body['id'];
  }

  if ($action === 'mark_read') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    if ($id <= 0) { echo json_encode(['ok'=>false,'error'=>'missing id']); exit; }
    $changed = false;
    foreach ($items as &$it) {
      if (isset($it['id']) && (int)$it['id'] === $id) {
        $it['read'] = true; $changed = true; break;
      }
    }
    if ($changed) {
      file_put_contents($storePath, json_encode($items, JSON_PRETTY_PRINT));
    }
    echo json_encode(['ok'=>true,'changed'=>$changed]);
    exit;
  }

  if ($action === 'mark_all_read') {
    $changed = false;
    foreach ($items as &$it) { if (empty($it['read'])) { $it['read'] = true; $changed = true; } }
    if ($changed) file_put_contents($storePath, json_encode($items, JSON_PRETTY_PRINT));
    echo json_encode(['ok'=>true,'changed'=>$changed]);
    exit;
  }

  // unknown action
  echo json_encode(['ok'=>false,'error'=>'unknown action']);
  exit;
}

// fallback
echo json_encode(['ok'=>false,'error'=>'unsupported method']);
