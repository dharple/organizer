# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with
code in this repository.

## Project Overview

Organizer is a Laravel 13 web application for tracking physical storage boxes.
Users manage boxes (auto-numbered), organize them into hierarchical locations,
and categorize them by box model (make/model/size/color).

Data can be exported and imported in JSON, XML, and YAML format, using the
app's "full" export capability.

Data can also be exported in a "simple" export, which is easier for humans to
read, but cannot be reimported into the app.  This simple export supports CSV,
JSON, ODS, XML, XLSX, and YAML formats.

## Commands

### Setup

```bash
composer install
./artisan key:generate
./artisan migrate
```

### Running

```bash
composer go     # Flush caches and start PHP dev server on localhost:8000
```

### Manage Users

```bash
./artisan user:add email@example.com password
```

### Box and Location Helper Tools

```bash
./artisan box:list
./artisan box:move
./artisan location:list
```

### Data Export and Import Tools

```bash
./artisan data:export --format=json --type=full
./artisan data:import filename.json
```

### Database

```bash
./artisan migrate         # Apply migrations
./artisan migrate:fresh   # Drop and recreate database
```

### Dev Tools

```bash
# All of these commands can take one or more filenames or directories on the
# command line, to narrow the scope of their execution

composer phpstan    # Static Analysis
composer phpcs      # Check Code Style
composer phpcbf     # Fix Code Style
composer rector     # Show Opportunities for Automatic Refactoring
composer test       # Run unit tests

# Direct access tools

vendor/bin/rector   # Do Automatic Refactoring
vendor/bin/phpunit --filter <pattern> # Run unit tests matching a given pattern
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
