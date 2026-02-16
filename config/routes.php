<?php

declare(strict_types=1);

use App\Controllers\{AuthController, CarController, DriverController, EventController, TeamController, TrackController};
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

/**
 * Register application routes.
 *
 * @param App $app The Slim application instance.
 *
 * @return void
 */
return function (App $app): void {
    // Greet route
    $app->get('/hello/{name}', function (Response $response, string $name) {
        $response->getBody()->write("Hello, $name");
        return $response;
    });

    // ============================================
    // SPA PAGE ROUTES (Serve HTML)
    // ============================================
    // These routes render PHP pages with embedded JavaScript that call the API endpoints below

    $app->get('/', function (Request $request, Response $response) {
        ob_start();
        require __DIR__ . '/../public/mainPage.php';
        $html = ob_get_clean();
        $response->getBody()->write($html);
        return $response->withHeader('Content-Type', 'text/html');
    });

    $app->get('/teamsPage', function (Request $request, Response $response) {
        ob_start();
        require __DIR__ . '/../public/teamsPage.php';
        $html = ob_get_clean();
        $response->getBody()->write($html);
        return $response->withHeader('Content-Type', 'text/html');
    });

    $app->get('/driversPage', function (Request $request, Response $response) {
        ob_start();
        require __DIR__ . '/../public/driversPage.php';
        $html = ob_get_clean();
        $response->getBody()->write($html);
        return $response->withHeader('Content-Type', 'text/html');
    });

    $app->get('/tracksPage', function (Request $request, Response $response) {
        ob_start();
        require __DIR__ . '/../public/tracksPage.php';
        $html = ob_get_clean();
        $response->getBody()->write($html);
        return $response->withHeader('Content-Type', 'text/html');
    });

    $app->get('/eventsPage', function (Request $request, Response $response) {
        ob_start();
        require __DIR__ . '/../public/eventsPage.php';
        $html = ob_get_clean();
        $response->getBody()->write($html);
        return $response->withHeader('Content-Type', 'text/html');
    });

    $app->get('/carsPage', function (Request $request, Response $response) {
        ob_start();
        require __DIR__ . '/../public/carsPage.php';
        $html = ob_get_clean();
        $response->getBody()->write($html);
        return $response->withHeader('Content-Type', 'text/html');
    });

    // ============================================
    // API ROUTES (Return JSON)
    // ============================================
    // All API endpoints are namespaced under /api to prevent collision with SPA page routes

    // Team API routes
    $app->group('/api/teams', function (RouteCollectorProxy $group) {
        $group->get('', TeamController::class . ':getAll');
        $group->get('/{id:\d+}', TeamController::class . ':getById');
        $group->post('', TeamController::class . ':create');
        $group->patch('/{id:\d+}', TeamController::class . ':update');
        $group->delete('/{id:\d+}', TeamController::class . ':delete');
    });

    // Event API routes
    $app->group('/api/events', function (RouteCollectorProxy $group) {
        $group->get('', EventController::class . ':getAll');
        $group->get('/{id:\d+}', EventController::class . ':getById');
        $group->post('', EventController::class . ':create');
        $group->patch('/{id:\d+}', EventController::class . ':update');
        $group->delete('/{id:\d+}', EventController::class . ':delete');
    });

    // Track API routes
    $app->group('/api/tracks', function (RouteCollectorProxy $group) {
        $group->get('', TrackController::class . ':getAllWithParams');
        $group->get('/{id:\d+}', TrackController::class . ':getById');
        $group->post('', TrackController::class . ':create');
        $group->patch('/{id:\d+}', TrackController::class . ':update');
        $group->delete('/{id:\d+}', TrackController::class . ':delete');
    });

    // Driver API routes
    $app->group('/api/drivers', function (RouteCollectorProxy $group) {
        $group->get('', DriverController::class . ':getAll');
        $group->get('/{id:\d+}', DriverController::class . ':getById');
        $group->post('', DriverController::class . ':create');
        $group->patch('/{id:\d+}', DriverController::class . ':update');
        $group->delete('/{id:\d+}', DriverController::class . ':delete');
        $group->get('/search', DriverController::class . ':search');
    });

    // Car API routes
    $app->group('/api/cars', function (RouteCollectorProxy $group) {
        $group->get('', CarController::class . ':getAll');
        $group->get('/{id:\d+}', CarController::class . ':getById');
        $group->post('', CarController::class . ':create');
        $group->patch('/{id:\d+}', CarController::class . ':update');
        $group->delete('/{id:\d+}', CarController::class . ':delete');
    });

    // Auth API routes
    $app->group('/api/auth', function (RouteCollectorProxy $group) {
        $group->post('/login', AuthController::class . ':login');
        $group->post('/register', AuthController::class . ':register');
        $group->post('/revoke', AuthController::class . ':revoke');
    });
};
