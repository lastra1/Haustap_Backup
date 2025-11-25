<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>HausTap Home</title>
    <style>
        body { font-family: system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, sans-serif; margin: 2rem; }
        .wrap { max-width: 720px; margin: 0 auto; }
        h1 { margin-bottom: 0.25rem; }
        .sub { color: #666; margin-top: 0; }
        .links a { display: inline-block; margin-right: 1rem; }
    </style>
</head>
<body>
<div class="wrap">
    <h1>HausTap</h1>
    <p class="sub">Laravel MVC scaffolding in place.</p>
    <div class="links">
        <a href="/login">Login</a>
        <a href="/register">Register</a>
        <a href="/admin">Admin</a>
    </div>
    <p>This page is a placeholder view under <code>resources/views/guest/home.blade.php</code>. Migrate legacy pages here incrementally.</p>
    <p>API base: <code>{{ env('APP_URL') }}/api</code></p>
    <p>Legacy Admin: <code>{{ env('LEGACY_ADMIN_URL', 'http://localhost:5001/admin_haustap/admin_haustap/dashboard.php') }}</code></p>
</div>
</body>
</html>