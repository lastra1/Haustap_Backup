<?php
require_once __DIR__ . '/db.php';

function ensure_users_table(PDO $db): void
{
    $driver = $db->getAttribute(PDO::ATTR_DRIVER_NAME);
    if ($driver === 'mysql') {
        $db->exec("CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            role VARCHAR(50) NULL,
            created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    } else {
        $db->exec("CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            email TEXT NOT NULL UNIQUE,
            password TEXT NOT NULL,
            role TEXT NULL,
            created_at TEXT NULL,
            updated_at TEXT NULL
        )");
    }
}

function find_admin_user_by_email(string $email): ?array
{
    $db = get_db();
    ensure_users_table($db);
    $hasRoleCol = column_exists($db, 'users', 'role');
    if ($hasRoleCol) {
        $stmt = $db->prepare('SELECT id, name, email, password FROM users WHERE email = ? AND role = ? LIMIT 1');
        $stmt->execute([$email, 'admin']);
    } else if (table_exists($db, 'user_roles')) {
        $stmt = $db->prepare('SELECT u.id, u.name, u.email, u.password
                              FROM users u JOIN user_roles r ON r.user_id = u.id AND r.role = ?
                              WHERE u.email = ? LIMIT 1');
        $stmt->execute(['admin', $email]);
    } else {
        $stmt = $db->prepare('SELECT id, name, email, password FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
    }
    $row = $stmt->fetch();
    return $row ?: null;
}

function ensure_admin_role(PDO $db, int $userId): void
{
    if (column_exists($db, 'users', 'role')) {
        $stmt = $db->prepare('UPDATE users SET role = ? WHERE id = ?');
        $stmt->execute(['admin', $userId]);
        return;
    }
    if (table_exists($db, 'user_roles')) {
        $stmt = $db->prepare('INSERT IGNORE INTO user_roles (user_id, role, created_at, updated_at) VALUES (?, ?, NOW(), NOW())');
        $stmt->execute([$userId, 'admin']);
    }
}

function upsert_admin_user(string $email, string $password, string $name = 'Admin'): array
{
    $db = get_db();
    ensure_users_table($db);
    $hash = password_hash($password, PASSWORD_DEFAULT);
    // Try to find existing
    $existing = find_admin_user_by_email($email);
    if ($existing) {
        // Update password hash if needed
        $stmt = $db->prepare('UPDATE users SET password = ?, updated_at = NOW() WHERE id = ?');
        $stmt->execute([$hash, (int)$existing['id']]);
        ensure_admin_role($db, (int)$existing['id']);
        return [ 'id' => (int)$existing['id'], 'email' => $email, 'name' => $existing['name'] ?? $name ];
    }
    // Insert new user
    $stmt = $db->prepare('INSERT INTO users (name, email, password, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())');
    $stmt->execute([$name, $email, $hash]);
    $userId = (int)$db->lastInsertId();
    ensure_admin_role($db, $userId);
    return [ 'id' => $userId, 'email' => $email, 'name' => $name ];
}

function ensure_login_events_table(PDO $db): void
{
    $db->exec("CREATE TABLE IF NOT EXISTS admin_login_events (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id BIGINT NULL,
        email VARCHAR(255) NOT NULL,
        ip_address VARCHAR(45) NULL,
        user_agent TEXT NULL,
        success TINYINT(1) NOT NULL DEFAULT 0,
        ts TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
}

function record_login_event(?int $userId, string $email, bool $success): void
{
    try {
        $db = get_db();
        ensure_login_events_table($db);
        $stmt = $db->prepare('INSERT INTO admin_login_events (user_id, email, ip_address, user_agent, success) VALUES (?, ?, ?, ?, ?)');
        $ip = $_SERVER['REMOTE_ADDR'] ?? null;
        $ua = $_SERVER['HTTP_USER_AGENT'] ?? null;
        $stmt->execute([$userId, $email, $ip, $ua, $success ? 1 : 0]);
    } catch (Throwable $e) {
        // Don't block login on logging failure
    }
}

?>
