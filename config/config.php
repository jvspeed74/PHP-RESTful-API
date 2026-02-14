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
    /**
     * Configuration structure:
     * array<section-name, array<key, value>>
     *
     * @var array<string, array<string, string>>|null
     */
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

        // Ensure structure is the expected nested array<string,array<string,string>>
        $normalized = [];
        foreach ($iniConfig as $section => $values) {
            if (!is_array($values)) {
                continue;
            }
            $normalized[(string) $section] = [];
            foreach ($values as $k => $v) {
                // Only scalar or null values are expected from parse_ini_file; guard for safety
                if (is_scalar($v) || $v === null) {
                    $normalized[(string) $section][(string) $k] = (string) $v;
                } else {
                    // Fallback to empty string for non-scalar values (unexpected)
                    $normalized[(string) $section][(string) $k] = '';
                }
            }
        }

        self::$config = $normalized;

        // Override with environment variables (takes precedence)
        self::overrideWithEnvironment();
    }

    /**
     * Override configuration values with environment variables
     * Supports nested keys using SECTION_KEY env var naming (e.g. DATABASE_HOST)
     *
     * @return void
     */
    private static function overrideWithEnvironment(): void
    {
        if (self::$config === null) {
            return;
        }

        foreach (self::$config as $section => $values) {
            // $values is array<string,string> by construction

            foreach (array_keys($values) as $key) {
                $envKey = self::getEnvironmentKeyName($section, $key);

                if (array_key_exists($envKey, $_ENV)) {
                    $val = $_ENV[$envKey];
                    // Ensure we only convert scalars/null to string
                    if (!is_scalar($val) && $val !== null) {
                        $val = '';
                    }
                    self::$config[$section][$key] = (string) $val;
                }
            }
        }
    }

    /**
     * Generate environment variable name from section and key
     * database.host -> DATABASE_HOST
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

        $parts = explode('.', $key, 2);

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
     * @return array<string, string>
     */
    public static function getSection(string $section): array
    {
        self::init();

        return self::$config[$section] ?? [];
    }

    /**
     * Get database configuration
     *
     * @return array<string, string>
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

        $parts = explode('.', $key, 2);

        if (count($parts) !== 2) {
            return false;
        }

        [$section, $subkey] = $parts;

        return isset(self::$config[$section][$subkey]);
    }

    /**
     * Get all configuration
     *
     * @return array<string, array<string, string>>
     */
    public static function all(): array
    {
        self::init();

        return self::$config ?? [];
    }
}
