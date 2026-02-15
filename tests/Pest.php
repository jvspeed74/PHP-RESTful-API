<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "pest()" function to bind a different classes or traits.
|
*/

// pest()->extend(Tests\TestCase::class)->in('Feature');

/*
|--------------------------------------------------------------------------
| Bootstrap & Autoloading
|--------------------------------------------------------------------------
|
| Load the application bootstrap to ensure all classes are available
|
*/
require __DIR__ . '/../vendor/autoload.php';

// Load config classes without initializing (we'll do that in tests)
require __DIR__ . '/../config/DatabaseConfig.php';
require __DIR__ . '/../config/AppConfig.php';
require __DIR__ . '/../config/LogConfig.php';
require __DIR__ . '/../config/Config.php';

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});
*/



/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

/**
 * Store original $_ENV state before tests
 */
$GLOBALS['_ENV_BACKUP'] = $_ENV;

/**
 * Set environment variables for tests
 *
 * @param array<string, mixed> $vars Key-value pairs to set in $_ENV
 */
function setTestEnv(array $vars): void
{
    foreach ($vars as $key => $value) {
        $_ENV[$key] = $value;
    }
}

/**
 * Reset $_ENV to original state before tests
 */
function resetTestEnv(): void
{
    $_ENV = $GLOBALS['_ENV_BACKUP'];
}

/**
 * Get a specific environment variable for testing
 */
function getTestEnv(string $key, mixed $default = null): mixed
{
    return $_ENV[$key] ?? $default;
}
