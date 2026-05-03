# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Organizer is a personal Symfony 5.4 web application for tracking physical storage boxes. Users manage boxes (auto-numbered), organize them into hierarchical locations, and categorize them by box model (make/model/size/color). Export/import is supported via JSON, XML, YAML, CSV, ODS, and XLSX.

## Common Commands

```bash
# Install dependencies
composer install

# Development server
composer go        # PHP built-in server on localhost:8000
composer nugo      # Symfony CLI server (no TLS)

# Lint and static analysis
composer phpcs     # Check code style (outsanity/phpcs ruleset)
composer phpcbf    # Auto-fix code style
composer phpstan   # Static analysis at level 5

# Tests (no test classes exist yet)
bin/phpunit

# Database
bin/console doctrine:database:create
bin/console doctrine:migrations:migrate
bin/console user:add email@example.com password
```

## Architecture

**Stack:** Symfony 5.4 + Doctrine ORM 2.x + Twig 3.x + Bootstrap 5.3 (CDN). No JS build pipeline — no package.json, no webpack.

**Session storage:** Redis (`ext-redis`). Sessions require a running Redis instance.

**Routing:** PHP 8 `#[Route(...)]` attributes on controllers, discovered via `config/routes/annotations.yaml`.

**Entity base class:** All entities extend `AbstractEntity`, which applies Gedmo's `TimestampableEntity` and `SoftDeleteableEntity` traits. The Gedmo `SoftDeleteable` Doctrine filter is globally enabled, so soft-deleted records are hidden from all queries. `getNextBoxNumber()` temporarily disables this filter to preserve number continuity across soft-deleted boxes.

**Hierarchical locations:** `Location` is self-referential (ManyToOne `parentLocation`). `getDisplayLabel()` walks the parent chain to produce labels like "Home - Garage - Wire Rack" and guards against circular hierarchies.

**Export/Import:** `ExportService` has two modes — "simple" (box list only, via PHPSpreadsheet or Symfony Serializer) and "full" (all entities via Symfony Serializer into an `ExportContainer`). `ImportService` refuses to import if any entities already exist.

**`CrudTrait`:** Shared across `BoxController`, `BoxModelController`, and `LocationController` to consolidate form submission, persistence, flash messages, and redirect logic.

**Migrations:** Live in `src/Migrations/` (not the root `migrations/` directory, which is empty).

## Key Configuration Notes

- **Default `APP_ENV` is `prod`** — the committed `.env` sets `APP_ENV=prod`. Create `.env.local` and set `APP_ENV=dev` for debug mode.
- Security: all routes require `ROLE_USER` except `/login` and `/about`.
- PHPCS uses `outsanity/phpcs` which enforces alphabetically sorted methods and properties.
- `rector.php` targets PHP 8.1 sets despite `composer.json` requiring PHP ^8.2.29.
