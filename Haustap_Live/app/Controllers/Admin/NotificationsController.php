<?php
namespace App\Controllers\Admin;

final class NotificationsController {
    private string $storeFile;
    public function __construct() {
        $this->storeFile = \BASE_PATH . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'notifications.json';
    }
    private function loadStore(): array {
        if (is_file($this->storeFile)) {
            $raw = @file_get_contents($this->storeFile);
            $data = json_decode($raw ?: '[]', true);
            if (is_array($data)) { return $data; }
        }
        return [];
    }
    private function saveStore(array $items): void {
        @file_put_contents($this->storeFile, json_encode($items, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }
    private function json(array $payload, int $code = 200): void {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
    public function unreadCount(): void {
        $items = $this->loadStore();
        $count = 0;
        foreach ($items as $it) { if (!empty($it['unread'])) { $count++; } }
        $this->json(['success' => true, 'unread' => $count]);
    }
    public function stream(): void {
        if (!headers_sent()) {
            header('Content-Type: text/event-stream');
            header('Cache-Control: no-cache');
            header('Connection: keep-alive');
        }
        @ob_end_flush();
        @ob_implicit_flush(true);
        $lastCount = null;
        $ticks = 0;
        while ($ticks < 120) {
            $items = $this->loadStore();
            $curr = count(array_filter($items, fn($it) => !empty($it['unread'])));
            if ($curr !== $lastCount) {
                $payload = json_encode(['unread' => $curr], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                echo 'event: unread\n';
                echo 'data: ' . $payload . "\n\n";
                @flush();
                $lastCount = $curr;
            }
            usleep(500000);
            $ticks++;
        }
    }
    public function seedApplicants(): void {
        $items = $this->loadStore();
        $items[] = ['type' => 'applicant', 'message' => 'New applicant submitted', 'unread' => true, 'ts' => time()];
        $this->saveStore($items);
        $this->json(['success' => true]);
    }
    public function seedBookings(): void {
        $items = $this->loadStore();
        $items[] = ['type' => 'booking', 'message' => 'Provider accepted a booking', 'unread' => true, 'ts' => time()];
        $this->saveStore($items);
        $this->json(['success' => true]);
    }
}