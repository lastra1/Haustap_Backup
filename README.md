# HausTap — Backup Monorepo

This repository is a consolidated backup of the HausTap project. It contains the PHP web app, admin tools, Expo/React Native mobile app, backend utilities, mock APIs, Firebase functions, and Docker configs. No UI changes are included.

## Directory Overview

- `Haustap_Live/` — PHP website, admin tools, core MVC, docs
- `Haustap_Live/external_admin_cmir/` — legacy admin client management
- `Haustap_Live/android-capstone-main/HausTap/` — Expo/React Native mobile app
- `Haustap_Live/backend/` — backend services and Dockerized API
- `Haustap_Live/mock-api/` — lightweight PHP mock endpoints
- `Haustap_Live/functions/` — Firebase Cloud Functions
- `Haustap_Application_tmp/HausTap/` — alternate mobile app workspace

## Prerequisites

- PHP 8.1+
- Composer
- Node.js 18+ and npm
- Git
- Docker (optional for API/database stack)

## Quick Start

- Client website on `8001`:
  - `php -S localhost:8001 -t "Haustap_Live/Haustap_Capstone-Haustap_Connecting/Haustap_Capstone-Haustap_Connecting/guest"`
  - Open `http://localhost:8001/homepage.php`
- Expo dev server on `8082`:
  - `cd Haustap_Live/android-capstone-main/HausTap`
  - `npm install`
  - `npm run web -- --port 8082`
- Mobile app API base: `http://localhost:8001` (see `src/config/apiConfig.js`)
- Optional Node server:
  - `cd Haustap_Live/android-capstone-main/HausTap`
  - Create `.env` from `.env.example` with `PORT=3000`
  - `npm run server` → `http://localhost:3000`

## Environment Files

- Root examples: `.env.example`, `.env.development.example`, `.env.staging.example`, `.env.production.example`
- Backend examples: `Haustap_Live/backend/.env.*.example`
- Mobile app: `Haustap_Live/android-capstone-main/HausTap/.env`

Create local `.env` files from examples and fill credentials as needed. Secrets are ignored by Git.

## Fixed Ports

- Client Website: `8001` → `http://localhost:8001/`
- Expo Dev Server: `8082` → start with `npm run web -- --port 8082`
- Adminer (Docker DB UI): `8080` → `http://localhost:8080/` when Docker is running

## Backup to GitHub

From repository root:

- `git init`
- `git add .`
- `git branch -M main`
- `git commit -m "Backup: sync project and update README"`
- `git remote add origin https://github.com/lastra1/Haustap_Backup.git`
- `git push -u origin main`

If authentication is prompted, use a Personal Access Token (PAT) with `repo` scope.

## Troubleshooting

- `8082 in use`: close other Expo servers or kill the PID
- `8001 in use`: stop any local web servers using 8001 and re-run PHP server
- CSS/JS 404s: ensure PHP server `-t` points to `guest/`