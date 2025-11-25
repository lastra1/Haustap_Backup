<?php
// save-notification.php - API endpoint to save notifications to the database

header('Content-Type: application/json');

// Get JSON request data
$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['message'])) {
  http_response_code(400);
  echo json_encode(['error' => 'Missing required fields']);
  exit;
}

// Build notification object
$notification = [
  'id' => (int)time(),
  'message' => $input['message'] ?? '',
  'href' => $input['href'] ?? 'job_status_monitor.php',
  'created_at' => $input['created_at'] ?? date('c'),
  'read' => $input['read'] ?? false,
  'type' => $input['type'] ?? 'system'
];

// Load existing notifications
$notificationsPath = realpath(__DIR__ . '/../../storage/data/notifications.json');
$notifications = [];

if ($notificationsPath && is_file($notificationsPath)) {
  $raw = @file_get_contents($notificationsPath);
  $notifications = json_decode($raw ?: '[]', true) ?: [];
}

// Add new notification
$notifications[] = $notification;

// Save to file
if ($notificationsPath) {
  $result = @file_put_contents(
    $notificationsPath,
    json_encode($notifications, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT),
    LOCK_EX
  );
  
  if ($result === false) {
    http_response_code(500);
    echo json_encode(['error' => 'Could not save notification']);
    exit;
  }
}

// Return success response
http_response_code(200);
echo json_encode([
  'success' => true,
  'notification' => $notification
]);
?>
