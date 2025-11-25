<?php
header('Content-Type: application/json');
// Simple availability endpoint for dev environment
$dateStr = isset($_GET['date']) ? $_GET['date'] : null;
try {
    $d = $dateStr ? new DateTime($dateStr) : new DateTime();
} catch (Throwable $e) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'Invalid date']);
    exit;
}
// Fully booked on weekends: Sunday (0) and Saturday (6)
$dayOfWeek = (int) $d->format('w');
$available = ($dayOfWeek !== 0 && $dayOfWeek !== 6);
$slots = [];
if ($available) {
    $start = new DateTime($d->format('Y-m-d').' 08:00:00');
    $end = new DateTime($d->format('Y-m-d').' 20:00:00');
    $cursor = clone $start;
    while ($cursor <= $end) {
        $slots[] = $cursor->format('g:i A');
        $cursor->modify('+30 minutes');
    }
}
echo json_encode([
    'success' => true,
    'available' => $available,
    'slots' => $slots,
]);

