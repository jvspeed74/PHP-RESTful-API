<?php

declare(strict_types=1);

/**
 * Application Configuration
 *
 * Loads configuration from config.ini and environment variables.
 * Environment variables take precedence over config.ini values.
 */

class Config
{
    private static ?array $config = null;

    /**
     * Initialize configuration from ini file and environment variables
     */
    public static function init(): void
    {
        if (self::$config !== null) {
            return;
        }

        $configFile = __DIR__ . '/config.ini';

        if (!file_exists($configFile)) {
            throw new RuntimeException(
                "Configuration file not found: {$configFile}\n" .
                "Please copy config.ini.example to config.ini and update with your settings."
            );
        }

        // Load configuration from INI file
        $iniConfig = @parse_ini_file($configFile, true, INI_SCANNER_RAW);

        if ($iniConfig === false) {
            throw new RuntimeException("Failed to parse configuration file: {$configFile}");
        }

        self::$config = $iniConfig;

        // Override with environment variables (takes precedence)
        self::overrideWithEnvironment();
    }

    /**
     * Override configuration values with environment variables
     * Supports nested keys using dot notation: "database.host" → $_ENV['DB_HOST']
     */
    private static function overrideWithEnvironment(): void
    {
        if (!is_array(self::$config)) {
            return;
        }

        foreach (self::$config as $section => $values) {
            if (!is_array($values)) {
                continue;
            }

            foreach (array_keys($values) as $key) {
                $envKey = self::getEnvironmentKeyName($section, $key);

                if (isset($_ENV[$envKey])) {
                    self::$config[$section][$key] = trim($_ENV[$envKey]);
                }
            }
        }
    }

    /**
     * Generate environment variable name from section and key
     * database.host -> DB_HOST
     * app.name -> APP_NAME
     */
    private static function getEnvironmentKeyName(string $section, string $key): string
    {
        return strtoupper("{$section}_{$key}");
    }

    /**
     * Get a configuration value
     *
     * @param string $key Configuration key in dot notation (e.g., "database.host")
     * @param mixed $default Default value if key not found
     * @return mixed
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        self::init();

        $parts = explode('.', $key);

        if (count($parts) !== 2) {
            throw new InvalidArgumentException(
                "Invalid configuration key format: {$key}. Use section.key format."
            );
        }

        [$section, $subkey] = $parts;

        return self::$config[$section][$subkey] ?? $default;
    }

    /**
     * Get all configuration for a section
     *
     * @param string $section Section name
     * @return array
     */
    public static function getSection(string $section): array
    {
        self::init();

        return self::$config[$section] ?? [];
    }

    /**
     * Get database configuration
     *
     * @return array
     */
    public static function getDatabase(): array
    {
        return self::getSection('database');
    }

    /**
     * Check if a configuration key exists
     *
     * @param string $key Configuration key in dot notation
     * @return bool
     */
    public static function has(string $key): bool
    {
        self::init();

        $parts = explode('.', $key);

        if (count($parts) !== 2) {
            return false;
        }

        [$section, $subkey] = $parts;

        return isset(self::$config[$section][$subkey]);
    }

    /**
     * Get all configuration
     *
     * @return array
     */
    public static function all(): array
    {
        self::init();

        return self::$config ?? [];
    }
}
