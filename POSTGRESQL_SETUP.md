# PostgreSQL Setup Guide

This document explains how the application was configured to use PostgreSQL instead of SQLite.

## Problem

The original configuration used SQLite, but the PHP SQLite extension (pdo_sqlite) was not available in the environment due to:
- Network restrictions preventing package downloads
- Broken package manager (apt-get)
- No Docker available

## Solution

Initialized a local PostgreSQL database instance that runs without requiring system services or sudo.

## Setup Steps

### 1. Initialize PostgreSQL Database

```bash
# Create data directory
mkdir -p pgdata

# Initialize database as non-root user
su - claude -c "/usr/lib/postgresql/16/bin/initdb -D ./pgdata -U postgres -A trust"
```

### 2. Start PostgreSQL Server

```bash
# Start on custom port 5433 to avoid conflicts
su - claude -c "/usr/lib/postgresql/16/bin/pg_ctl -D ./pgdata -l pgdata/logfile -o '-p 5433' start"
```

### 3. Create Database

```bash
# Create the application database
su - claude -c "/usr/lib/postgresql/16/bin/createdb -h localhost -p 5433 -U postgres lusis_route"
```

### 4. Configure Laravel

Update `.env` file:

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5433
DB_DATABASE=lusis_route
DB_USERNAME=postgres
DB_PASSWORD=

# Also updated to use file-based storage
SESSION_DRIVER=file
CACHE_STORE=file
QUEUE_CONNECTION=sync
```

### 5. Run Migrations

```bash
php artisan migrate --force
```

### 6. Start Application

```bash
php artisan serve --host=0.0.0.0 --port=8000
```

## PostgreSQL Management Commands

### Check if PostgreSQL is Running

```bash
su - claude -c "/usr/lib/postgresql/16/bin/pg_ctl -D ./pgdata status"
```

### Stop PostgreSQL

```bash
su - claude -c "/usr/lib/postgresql/16/bin/pg_ctl -D ./pgdata stop"
```

### Restart PostgreSQL

```bash
su - claude -c "/usr/lib/postgresql/16/bin/pg_ctl -D ./pgdata restart"
```

### View PostgreSQL Logs

```bash
tail -f pgdata/logfile
```

### Connect to Database

```bash
psql -h localhost -p 5433 -U postgres -d lusis_route
```

## Current Status

✅ PostgreSQL 16 running on port 5433
✅ Database `lusis_route` created
✅ All migrations completed (11 tables)
✅ Application running on http://0.0.0.0:8000
✅ Sample data loaded for testing

## Database Tables Created

1. **migrations** - Laravel migration tracking
2. **users** - User accounts
3. **cache** - Cache entries
4. **jobs** - Queue jobs
5. **projects** - Routing projects
6. **services** - Tango services
7. **matches** - Route matching conditions
8. **route_files** - Routing configuration files
9. **deltas** - Message transformations
10. **rules** - Routing rules
11. **match_conditions** - Match condition details
12. **routes** - Main routing table

## Why PostgreSQL Instead of SQLite?

| Feature | SQLite | PostgreSQL |
|---------|--------|------------|
| **Installation** | ❌ Extension not available | ✅ Already installed |
| **Service Required** | No | ✅ Can run as user process |
| **sudo Required** | Yes (to install) | ❌ No (runs as user) |
| **Network Access** | Yes (to download) | ❌ No (already present) |
| **PHP Support** | ❌ pdo_sqlite missing | ✅ pdo_pgsql available |

PostgreSQL was the only viable option given the environment restrictions.

## Performance

PostgreSQL is actually more powerful than SQLite and provides:
- Better concurrency support
- Advanced query optimization
- Full ACID compliance
- Support for complex queries
- Better for multi-user scenarios

For this routing management application, PostgreSQL is an excellent choice.

## Data Directory Structure

```
lusis_route/
├── pgdata/                 # PostgreSQL data directory
│   ├── base/              # Database files
│   ├── global/            # Cluster-wide tables
│   ├── pg_wal/            # Write-ahead log
│   ├── logfile           # PostgreSQL server log
│   └── postgresql.conf    # Configuration
├── database/
│   ├── database.sqlite    # (unused, kept for reference)
│   └── migrations/        # Laravel migrations
└── .env                   # Laravel configuration
```

## Troubleshooting

### PostgreSQL Won't Start

Check if port 5433 is already in use:
```bash
lsof -i :5433
```

### Connection Refused

Ensure PostgreSQL is running:
```bash
su - claude -c "/usr/lib/postgresql/16/bin/pg_ctl -D ./pgdata status"
```

### Migration Errors

Check PostgreSQL logs:
```bash
tail -50 pgdata/logfile
```

### Permission Denied

Ensure correct ownership:
```bash
chown -R claude:claude pgdata/
```

## Backup and Restore

### Create Backup

```bash
su - claude -c "pg_dump -h localhost -p 5433 -U postgres lusis_route > backup.sql"
```

### Restore from Backup

```bash
su - claude -c "psql -h localhost -p 5433 -U postgres lusis_route < backup.sql"
```

## Production Considerations

For production deployment:

1. **Use System PostgreSQL Service**
   - Run as a proper system service
   - Configure automatic startup
   - Set up proper authentication

2. **Update Configuration**
   ```env
   DB_PORT=5432  # Standard PostgreSQL port
   DB_PASSWORD=<secure-password>
   ```

3. **Security**
   - Change authentication from `trust` to `md5` or `scram-sha-256`
   - Configure pg_hba.conf for proper access control
   - Use strong passwords

4. **Performance**
   - Tune postgresql.conf for your workload
   - Configure connection pooling
   - Set up regular vacuuming

## Summary

The application successfully runs with PostgreSQL after encountering environment limitations with SQLite. This setup demonstrates the flexibility of Laravel to work with multiple database systems without code changes.

**Application URL:** http://localhost:8000
**Database:** PostgreSQL 16 on port 5433
**Status:** ✅ Fully Operational
