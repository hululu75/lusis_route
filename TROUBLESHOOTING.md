# Troubleshooting Guide - Lusis Route Management Platform

## 🔴 500 Server Error

### Cause
The application cannot connect to the database.

### Solutions

#### Quick Fix (Recommended)
Run the quick start script:
```bash
./quick-start.sh
```

Choose the option that matches your environment:
- **Option 1**: SQLite (lightweight, requires SQLite extension)
- **Option 2**: MySQL (production-ready, requires MySQL server)
- **Option 3**: Docker (zero config, requires Docker)
- **Option 4**: Code review only (no database)

---

## 🔧 Common Issues

### Issue 1: "could not find driver" (SQLite)

**Error:**
```
Illuminate\Database\QueryException: could not find driver (Connection: sqlite)
```

**Cause:** PHP SQLite extension is not installed.

**Solution:**

**Ubuntu/Debian:**
```bash
sudo apt-get update
sudo apt-get install php8.4-sqlite3
sudo systemctl restart apache2  # or php8.4-fpm
```

**macOS:**
```bash
brew install php@8.4
# SQLite is usually included
```

**Verify installation:**
```bash
php -m | grep sqlite
```

You should see:
- `pdo_sqlite`
- `sqlite3`

---

### Issue 2: "SQLSTATE[HY000] [14] unable to open database file"

**Cause:** Database file doesn't exist or has wrong permissions.

**Solution:**
```bash
touch database/database.sqlite
chmod 664 database/database.sqlite
chmod 775 database/
```

---

### Issue 3: PHP version mismatch

**Error:**
```
Your lock file does not contain a compatible set of packages.
Root composer.json requires php ^8.2 but your php version (7.2.24) does not satisfy that requirement.
```

**Cause:** Using wrong PHP version.

**Solution:**

Check available PHP versions:
```bash
ls -la /usr/bin/php*
```

Use correct PHP version:
```bash
# Check version
php8.4 -v

# Run composer with correct PHP
php8.4 $(which composer) install

# Update alternatives (Ubuntu/Debian)
sudo update-alternatives --set php /usr/bin/php8.4
```

---

### Issue 4: Composer dependencies not installed

**Error:**
```
Fatal error: Class 'Illuminate\Foundation\Application' not found
```

**Cause:** Vendor directory is missing.

**Solution:**
```bash
composer install
```

---

### Issue 5: Permission denied errors

**Error:**
```
file_put_contents(...): Failed to open stream: Permission denied
```

**Cause:** Laravel needs write access to certain directories.

**Solution:**
```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache  # Linux/Apache
chown -R $USER:www-data storage bootstrap/cache     # Development
```

---

### Issue 6: APP_KEY not set

**Error:**
```
No application encryption key has been specified.
```

**Solution:**
```bash
php artisan key:generate
```

---

### Issue 7: MySQL connection refused

**Error:**
```
SQLSTATE[HY000] [2002] Connection refused
```

**Cause:** MySQL server is not running or wrong credentials.

**Solution:**

1. **Check MySQL is running:**
```bash
# Ubuntu/Debian
sudo systemctl status mysql

# macOS
brew services list
```

2. **Start MySQL:**
```bash
# Ubuntu/Debian
sudo systemctl start mysql

# macOS
brew services start mysql
```

3. **Test connection:**
```bash
mysql -h 127.0.0.1 -u root -p
```

4. **Create database:**
```sql
CREATE DATABASE lusis_route;
```

5. **Update .env file:**
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=lusis_route
DB_USERNAME=root
DB_PASSWORD=your_password
```

---

### Issue 8: Docker containers not starting

**Error:**
```
Cannot start service app: ...
```

**Solutions:**

1. **Check Docker is running:**
```bash
docker --version
docker-compose --version
```

2. **Stop all containers:**
```bash
docker-compose down
```

3. **Remove old containers:**
```bash
docker-compose down -v
```

4. **Rebuild:**
```bash
docker-compose up --build
```

5. **View logs:**
```bash
docker-compose logs -f
```

---

## 🚀 Quick Start Methods

### Method 1: Docker (Easiest)

```bash
# Start everything
docker-compose up -d

# View logs
docker-compose logs -f

# Access application
http://localhost:8000

# Stop
docker-compose down
```

### Method 2: SQLite (Lightweight)

```bash
# Install extension
sudo apt-get install php8.4-sqlite3

# Create database
touch database/database.sqlite
chmod 664 database/database.sqlite

# Run migrations
php artisan migrate

# Start server
php artisan serve
```

### Method 3: MySQL (Production)

```bash
# Configure .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=lusis_route
DB_USERNAME=root
DB_PASSWORD=your_password

# Run migrations
php artisan migrate

# Start server
php artisan serve
```

---

## 📋 Pre-flight Checklist

Before starting the application, verify:

- [ ] PHP 8.2+ is installed: `php -v`
- [ ] Composer is installed: `composer --version`
- [ ] Database driver is available:
  - SQLite: `php -m | grep sqlite`
  - MySQL: `mysql --version`
- [ ] `.env` file exists: `ls -la .env`
- [ ] APP_KEY is set in `.env`
- [ ] Dependencies are installed: `ls vendor/`
- [ ] Database exists (SQLite: `ls database/database.sqlite`)
- [ ] Permissions are correct: `ls -la storage/`

---

## 🔍 Debug Mode

Enable detailed error messages:

1. Edit `.env`:
```env
APP_DEBUG=true
APP_ENV=local
```

2. Clear cache:
```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

3. Check logs:
```bash
tail -f storage/logs/laravel.log
```

---

## 📞 Getting Help

If you're still stuck:

1. **Check Laravel logs:**
```bash
cat storage/logs/laravel.log
```

2. **Run diagnostics:**
```bash
php artisan about
```

3. **Test database connection:**
```bash
php artisan migrate:status
```

4. **Verify routes:**
```bash
php artisan route:list
```

---

## 🛠 Development Tools

### Useful Commands

```bash
# Clear all caches
php artisan optimize:clear

# Run migrations fresh
php artisan migrate:fresh

# Rollback last migration
php artisan migrate:rollback

# View routes
php artisan route:list

# Open tinker (REPL)
php artisan tinker

# Check environment
php artisan about

# Run tests
php artisan test
```

---

## 📖 Additional Resources

- **README.md** - Project overview and setup
- **SETUP.md** - Detailed setup instructions
- **FEATURES.md** - New features documentation
- **Laravel Documentation** - https://laravel.com/docs

---

## 🆘 Emergency: Run Without Database

If you just want to review the code without setting up a database:

1. Comment out database-related middleware in `bootstrap/app.php`
2. Or set `DB_CONNECTION=sqlite` and create an empty file:
```bash
touch database/database.sqlite
```

This will let you see the UI (though data operations won't work).
