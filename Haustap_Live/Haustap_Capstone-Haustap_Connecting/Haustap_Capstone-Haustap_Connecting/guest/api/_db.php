<?php

function load_env_file($path) {
  if (!file_exists($path)) return;
  $lines = @file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
  if (!$lines) return;
  foreach ($lines as $line) {
    $line = trim($line);
    if ($line === '' || $line[0] === '#') continue;
    $parts = explode('=', $line, 2);
    if (count($parts) !== 2) continue;
    $key = trim($parts[0]);
    $val = trim($parts[1]);
    if ((str_starts_with($val, '"') && str_ends_with($val, '"')) || (str_starts_with($val, "'") && str_ends_with($val, "'"))) {
      $val = substr($val, 1, -1);
    }
    if ($key !== '') {
      $_ENV[$key] = $val;
      putenv($key.'='.$val);
    }
  }
}

function haustap_db_conn() {
  // Attempt to load env if not provided by the OS
  $base = dirname(__DIR__, 3);
  $apiEnv = $base . DIRECTORY_SEPARATOR . 'backend' . DIRECTORY_SEPARATOR . 'api' . DIRECTORY_SEPARATOR . '.env';
  $rootEnv = $base . DIRECTORY_SEPARATOR . '.env';
  $needEnv = !(getenv('DB_HOST') && getenv('DB_PORT') && getenv('DB_DATABASE') && getenv('DB_USERNAME') && getenv('DB_PASSWORD') !== false);
  if ($needEnv) {
    load_env_file($apiEnv);
    $needEnv = !(getenv('DB_HOST') && getenv('DB_PORT') && getenv('DB_DATABASE') && getenv('DB_USERNAME') && getenv('DB_PASSWORD') !== false);
    if ($needEnv) load_env_file($rootEnv);
  }

  $host = getenv('DB_HOST') ?: null;
  $port = getenv('DB_PORT') ?: null;
  $db   = getenv('DB_DATABASE') ?: null;
  $user = getenv('DB_USERNAME') ?: null;
  $pass = getenv('DB_PASSWORD');

  if (!$host || !$port || !$db || !$user || $pass === false) {
    $sqlite = $base . DIRECTORY_SEPARATOR . 'backend' . DIRECTORY_SEPARATOR . 'api' . DIRECTORY_SEPARATOR . 'database' . DIRECTORY_SEPARATOR . 'database.sqlite';
    try {
      $dir = dirname($sqlite);
      if (!is_dir($dir)) @mkdir($dir, 0777, true);
      $pdo = new PDO('sqlite:' . $sqlite, null, null, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
      ]);
      return $pdo;
    } catch (Throwable $e) {
      return null;
    }
  }

  $dsn = "mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4";
  try {
    $pdo = new PDO($dsn, $user, $pass, [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    return $pdo;
  } catch (Throwable $e) {
    return null;
  }
}

function ensure_locations_table($pdo) {
  if (!$pdo) return false;
  $sql = "CREATE TABLE IF NOT EXISTS booking_locations (
    id VARCHAR(64) PRIMARY KEY,
    address TEXT NULL,
    lat DOUBLE NULL,
    lng DOUBLE NULL,
    house_type VARCHAR(255) NULL,
    service_name VARCHAR(255) NULL,
    cleaning_type VARCHAR(255) NULL,
    user_key VARCHAR(255) NULL,
    created_at DATETIME NULL,
    INDEX idx_user (user_key)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
  try { $pdo->exec($sql); return true; } catch (Throwable $e) { return false; }
}

function ensure_saved_addresses_table($pdo) {
  if (!$pdo) return false;
  $sql = "CREATE TABLE IF NOT EXISTS saved_addresses (
    id VARCHAR(64) PRIMARY KEY,
    session_id VARCHAR(128) NOT NULL,
    user_key VARCHAR(255) NULL,
    address TEXT NOT NULL,
    created_at DATETIME NULL,
    INDEX idx_session (session_id),
    INDEX idx_user (user_key)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
  try { $pdo->exec($sql); return true; } catch (Throwable $e) { return false; }
}

function ensure_blackout_table($pdo) {
  if (!$pdo) return false;
  $sql = "CREATE TABLE IF NOT EXISTS blackout_dates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    date DATE NOT NULL,
    service_code VARCHAR(64) NULL,
    reason VARCHAR(255) NULL,
    UNIQUE KEY uniq_date_service (date, service_code)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
  try { $pdo->exec($sql); return true; } catch (Throwable $e) { return false; }
}

function ensure_capacity_table($pdo) {
  if (!$pdo) return false;
  $sql = "CREATE TABLE IF NOT EXISTS capacity_calendar (
    id INT AUTO_INCREMENT PRIMARY KEY,
    date DATE NOT NULL,
    slot VARCHAR(16) NOT NULL,
    zone VARCHAR(64) NULL,
    service_code VARCHAR(64) NULL,
    capacity_total INT NOT NULL DEFAULT 0,
    capacity_used INT NOT NULL DEFAULT 0,
    UNIQUE KEY uniq_date_slot_zone_service (date, slot, IFNULL(zone,'-'), IFNULL(service_code,'-'))
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
  try { $pdo->exec($sql); return true; } catch (Throwable $e) { return false; }
}

function ensure_holds_table($pdo) {
  if (!$pdo) return false;
  $sql = "CREATE TABLE IF NOT EXISTS booking_holds (
    id VARCHAR(64) PRIMARY KEY,
    user_key VARCHAR(255) NOT NULL,
    date DATE NOT NULL,
    time VARCHAR(16) NOT NULL,
    service_code VARCHAR(64) NULL,
    address TEXT NULL,
    expires_at DATETIME NOT NULL,
    created_at DATETIME NULL,
    INDEX idx_user (user_key)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
  try { $pdo->exec($sql); return true; } catch (Throwable $e) { return false; }
}

function ensure_bookings_table($pdo) {
  if (!$pdo) return false;
  $sql = "CREATE TABLE IF NOT EXISTS bookings (
    id VARCHAR(64) PRIMARY KEY,
    user_key VARCHAR(255) NOT NULL,
    date DATE NOT NULL,
    time VARCHAR(16) NOT NULL,
    service_code VARCHAR(64) NULL,
    address TEXT NULL,
    created_at DATETIME NULL,
    UNIQUE KEY uniq_user_date_time_service (user_key, date, time, IFNULL(service_code,'-'))
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
  try { $pdo->exec($sql); return true; } catch (Throwable $e) { return false; }
}

function ensure_services_table($pdo) {
  if (!$pdo) return false;
  $sql = "CREATE TABLE IF NOT EXISTS services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    category VARCHAR(128) NULL,
    price INT NULL,
    duration_minutes INT NULL,
    description TEXT NULL,
    created_at DATETIME NULL,
    updated_at DATETIME NULL,
    UNIQUE KEY uniq_name (name)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
  try { $pdo->exec($sql); return true; } catch (Throwable $e) { return false; }
}