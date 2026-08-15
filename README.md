# Tazcara

Tazcara is a bus-booking API built with Laravel 12, MySQL, and Docker. Customers search trips by city pair and date, see live seat availability per trip, and book one or more seats atomically.

The API runs on `http://localhost:8000`.

---

## Quick start (Docker)

**Prerequisites:** Docker + Docker Compose. No local PHP/MySQL/Composer required — the containers take care of everything.

```bash
# 1. Start the stack (builds images, copies .env from .env.example if missing)
make up

# 2. See the running services
make ps
```

`make up` alone is enough. On first boot the `app` container automatically:

1. creates `.env` from `.env.example` if it does not exist,
2. runs `composer install` if `vendor/` is missing,
3. generates `APP_KEY` if unset,
4. waits for MySQL to be healthy,
5. runs `php artisan migrate --force`,
6. seeds the database **only when it is empty and `APP_ENV != production`** (see [Seeding](#seeding)).

Then point your client at `http://localhost:8000/api/v1/...`.

### Make targets

| Command                  | What it does                                              |
| ------------------------ | --------------------------------------------------------- |
| `make up`                | `docker compose up -d --build` (auto-creates `.env`)      |
| `make down`              | Stop the stack                                            |
| `make restart`           | Restart the stack                                         |
| `make logs`              | Follow the app container logs                             |
| `make ps`                | Show container status                                     |
| `make migrate`           | Run pending migrations                                    |
| `make fresh`             | `migrate:fresh --seed` — **refused unless `APP_ENV=local`** |
| `make seed`              | `db:seed` — **refused unless `APP_ENV=local`**            |
| `make art CMD="route:list"` | Run an artisan command in the app container            |
| `make shell`             | Open a shell inside the app container                     |
| `make composer ARG="require foo/bar"` | Run Composer in the app container        |

---

## Configuration

Configuration lives in `.env` (copied from `.env.example`). The values the containers actually need:

| Variable            | Default      | Notes                                                        |
| ------------------- | ------------ | ------------------------------------------------------------ |
| `APP_ENV`           | `local`      | `make fresh` / `make seed` are refused unless `local`/`testing`. |
| `APP_URL`           | `http://localhost:8000` | Matches the Nginx port mapping.                    |
| `DB_HOST` / `DB_PORT` | `127.0.0.1` / `3307` | Host-facing MySQL port.                    |
| `DB_DATABASE`       | `tazcara_db` |                                                              |
| `DB_USERNAME`       | `tazcara`    |                                                              |
| `DB_PASSWORD`       | `password`   |                                                              |
| `DB_ROOT_PASSWORD`  | `root`       |                                                              |

### Port notes

- **Nginx → `8000`**: the app is served on `http://localhost:8000`.
- **MySQL → `3307` (host)**: the `db` container is published on host port `3307` (configurable via `DB_PORT`). The `app` container always talks to it internally on `3306`. Keep `3307` if you run a local MySQL on `3306`; otherwise set it to `3306` so host-side `php artisan` commands hit the same database.

No other configuration is required. Sessions, cache, and the queue all use the database driver by default, so no Redis install is needed.

---

## Seeding

Seeders populate the data you need to try the API:

- a test user,
- cities (`CAI`, `FYM`, `MNY`, `ASY`, `ALX`, …),
- two buses and their seats (12 seats per bus),
- two trips with ordered stops, per-leg prices, and departure/arrival timestamps.

The entrypoint seeds only on an **empty** database and never in `production`. To reset everything locally: `make fresh` (protected by the `APP_ENV` guard — see `Makefile`).

### Test user

| Field    | Value            |
| -------- | ---------------- |
| Email    | `test@example.com` |
| Password | `password`       |

---

## Administration area

There is **no admin UI or admin API implemented yet**. The data model includes an `is_admin` boolean on `users` as the placeholder for future admin functionality (e.g. managing trips, buses, seats, and pricing). No admin routes, controllers, or panels exist in the codebase at this time.

Until an admin area is built:

- every API route requires a valid Sanctum bearer token (there are no anonymous admin endpoints), and
- marking a user as admin is only possible at the database level, for example:

```bash
make art CMD="tinker"
```

```php
App\Models\User::where('email', 'test@example.com')->update(['is_admin' => true]);
```

The `is_admin` flag is currently **not enforced anywhere**; it exists only as the schema placeholder for the upcoming administration feature.

---

## API

All endpoints live under `/api/v1`. Everything except `auth/register` and `auth/login` requires the `Authorization: Bearer <token>` header (Sanctum).

### Postman collection

A ready-to-use Postman collection is available:

**https://go.postman.co/workspace/d6721efd-1ef0-441b-8ac0-8450f1da36cd**

### Authentication

**Register**

```
POST /api/v1/auth/register
```

```json
{
  "first_name": "John",
  "last_name": "Doe",
  "email": "john@example.com",
  "mobile": "01000000000",
  "password": "secret123",
  "password_confirmation": "secret123"
}
```

**Login** (returns `access_token`)

```
POST /api/v1/auth/login
```

```json
{ "email": "test@example.com", "password": "password" }
```

### Trips

**Find trips and available seats** for a city pair on a date

```
GET /api/v1/trips/available-seats?from_city={city_uuid}&to_city={city_uuid}&date=YYYY-MM-DD
```

Returns the matching trips (those whose ordered stops contain both cities in the right order) together with their currently available seats.

### Bookings

**Book one or more seats on a trip** — atomic: either every seat is booked or none is

```
POST /api/v1/trips/{trip_uuid}/book-seats
```

```json
{
  "seats": ["{seat_uuid}", "{seat_uuid}"],
  "from_city": "{city_uuid}",
  "to_city": "{city_uuid}",
  "date": "2026-08-16"
}
```

- `{trip_uuid}` and the seat/city UUIDs are taken from the `available-seats` response.
- The price is computed server-side from the leg's `price_from_origin` values (never from the client).
- A seat is considered taken when a **confirmed** booking overlaps the requested leg. Booking a bus-full leg like Cairo → Al-Minya blocks shorter legs covering it (e.g. Al-Fayyum → Al-Minya) but not later ones (e.g. Al-Minya → Asyut).
- Responses: `201` booked · `400` invalid leg / seat not on this trip / wrong date · `401` unauthenticated · `404` resource not found · `409` seat(s) already taken.

---

## Testing

The suite uses [Pest](https://pestphp.com) with an in-memory SQLite database, so no external services are needed:

```bash
./vendor/bin/pest            # full suite
./vendor/bin/pest --filter=Booking   # subset
```

---

## Production safety

- `make fresh` and `make seed` refuse to run unless `APP_ENV=local`/`testing` (see `Makefile` → `guard-local`), so CI/CD pipelines can never wipe a production database.
- The entrypoint never seeds in `production` and only runs non-destructive `migrate --force`.
- For deployments, run `make migrate` — nothing else.