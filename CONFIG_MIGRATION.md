# Configuration Migration Guide

## Overview

This project has been migrated from using `config.ini` files to using `.env` files with strongly-typed configuration classes. This provides better type safety, IDE support, and follows modern PHP best practices.

## What Changed

### Old System (config.ini)
- Configuration stored in `config/config.ini`
- Untyped array access via `Config::get('section.key')`
- No type checking or IDE support
- Manual parsing of INI files

### New System (.env)
- Configuration stored in `.env` file at project root
- Strongly-typed configuration classes:
  - `Config\DatabaseConfig` - Database connection settings
  - `Config\AppConfig` - Application settings
  - `Config\LogConfig` - Logging configuration
- Full type safety with PHP 8.2+ readonly properties
- Uses `vlucas/phpdotenv` library for .env parsing

## Configuration Classes

### DatabaseConfig
Located at `config/DatabaseConfig.php`

**Properties:**
- `host: string` - Database host (default: `127.0.0.1`)
- `port: int` - Database port (default: `3306`)
- `database: string` - Database name (default: `f1_db`)
- `username: string` - Database username (default: `root`)
- `password: string` - Database password (default: `''`)
- `charset: string` - Character set (default: `utf8mb4`)
- `collation: string` - Collation (default: `utf8mb4_general_ci`)
- `driver: string` - Database driver (default: `mysql`)

**Usage:**
```php
// New strongly-typed approach (recommended)
$dbConfig = Config::database();
$host = $dbConfig->getHost();
$port = $dbConfig->getPort();

// As array (for Eloquent ORM compatibility)
$dbArray = Config::getDatabase();
```

### AppConfig
Located at `config/AppConfig.php`

**Properties:**
- `name: string` - Application name (default: `F1 Management API`)
- `env: string` - Environment (default: `development`)
- `debug: bool` - Debug mode (default: `true`)

**Usage:**
```php
$appConfig = Config::app();
$appName = $appConfig->getName();
$isDebug = $appConfig->isDebug();
$isProd = $appConfig->isProduction();
$isDev = $appConfig->isDevelopment();
```

### LogConfig
Located at `config/LogConfig.php`

**Properties:**
- `level: string` - Log level (default: `debug`)
- `path: string` - Log file path (default: `logs/app.log`)
- `channel: string` - Log channel name (default: `app`)

**Usage:**
```php
$logConfig = Config::log();
$logLevel = $logConfig->getLevel();
$logPath = $logConfig->getPath();
```

## Backward Compatibility

The migration maintains full backward compatibility with existing code:

```php
// Old approach (still works)
Config::get('database.host');
Config::get('app.debug');
Config::getSection('database');
Config::has('logging.level');
Config::all();

// New strongly-typed approach (recommended)
Config::database()->getHost();
Config::app()->isDebug();
Config::log()->getLevel();
```

## Environment Variables

### .env File Format

The `.env` file should be placed at the project root and contain:

```dotenv
# Database settings
DATABASE_HOST=127.0.0.1
DATABASE_PORT=3306
DATABASE_DATABASE=f1_db
DATABASE_USERNAME=root
DATABASE_PASSWORD=
DATABASE_CHARSET=utf8mb4
DATABASE_COLLATION=utf8mb4_general_ci
DATABASE_DRIVER=mysql

# Application settings
APP_NAME="F1 Management API"
APP_ENV=development
APP_DEBUG=true

# Log settings
LOG_LEVEL=debug
LOG_PATH=logs/app.log
LOG_CHANNEL=app
```

**Important Notes:**
- Comments must be on their own lines (not inline)
- Values with spaces should be quoted (e.g., `APP_NAME="F1 Management API"`)
- Boolean values: `true` or `false` (case-insensitive)
- Empty values are allowed (e.g., `DATABASE_PASSWORD=`)

### Setup for New Projects

1. Copy `.env.example` to `.env`:
   ```bash
   cp .env.example .env
   ```

2. Update values in `.env` as needed for your environment

3. The `.env` file is already in `.gitignore` - never commit it!

## Migration Checklist

If you're migrating an existing project:

- [x] Install `vlucas/phpdotenv` via Composer
- [x] Create `DatabaseConfig.php`, `AppConfig.php`, `LogConfig.php`
- [x] Update `config/config.php` to use .env files
- [x] Update `config/bootstrap.php` to load config classes
- [x] Create `.env` and `.env.example` files
- [x] Test configuration loading
- [ ] Update any existing code to use strongly-typed config (optional but recommended)
- [ ] Update documentation
- [ ] Update CI/CD pipeline if needed

## Testing

Run the test script to verify configuration:

```bash
php test_config.php
```

Expected output:
```
=== Configuration Test ===

1. Testing Database Configuration:
   Host: 127.0.0.1
   Port: 3306
   Database: f1_db
   Username: root
   Driver: mysql
   Charset: utf8mb4

2. Testing Application Configuration:
   Name: F1 Management API
   Environment: development
   Debug: true
   Is Production: false
   Is Development: true

3. Testing Log Configuration:
   Level: debug
   Path: logs/app.log
   Channel: app

4. Testing Backward Compatibility:
   Config::get('database.host'): 127.0.0.1
   Config::get('app.name'): F1 Management API
   Config::get('logging.level'): debug
   Config::has('database.host'): true

5. Testing getDatabase() array method:
   Array keys: driver, host, port, database, username, password, charset, collation

✓ All tests passed!
```

## Benefits of the New System

1. **Type Safety**: All configuration values are properly typed and validated
2. **IDE Support**: Full autocomplete and type hints in your IDE
3. **Immutability**: Configuration uses readonly properties (PHP 8.1+)
4. **Industry Standard**: Uses popular `phpdotenv` library
5. **Better Defaults**: Default values defined in code, not config files
6. **Validation**: Type coercion (e.g., `DATABASE_PORT` to int, `APP_DEBUG` to bool)
7. **Clean Separation**: Each config section has its own class
8. **Testability**: Easier to mock and test

## Common Issues and Solutions

### Issue: "Environment file not found"
**Solution**: Ensure `.env` file exists in the project root. Copy from `.env.example`:
```bash
cp .env.example .env
```

### Issue: Values not loading correctly
**Solution**: Check for:
- Inline comments (not supported - use separate lines)
- Missing quotes around values with spaces
- Incorrect environment variable names (must match `SECTION_KEY` format)

### Issue: Boolean values not working
**Solution**: Use `true` or `false` (not `1` or `0`). The system uses `filter_var($value, FILTER_VALIDATE_BOOLEAN)`.

### Issue: Port numbers as strings
**Solution**: The `DatabaseConfig` class automatically converts `DATABASE_PORT` to an integer.

## Future Enhancements

Potential improvements for the configuration system:

1. Add validation methods to each config class
2. Support for multiple environments (.env.production, .env.testing)
3. Configuration caching for production environments
4. Additional config classes (CacheConfig, MailConfig, etc.)
5. Configuration schema validation
6. Environment variable encryption for sensitive values

## Deprecation Notice

The `config/config.ini` file is now deprecated and will be removed in a future version. Please migrate to using `.env` files as soon as possible.

**Timeline:**
- Current: Both systems work (backward compatibility maintained)
- Future: config.ini support will be removed

## Support

For questions or issues related to configuration:
1. Check this guide first
2. Review the test_config.php script
3. Check the inline documentation in config classes
4. Consult the vlucas/phpdotenv documentation: https://github.com/vlucas/phpdotenv

