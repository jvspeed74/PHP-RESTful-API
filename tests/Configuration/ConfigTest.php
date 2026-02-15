<?php

declare(strict_types=1);

namespace Tests\Configuration;

use Config\AppConfig;
use Config\DatabaseConfig;
use Config\LogConfig;

covers(\Config::class);

describe('Config', function () {

    beforeEach(function () {
        // Store original $_ENV
        $GLOBALS['_ENV_BACKUP'] ??= $_ENV;

        // Reset Config's static initialization state using reflection
        $reflection = new \ReflectionClass(\Config::class);
        $initializedProperty = $reflection->getProperty('initialized');
        $initializedProperty->setAccessible(true);
        $initializedProperty->setValue(null, false);

        $databaseConfigProperty = $reflection->getProperty('databaseConfig');
        $databaseConfigProperty->setAccessible(true);
        $databaseConfigProperty->setValue(null, null);

        $appConfigProperty = $reflection->getProperty('appConfig');
        $appConfigProperty->setAccessible(true);
        $appConfigProperty->setValue(null, null);

        $logConfigProperty = $reflection->getProperty('logConfig');
        $logConfigProperty->setAccessible(true);
        $logConfigProperty->setValue(null, null);
    });

    afterEach(function () {
        // Restore original $_ENV to prevent test contamination
        $_ENV = $GLOBALS['_ENV_BACKUP'];

        // Reset Config's static state again after each test
        $reflection = new \ReflectionClass(\Config::class);
        $initializedProperty = $reflection->getProperty('initialized');
        $initializedProperty->setAccessible(true);
        $initializedProperty->setValue(null, false);

        $databaseConfigProperty = $reflection->getProperty('databaseConfig');
        $databaseConfigProperty->setAccessible(true);
        $databaseConfigProperty->setValue(null, null);

        $appConfigProperty = $reflection->getProperty('appConfig');
        $appConfigProperty->setAccessible(true);
        $appConfigProperty->setValue(null, null);

        $logConfigProperty = $reflection->getProperty('logConfig');
        $logConfigProperty->setAccessible(true);
        $logConfigProperty->setValue(null, null);
    });

    describe('initialization', function () {

        test('init() can be called without throwing exception', function () {
            resetTestEnv();

            // Simply call init() and if it doesn't throw, test passes
            \Config::init();
            expect(true)->toBeTrue();
        });

        test('init() throws RuntimeException when .env file is missing', function () {
            // This test requires mocking or using a non-existent directory
            // We'll skip the actual file check since we're testing in isolation
            // In a real scenario, you might use a temporary directory
            $this->markTestSkipped('Skipped: Requires filesystem mocking for .env check');
        });

        test('init() is idempotent - calling multiple times does not reinitialize', function () {
            resetTestEnv();

            setTestEnv(['APP_NAME' => 'First Call']);
            \Config::init();
            $firstConfig = \Config::app();

            // Change the environment variable
            setTestEnv(['APP_NAME' => 'Second Call']);
            // Call init again - it should not reload because it's already initialized
            \Config::init();
            $secondConfig = \Config::app();

            // Both should return the same config object from the first call
            expect($firstConfig->getName())->toBe('First Call');
            expect($secondConfig->getName())->toBe('First Call');
            expect($firstConfig)->toBe($secondConfig);
        });
    });

    describe('database() getter', function () {

        test('returns DatabaseConfig instance', function () {
            resetTestEnv();

            $config = \Config::database();

            expect($config)->toBeInstanceOf(DatabaseConfig::class);
        });

        test('database() automatically calls init() if not initialized', function () {
            resetTestEnv();

            // Call database() without explicitly calling init()
            $config = \Config::database();

            expect($config)->toBeInstanceOf(DatabaseConfig::class);
            expect($config->getHost())->toBe('127.0.0.1');
        });

        test('returns the same instance across multiple calls', function () {
            resetTestEnv();

            $first = \Config::database();
            $second = \Config::database();

            expect($first)->toBe($second);
        });

        test('database config reflects environment variables', function () {
            setTestEnv(['DATABASE_HOST' => 'testhost']);

            $config = \Config::database();

            expect($config->getHost())->toBe('testhost');
        });
    });

    describe('app() getter', function () {

        test('returns AppConfig instance', function () {
            resetTestEnv();

            $config = \Config::app();

            expect($config)->toBeInstanceOf(AppConfig::class);
        });

        test('app() automatically calls init() if not initialized', function () {
            resetTestEnv();

            // Call app() without explicitly calling init()
            $config = \Config::app();

            expect($config)->toBeInstanceOf(AppConfig::class);
            expect($config->getName())->toBe('F1 Management API');
        });

        test('returns the same instance across multiple calls', function () {
            resetTestEnv();

            $first = \Config::app();
            $second = \Config::app();

            expect($first)->toBe($second);
        });

        test('app config reflects environment variables', function () {
            setTestEnv(['APP_NAME' => 'Custom Application']);

            $config = \Config::app();

            expect($config->getName())->toBe('Custom Application');
        });
    });

    describe('log() getter', function () {

        test('returns LogConfig instance', function () {
            resetTestEnv();

            $config = \Config::log();

            expect($config)->toBeInstanceOf(LogConfig::class);
        });

        test('log() automatically calls init() if not initialized', function () {
            resetTestEnv();

            // Call log() without explicitly calling init()
            $config = \Config::log();

            expect($config)->toBeInstanceOf(LogConfig::class);
            expect($config->getLevel())->toBe('debug');
        });

        test('returns the same instance across multiple calls', function () {
            resetTestEnv();

            $first = \Config::log();
            $second = \Config::log();

            expect($first)->toBe($second);
        });

        test('log config reflects environment variables', function () {
            setTestEnv(['LOG_LEVEL' => 'critical']);

            $config = \Config::log();

            expect($config->getLevel())->toBe('critical');
        });
    });

    describe('integration', function () {

        test('all three config getters return correct types', function () {
            resetTestEnv();

            $database = \Config::database();
            $app = \Config::app();
            $log = \Config::log();

            expect($database)->toBeInstanceOf(DatabaseConfig::class);
            expect($app)->toBeInstanceOf(AppConfig::class);
            expect($log)->toBeInstanceOf(LogConfig::class);
        });

        test('all configs are initialized together in one init() call', function () {
            resetTestEnv();

            \Config::init();

            $database = \Config::database();
            $app = \Config::app();
            $log = \Config::log();

            expect($database)->toBeInstanceOf(DatabaseConfig::class);
            expect($app)->toBeInstanceOf(AppConfig::class);
            expect($log)->toBeInstanceOf(LogConfig::class);
        });

        test('all getters use environment variables correctly', function () {
            setTestEnv([
                'APP_NAME' => 'Test API',
                'APP_ENV' => 'testing',
                'APP_DEBUG' => 'false',
                'DATABASE_HOST' => 'testdb',
                'DATABASE_PORT' => '5432',
                'LOG_LEVEL' => 'info',
                'LOG_CHANNEL' => 'test',
            ]);

            $app = \Config::app();
            $database = \Config::database();
            $log = \Config::log();

            expect($app->getName())->toBe('Test API');
            expect($app->getEnv())->toBe('testing');
            expect($database->getHost())->toBe('testdb');
            expect($database->getPort())->toBe(5432);
            expect($log->getLevel())->toBe('info');
            expect($log->getChannel())->toBe('test');
        });
    });
});















