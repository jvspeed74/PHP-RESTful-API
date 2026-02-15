<?php

declare(strict_types=1);

use Config\AppConfig;
use Config\DatabaseConfig;
use Config\LogConfig;
use Dotenv\Dotenv;

/**
 * Application Configuration
 *
 * Loads configuration from .env file and provides strongly typed access
 * to configuration sections through dedicated config classes.
 */
class Config
{
    private static ?DatabaseConfig $databaseConfig = null;
    private static ?AppConfig $appConfig = null;
    private static ?LogConfig $logConfig = null;
    private static bool $initialized = false;

    /**
     * Initialize configuration from .env file
     */
    public static function init(): void
    {
        if (self::$initialized) {
            return;
        }

        $envFile = __DIR__ . '/../.env';

        if (!file_exists($envFile)) {
            throw new RuntimeException(
                "Environment file not found: {$envFile}\n" .
                "Please copy .env.example to .env and update with your settings."
            );
        }

        // Load .env file
        $dotenv = Dotenv::createImmutable(__DIR__ . '/..');
        $dotenv->load();

        // Initialize config objects
        self::$databaseConfig = DatabaseConfig::fromEnv();
        self::$appConfig = AppConfig::fromEnv();
        self::$logConfig = LogConfig::fromEnv();

        self::$initialized = true;
    }

    /**
     * Get database configuration
     */
    public static function database(): DatabaseConfig
    {
        self::init();
        return self::$databaseConfig;
    }

    /**
     * Get application configuration
     */
    public static function app(): AppConfig
    {
        self::init();
        return self::$appConfig;
    }

    /**
     * Get logging configuration
     */
    public static function log(): LogConfig
    {
        self::init();
        return self::$logConfig;
    }

    /**
     * Get database configuration as array (for backward compatibility)
     *
     * @return array<string, string|int>
     */
    public static function getDatabase(): array
    {
        return self::database()->toArray();
    }

    /**
     * Get a configuration value (for backward compatibility)
     * Supports dot notation: "database.host", "app.debug", "logging.level"
     *
     * @param string $key Configuration key in dot notation
     * @param mixed $default Default value if key not found
     * @return mixed
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        self::init();

        $parts = explode('.', $key, 2);

        if (count($parts) !== 2) {
            throw new InvalidArgumentException(
                "Invalid configuration key format: {$key}. Use section.key format."
            );
        }

        [$section, $subkey] = $parts;

        return match ($section) {
            'database' => match ($subkey) {
                'host' => self::$databaseConfig->getHost(),
                'port' => self::$databaseConfig->getPort(),
                'database' => self::$databaseConfig->getDatabase(),
                'username' => self::$databaseConfig->getUsername(),
                'password' => self::$databaseConfig->getPassword(),
                'charset' => self::$databaseConfig->getCharset(),
                'collation' => self::$databaseConfig->getCollation(),
                'driver' => self::$databaseConfig->getDriver(),
                default => $default,
            },
            'app' => match ($subkey) {
                'name' => self::$appConfig->getName(),
                'env' => self::$appConfig->getEnv(),
                'debug' => self::$appConfig->isDebug(),
                default => $default,
            },
            'logging' => match ($subkey) {
                'level' => self::$logConfig->getLevel(),
                'path' => self::$logConfig->getPath(),
                'channel' => self::$logConfig->getChannel(),
                default => $default,
            },
            default => $default,
        };
    }

    /**
     * Get all configuration for a section (for backward compatibility)
     *
     * @param string $section Section name
     * @return array<string, mixed>
     */
    public static function getSection(string $section): array
    {
        self::init();

        return match ($section) {
            'database' => self::$databaseConfig->toArray(),
            'app' => [
                'name' => self::$appConfig->getName(),
                'env' => self::$appConfig->getEnv(),
                'debug' => self::$appConfig->isDebug(),
            ],
            'logging' => [
                'level' => self::$logConfig->getLevel(),
                'path' => self::$logConfig->getPath(),
                'channel' => self::$logConfig->getChannel(),
            ],
            default => [],
        };
    }

    /**
     * Check if a configuration key exists (for backward compatibility)
     *
     * @param string $key Configuration key in dot notation
     * @return bool
     */
    public static function has(string $key): bool
    {
        try {
            $value = self::get($key);
            return $value !== null;
        } catch (InvalidArgumentException) {
            return false;
        }
    }

    /**
     * Get all configuration (for backward compatibility)
     *
     * @return array<string, array<string, mixed>>
     */
    public static function all(): array
    {
        self::init();

        return [
            'database' => self::getSection('database'),
            'app' => self::getSection('app'),
            'logging' => self::getSection('logging'),
        ];
    }
}
