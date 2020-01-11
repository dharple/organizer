# Overview

This project is an attempt at building a system for keeping track of storage
boxes and locations.

# Requirements

* PHP
* Any database supported by PDO (SQLite, MySQL, PostgreSQL, et al.)
* Redis

# Getting Started

Run `composer install`.

Copy `.env` to `.env.local` and set a DB url.  For instance, to set up SQLite, use:
```
DATABASE_URL=sqlite:///%kernel.project_dir%/var/organizer.db
```

Populate the schema for your database:
```
bin/console doctrine:database:create
bin/console doctrine:schema:create
```

Create a user:
```
bin/console user:add test@hotmail.com mySecurePassword
```

Start the server:
```
bin/console server:run
```

Connect to http://localhost:8000 and log in.
