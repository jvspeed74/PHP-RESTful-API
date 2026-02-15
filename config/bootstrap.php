<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

// Load config classes
require __DIR__ . '/DatabaseConfig.php';
require __DIR__ . '/AppConfig.php';
require __DIR__ . '/LogConfig.php';
require __DIR__ . '/Config.php';

// Initialize configuration from .env
Config::init();
