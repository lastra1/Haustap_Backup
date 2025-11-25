<?php
// Simple DB inspection script: prints driver, database, tables, columns, and row counts.

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->boot();

$default = Config::get('database.default');
$conn = DB::connection($default);
$driver = $conn->getDriverName();
$databaseName = null;
try { $databaseName = method_exists($conn, 'getDatabaseName') ? $conn->getDatabaseName() : null; } catch (Throwable $e) {}

$tables = [];
try {
    if ($driver === 'sqlite') {
        $rows = DB::select("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%' ORDER BY name");
        $tables = array_map(fn($r) => $r->name, $rows);
    } elseif ($driver === 'mysql' || $driver === 'mariadb') {
        $rows = DB::select('SHOW TABLES');
        $tables = array_map(function ($r) { return array_values((array)$r)[0]; }, $rows);
    } elseif ($driver === 'pgsql') {
        $rows = DB::select("SELECT tablename FROM pg_tables WHERE schemaname='public' ORDER BY tablename");
        $tables = array_map(fn($r) => $r->tablename, $rows);
    } else {
        $tables = [];
    }
} catch (Throwable $e) {
    $tables = [];
}

$out = [
    'driver' => $driver,
    'database' => $databaseName,
    'tables' => [],
];

foreach ($tables as $t) {
    try {
        $columns = Schema::hasTable($t) ? Schema::getColumnListing($t) : [];
    } catch (Throwable $e) { $columns = []; }
    try { $count = DB::table($t)->count(); } catch (Throwable $e) { $count = null; }
    $out['tables'][] = [ 'name' => $t, 'columns' => $columns, 'rows' => $count ];
}

echo json_encode($out, JSON_PRETTY_PRINT), "\n";