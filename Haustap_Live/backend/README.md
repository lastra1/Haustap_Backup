# HausTap Backend Libraries

Shared backend libraries and migrations.

## Setup

- `composer install`
- Libraries should be autoloaded via `vendor/autoload.php`.

## Usage

- Integrate from PHP apps using `require_once __DIR__ . '/vendor/autoload.php';`
- Ensure database settings are configured if migrations are used.

---

## Filament Admin Setup (Laravel)

To power the admin with Filament (panels, charts), create a Laravel app inside `backend/laravel-app` and install Filament.

### Create Laravel app

1. `composer create-project laravel/laravel backend/laravel-app`
2. `cd backend/laravel-app`
3. Copy `.env.example` to `.env` and set your DB connection.

### Install Filament and chart plugin

```
composer require filament/filament:^3.2 leandrocfe/filament-apex-charts:^3 spatie/laravel-permission:^6
```

### Generate Admin panel (Filament v3)

```
php artisan make:filament-panel Admin
```

This creates an Admin panel accessible at `/admin`.

### Create an admin user

Seed or create a user:

```
php artisan tinker
>>> \App\Models\User::updateOrCreate(['email' => 'admin@haustap.local'], ['name' => 'Admin', 'password' => bcrypt('Admin123!')])
```

---

## SSO Bridge from PHP Admin to Filament

Your PHP admin links to `http://localhost:8001/sso/admin` via `admin_haustap/admin_haustap/filament_redirect.php`. Implement this endpoint in Laravel to log in the user by email and redirect into Filament.

### .env

```
ADMIN_SSO_SECRET=dev-sso-secret
APP_URL=http://localhost:8001
```

### Route (`routes/web.php`)

```php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminSsoController;

Route::get('/sso/admin', [AdminSsoController::class, 'login']);
```

### Controller (`app/Http/Controllers/AdminSsoController.php`)

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AdminSsoController extends Controller
{
    public function login(Request $request)
    {
        $email = $request->query('email');
        $ts = (int) $request->query('ts');
        $sig = $request->query('sig');
        $redirect = $request->query('redirect', '/admin');

        if (!$email || !$ts || !$sig) {
            abort(400, 'Missing parameters');
        }
        if (abs(time() - $ts) > 300) { // 5 minutes
            abort(401, 'Link expired');
        }

        $secret = env('ADMIN_SSO_SECRET');
        $expected = hash_hmac('sha256', $email . '|' . $ts, $secret);
        if (!hash_equals($expected, $sig)) {
            abort(401, 'Invalid signature');
        }

        $user = User::firstOrCreate(['email' => $email], [
            'name' => 'Admin',
            'password' => bcrypt(str()->random(16)),
        ]);

        Auth::login($user, true);
        return redirect($redirect);
    }
}
```

---

## Example Pie Chart Widget (Filament ApexCharts)

Install the plugin (see above), then create a widget at `app/Filament/Widgets/BookingStatusPieChart.php`:

```php
<?php

namespace App\Filament\Widgets;

use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class BookingStatusPieChart extends ApexChartWidget
{
    protected static string $chartId = 'bookingStatusPie';
    protected static ?string $heading = 'Booking Status';

    protected function getOptions(): array
    {
        // Replace with real counts from your database
        $series = [10, 3, 7, 2]; // Pending, Scheduled, Ongoing, Completed

        return [
            'chart' => ['type' => 'pie', 'height' => 300],
            'labels' => ['Pending', 'Scheduled', 'Ongoing', 'Completed'],
            'series' => $series,
            'colors' => ['#f59e0b', '#06b6d4', '#8b5cf6', '#22c55e'],
        ];
    }
}
```

Register the widget in your Admin panel provider so it appears on the dashboard.

---

## Run Locally

From `backend/laravel-app`:

- `php artisan serve --host=localhost --port=8001`
- Open `http://localhost:8001/admin` directly, or from the PHP admin click “Open Filament Admin” (which calls the SSO endpoint).

---

This document adds backend setup instructions. No changes to the existing PHP admin UI are required to use Filament.
This folder will host the unified Laravel API for Admin, Client (web), and the Expo application. The Laravel app is scaffolded under `backend/api`.

Dev quickstart:
- Serve Admin: `php -S localhost:5000 -t admin_haustap/admin_haustap`
- Serve Client: `php -S localhost:5001 router.php`
- Serve Laravel API: from `backend/api`, run `php artisan serve --host 127.0.0.1 --port 8001`
- Expo (web): `npx expo start --web`

API base for web client:
- In the browser console on client pages, run:
  `window.API_TARGET = 'backend'; window.BACKEND_BASE = 'http://127.0.0.1:8001/api';`
  This keeps UI unchanged while switching data calls to the Laravel backend.

Endpoints (initial):
- `POST /api/auth/otp/send`, `POST /api/auth/otp/verify`
- `GET/POST /api/bookings`, `POST /api/bookings/{id}/cancel`, `POST /api/bookings/{id}/status`, `POST /api/bookings/{id}/rate`, `POST /api/bookings/{id}/return`, `GET /api/bookings/returns`
- `POST /api/chat/open`, `GET/POST /api/chat/{booking_id}/messages`
- `GET/POST /api/admin/settings`

These endpoints will initially use file-backed storage for dev parity with `mock-api` and can be migrated to DB models incrementally.
