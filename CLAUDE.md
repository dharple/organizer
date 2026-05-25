# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Organizer is a personal Laravel 13 web application for tracking physical storage boxes. Users manage boxes (auto-numbered), organize them into hierarchical locations, and categorize them by box model (make/model/size/color). Export/import is supported via JSON, XML, YAML, CSV, ODS, and XLSX.

## Common Commands

```bash
# Install dependencies
composer install

# Development server
composer go        # PHP built-in server on localhost:8000
php artisan serve  # Laravel development server

# Lint and static analysis
composer phpcs     # Check code style (outsanity/phpcs ruleset)
composer phpcbf    # Auto-fix code style
composer phpstan   # Static analysis at level 5

# Tests (no test classes exist yet)
php artisan test

# Database
php artisan migrate
php artisan user:add email@example.com password

# Artisan commands
php artisan box:list
php artisan box:move
php artisan location:list
php artisan data:export --format=json --type=full
php artisan data:import filename.json
```

## Architecture

**Stack:** Laravel 13 + Eloquent ORM + Blade + Bootstrap 5.3 (CDN). No JS build pipeline — no package.json, no webpack.

**Session storage:** Redis (`ext-redis`). Sessions require a running Redis instance. Session driver configured via `SESSION_DRIVER=redis` in `.env`.

**Routing:** Explicit route definitions in `routes/web.php` using named routes. All authenticated routes are grouped under the `auth` middleware.

**Model base class:** All models extend `App\Models\BaseModel`, which extends `Illuminate\Database\Eloquent\Model` and applies `SoftDeletes`. `$timestamps = true` is the Eloquent default. Models implement `App\Models\ModelInterface`.

**Auto box numbering:** `Box::booted()` registers a `creating` event that sets `box_number` to `Box::withTrashed()->max('box_number') + 1`. `withTrashed()` preserves number continuity across soft-deleted boxes.

**Hierarchical locations:** `Location` is self-referential (`parent_location_id` foreign key). `getDisplayLabel()` walks the parent chain via `parentWalker()` to produce labels like "Home - Garage - Wire Rack" and guards against circular hierarchies in `setParentLocation()`.

**Export/Import:** `ExportService` has two modes — "simple" (box list only, via PHPSpreadsheet or Symfony Serializer) and "full" (all entities via Symfony Serializer into an `ExportContainer`). `symfony/serializer` and `symfony/yaml` are retained as standalone Composer packages (no Symfony framework). `ImportService` refuses to import if any entities already exist. Export files use snake_case column names (Eloquent convention).

**Form validation:** Laravel Form Requests (`app/Http/Requests/`) replace Symfony Form Types. Blade templates contain plain HTML forms.

**Migrations:** Live in `database/migrations/`. Four migrations represent the final schema.

## Key Configuration Notes

- **Default `APP_ENV` is `production`** — the committed `.env` sets `APP_ENV=production`. Create `.env.local` (or set env vars) and set `APP_ENV=local` for debug mode.
- Security: all routes require authentication except `/login`, `/logout`, and `/about`. Enforced via `auth` middleware on a route group in `routes/web.php`.
- PHPCS uses `outsanity/phpcs` which enforces alphabetically sorted methods and properties.
- `rector.php` targets PHP 8.3.
