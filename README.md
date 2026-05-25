# Overview

A web application for keeping track of storage boxes.

# Requirements

* PHP
* MySQL or SQLite
* Redis

# Getting Started

Run `composer install`.

Copy `.env` to `.env.local` and set a DB url.  For instance, to set up SQLite, use:
```bash
DATABASE_URL=sqlite:///%kernel.project_dir%/var/organizer.db
```

Create your database, if it doesn't already exist.
```bash
bin/console doctrine:database:create
```

Populate the schema for your database:
```bash
bin/console doctrine:migrations:migrate
```

Create a user:
```bash
bin/console user:add test@hotmail.com mySecurePassword
```

Start the server:
```bash
composer go
```

Connect to http://localhost:8000 and log in.
