<?php


declare(strict_types=1);

use App\Authentication\JWTAuthenticator;
use Illuminate\Database\Capsule\Manager;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Log\LoggerInterface;
use Slim\Psr7\Factory\ResponseFactory;

return [
    // Define Monolog logger as a service
    LoggerInterface::class => function () {
        $logger = new Logger('app');

        $fileHandler = new StreamHandler(__DIR__ . '/../logs/app.log', Level::Debug);
        $fileHandler->setFormatter(
            new LineFormatter(
                "[%datetime%] %channel%.%level_name%: %message%\n",
                "Y-m-d H:i:s",
                true,
                true
            ),
        );
        $logger->pushHandler($fileHandler);

        return $logger;
    },
    'db' => function () {
        $capsule = new Manager();
        $dbConfig = Config::getDatabase();

        $capsule->addConnection(
            [
                'driver' => $dbConfig['driver'] ?? 'mysql',
                'host' => $dbConfig['host'] ?? '127.0.0.1',
                'port' => $dbConfig['port'] ?? 3306,
                'database' => $dbConfig['database'] ?? 'f1_db',
                'username' => $dbConfig['username'] ?? 'root',
                'password' => $dbConfig['password'] ?? '',
                'charset' => $dbConfig['charset'] ?? 'utf8mb4',
                'collation' => $dbConfig['collation'] ?? 'utf8mb4_general_ci',
            ],
        );
        $capsule->setAsGlobal();
        $capsule->bootEloquent();
        return $capsule;
    },
];
