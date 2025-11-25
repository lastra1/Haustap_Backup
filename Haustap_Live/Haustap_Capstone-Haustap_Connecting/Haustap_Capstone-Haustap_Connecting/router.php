<?php
$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
$docRoot = __DIR__;
$normalized = str_replace('/', DIRECTORY_SEPARATOR, $uri);
$fullPath = $docRoot . $normalized;
// Resolve project root and admin app path for cross-app routing
$projectRoot = dirname(__DIR__, 2);
$adminCanonical = $projectRoot . DIRECTORY_SEPARATOR . 'admin_haustap' . DIRECTORY_SEPARATOR . 'admin_haustap';
$adminLegacy = $projectRoot . DIRECTORY_SEPARATOR . 'external_admin_cmir';
$ADMIN_APP_PATH = is_dir($adminCanonical) ? $adminCanonical : (is_dir($adminLegacy) ? $adminLegacy : $adminCanonical);

// Environment and logging setup
$envLoad = function(string $file) {
  if (!is_file($file)) return;
  foreach (file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
    $line = trim($line);
    if ($line === '' || $line[0] === '#' || strpos($line,'=') === false) continue;
    [$k,$v] = array_map('trim', explode('=', $line, 2));
    $_ENV[$k] = $v;
    putenv($k.'='.$v);
  }
};
$envLoad($projectRoot . DIRECTORY_SEPARATOR . '.env');
$appEnv = getenv('APP_ENV');
if (!$appEnv || $appEnv === '') { $appEnv = 'development'; }
$envLoad($projectRoot . DIRECTORY_SEPARATOR . '.env.' . $appEnv);

$logDir = $projectRoot . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'logs';
if (!is_dir($logDir)) { @mkdir($logDir, 0777, true); }
@ini_set('error_log', $logDir . DIRECTORY_SEPARATOR . 'php-error.log');
set_error_handler(function($severity, $message, $file = null, $line = null) {
  $payload = json_encode(['level'=>'error','type'=>'php_error','message'=>$message,'file'=>$file,'line'=>$line], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
  @error_log($payload);
});
set_exception_handler(function($e) {
  $payload = json_encode(['level'=>'error','type'=>'exception','message'=>$e->getMessage(),'file'=>$e->getFile(),'line'=>$e->getLine()], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
  @error_log($payload);
  if (!headers_sent()) { http_response_code(500); }
  echo 'Internal Server Error';
});

$envBool = function(string $key, bool $default): bool {
  $val = getenv($key);
  if ($val === false || $val === '') return $default;
  $val = strtolower((string)$val);
  return in_array($val, ['1','true','yes','on'], true);
};
$enableMock = $envBool('ENABLE_MOCK_API', $appEnv !== 'production');

// Default landing: redirect root to the client homepage
if ($uri === '/' || $uri === '/index.php') {
  header('Location: /client/homepage.php', true, 302);
  exit;
}

if ($uri === '/health') {
  if (!headers_sent()) header('Content-Type: text/plain');
  echo 'ok';
  return true;
}

// Ensure UTF-8 Content-Type for dynamic HTML responses
function ensureUtf8HtmlHeader() {
  if (!headers_sent()) {
    header('Content-Type: text/html; charset=UTF-8');
  }
}

if ($uri === '/api/system/firebase-config') {
  header('Content-Type: application/json');
  try {
    $base = dirname(__DIR__, 2);
    $envPath = $base . DIRECTORY_SEPARATOR . 'backend' . DIRECTORY_SEPARATOR . 'api' . DIRECTORY_SEPARATOR . '.env';
    $env = [];
    if (is_file($envPath)) {
      foreach (@file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [] as $line) {
        $line = trim($line);
        if ($line === '' || $line[0] === '#') continue;
        $pos = strpos($line, '=');
        if ($pos === false) continue;
        $key = trim(substr($line, 0, $pos));
        $val = trim(substr($line, $pos + 1));
        $val = preg_replace('/^"|"$/', '', $val);
        $val = preg_replace("/^'|'$/", '', $val);
        $env[$key] = $val;
      }
    }
    $cfg = [
      'apiKey' => $env['FIREBASE_API_KEY'] ?? getenv('FIREBASE_API_KEY') ?? '',
      'authDomain' => $env['FIREBASE_AUTH_DOMAIN'] ?? getenv('FIREBASE_AUTH_DOMAIN') ?? '',
      'projectId' => $env['FIREBASE_PROJECT_ID'] ?? getenv('FIREBASE_PROJECT_ID') ?? '',
      'appId' => $env['FIREBASE_APP_ID'] ?? getenv('FIREBASE_APP_ID') ?? '',
      'storageBucket' => $env['FIREBASE_STORAGE_BUCKET'] ?? getenv('FIREBASE_STORAGE_BUCKET') ?? '',
      'messagingSenderId' => $env['FIREBASE_MESSAGING_SENDER_ID'] ?? getenv('FIREBASE_MESSAGING_SENDER_ID') ?? ''
    ];
    if (!empty($env['FIREBASE_MEASUREMENT_ID']) || getenv('FIREBASE_MEASUREMENT_ID')) {
      $cfg['measurementId'] = $env['FIREBASE_MEASUREMENT_ID'] ?? getenv('FIREBASE_MEASUREMENT_ID');
    }
    echo json_encode(['success' => true, 'config' => $cfg], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    return true;
  } catch (Throwable $e) {
    echo json_encode(['success' => false], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    return true;
  }
}

if ($uri === '/api/system/providers') {
  header('Content-Type: application/json');
  try {
    $base = dirname(__DIR__, 2);
    @require_once $base . DIRECTORY_SEPARATOR . 'backend' . DIRECTORY_SEPARATOR . 'api' . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
    $envPath = $base . DIRECTORY_SEPARATOR . 'backend' . DIRECTORY_SEPARATOR . 'api' . DIRECTORY_SEPARATOR . '.env';
    $env = [];
    if (is_file($envPath)) {
      foreach (@file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [] as $line) {
        $line = trim($line);
        if ($line === '' || $line[0] === '#') continue;
        $pos = strpos($line, '=');
        if ($pos === false) continue;
        $key = trim(substr($line, 0, $pos));
        $val = trim(substr($line, $pos + 1));
        $val = preg_replace('/^"|"$/', '', $val);
        $val = preg_replace("/^'|'$/", '', $val);
        $env[$key] = $val;
      }
    }
    $projectId = $env['FIREBASE_PROJECT_ID'] ?? getenv('FIREBASE_PROJECT_ID') ?? null;
    $primary = getenv('PRIMARY_API_URL') ?: '';
    if ($appEnv === 'production' && $primary !== '' && $httpOk(rtrim($primary,'/') . '/api/health')) {
      echo json_encode(['success' => true, 'providers' => [], 'source' => 'primary_short_circuit'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
      return true;
    }
    if ($projectId && class_exists('Google\\Auth\\ApplicationDefaultCredentials')) {
      $scopes = ['https://www.googleapis.com/auth/datastore'];
      $creds = \Google\Auth\ApplicationDefaultCredentials::getCredentials($scopes);
      $tokenInfo = $creds->fetchAuthToken();
      $accessToken = is_array($tokenInfo) ? ($tokenInfo['access_token'] ?? null) : null;
      if ($accessToken) {
        $url = 'https://firestore.googleapis.com/v1/projects/' . $projectId . '/databases/(default)/documents/providers?pageSize=100';
        $ctx = stream_context_create([
          'http' => [
            'method' => 'GET',
            'header' => [
              'Authorization: Bearer ' . $accessToken,
              'Accept: application/json'
            ],
            'ignore_errors' => true,
            'timeout' => 15
          ]
        ]);
        $raw = $fetchWithBackoff($url, ['method' => 'GET','header' => ['Authorization: Bearer ' . $accessToken, 'Accept: application/json'],'ignore_errors' => true,'timeout' => 8], 3, 250);
        $json = json_decode($raw ?: 'null', true);
        $items = [];
        $docs = is_array($json['documents'] ?? null) ? $json['documents'] : [];
        foreach ($docs as $d) {
          $namePath = (string)($d['name'] ?? '');
          $id = ($namePath && strpos($namePath, '/documents/providers/') !== false) ? substr($namePath, strrpos($namePath, '/') + 1) : null;
          $f = $d['fields'] ?? [];
          $nm = (string)($f['name']['stringValue'] ?? '');
          if ($nm === '' && $id) { $nm = $id; }
          $items[] = [
            'id' => $id,
            'name' => $nm,
            'rating' => (float)($f['rating']['doubleValue'] ?? ($f['rating']['integerValue'] ?? 0)),
            'distanceKm' => (float)($f['distanceKm']['doubleValue'] ?? ($f['distanceKm']['integerValue'] ?? 0))
          ];
        }
        echo json_encode(['success' => true, 'providers' => $items, 'source' => 'firestore'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        return true;
      }
    }
    echo json_encode(['success' => true, 'providers' => [], 'source' => 'fallback'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    return true;
  } catch (Throwable $e) {
    echo json_encode(['success' => false], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    return true;
  }
}

// Friendly routes for Services (guest-facing)
if ($uri === '/services/cleaning') {
  ensureUtf8HtmlHeader();
  require __DIR__ . '/Homecleaning/cleaning_services.php';
  return true;
}
if ($uri === '/services/indoor') {
  ensureUtf8HtmlHeader();
  require __DIR__ . '/Indoor_services/Handyman.php';
  return true;
}
if ($uri === '/services/outdoor') {
  ensureUtf8HtmlHeader();
  require __DIR__ . '/Outdoor_Services/Outdoor-Services.php';
  return true;
}

if (strpos($uri, '/api/system/bookings') === 0) {
  header('Content-Type: application/json');
  $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
  try {
    $base = dirname(__DIR__, 2);
    @require_once $base . DIRECTORY_SEPARATOR . 'backend' . DIRECTORY_SEPARATOR . 'api' . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
    $envPath = $base . DIRECTORY_SEPARATOR . 'backend' . DIRECTORY_SEPARATOR . 'api' . DIRECTORY_SEPARATOR . '.env';
    $env = [];
    if (is_file($envPath)) {
      foreach (@file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [] as $line) {
        $line = trim($line);
        if ($line === '' || $line[0] === '#') continue;
        $pos = strpos($line, '=');
        if ($pos === false) continue;
        $key = trim(substr($line, 0, $pos));
        $val = trim(substr($line, $pos + 1));
        $val = preg_replace('/^"|"$/', '', $val);
        $val = preg_replace("/^'|'$/", '', $val);
        $env[$key] = $val;
      }
    }
    $projectId = $env['FIREBASE_PROJECT_ID'] ?? getenv('FIREBASE_PROJECT_ID') ?? null;
    if (!$projectId || !class_exists('Google\\Auth\\ApplicationDefaultCredentials')) {
      echo json_encode(['success' => false, 'error' => 'unavailable'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
      return true;
    }
    $scopes = ['https://www.googleapis.com/auth/datastore'];
    $creds = \Google\Auth\ApplicationDefaultCredentials::getCredentials($scopes);
    $tokenInfo = $creds->fetchAuthToken();
    $accessToken = is_array($tokenInfo) ? ($tokenInfo['access_token'] ?? null) : null;
    if (!$accessToken) {
      echo json_encode(['success' => false, 'error' => 'no_token'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
      return true;
    }
    $baseUrl = 'https://firestore.googleapis.com/v1/projects/' . $projectId . '/databases/(default)/documents';
    // GET /api/system/bookings -> list
    if ($method === 'GET' && $uri === '/api/system/bookings') {
      $url = $baseUrl . '/bookings?pageSize=50';
      $ctx = stream_context_create(['http' => ['method' => 'GET', 'header' => ['Authorization: Bearer ' . $accessToken, 'Accept: application/json'], 'ignore_errors' => true, 'timeout' => 15]]);
      $raw = $fetchWithBackoff($url, ['method' => 'GET','header' => ['Authorization: Bearer ' . $accessToken, 'Accept: application/json'],'ignore_errors' => true,'timeout' => 8], 3, 250);
      $json = json_decode($raw ?: 'null', true);
      $out = [];
      $docs = is_array($json['documents'] ?? null) ? $json['documents'] : [];
      foreach ($docs as $d) {
        $namePath = (string)($d['name'] ?? '');
        $id = ($namePath && strpos($namePath, '/documents/bookings/') !== false) ? substr($namePath, strrpos($namePath, '/') + 1) : null;
        $f = $d['fields'] ?? [];
        $out[] = [
          'id' => $id,
          'provider_id' => (int)($f['provider_id']['integerValue'] ?? 0),
          'service_name' => (string)($f['service_name']['stringValue'] ?? ''),
          'scheduled_date' => (string)($f['scheduled_date']['stringValue'] ?? ''),
          'scheduled_time' => (string)($f['scheduled_time']['stringValue'] ?? ''),
          'price' => (float)($f['price']['doubleValue'] ?? ($f['price']['integerValue'] ?? 0))
        ];
      }
      echo json_encode(['success' => true, 'data' => $out], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
      return true;
    }
    // POST /api/system/bookings -> create
    if ($method === 'POST' && $uri === '/api/system/bookings') {
      $rawIn = file_get_contents('php://input');
      $in = json_decode($rawIn ?: 'null', true);
      if (!is_array($in)) { $in = []; }
      $fields = [];
      $fields['provider_id'] = ['integerValue' => (int)($in['provider_id'] ?? 0)];
      $fields['service_name'] = ['stringValue' => (string)($in['service_name'] ?? '')];
      $fields['scheduled_date'] = ['stringValue' => (string)($in['scheduled_date'] ?? '')];
      $fields['scheduled_time'] = ['stringValue' => (string)($in['scheduled_time'] ?? '')];
      $fields['address'] = ['stringValue' => (string)($in['address'] ?? '')];
      $fields['lat'] = ['doubleValue' => isset($in['lat']) ? (float)$in['lat'] : 0];
      $fields['lng'] = ['doubleValue' => isset($in['lng']) ? (float)$in['lng'] : 0];
      $fields['price'] = ['doubleValue' => isset($in['price']) ? (float)$in['price'] : 0];
      $arr = [];
      if (is_array($in['service_items'] ?? null)) {
        foreach ($in['service_items'] as $s) { $arr[] = ['stringValue' => (string)$s]; }
      }
      $fields['service_items'] = ['arrayValue' => ['values' => $arr]];
      $fields['notes'] = ['stringValue' => (string)($in['notes'] ?? '')];
      $body = json_encode(['fields' => $fields], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
      $url = $baseUrl . '/bookings';
      $ctx = stream_context_create(['http' => ['method' => 'POST', 'header' => ['Authorization: Bearer ' . $accessToken, 'Content-Type: application/json'], 'content' => $body, 'ignore_errors' => true, 'timeout' => 15]]);
      $raw = @file_get_contents($url, false, $ctx);
      $json = json_decode($raw ?: 'null', true);
      $namePath = is_array($json) ? (string)($json['name'] ?? '') : '';
      $id = ($namePath && strpos($namePath, '/documents/bookings/') !== false) ? substr($namePath, strrpos($namePath, '/') + 1) : null;
      echo json_encode(['success' => true, 'data' => ['id' => $id]], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
      return true;
    }
    // GET /api/system/bookings/{id}
    if ($method === 'GET' && preg_match('#^/api/system/bookings/([^/]+)$#', $uri, $m)) {
      $id = $m[1];
      $url = $baseUrl . '/bookings/' . rawurlencode($id);
      $ctx = stream_context_create(['http' => ['method' => 'GET', 'header' => ['Authorization: Bearer ' . $accessToken, 'Accept: application/json'], 'ignore_errors' => true, 'timeout' => 15]]);
      $raw = @file_get_contents($url, false, $ctx);
      $d = json_decode($raw ?: 'null', true);
      $f = is_array($d['fields'] ?? null) ? $d['fields'] : [];
      $out = [
        'id' => $id,
        'provider_id' => (int)($f['provider_id']['integerValue'] ?? 0),
        'service_name' => (string)($f['service_name']['stringValue'] ?? ''),
        'scheduled_date' => (string)($f['scheduled_date']['stringValue'] ?? ''),
        'scheduled_time' => (string)($f['scheduled_time']['stringValue'] ?? ''),
        'address' => (string)($f['address']['stringValue'] ?? ''),
        'price' => (float)($f['price']['doubleValue'] ?? ($f['price']['integerValue'] ?? 0)),
        'status' => (string)($f['status']['stringValue'] ?? 'pending')
      ];
      echo json_encode(['success' => true, 'data' => $out], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
      return true;
    }
    // POST /api/system/bookings/{id}/status
    if ($method === 'POST' && preg_match('#^/api/system/bookings/([^/]+)/status$#', $uri, $m)) {
      $id = $m[1];
      $in = json_decode(file_get_contents('php://input') ?: 'null', true);
      $status = (string)($in['status'] ?? 'pending');
      $body = json_encode(['fields' => ['status' => ['stringValue' => $status]]], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
      $url = $baseUrl . '/bookings/' . rawurlencode($id);
      $ctx = stream_context_create(['http' => ['method' => 'PATCH', 'header' => ['Authorization: Bearer ' . $accessToken, 'Content-Type: application/json'], 'content' => $body, 'ignore_errors' => true, 'timeout' => 15]]);
      @file_get_contents($url, false, $ctx);
      echo json_encode(['success' => true], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
      return true;
    }
    // POST /api/system/bookings/{id}/cancel
    if ($method === 'POST' && preg_match('#^/api/system/bookings/([^/]+)/cancel$#', $uri, $m)) {
      $id = $m[1];
      $in = json_decode(file_get_contents('php://input') ?: 'null', true);
      $reason = (string)($in['reason'] ?? '');
      $body = json_encode(['fields' => ['status' => ['stringValue' => 'cancelled'], 'cancel_reason' => ['stringValue' => $reason]]], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
      $url = $baseUrl . '/bookings/' . rawurlencode($id);
      $ctx = stream_context_create(['http' => ['method' => 'PATCH', 'header' => ['Authorization: Bearer ' . $accessToken, 'Content-Type: application/json'], 'content' => $body, 'ignore_errors' => true, 'timeout' => 15]]);
      @file_get_contents($url, false, $ctx);
      echo json_encode(['success' => true], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
      return true;
    }
    // POST /api/system/bookings/{id}/rate
    if ($method === 'POST' && preg_match('#^/api/system/bookings/([^/]+)/rate$#', $uri, $m)) {
      $id = $m[1];
      $in = json_decode(file_get_contents('php://input') ?: 'null', true);
      $rating = isset($in['rating']) ? (float)$in['rating'] : 0;
      $body = json_encode(['fields' => ['rating' => ['doubleValue' => $rating]]], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
      $url = $baseUrl . '/bookings/' . rawurlencode($id);
      $ctx = stream_context_create(['http' => ['method' => 'PATCH', 'header' => ['Authorization: Bearer ' . $accessToken, 'Content-Type: application/json'], 'content' => $body, 'ignore_errors' => true, 'timeout' => 15]]);
      @file_get_contents($url, false, $ctx);
      echo json_encode(['success' => true], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
      return true;
    }
    // POST /api/system/bookings/{id}/return -> create record in returns
    if ($method === 'POST' && preg_match('#^/api/system/bookings/([^/]+)/return$#', $uri, $m)) {
      $id = $m[1];
      $in = json_decode(file_get_contents('php://input') ?: 'null', true);
      $issues = is_array($in['issues'] ?? null) ? $in['issues'] : [];
      $notes = (string)($in['notes'] ?? '');
      $fields = [
        'booking_id' => ['stringValue' => $id],
        'notes' => ['stringValue' => $notes],
      ];
      $arr = [];
      foreach ($issues as $s) { $arr[] = ['stringValue' => (string)$s]; }
      $fields['issues'] = ['arrayValue' => ['values' => $arr]];
      $body = json_encode(['fields' => $fields], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
      $url = $baseUrl . '/returns';
      $ctx = stream_context_create(['http' => ['method' => 'POST', 'header' => ['Authorization: Bearer ' . $accessToken, 'Content-Type: application/json'], 'content' => $body, 'ignore_errors' => true, 'timeout' => 15]]);
      $raw = @file_get_contents($url, false, $ctx);
      $j = json_decode($raw ?: 'null', true);
      $namePath = is_array($j) ? (string)($j['name'] ?? '') : '';
      $retId = ($namePath && strpos($namePath, '/documents/returns/') !== false) ? substr($namePath, strrpos($namePath, '/') + 1) : null;
      echo json_encode(['success' => true, 'data' => ['id' => $retId]], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
      return true;
    }
    // GET /api/system/bookings/returns
    if ($method === 'GET' && $uri === '/api/system/bookings/returns') {
      $url = $baseUrl . '/returns?pageSize=50';
      $ctx = stream_context_create(['http' => ['method' => 'GET', 'header' => ['Authorization: Bearer ' . $accessToken, 'Accept: application/json'], 'ignore_errors' => true, 'timeout' => 15]]);
      $raw = @file_get_contents($url, false, $ctx);
      $json = json_decode($raw ?: 'null', true);
      $out = [];
      $docs = is_array($json['documents'] ?? null) ? $json['documents'] : [];
      foreach ($docs as $d) {
        $f = $d['fields'] ?? [];
        $out[] = [
          'booking' => [
            'service_name' => '',
            'scheduled_date' => '',
            'scheduled_time' => '',
            'address' => ''
          ],
          'issues' => [],
          'notes' => (string)($f['notes']['stringValue'] ?? '')
        ];
      }
      echo json_encode(['success' => true, 'data' => $out], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
      return true;
    }
    echo json_encode(['success' => false, 'error' => 'method_not_allowed'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    return true;
  } catch (Throwable $e) {
    echo json_encode(['success' => false, 'error' => 'failed'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    return true;
  }
}

// Stream static files with appropriate Content-Type
function serveStatic($file) {
  $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
  $types = [
    'css' => 'text/css',
    'js' => 'application/javascript',
    'png' => 'image/png',
    'jpg' => 'image/jpeg',
    'jpeg' => 'image/jpeg',
    'gif' => 'image/gif',
    'svg' => 'image/svg+xml',
    'ico' => 'image/x-icon',
    'woff' => 'font/woff',
    'woff2' => 'font/woff2',
    'ttf' => 'font/ttf',
    'map' => 'application/json'
  ];
  if (isset($types[$ext]) && !headers_sent()) {
    header('Content-Type: ' . $types[$ext]);
  }
  readfile($file);
}

// Friendly route aliases to legacy PHP files for local dev
$aliases = [
  '/homepage.php' => '/client/homepage.php',
  '/About.php' => '/client/About.php',
  '/Contact.php' => '/client/contact_client.php',
  '/services' => '/client/services.php',
  '/login' => '/login_sign up/login-firebase.php',
  '/signup' => '/login_sign up/sign up.php',
  '/booking/choose-sp' => '/booking_process/choose_sp.php',
  '/booking/location' => '/booking_process/booking_location.php',
  '/booking/schedule' => '/booking_process/booking_schedule.php',
  '/booking/confirm' => '/booking_process/confirm_booking.php',
  '/booking/overview' => '/booking_process/booking_overview.php',
  '/services/cleaning' => '/Homecleaning/cleaning_services.php',
  '/services/cleaning/ac' => '/Homecleaning/aircon.php',
  '/services/cleaning/ac-deep' => '/Homecleaning/aircon_deep_clean.php',
  // Additional category aliases
  '/services/outdoor' => '/Outdoor_Services/Outdoor-Services.php',
  '/services/repairs' => '/Indoor_services/Handyman.php',
  '/services/beauty' => '/beauty_services/packages_services.php',
  '/services/wellness' => '/wellness_services/packages.php',
  '/services/tech' => '/tech_gadget/mobile_phone.php',
  // Account pages
  '/account' => '/my_account/my_account.php',
  '/account/address' => '/my_account/account_address.php',
  '/account/privacy' => '/my_account/privacy_settings.php',
  '/account/change-password' => '/my_account/change_password.php',
  '/account/current-password' => '/my_account/current_password.php',
  '/account/password-saved' => '/my_account/password_saved.php',
  '/account/verification-code' => '/my_account/verification_code.php',
  '/account/voucher' => '/my_account/my_voucher.php',
  '/account/referral' => '/my_account/account_referral.php',
  '/account/referral-success' => '/my_account/referral_success.php',
  '/account/connect' => '/my_account/connect_haustap.php',
    '/account/terms' => '/client/terms.php',
  '/about' => '/client/About.php',
];
if (isset($aliases[$uri])) {
  $aliasPath = $docRoot . str_replace('/', DIRECTORY_SEPARATOR, $aliases[$uri]);
  if (is_file($aliasPath)) {
    $ext = strtolower(pathinfo($aliasPath, PATHINFO_EXTENSION));
    if ($ext === 'php') {
      // Inject <base> for aliased pages to keep asset paths correct
      $baseDir = dirname($aliases[$uri]);
      $baseDir = trim($baseDir, '/');
      $baseHref = $baseDir === '' || $baseDir === '.' ? '/' : ('/' . $baseDir . '/');
      ensureUtf8HtmlHeader();
      ob_start();
      @chdir(dirname($aliasPath));
      require $aliasPath;
      $html = ob_get_clean();
      if (stripos($html, '<base ') === false) {
        $html = preg_replace('/<head(\s*>)/i', '<head$1<base href="' . $baseHref . '">', $html, 1);
      }
      echo $html;
      return true;
    }
    serveStatic($aliasPath);
    return true;
  }
}

if ($uri === '/api/system/categories') {
  header('Content-Type: application/json');
  $fallback = [
    [ 'slug' => 'cleaning', 'name' => 'Cleaning Services', 'description' => 'Professional and reliable cleaning to keep your space at its best.' ],
    [ 'slug' => 'outdoor', 'name' => 'Outdoor Services', 'description' => 'Expert gardening and outdoor care services for beautiful spaces.' ],
    [ 'slug' => 'repairs', 'name' => 'Home Repairs', 'description' => 'Quick and reliable repairs for home maintenance needs.' ],
    [ 'slug' => 'beauty', 'name' => 'Beauty Services', 'description' => 'Salon-quality beauty services from certified professionals.' ],
    [ 'slug' => 'wellness', 'name' => 'Wellness Services', 'description' => 'Relaxing wellness and self-care services at home.' ],
    [ 'slug' => 'tech', 'name' => 'Tech & Gadget Services', 'description' => 'Help with device setup, repairs, and smart home installations.' ],
  ];
  try {
    $base = dirname(__DIR__, 2);
    @require_once $base . DIRECTORY_SEPARATOR . 'backend' . DIRECTORY_SEPARATOR . 'api' . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
    $envPath = $base . DIRECTORY_SEPARATOR . 'backend' . DIRECTORY_SEPARATOR . 'api' . DIRECTORY_SEPARATOR . '.env';
    $env = [];
    if (is_file($envPath)) {
      foreach (@file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [] as $line) {
        $line = trim($line);
        if ($line === '' || $line[0] === '#') continue;
        $pos = strpos($line, '=');
        if ($pos === false) continue;
        $key = trim(substr($line, 0, $pos));
        $val = trim(substr($line, $pos + 1));
        $val = preg_replace('/^"|"$/', '', $val);
        $val = preg_replace("/^'|'$/", '', $val);
        $env[$key] = $val;
      }
    }
    $projectId = $env['FIREBASE_PROJECT_ID'] ?? getenv('FIREBASE_PROJECT_ID') ?? null;
    if ($projectId && class_exists('Google\\Auth\\ApplicationDefaultCredentials')) {
      $scopes = ['https://www.googleapis.com/auth/datastore'];
      $creds = \Google\Auth\ApplicationDefaultCredentials::getCredentials($scopes);
      $tokenInfo = $creds->fetchAuthToken();
      $accessToken = is_array($tokenInfo) ? ($tokenInfo['access_token'] ?? null) : null;
      if ($accessToken) {
        $url = 'https://firestore.googleapis.com/v1/projects/' . $projectId . '/databases/(default)/documents/categories?pageSize=200';
        $ctx = stream_context_create([
          'http' => [
            'method' => 'GET',
            'header' => [
              'Authorization: Bearer ' . $accessToken,
              'Accept: application/json'
            ],
            'ignore_errors' => true,
            'timeout' => 15
          ]
        ]);
        $raw = $fetchWithBackoff($url, ['method' => 'GET','header' => ['Authorization: Bearer ' . $accessToken, 'Accept: application/json'],'ignore_errors' => true,'timeout' => 8], 3, 250);
        $json = json_decode($raw ?: 'null', true);
        $items = [];
        $docs = is_array($json['documents'] ?? null) ? $json['documents'] : [];
        foreach ($docs as $d) {
          $f = $d['fields'] ?? [];
          $slug = (string)($f['slug']['stringValue'] ?? '');
          $name = (string)($f['name']['stringValue'] ?? '');
          $desc = (string)($f['description']['stringValue'] ?? '');
          if ($slug === '') {
            $slug = preg_replace('/[^a-z0-9]+/', '-', strtolower($name));
            $slug = trim($slug, '-');
          }
          if ($name !== '') {
            $items[] = ['slug' => $slug ?: $name, 'name' => $name, 'description' => $desc];
          }
        }
        if (!empty($items)) {
          echo json_encode(['success' => true, 'categories' => $items, 'source' => 'firestore'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
          return true;
        }
      }
    }
    $dbFile = $ADMIN_APP_PATH . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'db.php';
    if (is_file($dbFile)) {
      require_once $dbFile;
      $pdo = get_db();
      $stmt = $pdo->query('SELECT service_categories FROM providers WHERE service_categories IS NOT NULL');
      $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
      $unique = [];
      foreach ($rows as $r) {
        $cats = json_decode((string)($r['service_categories'] ?? '[]'), true);
        if (!is_array($cats)) continue;
        foreach ($cats as $c) {
          $name = '';
          $desc = '';
          if (is_string($c)) { $name = trim($c); }
          elseif (is_array($c)) { $name = trim((string)($c['name'] ?? '')); $desc = trim((string)($c['description'] ?? '')); }
          if ($name === '') continue;
          $slug = preg_replace('/[^a-z0-9]+/', '-', strtolower($name));
          $slug = trim($slug, '-');
          if ($slug === '') continue;
          if (!isset($unique[$slug])) { $unique[$slug] = ['slug' => $slug, 'name' => $name, 'description' => $desc]; }
        }
      }
      $result = array_values($unique);
      if (empty($result)) $result = $fallback;
      echo json_encode(['success' => true, 'categories' => $result, 'source' => empty($unique) ? 'fallback' : 'db'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
      return true;
    }
    echo json_encode(['success' => true, 'categories' => $fallback, 'source' => 'fallback'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    return true;
  } catch (Throwable $e) {
    echo json_encode(['success' => true, 'categories' => $fallback, 'source' => 'fallback'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    return true;
  }
}

// If the requested file exists (including .php), let the server handle it
if (is_file($fullPath)) {
  return false;
}

// Admin alias: serve admin app under /admin/* even from site router
if (strpos($uri, '/admin/') === 0) {
  $prefix = '/admin/';
  $rel = substr($uri, strlen($prefix));
  if ($rel === false || $rel === '') { $rel = 'dashboard.php'; }
  $candidate = $ADMIN_APP_PATH . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $rel);

  // Direct file
  if (is_file($candidate)) {
    $ext = strtolower(pathinfo($candidate, PATHINFO_EXTENSION));
    if ($ext === 'php') {
      ensureUtf8HtmlHeader();
      @chdir(dirname($candidate));
      require $candidate;
      return true;
    }
    serveStatic($candidate);
    return true;
  }
  // Clean path to PHP file (e.g., /admin/login)
  if (is_file($candidate . '.php')) {
    ensureUtf8HtmlHeader();
    @chdir(dirname($candidate . '.php'));
    require $candidate . '.php';
    return true;
  }
  // Directory index
  if (is_dir($candidate)) {
    $index = $candidate . DIRECTORY_SEPARATOR . 'index.php';
    if (is_file($index)) {
      ensureUtf8HtmlHeader();
      @chdir(dirname($index));
      require $index;
      return true;
    }
  }
}

// Explicit routing for mock-api: map directories to their index.php
if ($enableMock && strpos($uri, '/mock-api/') === 0) {
  // Special-case: forward any /mock-api/bookings/* request to bookings/index.php
  if (strpos($uri, '/mock-api/bookings/') === 0) {
    ensureUtf8HtmlHeader();
    require __DIR__ . DIRECTORY_SEPARATOR . 'mock-api' . DIRECTORY_SEPARATOR . 'bookings' . DIRECTORY_SEPARATOR . 'index.php';
    return true;
  }
  if (is_dir($fullPath)) {
    $index = $fullPath . DIRECTORY_SEPARATOR . 'index.php';
    if (is_file($index)) {
      ensureUtf8HtmlHeader();
      require $index;
      return true;
    }
  }
  // If a direct PHP file under mock-api is targeted
  if (is_file($fullPath . '.php')) {
    ensureUtf8HtmlHeader();
    require $fullPath . '.php';
    return true;
  }
}

// Serve static assets directly
if (preg_match('/\.(?:png|jpg|jpeg|gif|svg|css|js|ico|woff2?|ttf|map)$/i', $uri)) {
  if (is_file($fullPath)) {
    return false; // let built-in server handle
  }
}

// If a directory is requested, load its index.php if present
if (is_dir($fullPath)) {
  $index = $fullPath . DIRECTORY_SEPARATOR . 'index.php';
  if (is_file($index)) {
    ensureUtf8HtmlHeader();
    require $index;
    return true;
  }
}

// If a PHP file exists for the requested path, run it
if (is_file($fullPath) && substr($fullPath, -4) === '.php') {
  ensureUtf8HtmlHeader();
  require $fullPath;
  return true;
}

// Fallback to project root index.php
http_response_code(404);
ensureUtf8HtmlHeader();
echo 'Not Found';
$httpOk = function(string $url, int $timeoutSec = 2): bool {
  $ctx = stream_context_create(['http' => ['method' => 'GET','ignore_errors' => true,'timeout' => $timeoutSec]]);
  $raw = @file_get_contents($url, false, $ctx);
  return $raw !== false;
};

$fetchWithBackoff = function(string $url, array $httpOpts, int $attempts = 3, int $initialMs = 250): string|false {
  $delay = $initialMs;
  for ($i = 0; $i < $attempts; $i++) {
    $ctx = stream_context_create(['http' => $httpOpts]);
    $raw = @file_get_contents($url, false, $ctx);
    if ($raw !== false && $raw !== '') return $raw;
    usleep($delay * 1000);
    $delay = min($delay * 2, 2000);
  }
  return false;
};
