<?php
require_once __DIR__ . '/../../../config.php';
require_once __DIR__ . '/../../../_lib/smtp.php';
// OTP send endpoint: generates a real OTP, stores it, and attempts email delivery
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Methods: POST, OPTIONS');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
  http_response_code(204);
  exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  echo json_encode(['message' => 'Method Not Allowed']);
  exit;
}

$raw = file_get_contents('php://input');
$data = json_decode($raw, true) ?? [];
$email = trim($data['email'] ?? '');
$mobile = trim($data['mobile'] ?? '');

if ($email === '' && $mobile === '') {
  http_response_code(422);
  echo json_encode(['message' => 'Email or mobile required']);
  exit;
}

// Generate a random 6-digit OTP
$otpCode = str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);

// Prepare OTP record
$otpId = 'mock-otp-' . substr(md5(($email ?: $mobile) . microtime(true)), 0, 8);
$record = [
  'id' => $otpId,
  'code' => $otpCode,
  'email' => $email ?: null,
  'mobile' => $mobile ?: null,
  'created_at' => time(),
  'expires_at' => time() + 5 * 60 // 5 minutes
];

// Persist record to mock store
$storePath = __DIR__ . '/../../../_data/otps.json';
if (!file_exists($storePath)) {
  @mkdir(dirname($storePath), 0777, true);
  file_put_contents($storePath, '{}');
}
$storeRaw = file_get_contents($storePath);
$store = json_decode($storeRaw, true);
if (!is_array($store)) { $store = []; }
$store[$otpId] = $record;
file_put_contents($storePath, json_encode($store, JSON_PRETTY_PRINT));

// Attempt to send email via Laravel backend if email is provided
$delivery = $email ? 'email' : 'sms';
$maskedEmail = $email ? preg_replace('/(^.).*(@.*$)/', '$1***$2', $email) : null;
$maskedMobile = $mobile ? preg_replace('/(\d{2})\d+(\d{2})/', '$1****$2', $mobile) : null;

if ($email !== '') {
  $cfg = include __DIR__ . '/../../../config.php';
  $canSmtp = !empty($cfg['smtp_user']) && !empty($cfg['smtp_pass']) && !empty($cfg['from_email']);
  $sent = false;
  if ($canSmtp) {
    $subject = 'Your HausTap verification code';
    $body = 'Your verification code is: ' . $otpCode . "\nThis code expires in 5 minutes.";
    $sent = smtp_send(
      $cfg['smtp_host'],
      $cfg['smtp_port'],
      $cfg['smtp_secure'],
      $cfg['smtp_user'],
      $cfg['smtp_pass'],
      $cfg['from_email'],
      $cfg['from_name'],
      $email,
      $subject,
      $body
    );
  }
  if (!$sent) {
    try {
      $backendUrl = 'http://127.0.0.1:8001/auth/send-otp';
      $payload = json_encode(['email' => $email, 'otp' => $otpCode]);
      $ctx = stream_context_create([
        'http' => [
          'method' => 'POST',
          'header' => "Content-Type: application/json\r\n",
          'content' => $payload,
          'timeout' => 5
        ]
      ]);
      @file_get_contents($backendUrl, false, $ctx);
    } catch (\Throwable $e) {}
  }
}

echo json_encode([
  'success' => true,
  'otpId' => $otpId,
  'delivery' => $delivery,
  'masked' => $email ? $maskedEmail : $maskedMobile,
  // Dev convenience: expose code so testers can verify without email
  'devCode' => $otpCode
]);
exit;