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
    $root = dirname(dirname(__DIR__), 3);
    $env = parse_env_file([
        $root . DIRECTORY_SEPARATOR . 'Haustap_Live' . DIRECTORY_SEPARATOR . 'backend' . DIRECTORY_SEPARATOR . '.env',
        $root . DIRECTORY_SEPARATOR . 'Haustap_Live' . DIRECTORY_SEPARATOR . 'backend' . DIRECTORY_SEPARATOR . '.env.development',
        $root . DIRECTORY_SEPARATOR . 'Haustap_Live' . DIRECTORY_SEPARATOR . 'backend' . DIRECTORY_SEPARATOR . '.env.development.example',
        $root . DIRECTORY_SEPARATOR . 'Haustap_Live' . DIRECTORY_SEPARATOR . '.env',
        $root . DIRECTORY_SEPARATOR . 'Haustap_Live' . DIRECTORY_SEPARATOR . '.env.development.example',
    ]);

    $driver = strtolower($env['DB_CONNECTION'] ?? 'mysql');

    $makeSqlite = function () use ($root) {
        $sqlite = $root . DIRECTORY_SEPARATOR . 'Haustap_Live' . DIRECTORY_SEPARATOR . 'backend' . DIRECTORY_SEPARATOR . 'api' . DIRECTORY_SEPARATOR . 'database' . DIRECTORY_SEPARATOR . 'database.sqlite';
        $dir = dirname($sqlite);
        if (!is_dir($dir)) mkdir($dir, 0777, true);
        $pdo = new PDO('sqlite:' . $sqlite);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    };

    if ($driver === 'mysql') {
        $host = $env['DB_HOST'] ?? ($env['DB_HOSTNAME'] ?? null);
        $port = $env['DB_PORT'] ?? null;
        $db   = $env['DB_DATABASE'] ?? ($env['DB_NAME'] ?? null);
        $user = $env['DB_USERNAME'] ?? ($env['DB_USER'] ?? null);
        $pass = $env['DB_PASSWORD'] ?? null;
        if (!$host || !$port || !$db || !$user || $pass === null) {
            return $makeSqlite();
        }
        $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', $host, $port, $db);
        try {
            $pdo = new PDO($dsn, $user, $pass, [ PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION ]);
            return $pdo;
        } catch (Throwable $e) {
            // Fallback to SQLite when MySQL is unreachable or misconfigured
            $pdo = $makeSqlite();
            return $pdo;
        }
    }

    $pdo = $makeSqlite();
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
