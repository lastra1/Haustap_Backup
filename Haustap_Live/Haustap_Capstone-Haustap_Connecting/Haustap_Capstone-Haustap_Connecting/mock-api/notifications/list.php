<?php
require __DIR__ . '/_util.php';

// GET /mock-api/notifications/list?user_id=123
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
  notif_reply(['error' => 'Method not allowed'], 405);
  exit;
}

$uid = notif_user_from_request();
if ($uid === null) {
  notif_reply(['error' => 'Missing user_id'], 400);
  exit;
}

$all = notif_load($uid);
// Optional limiting
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 100;
if ($limit > 0) {
  $all = array_slice($all, -$limit);
}

notif_reply(['notifications' => array_values($all)]);

