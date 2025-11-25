<?php
// Lightweight DB introspection script for local development.
// Prints tables, columns, and row counts from the current SQLite database.

// Resolve path to SQLite file
$dbPath = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'database' . DIRECTORY_SEPARATOR . 'database.sqlite';

if (!is_file($dbPath)) {
    fwrite(STDERR, "SQLite DB file not found: {$dbPath}\n");
    exit(1);
}

try {
    $pdo = new PDO('sqlite:' . $dbPath);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Throwable $e) {
    fwrite(STDERR, "Failed to open SQLite DB: " . $e->getMessage() . "\n");
    exit(1);
}

// List tables
$tables = [];
try {
    $stmt = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' ORDER BY name");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $tables[] = $row['name'];
    }
} catch (Throwable $e) {
    fwrite(STDERR, "Failed to list tables: " . $e->getMessage() . "\n");
    exit(1);
}

$result = [];
foreach ($tables as $t) {
    // Skip internal tables sometimes created by SQLite
    if ($t === 'sqlite_sequence') { continue; }

    $info = [
        'columns' => [],
        'count' => null,
        'sample' => [],
    ];

    // Columns
    try {
        $cols = $pdo->query("PRAGMA table_info('" . str_replace("'", "''", $t) . "')");
        while ($c = $cols->fetch(PDO::FETCH_ASSOC)) {
            $info['columns'][] = [
                'name' => $c['name'] ?? '',
                'type' => $c['type'] ?? '',
                'notnull' => (int)($c['notnull'] ?? 0) === 1,
                'pk' => (int)($c['pk'] ?? 0) === 1,
            ];
        }
    } catch (Throwable $e) {
        $info['columns_error'] = $e->getMessage();
    }

    // Row count
    try {
        $cnt = $pdo->query("SELECT COUNT(*) AS cnt FROM '" . str_replace("'", "''", $t) . "'");
        $info['count'] = (int)($cnt->fetch(PDO::FETCH_ASSOC)['cnt'] ?? 0);
    } catch (Throwable $e) {
        $info['count_error'] = $e->getMessage();
    }

    // Sample rows
    try {
        $sample = $pdo->query("SELECT * FROM '" . str_replace("'", "''", $t) . "' LIMIT 5");
        while ($r = $sample->fetch(PDO::FETCH_ASSOC)) {
            $info['sample'][] = $r;
        }
    } catch (Throwable $e) {
        $info['sample_error'] = $e->getMessage();
    }

    $result[$t] = $info;
}

// Pretty-print JSON result
echo json_encode(['database' => $dbPath, 'tables' => $result], JSON_PRETTY_PRINT), "\n";