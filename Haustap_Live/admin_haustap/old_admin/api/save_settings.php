<?php
header('Content-Type: application/json');

require_once dirname(__DIR__) . '/includes/db.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        exit;
    }
    $name = trim((string)($_POST['system_name'] ?? ''));
    $email = trim((string)($_POST['contact_email'] ?? ''));
    $renewal = (int)($_POST['renewal_cycle'] ?? 0);
    $voucher = (int)($_POST['voucher_deduction'] ?? 0);
    $sendEmail = (int)($_POST['send_email_notifications'] ?? 0);
    $alerts = (int)($_POST['in_app_alerts'] ?? 0);
    if ($name === '' || $email === '') {
        http_response_code(400);
        echo json_encode(['error' => 'Missing fields']);
        exit;
    }

    $db = get_db();
    if (!table_exists($db, 'system_settings')) {
        $driver = $db->getAttribute(PDO::ATTR_DRIVER_NAME);
        if ($driver === 'mysql') {
            $db->exec("CREATE TABLE IF NOT EXISTS system_settings (
                id INT AUTO_INCREMENT PRIMARY KEY,
                system_name VARCHAR(255) NOT NULL DEFAULT 'HausTap',
                contact_email VARCHAR(255) NOT NULL DEFAULT 'support@example.com',
                renewal_cycle TINYINT(1) NOT NULL DEFAULT 0,
                voucher_deduction TINYINT(1) NOT NULL DEFAULT 0,
                send_email_notifications TINYINT(1) NOT NULL DEFAULT 0,
                in_app_alerts TINYINT(1) NOT NULL DEFAULT 1,
                created_at TIMESTAMP NULL,
                updated_at TIMESTAMP NULL
            )");
        } else {
            $db->exec("CREATE TABLE IF NOT EXISTS system_settings (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                system_name TEXT NOT NULL DEFAULT 'HausTap',
                contact_email TEXT NOT NULL DEFAULT 'support@example.com',
                renewal_cycle INTEGER NOT NULL DEFAULT 0,
                voucher_deduction INTEGER NOT NULL DEFAULT 0,
                send_email_notifications INTEGER NOT NULL DEFAULT 0,
                in_app_alerts INTEGER NOT NULL DEFAULT 1,
                created_at TEXT NULL,
                updated_at TEXT NULL
            )");
        }
    }

    foreach ([
        ['renewal_cycle', 'TINYINT(1)', 'INTEGER', 0],
        ['voucher_deduction', 'TINYINT(1)', 'INTEGER', 0],
        ['send_email_notifications', 'TINYINT(1)', 'INTEGER', 0],
        ['in_app_alerts', 'TINYINT(1)', 'INTEGER', 1],
    ] as [$col, $typeMysql, $typeSqlite, $def]) {
        if (!column_exists($db, 'system_settings', $col)) {
            $driver = $db->getAttribute(PDO::ATTR_DRIVER_NAME);
            if ($driver === 'mysql') {
                $db->exec("ALTER TABLE system_settings ADD COLUMN $col $typeMysql NOT NULL DEFAULT $def");
            } else {
                $db->exec("ALTER TABLE system_settings ADD COLUMN $col $typeSqlite NOT NULL DEFAULT $def");
            }
        }
    }

    $row = $db->query('SELECT id FROM system_settings ORDER BY id ASC LIMIT 1')->fetch();
    $now = date('Y-m-d H:i:s');
    if ($row && isset($row['id'])) {
        $stmt = $db->prepare('UPDATE system_settings SET system_name = ?, contact_email = ?, renewal_cycle = ?, voucher_deduction = ?, send_email_notifications = ?, in_app_alerts = ?, updated_at = ? WHERE id = ?');
        $stmt->execute([$name, $email, $renewal, $voucher, $sendEmail, $alerts, $now, (int)$row['id']]);
    } else {
        $stmt = $db->prepare('INSERT INTO system_settings (system_name, contact_email, renewal_cycle, voucher_deduction, send_email_notifications, in_app_alerts, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([$name, $email, $renewal, $voucher, $sendEmail, $alerts, $now, $now]);
    }

    echo json_encode(['ok' => true]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}

?>

