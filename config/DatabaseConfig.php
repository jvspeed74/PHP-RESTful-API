<?php

declare(strict_types=1);

namespace Config;

/**
 * Database Configuration
 *
 * Strongly typed representation of database settings from environment variables.
 */
class DatabaseConfig
{
    public function __construct(
        private readonly string $host,
        private readonly int $port,
        private readonly string $database,
        private readonly string $username,
        private readonly string $password,
        private readonly string $charset,
        private readonly string $collation,
        private readonly string $driver,
    ) {
    }

    /**
     * Create DatabaseConfig from environment variables
     */
    public static function fromEnv(): self
    {
        return new self(
            host: $_ENV['DATABASE_HOST'] ?? '127.0.0.1',
            port: (int) ($_ENV['DATABASE_PORT'] ?? 3306),
            database: $_ENV['DATABASE_DATABASE'] ?? 'f1_db',
            username: $_ENV['DATABASE_USERNAME'] ?? 'root',
            password: $_ENV['DATABASE_PASSWORD'] ?? '',
            charset: $_ENV['DATABASE_CHARSET'] ?? 'utf8mb4',
            collation: $_ENV['DATABASE_COLLATION'] ?? 'utf8mb4_general_ci',
            driver: $_ENV['DATABASE_DRIVER'] ?? 'mysql',
        );
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function getPort(): int
    {
        return $this->port;
    }

    public function getDatabase(): string
    {
        return $this->database;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getCharset(): string
    {
        return $this->charset;
    }

    public function getCollation(): string
    {
        return $this->collation;
    }

    public function getDriver(): string
    {
        return $this->driver;
    }

    /**
     * Get database configuration as an array (for Eloquent)
     *
     * @return array<string, string|int>
     */
    public function toArray(): array
    {
        return [
            'driver' => $this->driver,
            'host' => $this->host,
            'port' => $this->port,
            'database' => $this->database,
            'username' => $this->username,
            'password' => $this->password,
            'charset' => $this->charset,
            'collation' => $this->collation,
        ];
    }
}

