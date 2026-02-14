# Docker Architecture & Service Boundaries Diagram

## System Overview

```
╔════════════════════════════════════════════════════════════════════════════╗
║                         DOCKER COMPOSE ENVIRONMENT                         ║
║                     (Bridge Network: f1_network)                           ║
╚════════════════════════════════════════════════════════════════════════════╝

┌─────────────────────────────────────────────────────────────────────────────┐
│                          BRIDGE NETWORK: f1_network                         │
│                                                                              │
│  ┌──────────────────────────┐      ┌──────────────────────────┐            │
│  │     PHP-APP SERVICE      │      │    MARIADB SERVICE       │            │
│  │  (Container: php-app)    │      │  (Container: mariadb)    │            │
│  │                          │      │                          │            │
│  │  Image: PHP 8.2 CLI      │      │  Image: MariaDB 11.4     │            │
│  │  ├─ PDO MySQL            │      │  ├─ Default user: root   │            │
│  │  ├─ MySQLi               │      │  ├─ DB user: f1_user     │            │
│  │  ├─ Xdebug (Debug)       │      │  ├─ DB: f1_db            │            │
│  │  ├─ Composer             │      │  └─ Init: f1_db.sql      │            │
│  │  └─ PHP Dev Server       │      │                          │            │
│  │                          │      │  Volumes:                │            │
│  │  Listens: 0.0.0.0:8080  │      │  └─ f1_db_data           │            │
│  │                          │      │     (persistent data)    │            │
│  │  Volumes (Host Mount):   │      │                          │            │
│  │  ├─ ./  → /var/www/html │      │  Port Exposed:           │            │
│  │  │  (hot-reload)        │      │  └─ 3306 (MySQL/DB)      │            │
│  │  ├─ ./logs → logs/      │      │                          │            │
│  │  │  (application logs)  │      │  Environment:            │            │
│  │  └─ /vendor (named)     │      │  ├─ MARIADB_DATABASE=... │            │
│  │     (performance)       │      │  ├─ MARIADB_USER=...     │            │
│  │                          │      │  └─ MARIADB_PASSWORD=... │            │
│  │  Environment:            │      │                          │            │
│  │  ├─ DB_HOST=mariadb      │      │  Health Check:           │            │
│  │  ├─ DB_PORT=3306         │      │  └─ innodb_initialized   │            │
│  │  ├─ DB_DATABASE=f1_db    │      │                          │            │
│  │  ├─ DB_USERNAME=f1_user  │      │  Startup:                │            │
│  │  └─ DB_PASSWORD=...      │      │  └─ docker-entrypoint.sh │            │
│  │                          │      │                          │            │
│  │  Dependencies:           │      │  Status: Healthy ✓       │            │
│  │  └─ Waits for mariadb    │      │                          │            │
│  │     health check         │      │                          │            │
│  │                          │      │                          │            │
│  │  Status: Running ✓       │      │                          │            │
│  └──────────────────────────┘      └──────────────────────────┘            │
│          ↓                                    ↑                             │
│          │   MySQL Connection                │                             │
│          └────────────────────────────────────┘                             │
│                                                                              │
│  ┌──────────────────────────┐                                              │
│  │   PHPMYADMIN SERVICE     │                                              │
│  │(Container: phpmyadmin)   │                                              │
│  │                          │                                              │
│  │  Image: phpMyAdmin:latest│                                              │
│  │                          │                                              │
│  │  Listens: 0.0.0.0:80     │                                              │
│  │                          │                                              │
│  │  Environment:            │                                              │
│  │  ├─ PMA_HOST=mariadb     │                                              │
│  │  ├─ PMA_PORT=3306        │                                              │
│  │  ├─ PMA_USER=f1_user     │                                              │
│  │  └─ PMA_PASSWORD=...     │                                              │
│  │                          │                                              │
│  │  Status: Running ✓       │                                              │
│  └──────────────────────────┘                                              │
│          ↓                                                                   │
│          │   MySQL Connection                                              │
│          └────────────────────────────────────────────────────────────────┐│
│                                                                            ││
└────────────────────────────────────────────────────────────────────────────┘│
                                                                             │
                         Mariadb Service (Above)
                                    ↑


                         HOST MACHINE PORTS
                         ════════════════════════════════════════════════════

                            Port Mappings (Exposed)
                                    ↓
                         ┌──────────────────────┐
                         │  localhost:8080  ────┼────► php-app (API)
                         │  localhost:3306  ────┼────► mariadb (optional)
                         │  localhost:8081  ────┼────► phpmyadmin
                         └──────────────────────┘

                         Development Access Points
                         ════════════════════════════════════════════════════
                         
                         HTTP API:
                         └─ http://localhost:8080
                             └─ Slim Framework REST API
                             └─ All CRUD operations
                             └─ Authentication endpoints
                             
                         Database Management (Web):
                         └─ http://localhost:8081 (phpMyAdmin)
                             └─ User: f1_user
                             └─ Password: f1_password
                             └─ Database: f1_db
                             
                         Database Direct Access:
                         └─ localhost:3306 (MySQL client)
                             └─ For IDE database tools
                             └─ For CLI tools (mysql, mysqldump)
```

---

## Service Startup Sequence

```
┌────────────────────────────────────────────────────────────────┐
│           Docker Compose Startup Sequence                       │
└────────────────────────────────────────────────────────────────┘

Step 1: Build (First Run Only)
    ├─ Build PHP image from Dockerfile
    └─ Pull mariadb:11.4 image
       └─ Pull phpmyadmin:latest image

Step 2: Create Network
    └─ Create bridge network: f1_network

Step 3: Create Volumes
    ├─ Create volume: f1_db_data
    └─ Create volume: vendor cache

Step 4: Start MariaDB Service
    ├─ Start mariadb container
    ├─ Initialize database from f1_db.sql
    ├─ Run health check
    │   ├─ Test 1: Initial check (skip, start_period=30s)
    │   ├─ Test 2-5: Retry checks (10s interval)
    │   └─ Status: ✓ Healthy
    └─ Ready for connections

Step 5: Start PHP-App Service (Depends On: mariadb health)
    ├─ Start php-app container
    ├─ Execute docker-entrypoint.sh
    │   ├─ Wait for mariadb connection (with retries)
    │   ├─ Run: composer install
    │   ├─ Create logs directory
    │   └─ Start PHP dev server (0.0.0.0:8080)
    └─ Ready for API requests

Step 6: Start phpMyAdmin Service (Depends On: mariadb start)
    ├─ Start phpmyadmin container
    ├─ Connect to mariadb (via bridge network)
    └─ Ready for web access

Step 7: All Services Ready
    └─ Total time: 60-90 seconds (first run)
       └─ Subsequent starts: 10-15 seconds
```

---

## Network Communication Flow

```
┌─────────────────────────────────────────────────────────────────┐
│       Network Communication Inside Bridge Network                │
└─────────────────────────────────────────────────────────────────┘

Scenario 1: API Request
    ┌─────────────────────┐
    │ Developer Browser   │
    │ http://localhost    │  HTTP Request
    │ :8080               │────────────────┐
    └─────────────────────┘                │
                                           ↓
                        ┌──────────────────────────────────┐
                        │  Docker Host (Port Mapping)      │
                        │  :8080 → container:8080         │
                        └──────────────────────────────────┘
                                           ↓
                        ┌──────────────────────────────────┐
                        │  Bridge Network (f1_network)     │
                        │  DNS: localhost resolve ip       │
                        └──────────────────────────────────┘
                                           ↓
                        ┌──────────────────────────────────┐
                        │  php-app Container               │
                        │  PHP Dev Server :8080            │
                        │  ├─ Parse request                │
                        │  └─ Need database access         │
                        └──────────────────────────────────┘
                                           ↓
                        Query: "SELECT * FROM drivers"
                        Via: mysql/mysqli connection
                                           ↓
                        ┌──────────────────────────────────┐
                        │  Bridge Network DNS Resolution   │
                        │  mariadb → 172.17.0.2:3306      │
                        └──────────────────────────────────┘
                                           ↓
                        ┌──────────────────────────────────┐
                        │  mariadb Container               │
                        │  MySQL Server :3306              │
                        │  ├─ Execute query                │
                        │  └─ Return results               │
                        └──────────────────────────────────┘
                                           ↓
                        Response: JSON data
                                           ↓
                        ┌──────────────────────────────────┐
                        │  php-app Container               │
                        │  ├─ Format response              │
                        │  └─ Send HTTP 200                │
                        └──────────────────────────────────┘
                                           ↓
                        ┌──────────────────────────────────┐
                        │  Docker Host Port Mapping        │
                        │  container:8080 → :8080          │
                        └──────────────────────────────────┘
                                           ↓
    ┌─────────────────────┐
    │ Developer Browser   │
    │ Receive JSON Data   │
    └─────────────────────┘


Scenario 2: phpMyAdmin Access
    ┌─────────────────────┐
    │ Developer Browser   │
    │ http://localhost    │  HTTP Request
    │ :8081               │────────────────┐
    └─────────────────────┘                │
                                           ↓
                        ┌──────────────────────────────────┐
                        │  Docker Host Port Mapping        │
                        │  :8081 → phpmyadmin:80           │
                        └──────────────────────────────────┘
                                           ↓
                        ┌──────────────────────────────────┐
                        │  Bridge Network (f1_network)     │
                        └──────────────────────────────────┘
                                           ↓
                        ┌──────────────────────────────────┐
                        │  phpmyadmin Container            │
                        │  Web Server :80                  │
                        └──────────────────────────────────┘
                                           ↓
                        SQL Query: "SHOW DATABASES"
                        Via: mariadb:3306
                                           ↓
                        ┌──────────────────────────────────┐
                        │  Bridge Network DNS              │
                        │  mariadb → 172.17.0.2:3306       │
                        └──────────────────────────────────┘
                                           ↓
                        ┌──────────────────────────────────┐
                        │  mariadb Container               │
                        │  Returns database list           │
                        └──────────────────────────────────┘
                                           ↓
    ┌─────────────────────┐
    │ Developer Browser   │
    │ See phpMyAdmin UI   │
    └─────────────────────┘


Scenario 3: CLI Tools (docker-compose exec)
    ┌──────────────────────────────────────┐
    │ Host Terminal                        │
    │ $ docker-compose exec php-app bash   │────┐
    └──────────────────────────────────────┘    │
                                                │
                        ┌──────────────────────┐│
                        │ Docker Daemon        ││
                        │ Create exec stream   ││
                        └──────────────────────┘│
                                                │
                                                ↓
                        ┌──────────────────────────────────┐
                        │  php-app Container               │
                        │  Interactive Bash Shell          │
                        │  Full environment access         │
                        │  Can run: composer, php, etc.    │
                        └──────────────────────────────────┘
```

---

## Volume Mount Architecture

```
┌────────────────────────────────────────────────────────────────┐
│              Volume Mount Strategy                              │
└────────────────────────────────────────────────────────────────┘

HOST MACHINE                          CONTAINER
─────────────────────────────────────────────────────────────────

Project Directory Structure
/project-root/
├─ .                          ──────►  /var/www/html
│  │  (all code)              BIND     └─ Hot-reload capability
│  ├─ src/
│  ├─ config/
│  ├─ public/
│  └─ ... (all PHP files)
│
├─ ./logs/               ──────►  /var/www/html/logs
│  │  (application logs)  BIND     └─ Persistent logs on host
│  └─ app.log
│
└─ [Not on host]         ──────►  /var/www/html/vendor
   (created in container)NAMED     └─ Named volume for
   vendor cache           VOLUME     performance & caching


DATABASE VOLUMES
─────────────────────────────────────────────────────────────────

HOST MACHINE                          CONTAINER
─────────────────────────────────────────────────────────────────

[Docker Volume Storage]  ──────►  /var/lib/mysql
/var/lib/docker/          NAMED    ├─ Database tables
 volumes/f1_db_data/       VOLUME   ├─ InnoDB data
                                    └─ Persistent across restarts

./f1_db.sql            ──────►  /docker-entrypoint-initdb.d/
(init script)           BIND       └─ Auto-executed on first run
                        READ-ONLY


BENEFITS OF THIS STRATEGY
─────────────────────────────────────────────────────────────────

Bind Mounts (./code → /container/code)
├─ Pro: Real-time sync, easy file editing
├─ Pro: IDE access to container code
├─ Pro: Logs visible on host
└─ Con: Slower on Windows (due to Docker Desktop virtualization)

Named Volumes (/vendor, f1_db_data)
├─ Pro: Better performance
├─ Pro: Native Docker management
├─ Pro: Survives container recreation
├─ Pro: Database isolation
└─ Con: Not directly visible on host


PERSISTENCE & DATA FLOW
─────────────────────────────────────────────────────────────────

Container Restart Scenarios:
├─ docker-compose restart
│  ├─ Volumes preserved ✓
│  └─ Data intact ✓
│
├─ docker-compose down (without -v)
│  ├─ Volumes preserved ✓
│  ├─ Data on host preserved ✓
│  └─ Container recreated, volumes reattached ✓
│
└─ docker-compose down -v (full cleanup)
   ├─ All volumes deleted
   ├─ Database wiped
   └─ f1_db.sql re-imported on next up ✓
```

---

## Environment Variable Flow

```
┌────────────────────────────────────────────────────────────────┐
│          Environment Variable Configuration Flow                │
└────────────────────────────────────────────────────────────────┘

1. HOST MACHINE: Create .env file
   ┌──────────────────────┐
   │ .env (on host)       │
   │ ─────────────────    │
   │ DB_HOST=mariadb      │
   │ DB_PORT=3306         │
   │ DB_DATABASE=f1_db    │
   │ DB_USERNAME=f1_user  │
   │ DB_PASSWORD=f1_pass  │
   └──────────────────────┘
           ↓
2. DOCKER COMPOSE: Read environment section
   ┌──────────────────────────────┐
   │ docker-compose.yml           │
   │                              │
   │ services:                    │
   │   php-app:                   │
   │     environment:             │
   │     - DB_HOST=mariadb        │
   │     - DB_PORT=3306           │
   │     - DB_DATABASE=f1_db      │
   │     - DB_USERNAME=f1_user    │
   │     - DB_PASSWORD=f1_password│
   └──────────────────────────────┘
           ↓
3. CONTAINER STARTUP: docker-entrypoint.sh executes
   ┌──────────────────────────────┐
   │ Inside php-app container     │
   │ ─────────────────────────     │
   │ $ env (shows all vars)       │
   │ DB_HOST=mariadb ✓            │
   │ DB_PORT=3306 ✓               │
   │ etc.                         │
   └──────────────────────────────┘
           ↓
4. PHP APPLICATION: Load .env & use environment
   ┌──────────────────────────────────┐
   │ config/bootstrap.php             │
   │                                  │
   │ // Load .env file                │
   │ $envFile = __DIR__ . '/.env'     │
   │ if (file_exists($envFile)) {     │
   │   // Parse and populate $_ENV[]  │
   │   $_ENV['DB_HOST'] = 'mariadb'   │
   │   ...                            │
   │ }                                │
   └──────────────────────────────────┘
           ↓
5. DEPENDENCIES: Use environment variables
   ┌──────────────────────────────────┐
   │ config/dependencies.php          │
   │                                  │
   │ 'db' => function() {             │
   │   return new Manager()           │
   │     ->addConnection([            │
   │       'host' =>                  │
   │         $_ENV['DB_HOST']         │
   │         ?? '127.0.0.1',          │
   │       ...                        │
   │     ]);                          │
   │ }                                │
   └──────────────────────────────────┘
           ↓
6. APPLICATION: Connected to database
   ┌──────────────────────────────────┐
   │ Eloquent ORM (via Capsule)       │
   │ ├─ Connect to mariadb:3306       │
   │ ├─ Authenticate as f1_user       │
   │ ├─ Select database f1_db         │
   │ └─ Ready to execute queries ✓    │
   └──────────────────────────────────┘


FALLBACK MECHANISM (Non-Docker)
─────────────────────────────────────────────────────────────────

If .env not found or variables not set:
└─ config/dependencies.php provides defaults
   ├─ DB_HOST → '127.0.0.1' (localhost)
   ├─ DB_PORT → 3306
   ├─ DB_DATABASE → 'f1_db'
   ├─ DB_USERNAME → 'root'
   └─ DB_PASSWORD → '' (empty)

This allows XAMPP users to run without Docker
by updating .env with local credentials.
```

---

## Health Check & Dependency Sequence

```
┌────────────────────────────────────────────────────────────────┐
│        Service Startup & Health Check Sequence                  │
└────────────────────────────────────────────────────────────────┘

Timeline:
─────────────────────────────────────────────────────────────────

T=0s   "docker-compose up -d"
       └─ Start services

T=0s   mariadb service
       └─ Start container
       └─ Initialize database
       └─ Begin health checks

T=10s  Health Check #1 (after start_period=30s skip)
       └─ Command: healthcheck.sh --connect --innodb_initialized
       └─ Status: Checking...
       └─ Result: Pending (database still initializing)

T=20s  Health Check #2
       └─ Command: healthcheck.sh --connect --innodb_initialized
       └─ Result: Pending

T=30s  Health Check #3 (after start_period)
       └─ Command: healthcheck.sh --connect --innodb_initialized
       └─ Result: Starting to pass

T=40s  Health Check #4
       └─ Result: ✓ Healthy

Now MariaDB is healthy, php-app can start
─────────────────────────────────────────────────────────────────

T=40s  php-app service starts (depends_on: mariadb healthy)
       └─ Start container
       └─ Execute docker-entrypoint.sh
           ├─ Wait loop: Attempt connection to mariadb
           │   ├─ Attempt 1: Connected! ✓
           │   └─ mariadb:3306 responding
           ├─ Run: composer install (if not cached)
           │   └─ ~20-40 seconds (first run)
           │   └─ ~5 seconds (subsequent)
           ├─ Create logs directory
           └─ Start PHP dev server
               └─ "Server running at http://0.0.0.0:8080"

T=45s  phpmyadmin service starts (depends_on: mariadb started)
       └─ Start container
       └─ Connect to mariadb:3306
       └─ Web server ready

T=50s  ✓ All services ready!
       ├─ php-app: http://localhost:8080 ✓
       ├─ phpMyAdmin: http://localhost:8081 ✓
       └─ mariadb: localhost:3306 ✓

       Total startup time: ~50 seconds (first run)
       Total startup time: ~10-15 seconds (subsequent runs)


DEPENDENCY RULES
─────────────────────────────────────────────────────────────────

mariadb (no dependencies)
├─ Starts first
├─ Must reach "Healthy" status
└─ Then signals other services

php-app (depends_on: mariadb)
├─ Waits for: mariadb service to be healthy
├─ Then starts
└─ Internally waits for DB connection in entrypoint

phpmyadmin (depends_on: mariadb)
├─ Waits for: mariadb service to START (not necessarily healthy)
├─ Then starts
└─ Connects to mariadb


WHAT IF MARIADB FAILS?
─────────────────────────────────────────────────────────────────

Scenario 1: MariaDB container crashes
└─ Health checks fail for all retries
└─ php-app waiting for health check times out
└─ docker-compose reports:
   "Service X failed to reach 'healthy' state"

Recovery:
└─ docker-compose logs mariadb (see what went wrong)
└─ docker-compose restart mariadb
└─ or: docker-compose up -d --build

Scenario 2: MariaDB is "up" but not ready
└─ php-app container starts
└─ docker-entrypoint.sh waits for connection
└─ Retries connection 30 times with 2-second intervals
└─ If still failing after 60 seconds, exits with error

Recovery:
└─ docker-compose logs php-app (see connection errors)
└─ Ensure DB_HOST=mariadb (correct hostname)
└─ Check mariadb logs for database errors
```

---

## Summary

This architecture provides:
- **Clear service boundaries** with explicit network isolation
- **Predictable startup** with health checks and dependencies
- **Data persistence** through volumes
- **Hot-reload development** through bind mounts
- **Database management** through both CLI and phpMyAdmin
- **Debugging capability** via Xdebug on port 9003
- **Production-ready foundation** with environment variables
