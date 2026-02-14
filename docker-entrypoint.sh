#!/bin/bash
set -e

echo "=== F1 Management API - Docker Startup ==="

DB_HOST="${DATABASE_HOST:-mariadb}"
DB_PORT="${DATABASE_PORT:-3306}"
DB_USERNAME="${DATABASE_USERNAME:-f1_user}"
DB_PASSWORD="${DATABASE_PASSWORD:-f1_password}"
DB_DATABASE="${DATABASE_DATABASE:-f1_db}"

# Ensure logs directory exists and has proper permissions
mkdir -p logs
chmod -R 777 logs

# Wait for MariaDB to be ready
if command -v mysql >/dev/null 2>&1; then
    echo "Waiting for MariaDB to be ready..."
    max_attempts=30
    attempt=0
    while [ $attempt -lt $max_attempts ]; do
        if mysql -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USERNAME" -p"$DB_PASSWORD" -e "SELECT 1;" 2>/dev/null; then
            echo "✓ MariaDB is ready!"
            break
        fi
        attempt=$((attempt + 1))
        echo "  Attempt $attempt/$max_attempts... waiting"
        sleep 2
    done

    if [ $attempt -eq $max_attempts ]; then
        echo "✗ MariaDB failed to start in time"
        exit 1
    fi
fi

# Install composer dependencies if needed
if [ ! -d "vendor" ]; then
    echo "Installing Composer dependencies..."
    composer install --prefer-dist --no-progress --no-interaction
else
    echo "Updating Composer dependencies..."
    composer install --prefer-dist --no-progress --no-interaction
fi

echo ""
echo "=== Environment Configuration ==="
echo "DATABASE_HOST: $DB_HOST"
echo "DATABASE_PORT: $DB_PORT"
echo "DATABASE_DATABASE: $DB_DATABASE"
echo "DATABASE_USERNAME: $DB_USERNAME"
echo ""

echo "=== Starting PHP Development Server ==="
echo "Server running at http://localhost:8080"
echo "phpMyAdmin available at http://localhost:8081"

exec php -S 0.0.0.0:8080 -t public
