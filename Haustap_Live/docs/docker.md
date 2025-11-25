Docker (local dev)

Prerequisites
- Install Docker Desktop (Windows/macOS/Linux). Ensure "Docker Compose" is enabled.

Services
- `web` (Apache + PHP 8.3) serves the root PHP app on `http://localhost:8088/`.
- `api` (Laravel dev server) serves the backend API on `http://localhost:8001/`.
- `db` (MySQL 8.0) listens on `localhost:3306`.
- `adminer` (DB UI) is available on `http://localhost:8080/`.

Start the stack
- From the project root: `docker compose up -d`
- Open UI: `http://localhost:8088/`
- Open API: `http://localhost:8001/api`

Live edit
- `web` maps the project root to `/var/www` inside the container.
- `api` maps `./backend/api` to `/var/www` inside the container.
- Editing files locally updates immediately; the API runs `php artisan serve`.
 - On first start, `api` auto-configures: copies `.env.docker.example` → `.env` if missing, installs Composer deps, generates `APP_KEY`, and runs migrations.

Stop / rebuild
- Stop: `docker compose down`
- Rebuild after Dockerfile changes: `docker compose up --build -d`

Database
- Connect from containers: host `db`, user `haustap`, password `haustap`, database `haustap`.
- Connect from host: `127.0.0.1:3306` with the same credentials.
- Root access: user `root`, password `haustap`.
- Data persists in the `db_data` volume.

Database UI (Adminer)
- Open: `http://localhost:8080/`
- Server: `db` (container) or `127.0.0.1` (host)
- Username: `haustap` (or `root`), Password: `haustap`, Database: `haustap`
- Use Adminer to inspect tables, run SQL, and import/export data.
