<?php
declare(strict_types=1);

// Base paths for the two existing apps
define('BASE_PATH', __DIR__);
define('SITE_APP_PATH', BASE_PATH . DIRECTORY_SEPARATOR . 'Haustap_Capstone-Haustap_Connecting' . DIRECTORY_SEPARATOR . 'Haustap_Capstone-Haustap_Connecting');
$adminCanonical = BASE_PATH . DIRECTORY_SEPARATOR . 'admin_haustap' . DIRECTORY_SEPARATOR . 'admin_haustap';
$adminNew = BASE_PATH . DIRECTORY_SEPARATOR . 'admin_haustap' . DIRECTORY_SEPARATOR . 'old_admin';
$adminLegacy = BASE_PATH . DIRECTORY_SEPARATOR . 'external_admin_cmir';
define('ADMIN_APP_PATH', is_dir($adminNew) ? $adminNew : (is_dir($adminCanonical) ? $adminCanonical : $adminLegacy));

// Simple PSR-4 style autoloader for Core/ and App/
spl_autoload_register(function ($class) {
    $prefixes = [
        'Core\\' => BASE_PATH . '/core/',
        'App\\'  => BASE_PATH . '/app/',
    ];
    foreach ($prefixes as $prefix => $dir) {
        $len = strlen($prefix);
        if (strncmp($class, $prefix, $len) === 0) {
            $relative = str_replace('\\', '/', substr($class, $len));
            $file = $dir . $relative . '.php';
            if (is_file($file)) {
                require $file;
                return;
            }
        }
    }
});

// Environment loader: load base .env then environment-specific overrides
function loadEnvFile(string $file): void {
    if (!is_file($file)) { return; }
    foreach (file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) { continue; }
        [$k, $v] = array_map('trim', explode('=', $line, 2));
        $_ENV[$k] = $v;
        putenv("$k=$v");
    }
}

// Load base .env
loadEnvFile(BASE_PATH . '/.env');

// Load environment-specific .env.{APP_ENV} if present (overrides base)
$appEnv = getenv('APP_ENV');
if (!$appEnv || $appEnv === '') { $appEnv = 'development'; }
loadEnvFile(BASE_PATH . '/.env.' . $appEnv);

// Error reporting based on APP_DEBUG
$appDebug = strtolower((string) (getenv('APP_DEBUG') ?: 'true'));
if ($appDebug === '1' || $appDebug === 'true' || $appDebug === 'yes') {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT);
    ini_set('display_errors', '0');
}

// Ensure error log directory exists and set error_log
$logDir = BASE_PATH . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'logs';
if (!is_dir($logDir)) {
    @mkdir($logDir, 0777, true);
}
ini_set('error_log', $logDir . DIRECTORY_SEPARATOR . 'php-error.log');

set_error_handler(function($severity, $message, $file = null, $line = null) {
    $payload = json_encode(['level'=>'error','type'=>'php_error','message'=>$message,'file'=>$file,'line'=>$line,'request_id'=>($_SERVER['HT_REQUEST_ID'] ?? null)], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    @error_log($payload);
});
set_exception_handler(function($e) {
    $payload = json_encode(['level'=>'error','type'=>'exception','message'=>$e->getMessage(),'file'=>$e->getFile(),'line'=>$e->getLine(),'request_id'=>($_SERVER['HT_REQUEST_ID'] ?? null)], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    @error_log($payload);
    $debug = strtolower((string) (getenv('APP_DEBUG') ?: 'true'));
    if ($debug !== '1' && $debug !== 'true' && $debug !== 'yes') {
        if (!headers_sent()) { http_response_code(500); }
        echo 'Internal Server Error';
    }
});
$rid = null;
try { $rid = bin2hex(random_bytes(16)); } catch (Throwable $e) { $rid = uniqid('req_', true); }
$_SERVER['HT_REQUEST_ID'] = $rid;
if (!headers_sent()) { header('X-Request-Id: ' . $rid); }
