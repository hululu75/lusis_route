# Lusis Route Management Platform - Setup Guide

## Overview
This Laravel 11 application manages Tango-compatible routing configurations with SQLite database.

## Requirements
- PHP 8.2 or higher
- Composer
- **PHP SQLite extension (pdo_sqlite)**

## Installation

### 1. Install PHP SQLite Extension

**Ubuntu/Debian:**
```bash
sudo apt-get update
sudo apt-get install php8.4-sqlite3
```

**CentOS/RHEL:**
```bash
sudo yum install php-sqlite3
```

**macOS:**
```bash
brew install php
# SQLite is usually included by default
```

Verify SQLite extension is loaded:
```bash
php -m | grep -i sqlite
```

You should see:
- pdo_sqlite
- sqlite3

### 2. Run Migrations

Once SQLite extension is installed, run:
```bash
php artisan migrate
```

## Database Schema

The application includes the following tables:
- **projects** - Top-level project containers
- **services** - Tango service definitions
- **route_files** - Routing configuration files
- **matches** - Conditional matching rules
- **match_conditions** - Individual match conditions
- **deltas** - Message transformation definitions
- **rules** - Routing rules and destinations
- **routes** - Complete routing definitions

## Models

All Eloquent models are located in `app/Models/`:
- Project
- Service
- RouteFile
- RouteMatch (table: matches)
- MatchCondition
- Delta
- Rule
- Route

## Configuration

The application is pre-configured to use SQLite:
- Database connection: `sqlite` (see `.env`)
- Database file: `database/database.sqlite`

## Next Steps

After installing SQLite extension:
1. Run migrations: `php artisan migrate`
2. Start development server: `php artisan serve`
3. Access application at: `http://localhost:8000`

## Troubleshooting

### "could not find driver" Error
This means the SQLite PHP extension is not installed. Follow step 1 above to install it.

### Database File Permissions
Ensure the database file and directory are writable:
```bash
chmod 664 database/database.sqlite
chmod 775 database/
```
