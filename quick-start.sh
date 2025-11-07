#!/bin/bash

echo "🚀 Lusis Route Management Platform - Quick Start"
echo "================================================"
echo ""

# Check PHP version
PHP_VERSION=$(php -v | head -n 1 | cut -d ' ' -f 2 | cut -d '.' -f 1,2)
echo "✓ PHP Version: $PHP_VERSION"

# Check if SQLite extension is available
if php -m | grep -q "pdo_sqlite"; then
    echo "✓ SQLite extension is installed"
    USE_SQLITE=true
else
    echo "⚠ SQLite extension NOT found"
    USE_SQLITE=false
fi

echo ""
echo "Choose your setup option:"
echo ""
echo "1) Use SQLite (lightweight, file-based)"
echo "   Requires: PHP SQLite extension"
echo ""
echo "2) Use MySQL (production-ready)"
echo "   Requires: MySQL server running"
echo ""
echo "3) Use Docker (recommended, zero config)"
echo "   Requires: Docker and Docker Compose"
echo ""
echo "4) Skip database setup (view code only)"
echo ""

read -p "Enter your choice (1-4): " choice

case $choice in
    1)
        if [ "$USE_SQLITE" = true ]; then
            echo ""
            echo "📦 Setting up SQLite database..."

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
                php artisan serve
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
            echo "Then restart this script."
            exit 1
        fi
        ;;

    2)
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
        sed -i "s/DB_CONNECTION=sqlite/DB_CONNECTION=mysql/" .env
        sed -i "s/# DB_HOST=127.0.0.1/DB_HOST=$DB_HOST/" .env
        sed -i "s/# DB_PORT=3306/DB_PORT=$DB_PORT/" .env
        sed -i "s/# DB_DATABASE=laravel/DB_DATABASE=$DB_DATABASE/" .env
        sed -i "s/# DB_USERNAME=root/DB_USERNAME=$DB_USERNAME/" .env
        sed -i "s/# DB_PASSWORD=/DB_PASSWORD=$DB_PASSWORD/" .env

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
                php artisan serve
            else
                echo "❌ Migration failed. Please check the error above."
                exit 1
            fi
        else
            echo "❌ Could not connect to MySQL. Please check your credentials."
            exit 1
        fi
        ;;

    3)
        echo ""
        echo "📦 Starting with Docker..."
        echo ""

        # Check if Docker is installed
        if ! command -v docker &> /dev/null; then
            echo "❌ Docker is not installed."
            echo ""
            echo "Please install Docker from: https://docs.docker.com/get-docker/"
            exit 1
        fi

        if ! command -v docker-compose &> /dev/null && ! docker compose version &> /dev/null; then
            echo "❌ Docker Compose is not installed."
            echo ""
            echo "Please install Docker Compose from: https://docs.docker.com/compose/install/"
            exit 1
        fi

        echo "Building and starting containers..."
        docker-compose up --build -d

        if [ $? -eq 0 ]; then
            echo ""
            echo "✅ Docker containers are running!"
            echo ""
            echo "🌐 Access the application at: http://localhost:8000"
            echo ""
            echo "To view logs: docker-compose logs -f"
            echo "To stop: docker-compose down"
        else
            echo "❌ Failed to start Docker containers."
            exit 1
        fi
        ;;

    4)
        echo ""
        echo "📖 Code-only mode selected."
        echo ""
        echo "The application code is ready to review."
        echo "To run the application, restart this script and choose option 1, 2, or 3."
        echo ""
        echo "Documentation:"
        echo "  - README.md - Project overview"
        echo "  - SETUP.md - Setup instructions"
        echo "  - FEATURES.md - New features documentation"
        ;;

    *)
        echo ""
        echo "❌ Invalid choice. Please run the script again."
        exit 1
        ;;
esac
