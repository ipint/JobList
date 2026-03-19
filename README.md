# JobList Backend

Laravel 13 + Filament 5 backend for a UK jobs platform, built for local Docker development and a cPanel deployment path.

## What This Repo Includes

- Laravel 13 application
- Filament 5 admin panel
- MySQL 8 Docker setup
- UK counties lookup table and seeder
- Jobs resource in Filament
- 30 seeded UK job records
- cPanel-friendly defaults for queue, cache, and sessions

## Stack

- PHP 8.3
- Laravel 13
- Filament 5
- MySQL 8.4
- Docker Compose
- Database-backed queue, cache, and sessions

## Prerequisites

Make sure you have:

- Docker Desktop
- Git
- Composer
- PHP 8.3 or newer on your local machine

## Local Setup

1. Clone the repository:

```bash
git clone <your-github-repo-url>
cd JobList
```

2. Copy the environment file:

```bash
cp .env.example .env
```

3. Start Docker:

```bash
docker compose up -d --build
```

4. Install dependencies if needed:

```bash
composer install
```

5. Generate the application key:

```bash
php artisan key:generate
```

6. Run migrations and seed the database:

```bash
docker compose exec app php artisan migrate --seed
```

7. Create a Filament admin user:

```bash
docker compose exec app php artisan make:filament-user
```

When prompted, you can create the user with:

- Email: `hello@example.com`
- Password: `Admin@123`

Example:

```text
 Name:
 > Admin User

 Email address:
 > hello@example.com

 Password:
 > Admin@123

 Password confirmation:
 > Admin@123
```

8. Open the admin panel:

```text
http://localhost:8088/admin
```

## Seeded Data

The database is seeded with:

- UK counties lookup data
- 30 UK-based jobs

Jobs are available in the admin panel at:

```text
http://localhost:8088/admin/jobs
```

## Public Jobs API

The frontend can fetch public jobs from these public endpoints:

```text
GET http://localhost:8088/api/jobs
GET http://localhost:8088/api/jobs/{slug}
```

Only jobs with `status=published`, a valid `published_at`, and no past `expires_at` are returned.

### List Jobs

Endpoint:

```text
GET /api/jobs
```

Supported query parameters:

- `search`
- `county`
- `department`
- `employment_type`
- `work_mode`
- `experience_level`
- `featured`
- `per_page`

Example request:

```text
http://localhost:8088/api/jobs?search=Laravel&work_mode=remote&featured=1
```

Example response:

```json
{
  "data": [
    {
      "id": 1,
      "title": "Senior Laravel Developer",
      "slug": "senior-laravel-developer-123ab",
      "reference": "JOB-12345",
      "company_name": "Northbridge Digital",
      "department": "Technology",
      "description": "Job description...",
      "requirements": "Job requirements...",
      "benefits": "Job benefits...",
      "employment_type": "full_time",
      "work_mode": "remote",
      "experience_level": "senior",
      "status": "published",
      "location": {
        "county": "Greater London",
        "county_slug": "greater-london",
        "city": "London",
        "postcode": "E1 6AN",
        "location_name": "London Office"
      },
      "salary": {
        "min": "55000.00",
        "max": "75000.00",
        "currency": "GBP",
        "period": "year",
        "text": "Competitive salary",
        "is_visible": true
      },
      "application": {
        "url": "https://example.com/jobs/apply",
        "email": "jobs@example.com"
      },
      "visa_sponsorship_available": false,
      "right_to_work_required": true,
      "closing_date": "2026-04-30",
      "published_at": "2026-03-19T12:00:00+00:00",
      "expires_at": "2026-04-30T12:00:00+00:00",
      "is_featured": true,
      "created_at": "2026-03-19T12:00:00+00:00",
      "updated_at": "2026-03-19T12:00:00+00:00"
    }
  ],
  "links": {
    "first": "http://localhost:8088/api/jobs?page=1",
    "last": "http://localhost:8088/api/jobs?page=3",
    "prev": null,
    "next": "http://localhost:8088/api/jobs?page=2"
  },
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 3,
    "path": "http://localhost:8088/api/jobs",
    "per_page": 12,
    "to": 12,
    "total": 30
  }
}
```

### Get Job Details

Endpoint:

```text
GET /api/jobs/{slug}
```

Example request:

```text
http://localhost:8088/api/jobs/senior-laravel-developer-123ab
```

This returns the same `data` structure for a single job. If the slug does not exist, or the job is not publicly visible, the API returns `404`.

## Default Local Services

- App URL: `http://localhost:8088`
- Filament login: `http://localhost:8088/admin/login`
- MySQL host inside Docker: `mysql`
- MySQL port on host: `33060`
- MySQL database: `joblist`
- MySQL username: `joblist`
- MySQL password: `secret`

## Important Project Decisions

### Queue Table Naming

Laravel’s default database queue uses a `jobs` table. Since this project needs a real `jobs` table for job listings, queue tables were renamed to:

- `queued_jobs`
- `failed_queued_jobs`

This avoids a domain conflict later.

### PHP 8.3 Compatibility

Composer is pinned to PHP 8.3 at the platform level so the lockfile stays compatible with the cPanel target server.

### Counties as a Required Dropdown

This project uses a seeded UK counties lookup table and requires county selection in the Filament Jobs form. That gives you cleaner admin filtering and more consistent job location data.

## Useful Commands

Start the stack:

```bash
docker compose up -d
```

Stop the stack:

```bash
docker compose down
```

Rebuild containers:

```bash
docker compose up -d --build
```

Run tests:

```bash
php artisan test
```

Reseed counties and jobs:

```bash
docker compose exec app php artisan db:seed
```

Run new migrations:

```bash
docker compose exec app php artisan migrate
```

## cPanel Deployment Notes

This project was designed around a dedicated cPanel server running PHP 8.3.

Recommended deployment flow:

1. Pull the latest code from GitHub onto the server.
2. Keep `.env` only on the server.
3. Point the domain or subdomain document root to `public/`.
4. Run:

```bash
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

5. Use cron for:

```bash
php artisan schedule:run
```

6. If Redis and Supervisor are not available, use the database queue and trigger:

```bash
php artisan queue:work --stop-when-empty --tries=3
```

from cron.

## Repo Structure

- `app/Filament` contains the admin resources
- `app/Models` contains the Eloquent models
- `database/migrations` contains schema changes
- `database/seeders` contains lookup and sample data
- `database/factories` contains generated fake data
- `docker/` contains PHP and Nginx container config
- `docker-compose.yml` runs the local stack

## Troubleshooting

### Port Already Allocated

If `8088` is already in use on your machine, change the exposed Nginx port in `docker-compose.yml` and update `APP_URL` in `.env`.

### Filament Component Class Errors

This project uses Filament 5, so some components such as `Section` come from `Filament\Schemas\Components`, not older Filament namespaces.

### Empty Jobs Table in Admin

If the admin panel loads but jobs are missing, run:

```bash
docker compose exec app php artisan migrate --seed
```

## Blog Version

A blog-style write-up of the full setup process lives in:

- `docs/blog-laravel-filament-jobs-backend.md`

## License

This repository currently retains the existing `LICENSE` file in the repo root.
