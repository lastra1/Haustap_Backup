<?php
require_once __DIR__ . '/db.php';

function count_bookings(): int {
    try {
        $db = get_db();
        if (!table_exists($db, 'bookings')) return 0;
        $q = $db->query('SELECT COUNT(*) AS c FROM bookings');
        return (int)($q->fetch(PDO::FETCH_ASSOC)['c'] ?? 0);
    } catch (Throwable $e) { return 0; }
}

function count_pending_jobs(): int {
    try {
        $db = get_db();
        if (!table_exists($db, 'jobs')) return 0;
        $q = $db->query("SELECT COUNT(*) AS c FROM jobs WHERE status IN ('pending','awaiting','queued')");
        return (int)($q->fetch(PDO::FETCH_ASSOC)['c'] ?? 0);
    } catch (Throwable $e) { return 0; }
}

function count_verified_providers(): int {
    try {
        $db = get_db();
        if (!table_exists($db, 'providers')) return 0;
        $q = $db->query("SELECT COUNT(*) AS c FROM providers WHERE verified = 1 OR status = 'verified'");
        return (int)($q->fetch(PDO::FETCH_ASSOC)['c'] ?? 0);
    } catch (Throwable $e) { return 0; }
}

function count_clients(?string $q = null): int {
    try {
        $db = get_db();
        if (!table_exists($db, 'users')) return 0;
        $hasRole = column_exists($db, 'users', 'role');
        $sql = $hasRole ? 'SELECT COUNT(*) AS c FROM users WHERE role = ?' : 'SELECT COUNT(*) AS c FROM users';
        if ($q && $q !== '') {
            $like = '%' . $q . '%';
            if ($hasRole) {
                $stmt = $db->prepare($sql . ' AND (name LIKE ? OR email LIKE ?)');
                $stmt->execute(['client', $like, $like]);
            } else {
                $stmt = $db->prepare($sql . ' WHERE name LIKE ? OR email LIKE ?');
                $stmt->execute([$like, $like]);
            }
            return (int)($stmt->fetch(PDO::FETCH_ASSOC)['c'] ?? 0);
        }
        if ($hasRole) {
            $stmt = $db->prepare($sql); $stmt->execute(['client']);
            return (int)($stmt->fetch(PDO::FETCH_ASSOC)['c'] ?? 0);
        }
        $r = $db->query($sql)->fetch(PDO::FETCH_ASSOC);
        return (int)($r['c'] ?? 0);
    } catch (Throwable $e) { return 0; }
}

function search_clients(?string $q, ?string $status, int $limit, int $offset): array {
    try {
        $db = get_db();
        if (!table_exists($db, 'users')) return [];
        $hasRole = column_exists($db, 'users', 'role');
        $hasStatus = column_exists($db, 'users', 'status');
        $hasJoined = column_exists($db, 'users', 'created_at');
        $where = [];
        $params = [];
        if ($hasRole) { $where[] = 'role = ?'; $params[] = 'client'; }
        if ($q && $q !== '') { $where[] = '(name LIKE ? OR email LIKE ?)'; $params[] = '%' . $q . '%'; $params[] = '%' . $q . '%'; }
        if ($status && $hasStatus) { $where[] = 'LOWER(status) = LOWER(?)'; $params[] = $status; }
        $sql = 'SELECT id, name, email' . ($hasStatus ? ', status' : '') . ($hasJoined ? ', created_at AS date_joined' : '') . ' FROM users';
        if ($where) $sql .= ' WHERE ' . implode(' AND ', $where);
        $sql .= ' ORDER BY id DESC LIMIT ? OFFSET ?';
        $params[] = $limit; $params[] = $offset;
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        foreach ($rows as &$r) { if (!isset($r['status'])) $r['status'] = 'active'; if (!isset($r['date_joined'])) $r['date_joined'] = ''; }
        return $rows;
    } catch (Throwable $e) { return []; }
}

function client_status_counts(): array {
    $out = ['total' => 0, 'active' => 0, 'inactive' => 0, 'suspend' => 0];
    try {
        $db = get_db();
        if (!table_exists($db, 'users')) return $out;
        $hasStatus = column_exists($db, 'users', 'status');
        if (!$hasStatus) { $out['total'] = count_clients(); return $out; }
        $q = $db->query('SELECT LOWER(status) AS s, COUNT(*) AS c FROM users GROUP BY LOWER(status)');
        while ($r = $q->fetch(PDO::FETCH_ASSOC)) {
            $out['total'] += (int)($r['c'] ?? 0);
            $s = $r['s'] ?? '';
            if (isset($out[$s])) $out[$s] += (int)$r['c'];
        }
        return $out;
    } catch (Throwable $e) { return $out; }
}

?>
