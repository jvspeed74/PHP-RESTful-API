<?php

/** @noinspection PhpExpressionResultUnusedInspection */

/** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

namespace Tests\Configuration;

use Config\LogConfig;

covers(LogConfig::class);

describe('LogConfig', function () {
    beforeEach(function () {
        // Store original $_ENV
        $GLOBALS['_ENV_BACKUP'] ??= $_ENV;
    });

    afterEach(function () {
        // Restore original $_ENV to prevent test contamination
        $_ENV = $GLOBALS['_ENV_BACKUP'];
    });

    describe('fromEnv()', function () {
        test('uses default values when environment variables are not set', function () {
            resetTestEnv();

            $config = LogConfig::fromEnv();

            expect($config->getLevel())->toBe('debug');
            expect($config->getPath())->toBe('logs/app.log');
            expect($config->getChannel())->toBe('app');
        });

        test('uses custom environment variable values when set', function () {
            setTestEnv([
                'LOG_LEVEL' => 'error',
                'LOG_PATH' => 'storage/logs/custom.log',
                'LOG_CHANNEL' => 'custom_channel',
            ]);

            $config = LogConfig::fromEnv();

            expect($config->getLevel())->toBe('error');
            expect($config->getPath())->toBe('storage/logs/custom.log');
            expect($config->getChannel())->toBe('custom_channel');
        });

        test('allows partial environment variable overrides', function () {
            setTestEnv([
                'LOG_LEVEL' => 'warning',
                // LOG_PATH and LOG_CHANNEL use defaults
            ]);

            $config = LogConfig::fromEnv();

            expect($config->getLevel())->toBe('warning');
            expect($config->getPath())->toBe('logs/app.log');
            expect($config->getChannel())->toBe('app');
        });
    });

    describe('getters', function () {
        test('getLevel() returns log level', function () {
            setTestEnv(['LOG_LEVEL' => 'info']);

            $config = LogConfig::fromEnv();

            expect($config->getLevel())->toBe('info');
        });

        test('getPath() returns log file path', function () {
            setTestEnv(['LOG_PATH' => 'var/logs/application.log']);

            $config = LogConfig::fromEnv();

            expect($config->getPath())->toBe('var/logs/application.log');
        });

        test('getChannel() returns log channel', function () {
            setTestEnv(['LOG_CHANNEL' => 'database']);

            $config = LogConfig::fromEnv();

            expect($config->getChannel())->toBe('database');
        });
    });

    describe('default values', function () {
        test('default level is debug', function () {
            resetTestEnv();

            $config = LogConfig::fromEnv();

            expect($config->getLevel())->toBe('debug');
        });

        test('default path is logs/app.log', function () {
            resetTestEnv();

            $config = LogConfig::fromEnv();

            expect($config->getPath())->toBe('logs/app.log');
        });

        test('default channel is app', function () {
            resetTestEnv();

            $config = LogConfig::fromEnv();

            expect($config->getChannel())->toBe('app');
        });
    });

    describe('environment variable variations', function () {
        test('supports various log levels', function () {
            $levels = ['debug', 'info', 'notice', 'warning', 'error', 'critical', 'alert', 'emergency'];

            foreach ($levels as $level) {
                setTestEnv(['LOG_LEVEL' => $level]);
                $config = LogConfig::fromEnv();
                expect($config->getLevel())->toBe($level);
            }
        });

        test('supports custom log paths with different formats', function () {
            setTestEnv(['LOG_PATH' => '/var/log/app.log']);
            $config = LogConfig::fromEnv();
            expect($config->getPath())->toBe('/var/log/app.log');

            setTestEnv(['LOG_PATH' => './logs/debug.log']);
            $config = LogConfig::fromEnv();
            expect($config->getPath())->toBe('./logs/debug.log');

            setTestEnv(['LOG_PATH' => '/tmp/error.log']);
            $config = LogConfig::fromEnv();
            expect($config->getPath())->toBe('/tmp/error.log');
        });
    });
});
