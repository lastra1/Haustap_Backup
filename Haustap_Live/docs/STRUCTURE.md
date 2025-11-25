# Project Structure

This document outlines the canonical layout after cleanup so the project stays organized and easy to navigate.

## Top-Level
- `backend/` — Legacy PHP site and helper scripts that front the custom router.
- `backend/api/` — Laravel API application providing routes, controllers, models, and Blade views.
- `mobile_app/` — React Native / Expo mobile application source.
- `docker/` — Container build contexts and configs.
- `docker-compose.yml` — Orchestrates web, db, adminer, and api services.
- `docs/` — Project documentation, migration guides, and structure overviews.
- `public/` — Front controller and static assets for the custom PHP site.
- `core/` — Minimal router and bootstrap for the custom PHP site.

## Laravel API (`backend/api`)
- `app/Http/Controllers/` — Controllers for API and simple web views.
- `app/Models/` — Eloquent models (`User.php`).
- `app/Support/` — Utility classes like `FileJsonStore.php`.
- `resources/views/` — Blade views (`admin/dashboard.blade.php`, `welcome.blade.php`, plus guest views).
- `routes/web.php` — Web routes for Blade views.
- `artisan` — Laravel CLI entry point.
- `storage/` — Logs and framework caches. Gitignored except `.gitkeep`.

## Custom PHP Site
- `public/index.php` — Front controller defining both legacy PHP routes and MVC-style routes.
- `core/Router.php` — Simple router supporting exact and wildcard matches.
- `bootstrap.php` — Environment loading, autoloader, and base path constants.
- `backend/` — Legacy PHP pages gradually migrated into Laravel.

## Mobile App
- `android-capstone-main/HausTap/` — Primary React Native app folder (Expo managed workflow).
- Build artifacts (`android/`, `ios/`, `.expo/`) are gitignored.

## Docker and Infra
- `docker/web/` — Web server config (e.g., Apache/Nginx site definitions).
- `docker/db/` — Database image and init scripts.
- `docker/adminer/` — Adminer service configs.
- `docker-compose.yml` — Service definitions, volumes, and health checks.

## Conventions
- Favor Laravel MVC for new web pages and migrate legacy PHP incrementally.
- Keep temporary logs and snapshots out of version control (see `.gitignore`).
- Place documentation in `docs/` with concise, task-oriented guides.

## Migration Checklist
- Move legacy page templates into `resources/views/` as Blade views.
- Shift PHP logic to Laravel controllers under `app/Http/Controllers`.
- Update `routes/web.php` to route to controllers instead of direct PHP files.
- Preserve the legacy admin iframe (`admin.dashboard`) until fully replaced.