# Tazcara

Tazcara is a bus-booking API built with Laravel 13, MySQL, and Docker. Customers search trips by city pair and date, see live seat availability per trip, and book one or more seats atomically. A Sanctum-protected administration area lets admins manage cities, buses (with seats), and trips (with ordered stops) via `/api/v1/admin`.

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
| `make test`              | Run the Pest suite (in-memory SQLite) inside the app container |
| `make pint`              | Run Laravel Pint (code style) inside the app container    |
| `make dump`              | Regenerate `database/dumps/tazcara.sql` from the live DB  |

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
- six buses (First Class, Business, Standard) with 12 seats each,
- two trips with ordered stops, per-leg prices, and departure/arrival timestamps.

The entrypoint seeds only on an **empty** database and never in `production`. To reset everything locally: `make fresh` (protected by the `APP_ENV` guard — see `Makefile`).

A ready-to-import database dump with the exact seeded data is committed at `database/dumps/tazcara.sql`:

```bash
mysql -h 127.0.0.1 -P 3307 -u tazcara -p tazcara_db < database/dumps/tazcara.sql
```

The dump is regenerated from the live database with `make dump` (reads the credentials from `.env` and writes a deterministic, comment-free dump). Regenerate it whenever the seeders change.

### Test user

| Field    | Value            |
| -------- | ---------------- |
| Email    | `test@example.com` |
| Password | `password`       |

---

### Admin user

| Field    | Value            |
| -------- | ---------------- |
| Email    | `admin@example.com` |
| Password | `password`       |

---

## Administration area

The administration area is exposed as a Sanctum-protected API group under `/api/v1/admin`. Only users with `is_admin = true` can access it; any other authenticated user gets `403`.

### Access

1. Log in as the admin user to obtain a bearer token:

```
POST /api/v1/auth/login
```

```json
{ "email": "admin@example.com", "password": "password" }
```

2. Send the returned `access_token` in the `Authorization: Bearer` header on every admin request.

### Manage trips

**List all trips** (with their ordered stops)

```
GET /api/v1/admin/trips
```

**Create a trip with its ordered stops**

```
POST /api/v1/admin/trips
```

```json
{
  "bus_id": "{bus_uuid}",
  "stops": [
    { "city_id": "{city_uuid}", "price_from_origin": 0,   "departure_timestamp": "2026-08-20 07:00:00", "arrival_timestamp": "2026-08-20 07:00:00" },
    { "city_id": "{city_uuid}", "price_from_origin": 50,  "departure_timestamp": "2026-08-20 09:00:00", "arrival_timestamp": "2026-08-20 09:00:00" },
    { "city_id": "{city_uuid}", "price_from_origin": 90,  "departure_timestamp": "2026-08-20 11:30:00", "arrival_timestamp": "2026-08-20 11:30:00" },
    { "city_id": "{city_uuid}", "price_from_origin": 140, "departure_timestamp": "2026-08-20 14:00:00", "arrival_timestamp": "2026-08-20 14:00:00" }
  ]
}
```

- `bus_id` and `city_id` are UUIDs; city UUIDs come from the cities table.
- The trip's origin/destination are derived from the first and last stops; `price_from_origin` is the **cumulative** price from the origin (must start at `0` and strictly increase, so free legs are rejected).
- Stops must be a distinct, chronologically ordered sequence of at most 30 (`arrival >= departure` per stop, departure of a stop >= arrival of the previous one), departing today or later.
- A bus cannot be scheduled on two overlapping trips; overlapping departures/arrivals are rejected.
- Responses: `201` created with the full trip (bus, cities, ordered stops) · `401` unauthenticated · `403` not an admin · `404` resource not found · `409` the bus is already scheduled on an overlapping trip · `422` validation errors.

**Soft-delete a trip**

```
DELETE /api/v1/admin/trips/{trip_uuid}
```

- Removes the trip (and its seats from availability) without affecting bookings or the seat records themselves. Responses: `204` · `401` · `403` · `404`.

### Manage cities

Create, list, and soft-delete cities under `/api/v1/admin/cities`:

- `GET` `/api/v1/admin/cities` — list all cities
- `POST` `/api/v1/admin/cities` — create, `{ "name": "Ras Sedr", "code": "RSD" }`
- `DELETE` `/api/v1/admin/cities/{city_uuid}` — soft-delete (`204`; `409` if the city is part of a trip)

`name` and `code` are required and unique on create.

### Manage buses (with their seats)

Create, list, and soft-delete buses under `/api/v1/admin/buses`. Buses expose their `seats` in every response.

- `GET` `/api/v1/admin/buses` — list all buses with seats
- `POST` `/api/v1/admin/buses` — create, optionally with seats:

```json
{
  "class": "First Class",
  "plate_number": "XYZ-1234",
  "seats": ["A1", "A2", "A3"]
}
```

- `DELETE` `/api/v1/admin/buses/{bus_uuid}` — soft-delete (`204`; `409` if the bus is assigned to a trip)

`class` and `plate_number` are required on create; `plate_number` is unique and `seats` codes must be distinct.

The `is_admin` flag is enforced by the `admin` route middleware on every endpoint in the group.

---

## API

All endpoints live under `/api/v1`. Everything except `auth/register` and `auth/login` requires the `Authorization: Bearer <token>` header (Sanctum).

### Postman collection

A ready-to-use Postman collection (Collection v2.1) is committed at [`postman/tazcara_collection.json`](postman/tazcara_collection.json). Import it in Postman via **Import → Upload Files**, then set the collection variables (`access_token`, `trip_id`, `seat_id`, `city_id`, `bus_id`) from the API responses.

The legacy cloud workspace is at **https://go.postman.co/workspace/d6721efd-1ef0-441b-8ac0-8450f1da36cd**.

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

The response is `{ "access_token": "…", "token_type": "Bearer", "expires_in": 86400, "user": { … } }`. Tokens are stateless Sanctum tokens that expire after **24 hours** (`sanctum.expiration` = 1440 minutes); send them as `Authorization: Bearer <access_token>`.

**Logout** (revokes the current token)

```
POST /api/v1/auth/logout
```

Requires the `Authorization: Bearer` header. Invalidates the token — it can no longer be used afterwards. Login and register are throttled (`throttle:6,1`).

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
make test                  # or: docker compose exec app php artisan test
./vendor/bin/pest --filter=Booking   # subset
```

Code style is enforced by [Laravel Pint](https://laravel.com/docs/pint):

```bash
make pint                  # fix style (use `./vendor/bin/pint --test` to only check)
```

CI (`.github/workflows/tests.yml`) runs `pint --test` and the full Pest suite on every push/PR to `main`.

---

## For code reviewers

### Stack

Laravel 13 (`laravel/framework ^13.17`), Laravel Sanctum v4, MySQL 8, Docker Compose (nginx + php-fpm + mysql), Pest, Pint, PHP 8.3+.

### Layering and conventions

- **Controllers are thin.** Each controller validates via a `FormRequest`, delegates the work to a service, and shapes the response with a resource — no business logic or inline `$request->validate` in controllers.
- **Services own the business logic** (`app/Services`): `AuthService`, `TripService`, `SeatAvailabilityService`, `BookingService`, `AdminCityService`, `AdminBusService`, `AdminTripService`. Services return models/collections; controllers decide the HTTP shape. Queries avoid N+1: eager loads, batched inserts via `createMany`, and `pluck`+`whereIn` instead of per-row lookups.
- **FormRequests own validation** (`app/Http/Requests`), including cross-field rules (`withValidator` in `StoreTripRequest`) and header merging (`Idempotency-Key` in `StoreBookingRequest`). Requests that act on the authenticated user declare `authorize()` returning `auth()->check()`.
- **Resources own the response shape** (`app/Http/Resources`). `whenLoaded` prevents extra queries and lets the admin resources (`CityResource`, `BusResource`, `TripResource`) be reused by the consumer resources (`TripSeatsResource`, `BookingResource`).
- **Middleware** — `admin` alias → `EnsureUserIsAdmin` (`403` unless `is_admin`), `auth` (custom JSON `Authenticate`), and Sanctum `abilities`/`ability`. Registered in `bootstrap/app.php`, which also centralizes JSON error rendering.
- **Response envelope** — every payload is `{ "error": bool, "message": string, "data": mixed }` plus `errors` on validation failures, via `BaseController` + `ApiResponseTrait`.
- **Model scopes** use Laravel 13 `#[Scope]` attributes (see the query fragments in `app/Models`). UUID primary keys via `HasUuids`; `#[Fillable]`/`#[Cast]` attributes.

### HTTP status conventions

| Status | Meaning                                                                 |
| ------ | ----------------------------------------------------------------------- |
| 200    | success (list/create)                                                    |
| 201    | resource created (`POST` admin trips/buses/cities, bookings)            |
| 204    | soft-deleted (admin `DELETE`, empty body)                                |
| 400    | invalid argument (bad booking leg, seats not on the trip, wrong date)    |
| 401    | unauthenticated / missing, invalid, or expired token                     |
| 403    | authenticated but not an admin                                           |
| 404    | resource not found (uniform `"Resource not found"`)                      |
| 409    | conflict — overlapping bus schedule, city/bus in use, seats taken, idempotency-key reuse |
| 422    | validation errors (includes an `errors` map)                             |

### Error handling

Centralized in `bootstrap/app.php`: responses are JSON for `api/*`. `ModelNotFoundException`/`NotFoundHttpException` → `404`; `AuthenticationException` → `401`; `ValidationException` → `422` with field errors; `HttpException` → its status (used for the `409` conflicts); anything else → `500 "Something went wrong"`. No stack traces leak when `APP_DEBUG=false`.

### Authentication

Stateless Sanctum tokens issued by `AuthService`. `login` authenticates with `Hash::check` and issues a fresh token each time (no token reuse); the response carries `access_token`, `token_type`, `expires_in` (24 h), and the user. `logout` revokes the current token. Login/register are throttled (`throttle:6,1`).

### Administration area

Under `/api/v1/admin`, guarded by `auth:sanctum` + `admin`, exposing `index`/`store`/`destroy` only (update/show intentionally omitted). Deletes are **soft deletes** (models use `SoftDeletes`; bookings keep FKs to seats and `trip_cities`). Referential guards raise `409` when deleting a city that is part of a trip or a bus assigned to a trip; a bus whose trips are all soft-deleted counts as free to delete.

### Trip creation

`StoreTripRequest` validates 2–30 distinct stops, strictly increasing cumulative prices starting at `0` (free legs rejected), chronologically ordered timestamps (`arrival >= departure` per stop, next departure ≥ previous arrival), today-or-later departures, and safe date parsing (garbage dates become `422`, never `500`). `AdminTripService` normalizes the stop array, derives origin/destination, checks the bus-schedule overlap (`409`), and writes the trip + all stops in one transaction via `createMany`.

### Bookings & idempotency

`StoreBookingRequest` merges the `Idempotency-Key` header; `BookingService` replays an existing confirmed booking for the same user/seats/leg instead of creating a duplicate, and raises `409` if the key is reused for a *different* request. Booking runs inside a DB transaction with `SELECT … FOR UPDATE` on seats and the unique `bookings (user_id, idempotency_key, seat_id)` index as the backstop for races. The price is always computed server-side from `price_from_origin` (never from the client); `409` when a seat is already taken on the requested leg.

### Seat availability

`TripService::getAvailableTripSeats` + `SeatAvailabilityService` return only trips whose ordered stops contain both cities in the correct order, with currently free seats. Seats are considered taken when a **confirmed** booking overlaps the requested leg.

### Database

MySQL, soft-deletes, unique constraints on `trip_cities (trip_id, city_id)` and `(trip_id, sequence)`, and a unique `bookings (idempotency_key, user_id)`-style guard for idempotency. The canonical seeded state is committed as `database/dumps/tazcara.sql`; regenerate it with `make dump` after seeder changes.

### Project layout

```
app/
├── Enums/                     BookingStatusEnum
├── Http/
│   ├── Controllers/           thin controllers + BaseController (ApiResponseTrait)
│   ├── Middleware/            EnsureUserIsAdmin, JSON Authenticate
│   ├── Requests/              FormRequests (all input validation lives here)
│   └── Resources/             response shaping (admin + consumer resources)
├── Models/                    Eloquent models, #[Scope] query fragments
├── Providers/
├── Services/                  business logic (auth, trips, seats, bookings, admin)
└── Traits/                    ApiResponseTrait
routes/api.php                 API routes (v1: auth, trips, admin)
bootstrap/app.php              middleware aliases + centralized JSON error rendering
database/seeders + dumps       canonical seed + importable dump
tests/                         Pest feature tests (in-memory SQLite)
```

### Checks before review

```bash
make pint          # code style
make test          # full Pest suite (25 tests)
```

---

## Production safety

- `make fresh` and `make seed` refuse to run unless `APP_ENV=local`/`testing` (see `Makefile` → `guard-local`), so CI/CD pipelines can never wipe a production database.
- The entrypoint never seeds in `production` and only runs non-destructive `migrate --force`.
- For deployments, run `make migrate` — nothing else.