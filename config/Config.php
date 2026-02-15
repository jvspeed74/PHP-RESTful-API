<?php

declare(strict_types=1);

namespace Config;

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

        if (file_exists($envFile)) {
            // Load .env file
            $dotenv = Dotenv::createImmutable(__DIR__ . '/..');
            $dotenv->load();
        }

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
}
