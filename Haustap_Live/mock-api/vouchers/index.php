<?php
// Mock Vouchers API for local development
// Endpoints:
// - GET  /mock-api/vouchers?email=<email>         -> fetch computed vouchers and progress
// - GET  /mock-api/vouchers/history?email=<email>  -> fetch voucher history for user (redeemed/expired)

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Methods: GET, OPTIONS');

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'OPTIONS') { echo json_encode(['success'=>true]); exit; }
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

$storeDir = __DIR__ . '/../_data';
$historyFile = $storeDir . '/vouchers.json';
$bookingsFile = $storeDir . '/bookings.json';
$referralsFile = $storeDir . '/referrals.json';

function load_json($file){
  if (is_file($file)) {
    $raw = file_get_contents($file);
    $data = json_decode($raw, true);
    if (is_array($data)) return $data;
  }
  return [];
}

function save_json($file, $data){
  $dir = dirname($file);
  if (!is_dir($dir)) { @mkdir($dir, 0777, true); }
  file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT));
}

function path_segments_after_base($base){
  $uri = $_SERVER['REQUEST_URI'] ?? '';
  $qpos = strpos($uri, '?'); if ($qpos !== false) { $uri = substr($uri, 0, $qpos); }
  $uri = preg_replace('/\/+/', '/', $uri);
  if ($base && strpos($uri, $base) === 0) { $rest = substr($uri, strlen($base)); } else { $rest = $uri; }
  $rest = trim($rest, '/'); if ($rest === '') return [];
  return explode('/', $rest);
}

$segments = path_segments_after_base('/mock-api/vouchers');

if ($method === 'GET' && count($segments) === 0) {
  $email = isset($_GET['email']) ? trim(strtolower((string)$_GET['email'])) : '';
  if ($email === '') { http_response_code(422); echo json_encode(['success'=>false,'message'=>'email required']); exit; }

  // Compute loyalty progress from bookings.json (status === completed)
  $bookings = load_json($bookingsFile);
  $completed = 0;
  foreach ($bookings as $b) {
    // In a real app, we'd check $b['user_email']; here we simply count completed entries
    if (strtolower($b['status'] ?? '') === 'completed') { $completed++; }
  }
  $loyaltyRequired = 10;
  if ($completed < 0) $completed = 0; if ($completed > $loyaltyRequired) $completed = $loyaltyRequired;

  // Check referral status from referrals.json
  $referrals = load_json($referralsFile);
  $referralsByEmail = [];
  foreach ($referrals as $it) { $em = strtolower($it['email'] ?? ''); if ($em !== '') $referralsByEmail[$em] = $it; }
  $myRef = $referralsByEmail[$email] ?? null;
  $earnedFromReferrals = 0;
  $pendingReferrals = 0;
  if ($myRef) {
    $list = isset($myRef['referrals']) && is_array($myRef['referrals']) ? $myRef['referrals'] : [];
    $pendingReferrals = count($list);
    // In a real system, we'd check booking completion of invited friends; keep pending for mock
    $earnedFromReferrals = 0;
  }

  // History for user
  $history = load_json($historyFile);
  $userHistory = [];
  foreach ($history as $h) {
    if (strtolower($h['email'] ?? '') === $email) { $userHistory[] = $h; }
  }

  $data = [
    'email' => $email,
    'loyalty' => [ 'required' => $loyaltyRequired, 'completed' => $completed, 'reward_amount' => 50, 'currency' => 'PHP' ],
    'welcome' => [ 'reward_amount' => 50, 'currency' => 'PHP', 'eligible' => true ],
    'referral' => [ 'reward_amount' => 10, 'currency' => 'PHP', 'earned' => $earnedFromReferrals, 'pending' => $pendingReferrals ],
    'history' => $userHistory
  ];
  echo json_encode(['success'=>true,'data'=>$data]);
  exit;
}

if ($method === 'GET' && count($segments) === 1 && $segments[0] === 'history') {
  $email = isset($_GET['email']) ? trim(strtolower((string)$_GET['email'])) : '';
  if ($email === '') { http_response_code(422); echo json_encode(['success'=>false,'message'=>'email required']); exit; }
  $history = load_json($historyFile);
  $userHistory = [];
  foreach ($history as $h) { if (strtolower($h['email'] ?? '') === $email) { $userHistory[] = $h; } }
  echo json_encode(['success'=>true,'data'=>$userHistory]);
  exit;
}

http_response_code(404);
echo json_encode(['success'=>false,'message'=>'Not Found']);
?>
