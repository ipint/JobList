# How I Built a Laravel + Filament UK Jobs Backend with Docker and MySQL

If you want a pragmatic Laravel backend for a jobs platform, the fastest stable path is usually not a microservice stack or a custom admin panel. It is a clean Laravel app, a solid database, and an admin tool that lets you move quickly without destroying future upgrade options.

That is exactly what this project does.

It uses:

- Laravel 13
- Filament 5
- MySQL 8
- Docker for local development
- cPanel-friendly deployment defaults

The code for the finished setup lives in this repository. Replace this sentence with your final GitHub URL when you publish the post.

## Why This Stack

The goal was straightforward:

- run locally in Docker
- use MySQL because production will use MySQL
- manage jobs from an admin panel
- keep it deployable to a dedicated cPanel server
- avoid infrastructure requirements like Redis and Supervisor at the start

Filament was the right fit because it gives a proper admin experience without forcing the entire application to live inside admin-specific abstractions.

Laravel was the right fit because the deployment target is PHP 8.3 on cPanel, and the ecosystem is mature enough to keep this maintainable.

## Step 1: Scaffold Laravel in an Existing Repo

The workspace started almost empty, so the first move was to scaffold a fresh Laravel application and merge it into the repository root.

One practical gotcha showed up immediately: Composer will not create a project in a non-empty directory. The workaround was to scaffold Laravel into a temporary subdirectory first, then merge it into the actual repo.

## Step 2: Add Docker for Local Development

The local stack was kept intentionally simple:

- PHP-FPM app container
- Nginx container
- MySQL 8 container

That setup lives in:

- `docker-compose.yml`
- `docker/php/Dockerfile`
- `docker/nginx/default.conf`

Nginx is exposed on port `8088` because common local ports such as `8000` and `8080` were already occupied on the machine.

## Step 3: Match Production Early

One of the easiest ways to create avoidable pain is to develop against one stack and deploy to another.

In this project:

- local database is MySQL
- production database is expected to be MySQL
- local PHP target is aligned with cPanel PHP 8.3

That sounds obvious, but it matters.

At one point, Composer dependencies were resolved using local PHP 8.4, which allowed Symfony 8 packages that required PHP 8.4. The Docker container and cPanel target were on PHP 8.3, so the app broke inside Docker even though it worked locally.

The fix was to pin the Composer platform to PHP 8.3 in `composer.json`. That forced the lockfile to stay compatible with the real deployment target instead of the local machine.

## Step 4: Install Filament

Filament 5 was installed directly into the Laravel 13 project, and the admin panel was generated at:

- `/admin`

That immediately gave:

- login page
- dashboard
- resource routing
- the structure needed for a proper back office

## Step 5: Avoid Laravel’s `jobs` Table Conflict

Laravel’s database queue migration creates a table called `jobs`.

That is a problem in a jobs platform, because the real domain also needs a `jobs` table for job listings.

Instead of discovering that conflict later, the queue tables were renamed up front to:

- `queued_jobs`
- `failed_queued_jobs`

That small change keeps the domain clean and prevents an annoying refactor later.

## Step 6: Model the Jobs Domain

The project then added a real `Job` model and schema with fields that are practical for a UK job platform:

- title
- slug
- reference
- company name
- department
- description
- requirements
- benefits
- employment type
- work mode
- experience level
- status
- county
- city
- postcode
- location display name
- salary range
- salary period
- application URL and email
- sponsorship and right-to-work flags
- publish / expiry dates
- featured flag

This was enough to make the admin panel genuinely useful instead of just technically present.

## Step 7: Add UK Counties as a Required Dropdown

The product requirement was clear: county should be selected from a dropdown and always present.

That meant creating:

- a `uk_counties` table
- a `UkCounty` model
- a seeder with a UK county list
- a `county_id` foreign key on jobs

The counties are seeded into the database and used directly by the Filament form.

This approach is much better than hardcoding dropdown options inside the UI layer because the list can be amended later without changing form logic.

## Step 8: Build the Filament Jobs Resource

The generated Filament resource stubs were then replaced with a working resource:

- a structured form
- a searchable table
- useful filters
- county relationship dropdown

The admin includes filters for:

- status
- employment type
- work mode
- county
- featured jobs

That is already enough for a recruiter-style internal workflow.

## Step 9: Seed Realistic Job Data

An empty admin panel is technically correct but not very helpful.

So the project adds:

- a `JobFactory`
- a `JobSeeder`
- 30 generated UK job records

The generated jobs include random departments like:

- Technology
- Sales
- Marketing
- Finance
- Operations
- Customer Support
- Product
- Legal

That makes the admin immediately usable for demos, testing, and layout validation.

## Step 10: Keep It cPanel Friendly

The deployment target in this case is not a modern container host. It is a dedicated cPanel server running PHP 8.3 without Redis or Supervisor.

That changes some decisions:

- use database queue, not Redis queue
- use cron, not Supervisor-managed long-running workers
- keep `.env` only on the server
- point the document root to `public/`

The deployment flow is still completely workable:

1. Pull the repo from GitHub.
2. Run Composer install.
3. Run migrations.
4. Cache config, routes, and views.
5. Use cron for `schedule:run` and queue processing.

## Problems We Hit Along the Way

This is where the setup became useful rather than generic.

### 1. Composer would not scaffold into the repo root

That happened because the repo was not empty. The fix was to scaffold Laravel into a temporary directory first.

### 2. Docker port collisions

Ports `8000` and `8080` were already in use locally. The final local app port became `8088`.

### 3. PHP 8.4 vs PHP 8.3 lockfile mismatch

Dependencies resolved on local PHP 8.4 broke inside Docker PHP 8.3. The fix was pinning Composer’s platform to PHP 8.3.

### 4. Filament 5 namespace changes

One form component error came from using an old namespace:

- `Section` needed to come from `Filament\Schemas\Components`

That was corrected once the form was tested in the browser.

## What the Finished Setup Gives You

At the end of the process, the project had:

- Docker-based Laravel development
- MySQL-backed local environment
- Filament admin login
- jobs resource
- counties dropdown
- 30 seeded jobs
- cPanel deployment path

That is a strong base for:

- an internal recruiter admin
- a company jobs dashboard
- a public jobs API
- future additions like companies, categories, saved jobs, and applications

## Final Thought

The important thing here is not that the stack is clever. It is that it is aligned:

- aligned with the deployment target
- aligned with the admin needs
- aligned with future Laravel upgrades
- aligned with a jobs domain that will grow over time

That is usually what makes these projects successful.

If you are building something similar, start with the boring stack that matches your real operational constraints. Then make a few good domain decisions early, especially around location, queue naming, and deployment expectations.

That will save far more time than chasing an idealized architecture.
