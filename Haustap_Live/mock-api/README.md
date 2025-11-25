# HausTap Mock API (PHP)

Simple mock endpoints for local development.

## Run Locally

- `php -S localhost:8009 -t mock-api`
- Configure endpoints in `config.php` and helpers in `_lib/`.

## Notes

- Ensure mail/OTP flows in `auth/otp` match your testing needs.
- No UI/UX changes.

## Endpoints

- `POST /mock-api/auth/login` — returns `{ success, token, user }` for any non-empty credentials (dev only)
- `POST /mock-api/auth/otp/send` — sends or simulates OTP delivery and returns a dev code
- `GET/POST /mock-api/bookings` and related actions — mock booking lifecycle
