# Overview

A web application for keeping track of storage boxes.

# Requirements

* PHP 8.3+
* MySQL or SQLite
* Redis

# Getting Started

Run `composer install`.

Copy `.env` to `.env.local` and configure your database. For SQLite:
```bash
DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/organizer.db
```

For MySQL:
```bash
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=organizer
DB_USERNAME=organizer
DB_PASSWORD=secret
```

Generate an application key:
```bash
php artisan key:generate
```

Run the database migrations:
```bash
php artisan migrate
```

Create a user:
```bash
php artisan user:add test@example.com mySecurePassword
```

Start the server:
```bash
composer go
```

Connect to http://localhost:8000 and log in.

...
