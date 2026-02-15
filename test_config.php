<?php

declare(strict_types=1);

require __DIR__ . '/config/bootstrap.php';

echo "=== Configuration Test ===\n\n";

echo "1. Testing Database Configuration:\n";
$dbConfig = Config::database();
echo "   Host: " . $dbConfig->getHost() . "\n";
echo "   Port: " . $dbConfig->getPort() . "\n";
echo "   Database: " . $dbConfig->getDatabase() . "\n";
echo "   Username: " . $dbConfig->getUsername() . "\n";
echo "   Driver: " . $dbConfig->getDriver() . "\n";
echo "   Charset: " . $dbConfig->getCharset() . "\n\n";

echo "2. Testing Application Configuration:\n";
$appConfig = Config::app();
echo "   Name: " . $appConfig->getName() . "\n";
echo "   Environment: " . $appConfig->getEnv() . "\n";
echo "   Debug: " . ($appConfig->isDebug() ? 'true' : 'false') . "\n";
echo "   Is Production: " . ($appConfig->isProduction() ? 'true' : 'false') . "\n";
echo "   Is Development: " . ($appConfig->isDevelopment() ? 'true' : 'false') . "\n\n";

echo "3. Testing Log Configuration:\n";
$logConfig = Config::log();
echo "   Level: " . $logConfig->getLevel() . "\n";
echo "   Path: " . $logConfig->getPath() . "\n";
echo "   Channel: " . $logConfig->getChannel() . "\n\n";


echo "✓ All tests passed!\n";

