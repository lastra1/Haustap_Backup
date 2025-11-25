# Laravel MVC Migration Guide

This repository already contains a Laravel app at `backend/api` with standard MVC layout:

- Controllers: `backend/api/app/Http/Controllers/*`
- Models: `backend/api/app/Models/*`
- Views (Blade): `backend/api/resources/views/*`
- Routes: `backend/api/routes/{web.php, api.php}`
- Public: `backend/api/public/*`

## Goals

Align legacy PHP pages with Laravel MVC without breaking existing behavior.

## Admin (Legacy UI)

- Served at `/admin` via `resources/views/admin/dashboard.blade.php`.
- The Blade view wraps the legacy admin using an `<iframe>` sourced from `LEGACY_ADMIN_URL`.
- Continue running the legacy admin with `php -S 0.0.0.0:5001 router.php`.

## Guest Pages

- New controller: `App\Http\Controllers\GuestController` with actions for Home, Login, Register.
- Routes: `/home`, `/login`, `/register` defined in `routes/web.php`.
- Views: `resources/views/guest/home.blade.php`, `resources/views/auth/{login,register}.blade.php`.

## Incremental Migration Checklist

1. Identify a legacy PHP page to migrate (e.g., `login_sign up/` or `client/`).
2. Create a corresponding Blade view under `resources/views/...`.
3. Add a controller method or use `Route::view(...)` in `routes/web.php`.
4. Move any inline PHP logic into a controller and/or a service class.
5. For data access, prefer models under `app/Models` or the existing `App\Support\FileJsonStore` during transition.
6. Keep assets under `backend/api/public` (CSS, JS, images) or use Vite for bundling.

## Notes

- API endpoints remain under `/api/*` as defined in `routes/api.php`.
- The Expo apps and Android builds read `EXPO_PUBLIC_API_BASE` and are unaffected by this migration.
- Avoid deleting legacy files until their Blade counterparts are verified.