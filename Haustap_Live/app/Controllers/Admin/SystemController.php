<?php
namespace App\Controllers\Admin;

final class SystemController {
    private function db(): \PDO {
        require_once \BASE_PATH . DIRECTORY_SEPARATOR . 'admin_haustap' . DIRECTORY_SEPARATOR . 'old_admin' . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'db.php';
        return \get_db();
    }
    private function json(array $payload, int $code = 200): void {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
    public function summary(): void {
        try {
            $db = $this->db();
            $bookings = 0; $providers = 0;
            if (\table_exists($db, 'bookings')) { $bookings = (int)($db->query('SELECT COUNT(*) AS c FROM bookings')->fetch(\PDO::FETCH_ASSOC)['c'] ?? 0); }
            if (\table_exists($db, 'providers')) { $providers = (int)($db->query("SELECT COUNT(*) AS c FROM providers WHERE verified = 1 OR status = 'verified'")->fetch(\PDO::FETCH_ASSOC)['c'] ?? 0); }
            $this->json(['success' => true, 'bookings' => $bookings, 'verified_providers' => $providers]);
        } catch (\Throwable $e) { $this->json(['success' => true, 'bookings' => 0, 'verified_providers' => 0]); }
    }
    public function all(): void {
        try {
            $db = $this->db();
            $out = [];
            $out['bookings'] = \table_exists($db, 'bookings') ? (int)($db->query('SELECT COUNT(*) AS c FROM bookings')->fetch(\PDO::FETCH_ASSOC)['c'] ?? 0) : 0;
            $out['providers'] = \table_exists($db, 'providers') ? (int)($db->query('SELECT COUNT(*) AS c FROM providers')->fetch(\PDO::FETCH_ASSOC)['c'] ?? 0) : 0;
            $out['clients'] = \table_exists($db, 'users') ? (int)($db->query("SELECT COUNT(*) AS c FROM users WHERE role = 'client'")->fetch(\PDO::FETCH_ASSOC)['c'] ?? 0) : 0;
            $out['vouchers'] = \table_exists($db, 'vouchers') ? (int)($db->query('SELECT COUNT(*) AS c FROM vouchers')->fetch(\PDO::FETCH_ASSOC)['c'] ?? 0) : 0;
            $this->json(['success' => true, 'data' => $out]);
        } catch (\Throwable $e) { $this->json(['success' => true, 'data' => []]); }
    }
    public function categories(): void {
        $this->json(['success' => true, 'categories' => []]);
    }
}