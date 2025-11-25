<?php
require __DIR__ . '/_util.php';

// POST /mock-api/notifications/mark-read
// Body: { user_id, ids: [..] } or { user_id, mark_all: true }
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
$ids = isset($body['ids']) && is_array($body['ids']) ? $body['ids'] : [];
$markAll = !empty($body['mark_all']);

$arr = notif_load($uid);
if ($markAll) {
  foreach ($arr as &$it) { $it['read'] = true; }
  unset($it);
} else if ($ids) {
  $set = array_fill_keys(array_map('strval', $ids), true);
  foreach ($arr as &$it) {
    if (isset($set[(string)($it['id'] ?? '')])) { $it['read'] = true; }
  }
  unset($it);
}

notif_save($uid, $arr);
notif_reply(['ok' => true]);

