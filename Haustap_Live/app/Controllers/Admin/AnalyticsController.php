<?php
namespace App\Controllers\Admin;

final class AnalyticsController {
    private string $analyticsFile;

    public function __construct() {
        $this->analyticsFile = \BASE_PATH . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'analytics.json';
    }

    private function loadFile(string $file): array {
        if (is_file($file)) {
            $raw = file_get_contents($file);
            $data = json_decode($raw, true);
            if (is_array($data)) { return $data; }
        }
        return [];
    }

    private function json(array $payload, int $code = 200): void {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    // GET /api/admin/analytics/summary
    public function summary(): void {
        $data = $this->loadFile($this->analyticsFile);
        $this->json(['success' => true, 'data' => $data]);
    }
}

