<?php
namespace App\Controllers\Guest;

final class HomeController {
    public function index(): void {
        // Keep asset paths intact by redirecting to the existing page
        header('Location: /guest/homepage.php', true, 302);
        exit;
    }
}