# Environment Setup: Development / Staging / Production

This repository contains two PHP web apps (Client and Admin) and a Laravel-style backend. This document outlines how to run each environment consistently.

## 1) Environments and .env files

Top-level `.env` loader lives in `bootstrap.php`. It loads:
- Base file: `.env`
- Overrides: `.env.{APP_ENV}` (e.g. `.env.development`, `.env.staging`, `.env.production`)

Environment variables are made available via `getenv()` and `$_ENV` to all PHP scripts.

Included templates:
- Root: `.env.example`, `.env.development.example`, `.env.staging.example`, `.env.production.example`
- Backend: `backend/.env.example`, `backend/.env.development.example`, `backend/.env.staging.example`, `backend/.env.production.example`

Recommended usage:
- Development: copy `.env.development.example` to `.env` at repo root. For backend, copy `backend/.env.development.example` to `backend/.env`.
- Staging: copy `.env.staging.example` to `.env` (root) and `backend/.env.staging.example` to `backend/.env` on the staging server.
- Production: copy `.env.production.example` to `.env` (root) and `backend/.env.production.example` to `backend/.env` on the production server.

Sensitive values (`DB_PASSWORD`, `SMTP_PASS`) must be set on each server; never commit secrets.

## 2) Error reporting and logs

- Controlled by `APP_DEBUG`. When `true`, errors are shown; otherwise hidden.
- PHP error logs go to `storage/logs/php-error.log` (created automatically).
- Laravel backend uses its own logging per `backend/config/logging.php` and `.env`.

## 3) Development

Monorepo dev quickstart (Windows):

```powershell
# From repo root
./start-all.ps1 -LegacyPort 5001 -ApiPort 8001 -ExpoPort 8082
```

Manual start:

```powershell
# Legacy PHP (friendly router at repo root)
php -S localhost:5001 router.php

# Laravel API
cd backend/api
php artisan serve --port 8001

# Expo Web (uses react-native-maps web stub)
cd android-capstone-main/HausTap
npx expo start --web --port 8082 --clear
```

Backend env:
- Copy `backend/api/.env.example` to `backend/api/.env`, then set:
  - `DB_CONNECTION=mysql`, `DB_HOST=127.0.0.1`, `DB_PORT=3306`
  - `DB_DATABASE=haustap`, `DB_USERNAME=haustap`, `DB_PASSWORD=haustap`
  - `STORE_DRIVER=mysql`
  - Optional: `LEGACY_ADMIN_URL=http://localhost:5001/admin_haustap/admin_haustap/dashboard.php`

Migrations and data sync:

```powershell
# Generate app key (once)
cd backend/api
php artisan key:generate

# Ensure MySQL is running, then migrate
php artisan migrate --force

# Optional: import existing JSON files into DB-backed store
php artisan store:sync
```

## 4) Staging

General approach:
- Host Client and Admin under a web server (Apache/Nginx/IIS) pointing to their respective directories.
- Set `.env` and `backend/.env` to the staging variants.
- Use a dedicated MySQL database (`haustap_staging`).
- Disable `APP_DEBUG`.

Example Nginx server blocks (Linux):

```
server {
    server_name staging.haustap.example.com;
    root /var/www/Haustap_Capstone/Haustap_Capstone-Haustap_Connecting/Haustap_Capstone-Haustap_Connecting;
    index index.php index.html;
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    location ~ \.php$ {
        include fastcgi_params;
        fastcgi_pass unix:/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }
}

server {
    server_name admin.staging.haustap.example.com;
    root /var/www/Haustap_Capstone/admin_haustap/admin_haustap;
    index index.php;
    location ~ \.php$ {
        include fastcgi_params;
        fastcgi_pass unix:/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }
}
```

## 5) Production

Same layout as staging with production `.env` files and hardening:
- `APP_DEBUG=false`
- Strict firewall and TLS
- Use managed MySQL or equivalent
- Enable caching (OPcache, HTTP cache) and CDN for static assets

## 6) Database strategy

- Development: local MySQL recommended (Docker or native). If unavailable, controllers fall back to file JSON stores.
- Staging: MySQL schema mirroring production
- Production: MySQL with backups and migrations

## 7) Mail strategy

- Development: test accounts or mailhog
- Staging: provider sandbox/tags
- Production: dedicated sender with DKIM/SPF

## 8) Environment variables overview

Key variables:
- `APP_ENV` — `development` | `staging` | `production`
- `APP_DEBUG` — `true`/`false` shows/hides detailed errors
- `APP_URL` — base URL used by backend
- `DB_*` — database connection per environment
- `SMTP_*` — mail settings consumed by `mock-api/config.php`
- `LOG_LEVEL` — logging granularity

## 9) Notes

- Do not commit secrets.
- Ensure CI/CD replaces `.env` files per environment during deploy.
- For Windows IIS, map sites to the two directories and enable PHP.
 - For onboarding, you can pull the tag `v0.1.0-onboarding` to ensure a consistent snapshot.
### Mobile App (Expo)

The mobile app lives in `android-capstone-main/HausTap/`.

- Create a local env file:
  - Copy `android-capstone-main/HausTap/.env.example` to `android-capstone-main/HausTap/.env`.
  - Set `GMAIL_USER` and `GMAIL_APP_PASSWORD` (use a Gmail App Password or SMTP credentials).
  - Optional: adjust `PORT` if `3000` is busy.

- Start the local email/Express server:
  - `cd android-capstone-main/HausTap`
  - `npm install`
  - `npm run server` (reads config from `.env`)

- Run the Expo app:
  - In the same directory: `npm start`
  - Open with Android emulator, iOS simulator, or Expo Go.

Notes:
- `.env` is intentionally ignored by Git; only `.env.example` is committed.
- `process.env.EXPO_OS` comes from Expo; you do not need to set it.

### Operational Notes
- Services and Ports
  - Legacy PHP: `http://localhost:5001/`
  - Laravel API: `http://127.0.0.1:8001/` (admin wrapper at `/admin`)
  - Expo Web: `http://localhost:8082/`
- CORS
  - `backend/api/config/cors.php` allows `localhost` and `127.0.0.1` across dev ports 5001 and 8082 by default.
- MySQL
  - If Docker Desktop is installed: `docker compose up -d db` (see `docker-compose.yml`).
  - Otherwise install MySQL locally, create DB `haustap`, user `haustap` with password `haustap`, then run migrations.
- Data persistence
  - When `STORE_DRIVER=mysql`, data is stored in `json_store` table.
  - If DB is unavailable, controllers use file JSON under `storage/data/*` automatically.
- Admin UI
  - The admin UI is unchanged; Laravel serves a Blade iframe wrapper at `/admin` pointing to the legacy admin.
