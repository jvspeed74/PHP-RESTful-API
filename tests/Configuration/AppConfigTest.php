<?php

declare(strict_types=1);

namespace Tests\Configuration;

use Config\AppConfig;

covers(AppConfig::class);

describe('AppConfig', function () {

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

            $config = AppConfig::fromEnv();

            expect($config->getName())->toBe('F1 Management API');
            expect($config->getEnv())->toBe('development');
            expect($config->isDebug())->toBeTrue();
        });

        test('uses custom environment variable values when set', function () {
            setTestEnv([
                'APP_NAME' => 'Custom API',
                'APP_ENV' => 'production',
                'APP_DEBUG' => 'false',
            ]);

            $config = AppConfig::fromEnv();

            expect($config->getName())->toBe('Custom API');
            expect($config->getEnv())->toBe('production');
            expect($config->isDebug())->toBeFalse();
        });

        test('converts debug string values to boolean correctly', function () {
            setTestEnv(['APP_DEBUG' => '0']);
            $config = AppConfig::fromEnv();
            expect($config->isDebug())->toBeFalse();

            setTestEnv(['APP_DEBUG' => '1']);
            $config = AppConfig::fromEnv();
            expect($config->isDebug())->toBeTrue();

            setTestEnv(['APP_DEBUG' => 'yes']);
            $config = AppConfig::fromEnv();
            expect($config->isDebug())->toBeTrue();

            setTestEnv(['APP_DEBUG' => 'no']);
            $config = AppConfig::fromEnv();
            expect($config->isDebug())->toBeFalse();
        });

        test('defaults APP_DEBUG to true when not set', function () {
            resetTestEnv();

            $config = AppConfig::fromEnv();

            expect($config->isDebug())->toBeTrue();
        });
    });

    describe('getters', function () {

        test('getName() returns application name', function () {
            setTestEnv(['APP_NAME' => 'My App']);

            $config = AppConfig::fromEnv();

            expect($config->getName())->toBe('My App');
        });

        test('getEnv() returns environment name', function () {
            setTestEnv(['APP_ENV' => 'staging']);

            $config = AppConfig::fromEnv();

            expect($config->getEnv())->toBe('staging');
        });

        test('isDebug() returns debug flag as boolean', function () {
            setTestEnv(['APP_DEBUG' => 'true']);
            $config = AppConfig::fromEnv();
            expect($config->isDebug())->toBeTrue();

            setTestEnv(['APP_DEBUG' => 'false']);
            $config = AppConfig::fromEnv();
            expect($config->isDebug())->toBeFalse();
        });
    });

    describe('environment detection', function () {

        test('isProduction() returns true when env is production', function () {
            setTestEnv(['APP_ENV' => 'production']);

            $config = AppConfig::fromEnv();

            expect($config->isProduction())->toBeTrue();
            expect($config->isDevelopment())->toBeFalse();
        });

        test('isProduction() returns false when env is not production', function () {
            setTestEnv(['APP_ENV' => 'development']);

            $config = AppConfig::fromEnv();

            expect($config->isProduction())->toBeFalse();
        });

        test('isDevelopment() returns true when env is development', function () {
            setTestEnv(['APP_ENV' => 'development']);

            $config = AppConfig::fromEnv();

            expect($config->isDevelopment())->toBeTrue();
            expect($config->isProduction())->toBeFalse();
        });

        test('isDevelopment() returns false when env is not development', function () {
            setTestEnv(['APP_ENV' => 'production']);

            $config = AppConfig::fromEnv();

            expect($config->isDevelopment())->toBeFalse();
        });

        test('neither isProduction nor isDevelopment when env is staging', function () {
            setTestEnv(['APP_ENV' => 'staging']);

            $config = AppConfig::fromEnv();

            expect($config->isProduction())->toBeFalse();
            expect($config->isDevelopment())->toBeFalse();
        });
    });
});



