<?php
// Simple bootstrap to ensure the MySQL database exists using values from .env

$envPath = __DIR__ . '/../.env';
$env = [];
if (file_exists($envPath)) {
    foreach (file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        if ($line === '' || $line[0] === '#' || strpos($line, '=') === false) continue;
        [$k, $v] = array_map('trim', explode('=', $line, 2));
        $v = trim($v, "\"'" );
        $env[$k] = $v;
    }
}

$host = $env['DB_HOST'] ?? null;
$port = $env['DB_PORT'] ?? null;
$user = $env['DB_USERNAME'] ?? null;
$pass = $env['DB_PASSWORD'] ?? null;
$db   = $env['DB_DATABASE'] ?? null;
if (!$host || !$port || !$user || $pass === null || !$db) {
    echo json_encode(['ok' => false, 'error' => 'missing_env', 'required' => ['DB_HOST','DB_PORT','DB_USERNAME','DB_PASSWORD','DB_DATABASE']], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL;
    exit(1);
}

$result = [ 'host' => $host, 'port' => $port, 'user' => $user, 'database' => $db ];

try {
    $dsn = sprintf('mysql:host=%s;port=%s;charset=utf8mb4', $host, $port);
    $pdo = new PDO($dsn, $user, $pass, [ PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION ]);
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$db}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $result['created_or_exists'] = true;
    $result['ok'] = true;
} catch (Throwable $e) {
    $result['ok'] = false;
    $result['error'] = $e->getMessage();
}

echo json_encode($result, JSON_PRETTY_PRINT) . PHP_EOL;