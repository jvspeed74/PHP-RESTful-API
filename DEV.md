# Developer Guide

## Table of Contents

1. [Docker Setup](#docker-setup)
2. [Composer](#composer)
3. [Coding Standards](#coding-standards)
4. [GitHub Actions](#github-actions)

## Docker Setup

### Prerequisites

- Docker Desktop installed ([Download](https://www.docker.com/products/docker-desktop))
- Docker Compose (comes with Docker Desktop)

### Quick Start

1. **Start the development environment:**
   ```bash
   docker-compose up -d
   ```
   This starts three services:
   - **php-app**: PHP 8.2 development server (http://localhost:8080)
   - **mariadb**: MariaDB database (port 3306)
   - **phpmyadmin**: Database management UI (http://localhost:8081)

2. **Access the API:**
   - API: http://localhost:8080
   - phpMyAdmin: http://localhost:8081 (credentials: `f1_user` / `f1_password`)

3. **Run Composer commands inside the container:**
   ```bash
   docker-compose exec php-app composer install
   docker-compose exec php-app composer test
   docker-compose exec php-app composer fix
   docker-compose exec php-app composer lint
   ```

4. **View logs:**
   ```bash
   docker-compose logs -f php-app   # PHP application logs
   docker-compose logs -f mariadb   # Database logs
   ```

5. **Stop the environment:**
   ```bash
   docker-compose down
   ```

6. **Rebuild the environment (if Dockerfile changes):**
   ```bash
   docker-compose down
   docker-compose up --build
   ```

### Service Boundaries

- **php-app** (Port 8080): Slim Framework PHP application with Xdebug support. Isolated from host except for volume mounts.
- **mariadb** (Port 3306): MariaDB database service. Data persists in Docker volume `f1_db_data`. Only accessible to php-app and phpmyadmin on the bridge network.
- **phpmyadmin** (Port 8081): Web interface for database management. Communicates with mariadb via the bridge network.
- **Bridge Network (f1_network)**: Services communicate via service hostname (e.g., `mariadb` instead of `localhost`).

### Debugging with Xdebug

Xdebug is configured in the container. Configure your IDE:
- **PhpStorm/IntelliJ**: Settings → Languages & Frameworks → PHP → Servers
  - Name: `localhost`
  - Host: `localhost`
  - Port: `8080`
  - Debugger: `Xdebug`

### Environment Variables

Configuration is managed via `/config/config.ini` file (created from `config.ini.example`):

```ini
[database]
host = mariadb
port = 3306
database = f1_db
username = f1_user
password = f1_password

[app]
env = development
debug = true
```

**For Docker Compose**: Environment variables override config.ini values
```env
DATABASE_HOST=mariadb
DATABASE_PORT=3306
DATABASE_DATABASE=f1_db
DATABASE_USERNAME=f1_user
DATABASE_PASSWORD=f1_password
APP_ENV=development
```

**For local XAMPP development** (without Docker), update `/config/config.ini`:
```ini
[database]
host = 127.0.0.1
username = root
password =
```

The `Config` class (`config/config.php`) loads settings with environment variables taking precedence.

## Composer

- The project includes a preconfigured `composer.json` file.
- The repository includes `composer.phar`, allowing Composer to be run without a global installation.
- Several scripts are included in the `composer.json` file to help with common tasks. 
  - You can run the following commands in your terminal (or via `docker-compose exec php-app` when using Docker):
    - `composer test` runs the test suite.
    - `composer fix` runs PHP-CS-Fixer, fixing any coding standard issues.
    - `composer lint` runs PHPStan, identifying any potential issues in the code.
    - `composer start` starts the built-in PHP server (when not using Docker).

## Coding Standards

- The project `.idea` file has been configured to adhere to PERCS-2.0 PHP standards.
- The `phpstan` and `php-cs-fixer` packages are required in the `dev` section of the `composer.json` file. Once
  downloaded, PhpStorm detects them and runs them in the background, providing code inspections on the fly.
- This setup does not require manual intervention.

## GitHub Actions

- On every PR leading to a major branch, a pipeline will run on GitHub to verify coding standards and ensure tests are successful.
- Nothing prevents code from being pushed to the repository; the pipeline is there to highlight key issues in our code.
- The project requires the `GitHub Action Manager` plugin, allowing you to see the results of the pipeline without
  needing to tab out from PhpStorm.
- To see the pipeline results, click on the circular icon with a play button, located in the bottom left corner of the
  IDE.





