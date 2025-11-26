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

    $pdoDb = new PDO(sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', $host, $port, $db), $user, $pass, [ PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION ]);

    $pdoDb->exec("CREATE TABLE IF NOT EXISTS users (
      id INT AUTO_INCREMENT PRIMARY KEY,
      name VARCHAR(255) NOT NULL,
      email VARCHAR(255) NOT NULL UNIQUE,
      password VARCHAR(255) NOT NULL,
      role VARCHAR(50) NULL,
      status VARCHAR(50) NULL,
      created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
      updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $pdoDb->exec("CREATE TABLE IF NOT EXISTS user_roles (
      id INT AUTO_INCREMENT PRIMARY KEY,
      user_id INT NOT NULL,
      role VARCHAR(50) NOT NULL,
      created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
      updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      INDEX idx_user_role (user_id, role),
      CONSTRAINT fk_user_roles_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $pdoDb->exec("CREATE TABLE IF NOT EXISTS providers (
      id INT AUTO_INCREMENT PRIMARY KEY,
      user_id INT NULL,
      name VARCHAR(255) NULL,
      email VARCHAR(255) NULL,
      verified TINYINT(1) NOT NULL DEFAULT 0,
      status VARCHAR(50) NULL,
      subscription_status VARCHAR(50) NULL,
      subscription_expires_at DATETIME NULL,
      service_categories JSON NULL,
      rating DOUBLE NULL,
      created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
      updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      INDEX idx_provider_status (status),
      CONSTRAINT fk_providers_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $pdoDb->exec("CREATE TABLE IF NOT EXISTS bookings (
      id BIGINT AUTO_INCREMENT PRIMARY KEY,
      client_id INT NULL,
      provider_id INT NULL,
      service_name VARCHAR(255) NULL,
      scheduled_date DATE NULL,
      scheduled_time VARCHAR(20) NULL,
      price DOUBLE NULL,
      status VARCHAR(50) NULL,
      rating DOUBLE NULL,
      created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
      updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      INDEX idx_booking_status (status),
      INDEX idx_booking_provider (provider_id),
      INDEX idx_booking_client (client_id),
      CONSTRAINT fk_booking_client FOREIGN KEY (client_id) REFERENCES users(id) ON DELETE SET NULL,
      CONSTRAINT fk_booking_provider FOREIGN KEY (provider_id) REFERENCES providers(id) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $pdoDb->exec("CREATE TABLE IF NOT EXISTS chat_messages (
      id BIGINT AUTO_INCREMENT PRIMARY KEY,
      room_id BIGINT NOT NULL,
      sender_id INT NULL,
      message TEXT NOT NULL,
      ts TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      INDEX idx_chat_room (room_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $pdoDb->exec("CREATE TABLE IF NOT EXISTS notifications (
      id BIGINT AUTO_INCREMENT PRIMARY KEY,
      user_id INT NULL,
      type VARCHAR(50) NULL,
      data JSON NULL,
      unread TINYINT(1) NOT NULL DEFAULT 1,
      ts TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      INDEX idx_notifications_user (user_id),
      CONSTRAINT fk_notifications_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $pdoDb->exec("CREATE TABLE IF NOT EXISTS vouchers (
      id INT AUTO_INCREMENT PRIMARY KEY,
      code VARCHAR(64) NOT NULL UNIQUE,
      discount DOUBLE NULL,
      user_id INT NULL,
      expires_at DATETIME NULL,
      created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
      INDEX idx_voucher_user (user_id),
      CONSTRAINT fk_voucher_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $pdoDb->exec("CREATE TABLE IF NOT EXISTS system_settings (
      id INT AUTO_INCREMENT PRIMARY KEY,
      system_name VARCHAR(255) NOT NULL,
      contact_email VARCHAR(255) NULL,
      renewal_cycle VARCHAR(50) NULL,
      voucher_deduction DOUBLE NULL,
      send_email_notifications TINYINT(1) NOT NULL DEFAULT 0,
      in_app_alerts TINYINT(1) NOT NULL DEFAULT 1,
      created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
      updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $pdoDb->exec("CREATE TABLE IF NOT EXISTS admin_applicants (
      id INT AUTO_INCREMENT PRIMARY KEY,
      name VARCHAR(255) NOT NULL,
      email VARCHAR(255) NULL,
      phone VARCHAR(50) NULL,
      status VARCHAR(50) NOT NULL DEFAULT 'pending_review',
      applied_at DATETIME NULL,
      created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
      updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      INDEX idx_applicant_status (status)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $pdoDb->exec("CREATE TABLE IF NOT EXISTS admin_login_events (
      id INT AUTO_INCREMENT PRIMARY KEY,
      user_id BIGINT NULL,
      email VARCHAR(255) NOT NULL,
      ip_address VARCHAR(45) NULL,
      user_agent TEXT NULL,
      success TINYINT(1) NOT NULL DEFAULT 0,
      ts TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
} catch (Throwable $e) {
    $result['ok'] = false;
    $result['error'] = $e->getMessage();
    try {
        $sqlitePath = __DIR__ . '/../database/database.sqlite';
        $dir = dirname($sqlitePath);
        if (!is_dir($dir)) { @mkdir($dir, 0777, true); }
        $pdo = new PDO('sqlite:' . $sqlitePath);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->exec("CREATE TABLE IF NOT EXISTS users (id INTEGER PRIMARY KEY AUTOINCREMENT, name TEXT NOT NULL, email TEXT NOT NULL UNIQUE, password TEXT NOT NULL, role TEXT NULL, status TEXT NULL, created_at TEXT NULL, updated_at TEXT NULL)");
        $pdo->exec("CREATE TABLE IF NOT EXISTS user_roles (id INTEGER PRIMARY KEY AUTOINCREMENT, user_id INTEGER NOT NULL, role TEXT NOT NULL, created_at TEXT NULL, updated_at TEXT NULL)");
        $pdo->exec("CREATE TABLE IF NOT EXISTS providers (id INTEGER PRIMARY KEY AUTOINCREMENT, user_id INTEGER NULL, name TEXT NULL, email TEXT NULL, verified INTEGER NOT NULL DEFAULT 0, status TEXT NULL, subscription_status TEXT NULL, subscription_expires_at TEXT NULL, service_categories TEXT NULL, rating REAL NULL, created_at TEXT NULL, updated_at TEXT NULL)");
        $pdo->exec("CREATE TABLE IF NOT EXISTS bookings (id INTEGER PRIMARY KEY AUTOINCREMENT, client_id INTEGER NULL, provider_id INTEGER NULL, service_name TEXT NULL, scheduled_date TEXT NULL, scheduled_time TEXT NULL, price REAL NULL, status TEXT NULL, rating REAL NULL, created_at TEXT NULL, updated_at TEXT NULL)");
        $pdo->exec("CREATE TABLE IF NOT EXISTS chat_messages (id INTEGER PRIMARY KEY AUTOINCREMENT, room_id INTEGER NOT NULL, sender_id INTEGER NULL, message TEXT NOT NULL, ts TEXT NULL)");
        $pdo->exec("CREATE TABLE IF NOT EXISTS notifications (id INTEGER PRIMARY KEY AUTOINCREMENT, user_id INTEGER NULL, type TEXT NULL, data TEXT NULL, unread INTEGER NOT NULL DEFAULT 1, ts TEXT NULL)");
        $pdo->exec("CREATE TABLE IF NOT EXISTS vouchers (id INTEGER PRIMARY KEY AUTOINCREMENT, code TEXT NOT NULL UNIQUE, discount REAL NULL, user_id INTEGER NULL, expires_at TEXT NULL, created_at TEXT NULL)");
        $pdo->exec("CREATE TABLE IF NOT EXISTS system_settings (id INTEGER PRIMARY KEY AUTOINCREMENT, system_name TEXT NOT NULL, contact_email TEXT NULL, renewal_cycle TEXT NULL, voucher_deduction REAL NULL, send_email_notifications INTEGER NOT NULL DEFAULT 0, in_app_alerts INTEGER NOT NULL DEFAULT 1, created_at TEXT NULL, updated_at TEXT NULL)");
        $pdo->exec("CREATE TABLE IF NOT EXISTS admin_applicants (id INTEGER PRIMARY KEY AUTOINCREMENT, name TEXT NOT NULL, email TEXT NULL, phone TEXT NULL, status TEXT NOT NULL DEFAULT 'pending_review', applied_at TEXT NULL, created_at TEXT NULL, updated_at TEXT NULL)");
        $pdo->exec("CREATE TABLE IF NOT EXISTS admin_login_events (id INTEGER PRIMARY KEY AUTOINCREMENT, user_id INTEGER NULL, email TEXT NOT NULL, ip_address TEXT NULL, user_agent TEXT NULL, success INTEGER NOT NULL DEFAULT 0, ts TEXT NULL)");
        $result['ok'] = true;
        unset($result['error']);
        $result['sqlite'] = $sqlitePath;
    } catch (Throwable $e2) {
        $result['error_sqlite'] = $e2->getMessage();
    }
}

echo json_encode($result, JSON_PRETTY_PRINT) . PHP_EOL;