<?php
// Mock Referral API for local development
// Endpoints:
// - GET  /mock-api/referral?email=<email>        -> fetch or create a referral record for user
// - POST /mock-api/referral/apply                 -> apply a friend's referral code { email, code }

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'OPTIONS') {
  echo json_encode(['success' => true]);
  exit;
}

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$storeDir = __DIR__ . '/../_data';
$file = $storeDir . '/referrals.json';

function load_store($file) {
  if (is_file($file)) {
    $raw = file_get_contents($file);
    $data = json_decode($raw, true);
    if (is_array($data)) return $data;
  }
  return [];
}

function save_store($file, $data) {
  $dir = dirname($file);
  if (!is_dir($dir)) { @mkdir($dir, 0777, true); }
  file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT));
}

function read_json(){
  $raw = file_get_contents('php://input');
  $data = json_decode($raw, true);
  return is_array($data) ? $data : [];
}

function path_segments_after_base($base){
  $uri = $_SERVER['REQUEST_URI'] ?? '';
  $qpos = strpos($uri, '?'); if ($qpos !== false) { $uri = substr($uri, 0, $qpos); }
  $uri = preg_replace('/\/+/', '/', $uri);
  if ($base && strpos($uri, $base) === 0) { $rest = substr($uri, strlen($base)); } else { $rest = $uri; }
  $rest = trim($rest, '/');
  if ($rest === '') return [];
  return explode('/', $rest);
}

function gen_code($existing) {
  $alphabet = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789'; // exclude easily-confused chars
  do {
    $code = '';
    for ($i=0; $i<6; $i++) { $code .= $alphabet[random_int(0, strlen($alphabet)-1)]; }
  } while (isset($existing[$code]));
  return $code;
}

$segments = path_segments_after_base('/mock-api/referral');

// GET /mock-api/referral?email=
if ($method === 'GET' && count($segments) === 0) {
  $email = isset($_GET['email']) ? trim(strtolower((string)$_GET['email'])) : '';
  if ($email === '') { http_response_code(422); echo json_encode(['success'=>false,'message'=>'email required']); exit; }
  $items = load_store($file);

  // Build lookup maps
  $byEmail = [];
  $byCode = [];
  foreach ($items as $it) {
    $em = strtolower($it['email'] ?? '');
    if ($em !== '') $byEmail[$em] = $it;
    $cd = strtoupper($it['code'] ?? '');
    if ($cd !== '') $byCode[$cd] = $it;
  }

  if (isset($byEmail[$email])) {
    echo json_encode(['success'=>true,'data'=>$byEmail[$email]]);
    exit;
  }

  // Create new code for this user
  $code = gen_code($byCode);
  $record = [ 'email' => $email, 'code' => $code, 'referred_by' => null, 'referrals' => [], 'created_at' => round(microtime(true)*1000) ];
  $items[] = $record;
  save_store($file, $items);
  echo json_encode(['success'=>true,'data'=>$record]);
  exit;
}

// POST /mock-api/referral/apply
if ($method === 'POST' && count($segments) === 1 && $segments[0] === 'apply') {
  $payload = read_json();
  $email = isset($payload['email']) ? trim(strtolower((string)$payload['email'])) : '';
  $code  = isset($payload['code'])  ? trim(strtoupper((string)$payload['code'])) : '';
  if ($email === '' || $code === '') { http_response_code(422); echo json_encode(['success'=>false,'message'=>'email and code required']); exit; }
  $items = load_store($file);

  // Build lookup maps
  $byEmail = [];
  $byCode = [];
  foreach ($items as $idx => $it) {
    $em = strtolower($it['email'] ?? '');
    if ($em !== '') $byEmail[$em] = $idx;
    $cd = strtoupper($it['code'] ?? '');
    if ($cd !== '') $byCode[$cd] = $idx;
  }

  if (!isset($byEmail[$email])) {
    // auto-create record for applicant if missing
    $newCode = gen_code($byCode);
    $items[] = [ 'email'=>$email, 'code'=>$newCode, 'referred_by'=>null, 'referrals'=>[], 'created_at'=>round(microtime(true)*1000) ];
    $byEmail[$email] = count($items)-1;
  }

  $applIdx = $byEmail[$email];
  $appl = $items[$applIdx];
  if (!empty($appl['referred_by'])) { http_response_code(409); echo json_encode(['success'=>false,'message'=>'referral already applied']); exit; }

  if (!isset($byCode[$code])) { http_response_code(404); echo json_encode(['success'=>false,'message'=>'invalid referral code']); exit; }
  $ownerIdx = $byCode[$code];
  $owner = $items[$ownerIdx];
  if (strtolower($owner['email']) === $email) { http_response_code(400); echo json_encode(['success'=>false,'message'=>'cannot use your own code']); exit; }

  // Apply referral: mark applicant and record referral under owner
  $appl['referred_by'] = strtolower($owner['email']);
  $appl['applied_code'] = $code;
  $items[$applIdx] = $appl;

  $owner['referrals'] = isset($owner['referrals']) && is_array($owner['referrals']) ? $owner['referrals'] : [];
  $owner['referrals'][] = [ 'email'=>$email, 'code_used'=>$code, 'ts'=>round(microtime(true)*1000) ];
  $items[$ownerIdx] = $owner;

  save_store($file, $items);
  echo json_encode(['success'=>true,'data'=>['applicant'=>$appl,'owner'=>$owner]]);
  exit;
}

http_response_code(404);
echo json_encode(['success'=>false,'message'=>'Not Found']);
?>
