<?php
namespace App\Controllers\Admin;

final class ApplicantsController {
    private string $storeFile;

    public function __construct() {
        $this->storeFile = \BASE_PATH . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'applicants.json';
    }

    private function loadStore(): array {
        if (is_file($this->storeFile)) {
            $raw = @file_get_contents($this->storeFile);
            $data = json_decode($raw ?: '[]', true);
            if (is_array($data)) { return $data; }
        }
        return [];
    }

    private static function normalizeStatus(?string $s): string {
        $s = strtolower(trim((string)($s ?? '')));
        if ($s === '') { return 'pending_review'; }
        // map some variations
        if ($s === 'pending' || $s === 'review') { return 'pending_review'; }
        return $s;
    }

    private function json(array $payload, int $code = 200): void {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    // GET /api/admin/applicants?status=...&search=...&page=1&limit=10
    public function index(): void {
        $status = isset($_GET['status']) ? strtolower(trim((string)$_GET['status'])) : 'all';
        $search = isset($_GET['search']) ? trim((string)$_GET['search']) : '';
        $page   = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $limit  = isset($_GET['limit']) ? max(1, min(100, (int)$_GET['limit'])) : 10;
        $offset = ($page - 1) * $limit;

        // Try DB first
        try {
            require_once \BASE_PATH . DIRECTORY_SEPARATOR . 'admin_haustap' . DIRECTORY_SEPARATOR . 'old_admin' . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'db.php';
            $pdo = \get_db();

            $clauses = [];
            $params = [];
            if ($status !== 'all') {
                $clauses[] = 'LOWER(REPLACE(status, " ", "_")) = :status';
                $params[':status'] = strtolower($status);
            }
            if ($search !== '') {
                $clauses[] = '(LOWER(name) LIKE :q OR LOWER(email) LIKE :q OR LOWER(phone) LIKE :q)';
                $params[':q'] = '%' . strtolower($search) . '%';
            }
            $where = $clauses ? ('WHERE ' . implode(' AND ', $clauses)) : '';

            $stmtCount = $pdo->prepare("SELECT COUNT(*) FROM admin_applicants {$where}");
            foreach ($params as $k => $v) { $stmtCount->bindValue($k, $v); }
            $stmtCount->execute();
            $total = (int)($stmtCount->fetchColumn() ?: 0);

            $stmt = $pdo->prepare("SELECT id, name, email, phone, applied_at, status
                                     FROM admin_applicants {$where}
                                     ORDER BY id ASC
                                     LIMIT :limit OFFSET :offset");
            foreach ($params as $k => $v) { $stmt->bindValue($k, $v); }
            $stmt->bindValue(':limit', (int)$limit, \PDO::PARAM_INT);
            $stmt->bindValue(':offset', (int)$offset, \PDO::PARAM_INT);
            $stmt->execute();
            $items = $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];

            $this->json([
                'success' => true,
                'items'   => $items,
                'page'    => $page,
                'limit'   => $limit,
                'total'   => $total,
            ]);
            return;
        } catch (\Throwable $e) {
            // Fallback to JSON store
            $items = $this->loadStore();

            $filtered = array_values(array_filter($items, function($it) use ($status, $search) {
                $itStatus = self::normalizeStatus($it['status'] ?? 'pending_review');
                if ($status !== 'all' && $itStatus !== $status) { return false; }
                if ($search !== '') {
                    $hay = strtolower(($it['name'] ?? '') . ' ' . ($it['email'] ?? '') . ' ' . ($it['phone'] ?? ''));
                    if (strpos($hay, strtolower($search)) === false) { return false; }
                }
                return true;
            }));

            $total = count($filtered);
            $paged = array_slice($filtered, $offset, $limit);

            $this->json([
                'success' => true,
                'items'   => $paged,
                'page'    => $page,
                'limit'   => $limit,
                'total'   => $total,
                'fallback' => 'json_store',
            ]);
        }
    }
}

