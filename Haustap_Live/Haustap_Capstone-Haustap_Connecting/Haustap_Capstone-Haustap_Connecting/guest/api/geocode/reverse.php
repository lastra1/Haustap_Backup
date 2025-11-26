<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../_db.php';

$lat = isset($_GET['lat']) ? trim($_GET['lat']) : '';
$lng = isset($_GET['lng']) ? trim($_GET['lng']) : '';
if ($lat === '' || $lng === '') {
  http_response_code(422);
  echo json_encode(['success' => false, 'message' => 'lat and lng required']);
  exit;
}

$url = 'https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=' . urlencode($lat) . '&lon=' . urlencode($lng);

try {
  $ch = curl_init();
  curl_setopt_array($ch, [
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
      'Accept: application/json',
      'User-Agent: Haustap-Geocoder/1.0'
    ],
    CURLOPT_TIMEOUT => 8,
  ]);
  $resp = curl_exec($ch);
  $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  curl_close($ch);
  if ($code >= 200 && $code < 300 && $resp) {
    $data = @json_decode($resp, true);
    $display = $data && isset($data['display_name']) ? $data['display_name'] : null;
    echo json_encode(['success' => true, 'data' => ['address' => $display]]);
    exit;
  }
} catch (Throwable $e) {}

echo json_encode(['success' => true, 'data' => ['address' => null]]);
exit;