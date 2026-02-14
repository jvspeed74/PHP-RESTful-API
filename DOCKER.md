# Docker Development Setup

## Overview

This project includes a complete Docker development environment using Docker Compose. The setup consists of three isolated services communicating via a bridge network, optimized for local PHP development with hot-reload, debugging, and database management.

## Services Architecture

### Service Boundaries

```
┌─────────────────────────────────────────┐
│         Docker Bridge Network            │
│            (f1_network)                  │
│                                          │
│  ┌──────────────────┐  ┌──────────────┐ │
│  │   php-app        │  │  mariadb     │ │
│  │ (Port: 8080)     │  │ (Port: 3306) │ │
│  │ PHP 8.2 + Xdebug │  │  MariaDB 11  │ │
│  │ Slim Framework   │  │ f1_db_data   │ │
│  │ Hot-reload ready │  │  (volume)    │ │
│  └──────────────────┘  └──────────────┘ │
│           │                   │          │
│           └───────────────────┘          │
│               (mysql queries)            │
│                                          │
│  ┌──────────────────┐                   │
│  │   phpmyadmin     │                   │
│  │ (Port: 8081)     │                   │
│  │ Database Admin   │                   │
│  └──────────────────┘                   │
│           │                              │
│           └──────────────────────────────┤
│               (http queries)             │
└─────────────────────────────────────────┘

Host Machine:
  localhost:8080  ──► php-app (API)
  localhost:3306  ──► mariadb (if direct access needed)
  localhost:8081  ──► phpmyadmin (Web UI)
```

### Service Details

#### 1. **php-app** (Port 8080)
- **Image**: Custom built from `Dockerfile`
- **Base**: PHP 8.2 CLI
- **Key Features**:
  - Xdebug enabled for IDE debugging
  - Composer pre-installed
  - MySQL/MariaDB extensions (PDO, mysqli)
  - Hot-reload via volume mounts
  - Runs PHP development server on startup
- **Volumes**:
  - `.` (project root) → `/var/www/html` (code, hot-reload)
  - `./logs` → `/var/www/html/logs` (application logs, persistent)
  - `vendor` (named volume to optimize build performance)
- **Environment**: Database credentials from `.env` file
- **Health**: Waits for mariadb service before starting

#### 2. **mariadb** (Port 3306)
- **Image**: `mariadb:11.4`
- **Key Features**:
  - MariaDB 11.4 (compatible with MySQL 8.0 API)
  - Auto-initializes database from `f1_db.sql` on first run
  - Health check ensures container is ready before php-app starts
- **Volumes**:
  - `f1_db_data` (named volume) → `/var/lib/mysql` (persistent data)
  - `./f1_db.sql` → `/docker-entrypoint-initdb.d/f1_db.sql` (schema import)
- **Environment**:
  - MARIADB_DATABASE=f1_db
  - MARIADB_USER=f1_user
  - MARIADB_PASSWORD=f1_password
  - MARIADB_ALLOW_EMPTY_PASSWORD=yes
- **Network**: Only accessible via bridge network (not exposed to host by default, but port 3306 is mapped for direct access)

#### 3. **phpmyadmin** (Port 8081)
- **Image**: `phpmyadmin:latest`
- **Key Features**:
  - Web-based database management
  - Pre-configured to connect to mariadb service
- **Environment**:
  - PMA_HOST=mariadb (resolves via bridge network)
  - PMA_USER=f1_user
  - PMA_PASSWORD=f1_password
- **Access**: http://localhost:8081

### Network Configuration

- **Network Type**: Bridge (`f1_network`)
- **DNS Resolution**: Service names resolve to container IPs (e.g., `mariadb` resolves to mariadb container IP)
- **Isolation**: Services cannot access the host directly; only expose mapped ports
- **Communication**: php-app connects to mariadb via hostname `mariadb:3306`

## Getting Started

### Prerequisites

- Docker Desktop installed ([Download](https://www.docker.com/products/docker-desktop))
- 2GB free disk space (for images and volumes)

### Initial Setup

1. **Clone/Download the project**
   ```bash
   cd /path/to/PHP-RESTful-API
   ```

2. **Create `.env` from `.env.example`** (if not already present)
   ```bash
   cp .env.example .env
   ```
   The `.env` file is pre-configured with Docker service defaults.

3. **Build and start services**
   ```bash
   docker-compose up -d
   ```
   - First run builds the PHP image (may take 2-3 minutes)
   - Subsequent runs are instant
   - Services start in order: mariadb → php-app → phpmyadmin
   - Database is automatically initialized from `f1_db.sql`

4. **Verify startup**
   ```bash
   docker-compose ps
   ```
   All three services should show "Up" status.

5. **Access the application**
   - API: http://localhost:8080
   - phpMyAdmin: http://localhost:8081 (f1_user / f1_password)

## Common Commands

### Container Management

```bash
# Start services in background
docker-compose up -d

# View running containers
docker-compose ps

# View logs for all services
docker-compose logs -f

# View logs for specific service
docker-compose logs -f php-app
docker-compose logs -f mariadb

# Stop services (preserves data)
docker-compose stop

# Stop and remove containers (keeps volumes)
docker-compose down

# Stop, remove containers, and volumes (full cleanup)
docker-compose down -v

# Rebuild services (if Dockerfile changes)
docker-compose up -d --build
```

### Execute Commands in Container

```bash
# Run Composer commands
docker-compose exec php-app composer install
docker-compose exec php-app composer update
docker-compose exec php-app composer test
docker-compose exec php-app composer fix
docker-compose exec php-app composer lint

# Run PHP directly
docker-compose exec php-app php -v

# Get interactive shell
docker-compose exec php-app bash
```

### Database Management

```bash
# Access database via MySQL client
docker-compose exec mariadb mysql -u f1_user -pf1_password -D f1_db

# Dump database
docker-compose exec mariadb mysqldump -u f1_user -pf1_password f1_db > backup.sql

# Import SQL file
docker-compose exec -T mariadb mysql -u f1_user -pf1_password f1_db < f1_db.sql
```

### View Data

```bash
# Check what's in volumes
docker volume ls

# Inspect a volume
docker volume inspect f1_db_data

# View application logs on host
cat logs/app.log

# View Xdebug logs
cat logs/xdebug.log
```

## Development Workflow

### Code Changes & Hot-Reload

1. **Edit code** in your IDE (on your host machine)
2. **Automatic reload**: Changes instantly reflected in container
3. **No container rebuild needed** (except Dockerfile changes)

```bash
# Example: Edit a controller
# → Changes visible at http://localhost:8080 immediately
```

### Running Tests

```bash
# Run all tests
docker-compose exec php-app composer test

# Run specific test file
docker-compose exec php-app vendor/bin/pest tests/Feature/YourTest.php

# Run tests with coverage
docker-compose exec php-app vendor/bin/pest --coverage
```

### Code Quality

```bash
# Fix coding standards
docker-compose exec php-app composer fix

# Run static analysis
docker-compose exec php-app composer lint

# Run OpenAPI linting (requires redocly-cli)
redocly lint docs/openapi.yaml
```

## Debugging with Xdebug

### PhpStorm Setup

1. **Configure PHP Interpreter**:
   - Settings → Languages & Frameworks → PHP
   - Click `...` next to PHP language level
   - Click `+` → From Docker Compose
   - Select service: `php-app`
   - Click `OK`

2. **Configure Debug Server**:
   - Settings → Languages & Frameworks → PHP → Debug → Servers
   - Click `+` to add server
   - Name: `localhost`
   - Host: `localhost`
   - Port: `8080`
   - Debugger: `Xdebug`

3. **Start Debugging**:
   - Set breakpoint in code
   - Press `Ctrl+Alt+F9` (or use Debug menu) to start listening for connections
   - Navigate to endpoint in browser
   - Code execution pauses at breakpoint

### VSCode Setup

1. **Install Xdebug extension** by Felice Polisky

2. **Create `.vscode/launch.json`**:
   ```json
   {
     "version": "0.2.0",
     "configurations": [
       {
         "name": "Listen for Xdebug",
         "type": "php",
         "request": "launch",
         "port": 9003,
         "pathMapping": {
           "/var/www/html": "${workspaceFolder}"
         }
       }
     ]
   }
   ```

3. **Start listening** and set breakpoints

## Environment Variables

All database configuration is managed via `.env` file:

```env
DB_HOST=mariadb          # Service hostname on bridge network
DB_PORT=3306             # MariaDB port
DB_DATABASE=f1_db        # Database name
DB_USERNAME=f1_user      # Database user
DB_PASSWORD=f1_password  # Database password
```

For **local development without Docker**, update `.env` with your local credentials:
```env
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=f1_db
DB_USERNAME=root
DB_PASSWORD=
```

The application reads these via `config/bootstrap.php` → `config/dependencies.php`.

## Troubleshooting

### Services Won't Start

```bash
# Check logs
docker-compose logs mariadb
docker-compose logs php-app

# Verify Docker is running
docker ps

# Check port conflicts
lsof -i :8080   # macOS/Linux
netstat -ano | findstr :8080  # Windows
```

### Database Connection Errors

```bash
# Test connection from php-app
docker-compose exec php-app mysql -h mariadb -u f1_user -pf1_password -D f1_db -e "SELECT 1;"

# If fails, restart mariadb
docker-compose restart mariadb

# Or force rebuild
docker-compose down -v
docker-compose up -d
```

### Vendor Directory Issues

```bash
# Vendor directory is mounted as named volume for performance
# If composer install fails, rebuild
docker-compose up -d --build

# Or manually install
docker-compose exec php-app composer install --prefer-dist
```

### Xdebug Not Working

1. Verify port 9003 is not blocked by firewall
2. Check Xdebug logs: `cat logs/xdebug.log`
3. Ensure IDE is listening for connections
4. Rebuild container: `docker-compose up -d --build`

## Performance Considerations

### Volume Mounts

- **Project code** (`.`): Mounted for hot-reload—changes visible immediately
- **Logs**: Separate mount to avoid rebuilding when logs grow
- **vendor**: Named volume (not synced) for build performance—survive container restarts

### Build Optimization

- Multi-stage Dockerfile (future enhancement for production)
- `.dockerignore` excludes unnecessary files
- Composer optimizations (--prefer-dist, --optimize-autoloader)

## Production Considerations

⚠️ **This setup is optimized for development. For production:**

- Replace Xdebug with production PHP extensions
- Use Nginx/Apache instead of PHP dev server
- Implement separate production Dockerfile
- Use environment-specific configs
- Add persistent backup strategy for database volume
- Implement proper logging infrastructure

## File Structure

```
project-root/
├── Dockerfile              # Development PHP image
├── docker-compose.yml      # Service orchestration
├── docker-entrypoint.sh    # Container startup script
├── .dockerignore           # Build exclusions
├── .env                    # Local environment vars (not in git)
├── .env.example            # Template for .env
├── config/
│   ├── bootstrap.php       # Loads .env file
│   └── dependencies.php    # Uses $_ENV for database config
├── logs/                   # Mounted volume (persistent)
├── f1_db.sql              # Database schema (imported on startup)
└── ... (other project files)
```

## Next Steps

1. **Start developing**: `docker-compose up -d`
2. **Test the API**: http://localhost:8080
3. **Check README.md** for API documentation
4. **Run tests**: `docker-compose exec php-app composer test`
5. **Set up IDE debugging** (see Xdebug section above)

## Additional Resources

- [Docker Documentation](https://docs.docker.com/)
- [Docker Compose Documentation](https://docs.docker.com/compose/)
- [Xdebug Documentation](https://xdebug.org/docs/debugger)
- [PHP CLI Image on Docker Hub](https://hub.docker.com/_/php/)
- [MariaDB Image on Docker Hub](https://hub.docker.com/_/mariadb)
