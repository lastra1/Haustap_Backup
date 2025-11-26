<?php
declare(strict_types=1);

require __DIR__ . '/../bootstrap.php';
// Debug: log each request path coming through the router
error_log('[router] request: ' . ($_SERVER['REQUEST_URI'] ?? '')); 

use Core\Router;

$router = new Router();

$envBool = function(string $key, bool $default): bool {
    $val = getenv($key);
    if ($val === false || $val === '') return $default;
    $val = strtolower((string)$val);
    return in_array($val, ['1','true','yes','on'], true);
};

$appEnv = getenv('APP_ENV') ?: 'development';
$enableMock = $envBool('ENABLE_MOCK_API', $appEnv !== 'production');

// Friendly routes using controllers
$router->get('/', fn() => (new \App\Controllers\Guest\HomeController())->index());
$router->get('/admin', fn() => (new \App\Controllers\Admin\DashboardController())->index());
// Health check
$router->get('/health', function() {
    header('Content-Type: text/plain');
    echo 'ok';
});

// Explicit admin legacy page routes to ensure dev-server compatibility
$router->get('/admin_haustap/admin_haustap/dashboard.php', function() {
    error_log('[router] explicit route hit: admin dashboard');
    run_php(ADMIN_APP_PATH . '/dashboard.php');
});
$router->get('/admin_haustap/admin_haustap/manage_applicant.php', function() {
    error_log('[router] explicit route hit: manage applicant');
    run_php(ADMIN_APP_PATH . '/manage_applicant.php');
});

// Alias: serve admin app under /admin/* for dev convenience
$router->get('/admin/dashboard.php', function() {
    error_log('[router] alias route hit: /admin/dashboard.php');
    run_php(ADMIN_APP_PATH . '/dashboard.php');
});
$router->get('/admin/manage_booking.php', function() {
    error_log('[router] alias route hit: /admin/manage_booking.php');
    run_php(ADMIN_APP_PATH . '/manage_booking.php');
});
$router->get('/admin/*', function() {
    $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
    $prefix = '/admin/';
    $rel = substr($path, strlen($prefix));
    if ($rel === false || $rel === '') { $rel = 'dashboard.php'; }
    $candidate = ADMIN_APP_PATH . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $rel);
    if (is_file($candidate)) {
        $ext = strtolower(pathinfo($candidate, PATHINFO_EXTENSION));
        if ($ext === 'php') { run_php($candidate); return; }
        serve_static($candidate); return;
    }
    if (is_dir($candidate)) {
        $index = $candidate . DIRECTORY_SEPARATOR . 'index.php';
        if (is_file($index)) { run_php($index); return; }
    }
    http_response_code(404);
    echo 'Not Found';
});

// Catch-all for legacy admin paths: /admin_haustap/admin_haustap/<anything>
$router->get('/admin_haustap/admin_haustap/*', function() {
    $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
    $prefix = '/admin_haustap/admin_haustap/';
    $rel = substr($path, strlen($prefix));
    if ($rel === false || $rel === '') { $rel = 'dashboard.php'; }
    $candidate = ADMIN_APP_PATH . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $rel);
    if (is_file($candidate)) {
        $ext = strtolower(pathinfo($candidate, PATHINFO_EXTENSION));
        if ($ext === 'php') { run_php($candidate); return; }
        serve_static($candidate); return;
    }
    http_response_code(404);
    echo 'Not Found';
});

// Admin JSON API routes
$router->get('/api/admin/applicants', fn() => (new \App\Controllers\Admin\ApplicantsController())->index());
$router->get('/api/admin/analytics/summary', fn() => (new \App\Controllers\Admin\AnalyticsController())->summary());
// Trailing-slash tolerant route for analytics summary
$router->get('/api/admin/analytics/summary/', fn() => (new \App\Controllers\Admin\AnalyticsController())->summary());
// System-wide aggregated data endpoints
$router->get('/api/admin/system/all', fn() => (new \App\Controllers\Admin\SystemController())->all());
$router->get('/api/admin/system/summary', fn() => (new \App\Controllers\Admin\SystemController())->summary());
$router->get('/api/admin/system/summary/', fn() => (new \App\Controllers\Admin\SystemController())->summary());
// Public system data
$router->get('/api/system/categories', fn() => (new \App\Controllers\Admin\SystemController())->categories());
// Notifications: unread count and server-sent events stream
$router->get('/api/admin/notifications/unread_count', fn() => (new \App\Controllers\Admin\NotificationsController())->unreadCount());
$router->get('/api/admin/notifications/stream', fn() => (new \App\Controllers\Admin\NotificationsController())->stream());
// Dev seeding endpoints to demo bell updates
$router->get('/api/admin/dev/seed/applicants', fn() => (new \App\Controllers\Admin\NotificationsController())->seedApplicants());
$router->get('/api/admin/dev/seed/bookings', fn() => (new \App\Controllers\Admin\NotificationsController())->seedBookings());
// Bookings list for admin tables
$router->get('/api/admin/bookings', fn() => (new \App\Controllers\Admin\BookingsController())->index());
// Clients and Providers list for admin tables
$router->get('/api/admin/clients', fn() => (new \App\Controllers\Admin\ClientsController())->index());
$router->get('/api/admin/providers', fn() => (new \App\Controllers\Admin\ProvidersController())->index());

$router->post('/api/admin/providers/approve', function() {
    header('Content-Type: application/json');
    $raw = file_get_contents('php://input');
    $json = json_decode($raw ?: 'null', true) ?: [];
    $email = trim((string)($json['email'] ?? ''));
    if ($email === '') { echo json_encode(['success' => false, 'message' => 'missing email']); return; }
    require_once ADMIN_APP_PATH . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'db.php';
    $pdo = get_db();
    try {
        $stmt = $pdo->prepare('UPDATE providers SET verified = 1, status = ? WHERE email = ?');
        $stmt->execute(['verified', $email]);
        $stmt2 = $pdo->prepare('UPDATE users SET role = ? WHERE email = ?');
        $stmt2->execute(['provider', $email]);
        echo json_encode(['success' => true]);
    } catch (Throwable $e) {
        echo json_encode(['success' => false, 'message' => 'db_error']);
    }
});

$router->post('/api/admin/providers/revoke', function() {
    header('Content-Type: application/json');
    $raw = file_get_contents('php://input');
    $json = json_decode($raw ?: 'null', true) ?: [];
    $email = trim((string)($json['email'] ?? ''));
    $removeRole = (bool)($json['remove_role'] ?? false);
    if ($email === '') { echo json_encode(['success' => false, 'message' => 'missing email']); return; }
    require_once ADMIN_APP_PATH . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'db.php';
    $pdo = get_db();
    try {
        $stmt = $pdo->prepare('UPDATE providers SET verified = 0, status = ? WHERE email = ?');
        $stmt->execute(['revoked', $email]);
        if ($removeRole) {
            $stmt2 = $pdo->prepare('UPDATE users SET role = NULL WHERE email = ?');
            $stmt2->execute([$email]);
        }
        echo json_encode(['success' => true]);
    } catch (Throwable $e) {
        echo json_encode(['success' => false, 'message' => 'db_error']);
    }
});

if ($enableMock) {
$router->get('/mock-api/*', function() {
    $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
    $prefix = '/mock-api/';
    $rel = substr($path, strlen($prefix));
    if ($rel === false) { $rel = ''; }
    $mockRoot = BASE_PATH . DIRECTORY_SEPARATOR . 'mock-api';
    $candidate = $mockRoot . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $rel);
    // Special-case: forward any /mock-api/bookings/* request to bookings/index.php
    // This ensures sub-routes like /mock-api/bookings/returns are handled by the index router.
    if (strpos($path, '/mock-api/bookings/') === 0) {
        $index = $mockRoot . DIRECTORY_SEPARATOR . 'bookings' . DIRECTORY_SEPARATOR . 'index.php';
        if (is_file($index)) { run_php($index); return; }
    }
    if (is_file($candidate)) {
        $ext = strtolower(pathinfo($candidate, PATHINFO_EXTENSION));
        if ($ext === 'php') { run_php($candidate); return; }
        serve_static($candidate); return;
    }
    if (is_dir($candidate)) {
        $index = $candidate . DIRECTORY_SEPARATOR . 'index.php';
        if (is_file($index)) { run_php($index); return; }
    }
    http_response_code(404);
    echo 'Not Found';
});

$router->post('/mock-api/*', function() {
    $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
    $prefix = '/mock-api/';
    $rel = substr($path, strlen($prefix));
    if ($rel === false) { $rel = ''; }
    $mockRoot = BASE_PATH . DIRECTORY_SEPARATOR . 'mock-api';
    $candidate = $mockRoot . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $rel);
    // Special-case: forward any /mock-api/bookings/* request to bookings/index.php
    if (strpos($path, '/mock-api/bookings/') === 0) {
        $index = $mockRoot . DIRECTORY_SEPARATOR . 'bookings' . DIRECTORY_SEPARATOR . 'index.php';
        if (is_file($index)) { run_php($index); return; }
    }
    if (is_file($candidate)) {
        $ext = strtolower(pathinfo($candidate, PATHINFO_EXTENSION));
        if ($ext === 'php') { run_php($candidate); return; }
        serve_static($candidate); return;
    }
    if (is_dir($candidate)) {
        $index = $candidate . DIRECTORY_SEPARATOR . 'index.php';
        if (is_file($index)) { run_php($index); return; }
    }
    http_response_code(404);
    echo 'Not Found';
});
}

// Friendly routes mapping legacy PHP pages to clean paths
// Auth
$router->get('/login', fn() => run_php(SITE_APP_PATH . '/login_sign up/login.php'));
$router->get('/signup', fn() => run_php(SITE_APP_PATH . '/login_sign up/sign up.php'));
$router->get('/reset-password', fn() => run_php(SITE_APP_PATH . '/login_sign up/Reset password.php'));
$router->get('/change-password', fn() => run_php(SITE_APP_PATH . '/login_sign up/Re-password.php'));

// Bookings overview and actions
$router->get('/bookings', fn() => run_php(SITE_APP_PATH . '/bookings/booking.php'));
$router->get('/bookings/details', fn() => run_php(SITE_APP_PATH . '/bookings/full_booking_details.php'));
$router->get('/bookings/rate', fn() => run_php(SITE_APP_PATH . '/bookings/rate_sp.php'));
$router->get('/bookings/return', fn() => run_php(SITE_APP_PATH . '/bookings/request_return.php'));

// Booking process steps
$router->get('/booking/overview', fn() => run_php(SITE_APP_PATH . '/booking_process/booking_overview.php'));
$router->get('/booking/schedule', fn() => run_php(SITE_APP_PATH . '/booking_process/booking_schedule.php'));
$router->get('/booking/location', fn() => run_php(SITE_APP_PATH . '/booking_process/booking_location.php'));
$router->get('/booking/choose-sp', fn() => run_php(SITE_APP_PATH . '/booking_process/choose_sp.php'));
$router->get('/booking/sp-details', fn() => run_php(SITE_APP_PATH . '/booking_process/show_sp_details.php'));
$router->get('/booking/confirm', fn() => run_php(SITE_APP_PATH . '/booking_process/confirm_booking.php'));
$router->get('/booking/voucher', fn() => run_php(SITE_APP_PATH . '/booking_process/booking_voucher.php'));
$router->get('/booking/edit-address', fn() => run_php(SITE_APP_PATH . '/booking_process/booking_edit_address.php'));

// Services - Cleaning
$router->get('/services/cleaning', fn() => run_php(SITE_APP_PATH . '/Homecleaning/cleaning_services.php'));
$router->get('/services/cleaning/ac', fn() => run_php(SITE_APP_PATH . '/Homecleaning/aircon.php'));
$router->get('/services/cleaning/ac-deep', fn() => run_php(SITE_APP_PATH . '/Homecleaning/aircon_deep_clean.php'));

// My Account
$router->get('/account', fn() => run_php(SITE_APP_PATH . '/my_account/my_account.php'));
$router->get('/account/voucher', fn() => run_php(SITE_APP_PATH . '/my_account/my_voucher.php'));
$router->get('/account/privacy', fn() => run_php(SITE_APP_PATH . '/my_account/privacy_settings.php'));
$router->get('/account/address', fn() => run_php(SITE_APP_PATH . '/my_account/account_address.php'));
$router->get('/account/referral', fn() => run_php(SITE_APP_PATH . '/my_account/account_referral.php'));
$router->get('/account/referral/success', fn() => run_php(SITE_APP_PATH . '/my_account/referral_success.php'));
$router->get('/account/connect', fn() => run_php(SITE_APP_PATH . '/my_account/connect_haustap.php'));
$router->get('/account/password/current', fn() => run_php(SITE_APP_PATH . '/my_account/current_password.php'));
$router->get('/account/password/saved', fn() => run_php(SITE_APP_PATH . '/my_account/password_saved.php'));

// Explicit, separated login pages for client and admin
$router->get('/client/login', fn() => run_php(SITE_APP_PATH . '/login_sign up/login.php'));
$router->get('/admin/login', fn() => run_php(BASE_PATH . '/admin_haustap/admin_haustap/login.php'));

// Friendly aliases
$router->get('/login', fn() => run_php(SITE_APP_PATH . '/login_sign up/login.php'));

// If MVC routes handled it, stop here
if ($router->dispatch($_SERVER['REQUEST_URI'] ?? '/', $_SERVER['REQUEST_METHOD'] ?? 'GET')) {
    return true;
}

// Dual-root fallback router to serve legacy pages and static assets
$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
$normalized = str_replace('/', DIRECTORY_SEPARATOR, $uri);

// Resolve in site app first
$sitePath = SITE_APP_PATH . $normalized;

// Resolve admin paths by stripping the admin prefix before joining
$ADMIN_PREFIX = '/admin_haustap/admin_haustap';
$adminResolved = null;
if (strpos($uri, $ADMIN_PREFIX) === 0) {
    $rel = substr($uri, strlen($ADMIN_PREFIX)); // e.g. /dashboard.php or /css/style.css
    if ($rel === false || $rel === '') { $rel = '/'; }
    $adminResolved = ADMIN_APP_PATH . str_replace('/', DIRECTORY_SEPARATOR, $rel);
}

// Early guard: if request targets admin app and the target file exists, serve it immediately
if ($adminResolved && is_file($adminResolved)) {
    $ext = strtolower(pathinfo($adminResolved, PATHINFO_EXTENSION));
    if ($ext === 'php') {
        error_log('[router] early admin php: ' . $adminResolved);
        run_php($adminResolved);
        return true;
    }
    error_log('[router] early admin asset: ' . $adminResolved);
    serve_static($adminResolved);
    return true;
}

// Serve static assets by streaming with the correct content type
function serve_static(string $file): void {
    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
    $types = [
        'css' => 'text/css',
        'js' => 'application/javascript',
        'png' => 'image/png',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'gif' => 'image/gif',
        'svg' => 'image/svg+xml',
        'ico' => 'image/x-icon',
        'woff' => 'font/woff',
        'woff2' => 'font/woff2',
        'ttf' => 'font/ttf',
        'map' => 'application/json'
    ];
    if (isset($types[$ext])) {
        header('Content-Type: ' . $types[$ext]);
    }
    readfile($file);
}

// Helper to require PHP files
function run_php(string $file): void {
    $dir = dirname($file);
    if (is_dir($dir)) {
        chdir($dir);
    }
    require $file;
}

// Static assets
if (preg_match('/\.(?:png|jpg|jpeg|gif|svg|css|js|ico|woff2?|ttf|map)$/i', $uri)) {
    // Alias asset resolution for friendly routes
    $aliasStaticPath = (function(string $uri): ?string {
        $aliases = [
            '/booking/' => SITE_APP_PATH . DIRECTORY_SEPARATOR . 'booking_process' . DIRECTORY_SEPARATOR,
            '/account/' => SITE_APP_PATH . DIRECTORY_SEPARATOR . 'my_account' . DIRECTORY_SEPARATOR,
            '/login/' => SITE_APP_PATH . DIRECTORY_SEPARATOR . 'login_sign up' . DIRECTORY_SEPARATOR,
            '/signup/' => SITE_APP_PATH . DIRECTORY_SEPARATOR . 'login_sign up' . DIRECTORY_SEPARATOR,
            '/reset-password/' => SITE_APP_PATH . DIRECTORY_SEPARATOR . 'login_sign up' . DIRECTORY_SEPARATOR,
            '/change-password/' => SITE_APP_PATH . DIRECTORY_SEPARATOR . 'login_sign up' . DIRECTORY_SEPARATOR,
            '/services/' => SITE_APP_PATH . DIRECTORY_SEPARATOR . 'Homecleaning' . DIRECTORY_SEPARATOR,
        ];
        foreach ($aliases as $prefix => $root) {
            if (strpos($uri, $prefix) === 0) {
                $rel = substr($uri, strlen($prefix));
                if ($rel === false) { $rel = ''; }
                $candidate = rtrim($root, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $rel);
                if (is_file($candidate)) { return $candidate; }
            }
        }
        return null;
    })($uri);
    if ($aliasStaticPath) { serve_static($aliasStaticPath); return true; }

    if (is_file($sitePath)) { serve_static($sitePath); return true; }
    // Fallback: serve project-root assets like /css/global.css, /client/... not under SITE_APP_PATH
    $rootPath = BASE_PATH . $normalized;
    if (is_file($rootPath)) { serve_static($rootPath); return true; }
    if ($adminResolved && is_file($adminResolved)) { serve_static($adminResolved); return true; }
}

// Direct PHP file
if (is_file($sitePath) && substr($sitePath, -4) === '.php') { 
    error_log('[router] site direct php: ' . $sitePath);
    run_php($sitePath); return true; 
}
if ($adminResolved && is_file($adminResolved) && substr($adminResolved, -4) === '.php') { 
    error_log('[router] admin direct php: ' . $adminResolved);
    run_php($adminResolved); return true; 
}

// Directory index
if (is_dir($sitePath)) {
    $index = $sitePath . DIRECTORY_SEPARATOR . 'index.php';
    if (is_file($index)) { run_php($index); return true; }
}
if ($adminResolved && is_dir($adminResolved)) {
    $index = $adminResolved . DIRECTORY_SEPARATOR . 'index.php';
    if (is_file($index)) { run_php($index); return true; }
}

// If the request is for the admin prefix but no file matched, return 404 here
if (strpos($uri, $ADMIN_PREFIX) === 0) {
    http_response_code(404);
    error_log('[router] admin 404 for uri: ' . $uri);
    echo 'Not Found';
    return true;
}

// Fallback to original site router for its custom logic (e.g., mock-api)
require SITE_APP_PATH . '/router.php';
return true;
