<?php

declare(strict_types=1);

namespace Config;

/**
 * Logging Configuration
 *
 * Strongly typed representation of logging settings from environment variables.
 */
class LogConfig
{
    public function __construct(
        private readonly string $level,
        private readonly string $path,
        private readonly string $channel,
    ) {
    }

    /**
     * Create LogConfig from environment variables
     */
    public static function fromEnv(): self
    {
        return new self(
            level: $_ENV['LOG_LEVEL'] ?? 'debug',
            path: $_ENV['LOG_PATH'] ?? 'logs/app.log',
            channel: $_ENV['LOG_CHANNEL'] ?? 'app',
        );
    }

    public function getLevel(): string
    {
        return $this->level;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getChannel(): string
    {
        return $this->channel;
    }
}

