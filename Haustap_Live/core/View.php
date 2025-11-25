<?php
namespace Core;

final class View {
    public static function php(string $absoluteFilePath, array $vars = []): void {
        if (!is_file($absoluteFilePath)) {
            http_response_code(404);
            echo 'View not found: ' . htmlspecialchars($absoluteFilePath);
            return;
        }
        extract($vars, EXTR_SKIP);
        require $absoluteFilePath;
    }
}