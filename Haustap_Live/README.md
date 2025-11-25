# HausTap Capstone — Monorepo

This repository contains multiple projects used across the HausTap Capstone:

- Web app (PHP) — `Haustap_Capstone-Haustap_Connecting/Haustap_Capstone-Haustap_Connecting`
- Admin panel (PHP) — `admin_haustap/admin_haustap`
- Root MVC app (PHP) — `public/`, `core/`, `app/`
- Mobile app (Expo/React Native) — `android-capstone-main/HausTap`
- Mock API (PHP) — `mock-api`
- Backend libraries — `backend`

No UI or UX changes are introduced by this document.

## Fixed Ports

- Client Website: `8001` → `http://localhost:8001/`
- Expo Dev Server (web + Metro): `8082` → always start with `npm run web -- --port 8082`
- Adminer (Docker database UI): `8080` → `http://localhost:8080/` when Docker is running
- Node Backend API (optional): `3000` → controlled via `.env` `PORT=3000`

## Prerequisites

- `PHP 8.1+`
- `Composer`
- `Node.js 18+` and `npm` (or `yarn`)
- `Git`

## Environment Files

- Root examples: `.env.example`, `.env.development.example`, `.env.staging.example`, `.env.production.example`
- Backend examples: `backend/.env.example`, `backend/.env.development.example`, `backend/.env.staging.example`, `backend/.env.production.example`
- Mobile app: `android-capstone-main/HausTap/.env`

Create local `.env` files from the provided examples and fill credentials as needed. Secrets are ignored by Git. Example: copy `.env.development.example` to `.env` (root) and `backend/.env.development.example` to `backend/.env` for local development.

## Start Everything (Ports don’t change)

1. Client Website on `8001`:
   - `php -S localhost:8001 -t "Haustap_Live/Haustap_Capstone-Haustap_Connecting/Haustap_Capstone-Haustap_Connecting/guest"`
   - Open `http://localhost:8001/homepage.php`
   - Categories: `http://localhost:8001/services/{slug}` (e.g. `cleaning`, `outdoor`, `repairs`, `beauty`, `wellness`, `tech`)
2. Expo Dev Server on `8082` (no auto port switching):
   - `cd android-capstone-main/HausTap`
   - `npm install`
   - `npm run web -- --port 8082`
   - Web: `http://localhost:8082`
   - Android: scan the QR shown; Metro will bind to `8082`
3. Mobile App API Base URL:
   - The app uses `http://localhost:8001` (`src/config/apiConfig.js`)
   - No change needed; keep website/API on `8001`
4. Optional Node server:
   - `cd android-capstone-main/HausTap`
   - Create `.env` from `.env.example` and set `PORT=3000`
   - `npm run server` → serves on `http://localhost:3000`

## Git Ignore & Line Endings

- A comprehensive `.gitignore` avoids committing OS, IDE, build, vendor, and secret files.
- `.gitattributes` ensures consistent line endings across platforms without changing UI/UX.

## Pushing to GitHub (Replace Contents)

From the repo root:

- `git init`
- `git add .`
- `git branch -M main`
- `git commit -m "Sync project and add updated .gitignore + README"`
- `git remote add origin https://github.com/lastra1/Haustap_Capstone.git`
- `git push -u origin main --force`

If authentication is required, use a Personal Access Token (PAT) with `repo` scope.

## Troubleshooting

- `8082 in use`: close other Expo servers, or kill with PowerShell:
  - `netstat -ano | findstr :8082`
  - `taskkill /PID <pid> /F`
- `8001 in use`: stop any local Apache/IIS using 8001, then re-run the PHP server command above.
- CSS/JS 404s on website: confirm the PHP server `-t` points to `guest/`.
- API calls from mobile/web resolve to `http://localhost:8001`; keep that server running.

## APK Builds & Download Links

- The workflow `.github/workflows/android-apk.yml` builds two debug APKs: Client and Provider.
- To generate download links:
  - Go to GitHub → Actions → "Build Android APKs (Client & Provider)" → Run workflow.
  - After it finishes, open the run and download artifacts named:
    - `haustap-client-debug-apk`
    - `haustap-provider-debug-apk`
- These debug APKs are unsigned but installable on most Android devices when "Install from unknown sources" is enabled.
- The mobile app’s API base (`EXPO_PUBLIC_API_BASE`) defaults to `http://26.242.103.174:8001` for both variants.

## Docker API & Database

- Start stack: `docker compose up -d`.
- The API container auto-initializes on first start:
  - Copies `backend/api/.env.docker.example` to `.env` if missing.
  - Runs `composer install`.
  - Generates `APP_KEY` if empty.
  - Runs `php artisan migrate --force`.
- Database credentials (inside Docker): host `db`, user `haustap`, pass `haustap`, db `haustap`.
- Adminer: `http://localhost:8080/` for inspecting tables and data.

## Notes

- This README is purely operational guidance. It does not modify any UI or UX.
