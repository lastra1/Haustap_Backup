<?php
namespace App\Controllers\Admin;

final class BookingsController {
    private function db(): \PDO {
        require_once \BASE_PATH . DIRECTORY_SEPARATOR . 'admin_haustap' . DIRECTORY_SEPARATOR . 'old_admin' . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'db.php';
        return \get_db();
    }
    private function json(array $payload, int $code = 200): void {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
    public function index(): void {
        $status = isset($_GET['status']) ? strtolower(trim((string)$_GET['status'])) : 'all';
        $search = isset($_GET['search']) ? trim((string)$_GET['search']) : '';
        $page   = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $limit  = isset($_GET['limit']) ? max(1, min(100, (int)$_GET['limit'])) : 10;
        $offset = ($page - 1) * $limit;
        try {
            $db = $this->db();
            $clauses = [];
            $params = [];
            if ($status !== 'all') { $clauses[] = 'LOWER(status) = LOWER(?)'; $params[] = $status; }
            if ($search !== '') { $clauses[] = '(LOWER(service_name) LIKE ? OR CAST(id AS CHAR) LIKE ?)'; $params[] = '%' . strtolower($search) . '%'; $params[] = '%' . strtolower($search) . '%'; }
            $where = $clauses ? ('WHERE ' . implode(' AND ', $clauses)) : '';
            $stmtCount = $db->prepare("SELECT COUNT(*) FROM bookings {$where}");
            $stmtCount->execute($params);
            $total = (int)($stmtCount->fetchColumn() ?: 0);
            $sql = "SELECT id, client_id, provider_id, service_name, scheduled_date, scheduled_time, price, status, rating FROM bookings {$where} ORDER BY id DESC LIMIT :limit OFFSET :offset";
            $stmt = $db->prepare($sql);
            foreach ($params as $i => $v) { $stmt->bindValue($i+1, $v); }
            $stmt->bindValue(':limit', (int)$limit, \PDO::PARAM_INT);
            $stmt->bindValue(':offset', (int)$offset, \PDO::PARAM_INT);
            $stmt->execute();
            $items = $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
            $this->json(['success' => true, 'items' => $items, 'page' => $page, 'limit' => $limit, 'total' => $total]);
        } catch (\Throwable $e) {
            $this->json(['success' => false, 'error' => 'unavailable'], 500);
        }
    }
}