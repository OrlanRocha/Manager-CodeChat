<?php

declare(strict_types=1);

session_start();

$basePath = dirname(__DIR__);

spl_autoload_register(function (string $class) use ($basePath): void {
    $prefix = 'App\\';
    $baseDir = $basePath . '/app/';

    if (str_starts_with($class, $prefix)) {
        $relativeClass = substr($class, strlen($prefix));
        $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
        if (file_exists($file)) {
            require $file;
        }
    }
});

$config = require $basePath . '/config/config.php';

use App\Core\Router;
use App\Controllers\AuthController;
use App\Controllers\InstanceController;

$router = new Router($config);

$router->get('/', function () {
    header('Location: /dashboard');
    exit;
});

$router->get('/login', [AuthController::class, 'showLogin']);
$router->post('/login', [AuthController::class, 'login']);
$router->post('/logout', [AuthController::class, 'logout']);

$router->get('/dashboard', [InstanceController::class, 'dashboard']);

$router->get('/api/instances', [InstanceController::class, 'listInstances']);
$router->post('/api/instances', [InstanceController::class, 'createInstance']);
$router->post('/api/instances/{id}/delete', [InstanceController::class, 'deleteInstance']);
$router->get('/api/instances/{id}/connect', [InstanceController::class, 'connectInstance']);
$router->get('/api/instances/{id}/status', [InstanceController::class, 'statusInstance']);
$router->get('/api/instances/{id}/unread', [InstanceController::class, 'unreadCount']);
$router->post('/api/instances/{id}/test-message', [InstanceController::class, 'testMessage']);

$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
