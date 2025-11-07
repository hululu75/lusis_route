#!/bin/bash

echo "🚀 Lusis Route Management Platform - Quick Start"
echo "================================================"
echo ""

# Check PHP version
PHP_VERSION=$(php -v | head -n 1 | cut -d ' ' -f 2 | cut -d '.' -f 1,2)
echo "✓ PHP Version: $PHP_VERSION"

# Check available database extensions
SQLITE_AVAILABLE=false
MYSQL_AVAILABLE=false
PGSQL_AVAILABLE=false

if php -m | grep -q "pdo_sqlite"; then
    echo "✓ SQLite extension is installed"
    SQLITE_AVAILABLE=true
else
    echo "⚠ SQLite extension NOT found"
fi

if php -m | grep -q "pdo_mysql"; then
    echo "✓ MySQL extension is installed"
    MYSQL_AVAILABLE=true
fi

if php -m | grep -q "pdo_pgsql"; then
    echo "✓ PostgreSQL extension is installed"
    PGSQL_AVAILABLE=true
fi

# Check Docker
DOCKER_AVAILABLE=false
if command -v docker &> /dev/null; then
    echo "✓ Docker is installed"
    DOCKER_AVAILABLE=true
fi

echo ""
echo "Choose your setup option:"
echo ""
echo "1) Use SQLite (lightweight, file-based)"
if [ "$SQLITE_AVAILABLE" = false ]; then
    echo "   ⚠ Requires: PHP SQLite extension (NOT AVAILABLE)"
else
    echo "   ✓ Available"
fi
echo ""
echo "2) Use PostgreSQL (production-ready, recommended)"
if [ "$PGSQL_AVAILABLE" = false ]; then
    echo "   ⚠ Requires: PHP PostgreSQL extension (NOT AVAILABLE)"
else
    echo "   ✓ Available"
fi
echo ""
echo "3) Use MySQL (production-ready)"
if [ "$MYSQL_AVAILABLE" = false ]; then
    echo "   ⚠ Requires: PHP MySQL extension (NOT AVAILABLE)"
else
    echo "   ✓ Available"
fi
echo ""
echo "4) Use Docker (recommended for new deployments)"
if [ "$DOCKER_AVAILABLE" = false ]; then
    echo "   ⚠ Requires: Docker and Docker Compose (NOT AVAILABLE)"
else
    echo "   ✓ Available"
fi
echo ""
echo "5) Check current status (if already running)"
echo ""
echo "6) Skip database setup (view code only)"
echo ""

read -p "Enter your choice (1-6): " choice

case $choice in
    1)
        if [ "$SQLITE_AVAILABLE" = true ]; then
            echo ""
            echo "📦 Setting up SQLite database..."

            # Update .env
            sed -i 's/^DB_CONNECTION=.*/DB_CONNECTION=sqlite/' .env

            # Create database file
            touch database/database.sqlite
            chmod 664 database/database.sqlite

            # Run migrations
            echo "🔄 Running migrations..."
            php artisan migrate --force

            if [ $? -eq 0 ]; then
                echo ""
                echo "✅ Setup complete!"
                echo ""
                echo "Starting development server..."
                php artisan serve --host=0.0.0.0 --port=8000
            else
                echo "❌ Migration failed. Please check the error above."
                exit 1
            fi
        else
            echo ""
            echo "❌ SQLite extension is not installed."
            echo ""
            echo "To install:"
            echo "  Ubuntu/Debian: sudo apt-get install php8.4-sqlite3"
            echo "  macOS: brew install php@8.4"
            echo ""
            echo "Or choose option 2 (PostgreSQL) or 3 (MySQL)"
            exit 1
        fi
        ;;

    2)
        if [ "$PGSQL_AVAILABLE" = false ]; then
            echo ""
            echo "❌ PostgreSQL extension is not installed."
            echo ""
            echo "To install:"
            echo "  Ubuntu/Debian: sudo apt-get install php8.4-pgsql"
            echo "  macOS: brew install php@8.4"
            exit 1
        fi

        echo ""
        echo "📦 Setting up PostgreSQL configuration..."
        echo ""

        # Check if PostgreSQL is already running on 5433
        PGSQL_RUNNING=false
        if pg_isready -h localhost -p 5433 -U postgres &>/dev/null; then
            PGSQL_RUNNING=true
            echo "✓ Found PostgreSQL running on port 5433"
            echo ""
        fi

        read -p "Do you want to initialize a local PostgreSQL instance? (y/n): " INIT_PG

        if [[ "$INIT_PG" =~ ^[Yy]$ ]] && [ "$PGSQL_RUNNING" = false ]; then
            echo ""
            echo "Initializing local PostgreSQL instance..."

            # Check if user exists
            if ! id -u claude &>/dev/null; then
                echo "Creating user 'claude'..."
                useradd -m claude
            fi

            # Initialize PostgreSQL
            if [ ! -d "pgdata" ]; then
                mkdir -p pgdata
                chown -R claude:claude pgdata
                su - claude -c "cd $(pwd) && /usr/lib/postgresql/16/bin/initdb -D ./pgdata -U postgres -A trust"
            fi

            # Start PostgreSQL
            echo "Starting PostgreSQL..."
            su - claude -c "cd $(pwd) && /usr/lib/postgresql/16/bin/pg_ctl -D ./pgdata -l pgdata/logfile -o '-p 5433' start"

            sleep 2

            # Create database
            echo "Creating database..."
            su - claude -c "cd $(pwd) && /usr/lib/postgresql/16/bin/createdb -h localhost -p 5433 -U postgres lusis_route"

            DB_HOST="127.0.0.1"
            DB_PORT="5433"
            DB_DATABASE="lusis_route"
            DB_USERNAME="postgres"
            DB_PASSWORD=""
        else
            # Manual configuration
            read -p "PostgreSQL Host (default: 127.0.0.1): " DB_HOST
            DB_HOST=${DB_HOST:-127.0.0.1}

            read -p "PostgreSQL Port (default: 5432): " DB_PORT
            DB_PORT=${DB_PORT:-5432}

            read -p "Database Name (default: lusis_route): " DB_DATABASE
            DB_DATABASE=${DB_DATABASE:-lusis_route}

            read -p "PostgreSQL Username (default: postgres): " DB_USERNAME
            DB_USERNAME=${DB_USERNAME:-postgres}

            read -sp "PostgreSQL Password (press Enter if none): " DB_PASSWORD
            echo ""
        fi

        # Update .env file
        echo ""
        echo "Updating configuration..."
        sed -i "s/^DB_CONNECTION=.*/DB_CONNECTION=pgsql/" .env
        sed -i "s/^DB_HOST=.*/DB_HOST=$DB_HOST/" .env
        sed -i "s/^DB_PORT=.*/DB_PORT=$DB_PORT/" .env
        sed -i "s/^DB_DATABASE=.*/DB_DATABASE=$DB_DATABASE/" .env
        sed -i "s/^DB_USERNAME=.*/DB_USERNAME=$DB_USERNAME/" .env
        sed -i "s/^DB_PASSWORD=.*/DB_PASSWORD=$DB_PASSWORD/" .env

        echo ""
        echo "Testing database connection..."

        # Test connection with artisan
        php artisan migrate:status &>/dev/null

        if [ $? -eq 0 ] || [ $? -eq 1 ]; then
            echo "✓ Database connection successful"
            echo ""
            echo "🔄 Running migrations..."
            php artisan migrate --force

            if [ $? -eq 0 ]; then
                echo ""
                echo "✅ Setup complete!"
                echo ""
                echo "PostgreSQL is running on port $DB_PORT"
                echo ""
                echo "Management commands:"
                echo "  Check status: su - claude -c \"/usr/lib/postgresql/16/bin/pg_ctl -D $(pwd)/pgdata status\""
                echo "  Stop: su - claude -c \"/usr/lib/postgresql/16/bin/pg_ctl -D $(pwd)/pgdata stop\""
                echo "  Start: su - claude -c \"/usr/lib/postgresql/16/bin/pg_ctl -D $(pwd)/pgdata -l pgdata/logfile -o '-p 5433' start\""
                echo ""
                echo "Starting development server..."
                php artisan serve --host=0.0.0.0 --port=8000
            else
                echo "❌ Migration failed. Please check the error above."
                exit 1
            fi
        else
            echo "❌ Could not connect to PostgreSQL. Please check your credentials."
            exit 1
        fi
        ;;

    3)
        if [ "$MYSQL_AVAILABLE" = false ]; then
            echo ""
            echo "❌ MySQL extension is not installed."
            echo ""
            echo "To install:"
            echo "  Ubuntu/Debian: sudo apt-get install php8.4-mysql"
            echo "  macOS: brew install php@8.4"
            exit 1
        fi

        echo ""
        echo "📦 Setting up MySQL configuration..."
        echo ""
        read -p "MySQL Host (default: 127.0.0.1): " DB_HOST
        DB_HOST=${DB_HOST:-127.0.0.1}

        read -p "MySQL Port (default: 3306): " DB_PORT
        DB_PORT=${DB_PORT:-3306}

        read -p "Database Name (default: lusis_route): " DB_DATABASE
        DB_DATABASE=${DB_DATABASE:-lusis_route}

        read -p "MySQL Username (default: root): " DB_USERNAME
        DB_USERNAME=${DB_USERNAME:-root}

        read -sp "MySQL Password: " DB_PASSWORD
        echo ""

        # Update .env file
        sed -i "s/^DB_CONNECTION=.*/DB_CONNECTION=mysql/" .env
        sed -i "s/^DB_HOST=.*/DB_HOST=$DB_HOST/" .env
        sed -i "s/^DB_PORT=.*/DB_PORT=$DB_PORT/" .env
        sed -i "s/^DB_DATABASE=.*/DB_DATABASE=$DB_DATABASE/" .env
        sed -i "s/^DB_USERNAME=.*/DB_USERNAME=$DB_USERNAME/" .env
        sed -i "s/^DB_PASSWORD=.*/DB_PASSWORD=$DB_PASSWORD/" .env

        echo ""
        echo "Testing database connection..."

        # Create database if it doesn't exist
        mysql -h"$DB_HOST" -P"$DB_PORT" -u"$DB_USERNAME" -p"$DB_PASSWORD" -e "CREATE DATABASE IF NOT EXISTS $DB_DATABASE;"

        if [ $? -eq 0 ]; then
            echo "✓ Database connection successful"
            echo ""
            echo "🔄 Running migrations..."
            php artisan migrate --force

            if [ $? -eq 0 ]; then
                echo ""
                echo "✅ Setup complete!"
                echo ""
                echo "Starting development server..."
                php artisan serve --host=0.0.0.0 --port=8000
            else
                echo "❌ Migration failed. Please check the error above."
                exit 1
            fi
        else
            echo "❌ Could not connect to MySQL. Please check your credentials."
            exit 1
        fi
        ;;

    4)
        if [ "$DOCKER_AVAILABLE" = false ]; then
            echo ""
            echo "❌ Docker is not installed."
            echo ""
            echo "Please install Docker from: https://docs.docker.com/get-docker/"
            echo ""
            echo "Alternative: Choose option 2 (PostgreSQL) or 3 (MySQL)"
            exit 1
        fi

        echo ""
        echo "📦 Starting with Docker..."
        echo ""

        if ! docker compose version &> /dev/null; then
            echo "❌ Docker Compose is not installed or not available."
            echo ""
            echo "Please install Docker Compose from: https://docs.docker.com/compose/install/"
            exit 1
        fi

        echo "Building and starting containers..."
        docker compose up --build -d

        if [ $? -eq 0 ]; then
            echo ""
            echo "✅ Docker containers are running!"
            echo ""
            echo "🌐 Access the application at: http://localhost:8000"
            echo ""
            echo "Useful commands:"
            echo "  View logs: docker compose logs -f"
            echo "  Stop: docker compose down"
            echo "  Restart: docker compose restart"
        else
            echo "❌ Failed to start Docker containers."
            exit 1
        fi
        ;;

    5)
        echo ""
        echo "📊 Checking current application status..."
        echo ""

        # Check if Laravel server is running
        if ps aux | grep "[p]hp artisan serve" >/dev/null; then
            echo "✓ Laravel server is running"
            ps aux | grep "[p]hp artisan serve" | awk '{print "  PID:", $2, "| Port: 8000"}'
        else
            echo "⚠ Laravel server is not running"
        fi

        echo ""

        # Check PostgreSQL
        if pg_isready -h localhost -p 5433 -U postgres &>/dev/null; then
            echo "✓ PostgreSQL is running on port 5433"
        else
            echo "⚠ PostgreSQL on port 5433 is not responding"
        fi

        echo ""

        # Test HTTP
        if curl -s -o /dev/null -w "" http://localhost:8000 2>/dev/null; then
            HTTP_STATUS=$(curl -s -o /dev/null -w "%{http_code}" http://localhost:8000)
            if [ "$HTTP_STATUS" = "200" ]; then
                echo "✓ Application is responding (HTTP $HTTP_STATUS)"
                echo ""
                echo "🌐 Access at: http://localhost:8000"
            else
                echo "⚠ Application returned HTTP $HTTP_STATUS"
            fi
        else
            echo "⚠ Application is not accessible on port 8000"
        fi

        echo ""
        echo "Database configuration in .env:"
        grep "^DB_CONNECTION=" .env || echo "  No database configured"
        grep "^DB_HOST=" .env
        grep "^DB_PORT=" .env
        grep "^DB_DATABASE=" .env

        echo ""
        echo "To start the server manually:"
        echo "  php artisan serve --host=0.0.0.0 --port=8000"
        ;;

    6)
        echo ""
        echo "📖 Code-only mode selected."
        echo ""
        echo "The application code is ready to review."
        echo "To run the application, restart this script and choose another option."
        echo ""
        echo "Documentation:"
        echo "  - README.md - Project overview"
        echo "  - SETUP.md - Setup instructions"
        echo "  - FEATURES.md - Features documentation"
        echo "  - POSTGRESQL_SETUP.md - PostgreSQL guide"
        echo "  - DOCKER_GUIDE.md - Docker deployment guide"
        echo "  - SUCCESS.md - Current deployment status"
        ;;

    *)
        echo ""
        echo "❌ Invalid choice. Please run the script again."
        exit 1
        ;;
esac
