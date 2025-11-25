<?php
function parse_env_file(array $paths): array {
    $env = [];
    foreach ($paths as $p) {
        if (!is_file($p)) continue;
        foreach (file($p, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
            if ($line === '' || $line[0] === '#' || strpos($line, '=') === false) continue;
            [$k, $v] = array_map('trim', explode('=', $line, 2));
            $env[$k] = trim($v, "\"'" );
        }
        break;
    }
    return $env;
}

function get_db(): PDO {
    static $pdo = null;
    if ($pdo instanceof PDO) return $pdo;
    $root = dirname(dirname(__DIR__), 2);
    $env = parse_env_file([
        $root . DIRECTORY_SEPARATOR . '.env',
        $root . DIRECTORY_SEPARATOR . '.env.development',
        $root . DIRECTORY_SEPARATOR . '.env.development.example',
        $root . DIRECTORY_SEPARATOR . 'backend' . DIRECTORY_SEPARATOR . '.env',
        $root . DIRECTORY_SEPARATOR . 'backend' . DIRECTORY_SEPARATOR . '.env.development',
        $root . DIRECTORY_SEPARATOR . 'backend' . DIRECTORY_SEPARATOR . '.env.development.example',
    ]);

    $driver = strtolower($env['DB_CONNECTION'] ?? 'sqlite');
    if ($driver === 'mysql') {
        $host = $env['DB_HOST'] ?? '127.0.0.1';
        $port = $env['DB_PORT'] ?? '3306';
        $db   = $env['DB_DATABASE'] ?? 'haustap';
        $user = $env['DB_USERNAME'] ?? 'root';
        $pass = $env['DB_PASSWORD'] ?? '';
        $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', $host, $port, $db);
        $pdo = new PDO($dsn, $user, $pass, [ PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION ]);
        return $pdo;
    }

    $candidates = [
        $root . DIRECTORY_SEPARATOR . 'backend' . DIRECTORY_SEPARATOR . 'api' . DIRECTORY_SEPARATOR . 'database' . DIRECTORY_SEPARATOR . 'database.sqlite',
        $root . DIRECTORY_SEPARATOR . 'backend' . DIRECTORY_SEPARATOR . 'database' . DIRECTORY_SEPARATOR . 'database.sqlite',
        __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'admin.sqlite',
    ];
    $path = null;
    foreach ($candidates as $c) { if ($path === null && $c) $path = $c; }
    $dir = dirname($path);
    if (!is_dir($dir)) mkdir($dir, 0777, true);
    $pdo = new PDO('sqlite:' . $path);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $pdo;
}

function table_exists(PDO $db, string $table): bool {
    $driver = $db->getAttribute(PDO::ATTR_DRIVER_NAME);
    if ($driver === 'mysql') {
        $stmt = $db->prepare('SHOW TABLES LIKE ?');
        $stmt->execute([$table]);
        return (bool)$stmt->fetchColumn();
    }
    $stmt = $db->prepare("SELECT name FROM sqlite_master WHERE type='table' AND name = ?");
    $stmt->execute([$table]);
    return (bool)$stmt->fetchColumn();
}

function column_exists(PDO $db, string $table, string $column): bool {
    $driver = $db->getAttribute(PDO::ATTR_DRIVER_NAME);
    if ($driver === 'mysql') {
        $stmt = $db->prepare('SHOW COLUMNS FROM `' . str_replace('`','``',$table) . '` LIKE ?');
        $stmt->execute([$column]);
        return (bool)$stmt->fetch(PDO::FETCH_ASSOC);
    }
    $stmt = $db->prepare('PRAGMA table_info(' . $table . ')');
    foreach ($stmt->execute() ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [] as $c) {
        if (($c['name'] ?? '') === $column) return true;
    }
    return false;
}

?>
