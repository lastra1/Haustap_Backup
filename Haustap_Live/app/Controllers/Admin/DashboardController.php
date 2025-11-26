<?php
namespace App\Controllers\Admin;

final class DashboardController {
    public function index(): void {
        // Redirect to the existing admin dashboard to preserve its asset paths
        header('Location: /admin_haustap/old_admin/dashboard.php', true, 302);
        exit;
    }
}