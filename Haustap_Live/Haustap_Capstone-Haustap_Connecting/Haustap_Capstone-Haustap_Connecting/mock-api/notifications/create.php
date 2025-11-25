<?php
require __DIR__ . '/_util.php';

// POST /mock-api/notifications/create
// Body: { user_id, notification: { id?, type, title, body, ts?, booking_id?, read? } }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  notif_reply(['error' => 'Method not allowed'], 405);
  exit;
}

$uid = notif_user_from_request();
if ($uid === null) {
  notif_reply(['error' => 'Missing user_id'], 400);
  exit;
}

$body = notif_json_input();
$n = $body['notification'] ?? $body;

if (!is_array($n)) {
  notif_reply(['error' => 'Invalid payload'], 400);
  exit;
}

$id = isset($n['id']) ? (string)$n['id'] : ('n_' . str_replace('.', '', uniqid('', true)));
$now = (int) (isset($n['ts']) ? $n['ts'] : round(microtime(true) * 1000));

$item = [
  'id' => $id,
  'type' => (string)($n['type'] ?? 'notification'),
  'title' => (string)($n['title'] ?? ''),
  'body' => (string)($n['body'] ?? ''),
  'ts' => $now,
  'booking_id' => $n['booking_id'] ?? ($n['bookingId'] ?? null),
  'read' => (bool)($n['read'] ?? false),
];

$arr = notif_load($uid);
$arr[] = $item;
notif_save($uid, $arr);

notif_reply(['ok' => true, 'notification' => $item]);

