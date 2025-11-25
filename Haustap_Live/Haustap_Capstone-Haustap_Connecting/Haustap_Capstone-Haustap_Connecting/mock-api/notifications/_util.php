<?php
// Utilities for file-based notifications mock API

function notif_storage_dir(): string {
  $base = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'notifications';
  if (!is_dir($base)) {
    @mkdir($base, 0777, true);
  }
  return $base;
}

function notif_user_file($userId): string {
  $uid = preg_replace('/[^0-9A-Za-z_-]/', '', (string)$userId);
  if ($uid === '') { $uid = 'guest'; }
  return notif_storage_dir() . DIRECTORY_SEPARATOR . 'user-' . $uid . '.json';
}

function notif_load($userId): array {
  $file = notif_user_file($userId);
  if (!is_file($file)) return [];
  $raw = @file_get_contents($file);
  if ($raw === false) return [];
  $data = json_decode($raw, true);
  return is_array($data) ? $data : [];
}

function notif_save($userId, array $arr): bool {
  $file = notif_user_file($userId);
  $json = json_encode(array_values($arr), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
  return @file_put_contents($file, $json) !== false;
}

function notif_json_input(): array {
  $raw = file_get_contents('php://input');
  $data = json_decode($raw, true);
  return is_array($data) ? $data : [];
}

function notif_user_from_request(): ?string {
  $uid = $_GET['user_id'] ?? $_GET['uid'] ?? null;
  if ($uid === null && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $body = notif_json_input();
    $uid = $body['user_id'] ?? $body['uid'] ?? null;
  }
  if ($uid === null) return null;
  return preg_replace('/[^0-9A-Za-z_-]/', '', (string)$uid);
}

function notif_reply($data, int $code=200): void {
  http_response_code($code);
  header('Content-Type: application/json; charset=UTF-8');
  echo json_encode($data, JSON_UNESCAPED_SLASHES);
}

