<?php

declare(strict_types=1);

namespace Config;

/**
 * Application Configuration
 *
 * Strongly typed representation of application settings from environment variables.
 */
readonly class AppConfig
{
    public function __construct(
        private string $name,
        private string $env,
        private bool $debug,
    ) {}

    /**
     * Create AppConfig from environment variables
     */
    public static function fromEnv(): self
    {
        $debug = $_ENV['APP_DEBUG'] ?? 'true';

        return new self(
            name: $_ENV['APP_NAME'] ?? 'F1 Management API',
            env: $_ENV['APP_ENV'] ?? 'development',
            debug: filter_var($debug, FILTER_VALIDATE_BOOLEAN),
        );
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEnv(): string
    {
        return $this->env;
    }

    public function isDebug(): bool
    {
        return $this->debug;
    }

    public function isProduction(): bool
    {
        return $this->env === 'production';
    }

    public function isDevelopment(): bool
    {
        return $this->env === 'development';
    }
}
