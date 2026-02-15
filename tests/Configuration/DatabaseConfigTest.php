<?php

declare(strict_types=1);

namespace Tests\Configuration;

use Config\DatabaseConfig;

covers(DatabaseConfig::class);

describe('DatabaseConfig', function () {

    beforeEach(function () {
        // Store original $_ENV
        $GLOBALS['_ENV_BACKUP'] ??= $_ENV;
    });

    afterEach(function () {
        // Restore original $_ENV to prevent test contamination
        $_ENV = $GLOBALS['_ENV_BACKUP'];
    });

    describe('fromEnv()', function () {

        test('uses all default values when environment variables are not set', function () {
            resetTestEnv();

            $config = DatabaseConfig::fromEnv();

            expect($config->getHost())->toBe('127.0.0.1');
            expect($config->getPort())->toBe(3306);
            expect($config->getDatabase())->toBe('f1_db');
            expect($config->getUsername())->toBe('root');
            expect($config->getPassword())->toBe('');
            expect($config->getCharset())->toBe('utf8mb4');
            expect($config->getCollation())->toBe('utf8mb4_general_ci');
            expect($config->getDriver())->toBe('mysql');
        });

        test('uses custom environment variable values when set', function () {
            setTestEnv([
                'DATABASE_HOST' => 'db.example.com',
                'DATABASE_PORT' => '5432',
                'DATABASE_DATABASE' => 'custom_db',
                'DATABASE_USERNAME' => 'admin',
                'DATABASE_PASSWORD' => 'secret123',
                'DATABASE_CHARSET' => 'utf8',
                'DATABASE_COLLATION' => 'utf8_general_ci',
                'DATABASE_DRIVER' => 'pgsql',
            ]);

            $config = DatabaseConfig::fromEnv();

            expect($config->getHost())->toBe('db.example.com');
            expect($config->getPort())->toBe(5432);
            expect($config->getDatabase())->toBe('custom_db');
            expect($config->getUsername())->toBe('admin');
            expect($config->getPassword())->toBe('secret123');
            expect($config->getCharset())->toBe('utf8');
            expect($config->getCollation())->toBe('utf8_general_ci');
            expect($config->getDriver())->toBe('pgsql');
        });

        test('converts port to integer type', function () {
            setTestEnv(['DATABASE_PORT' => '3307']);

            $config = DatabaseConfig::fromEnv();

            expect($config->getPort())->toBe(3307)->and($config->getPort())->toBeInt();
        });

        test('handles string port conversion with leading zeros', function () {
            setTestEnv(['DATABASE_PORT' => '08888']);

            $config = DatabaseConfig::fromEnv();

            expect($config->getPort())->toBe(8888)->and($config->getPort())->toBeInt();
        });

        test('allows empty password by default', function () {
            resetTestEnv();

            $config = DatabaseConfig::fromEnv();

            expect($config->getPassword())->toBe('');
        });
    });

    describe('getters', function () {

        test('getHost() returns database host', function () {
            setTestEnv(['DATABASE_HOST' => 'localhost']);

            $config = DatabaseConfig::fromEnv();

            expect($config->getHost())->toBe('localhost');
        });

        test('getPort() returns port as integer', function () {
            setTestEnv(['DATABASE_PORT' => '3306']);

            $config = DatabaseConfig::fromEnv();

            expect($config->getPort())->toBe(3306)->and($config->getPort())->toBeInt();
        });

        test('getDatabase() returns database name', function () {
            setTestEnv(['DATABASE_DATABASE' => 'test_db']);

            $config = DatabaseConfig::fromEnv();

            expect($config->getDatabase())->toBe('test_db');
        });

        test('getUsername() returns database username', function () {
            setTestEnv(['DATABASE_USERNAME' => 'testuser']);

            $config = DatabaseConfig::fromEnv();

            expect($config->getUsername())->toBe('testuser');
        });

        test('getPassword() returns database password', function () {
            setTestEnv(['DATABASE_PASSWORD' => 'mypassword']);

            $config = DatabaseConfig::fromEnv();

            expect($config->getPassword())->toBe('mypassword');
        });

        test('getCharset() returns character set', function () {
            setTestEnv(['DATABASE_CHARSET' => 'latin1']);

            $config = DatabaseConfig::fromEnv();

            expect($config->getCharset())->toBe('latin1');
        });

        test('getCollation() returns collation', function () {
            setTestEnv(['DATABASE_COLLATION' => 'latin1_general_ci']);

            $config = DatabaseConfig::fromEnv();

            expect($config->getCollation())->toBe('latin1_general_ci');
        });

        test('getDriver() returns database driver', function () {
            setTestEnv(['DATABASE_DRIVER' => 'postgresql']);

            $config = DatabaseConfig::fromEnv();

            expect($config->getDriver())->toBe('postgresql');
        });
    });

    describe('default values', function () {

        test('default charset is utf8mb4', function () {
            resetTestEnv();

            $config = DatabaseConfig::fromEnv();

            expect($config->getCharset())->toBe('utf8mb4');
        });

        test('default collation is utf8mb4_general_ci', function () {
            resetTestEnv();

            $config = DatabaseConfig::fromEnv();

            expect($config->getCollation())->toBe('utf8mb4_general_ci');
        });

        test('default driver is mysql', function () {
            resetTestEnv();

            $config = DatabaseConfig::fromEnv();

            expect($config->getDriver())->toBe('mysql');
        });

        test('default port is 3306', function () {
            resetTestEnv();

            $config = DatabaseConfig::fromEnv();

            expect($config->getPort())->toBe(3306);
        });
    });
});



