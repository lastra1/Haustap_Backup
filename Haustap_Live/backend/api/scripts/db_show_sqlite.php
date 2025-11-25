<?php
// Lightweight SQLite browser: lists tables, columns, and row counts.

$dbPath = __DIR__ . '/../database/database.sqlite';
if (!file_exists($dbPath)) {
    fwrite(STDERR, "SQLite file not found: {$dbPath}\n");
    exit(1);
}

$pdo = new PDO('sqlite:' . $dbPath);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$tablesStmt = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%' ORDER BY name");
$tables = $tablesStmt->fetchAll(PDO::FETCH_COLUMN);

$out = [
    'driver' => 'sqlite',
    'database' => realpath($dbPath),
    'tables' => [],
];

foreach ($tables as $t) {
    $colsStmt = $pdo->query("PRAGMA table_info('" . str_replace("'", "''", $t) . "')");
    $cols = array_map(function ($row) { return $row['name']; }, $colsStmt->fetchAll(PDO::FETCH_ASSOC));
    $countStmt = $pdo->query("SELECT COUNT(*) AS c FROM '" . str_replace("'", "''", $t) . "'");
    $count = (int)$countStmt->fetch(PDO::FETCH_ASSOC)['c'];
    $out['tables'][] = [ 'name' => $t, 'columns' => $cols, 'rows' => $count ];
}

echo json_encode($out, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES), "\n";