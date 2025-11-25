<?php
// Shim to make /admin/manage_booking.php work under any dev server setup.
// Directly include the real page from the legacy admin app so assets and includes resolve.
require __DIR__ . '/../../admin_haustap/admin_haustap/manage_booking.php';