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
use App\Controllers\InstallController;
use App\Controllers\LogController;
use App\Core\Database;

$router = new Router($config);

$currentPath = '/' . trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/', '/');
$isInstallRoute = str_starts_with($currentPath, '/install');
$envReady = file_exists($basePath . '/.env');
if (!$envReady) {
    $config['app']['is_installed'] = false;
    $config['db'] = [
        'host' => 'localhost',
        'name' => '',
        'user' => '',
        'pass' => '',
        'charset' => 'utf8mb4',
    ];
}

if (!$isInstallRoute && $envReady) {
    try {
        $connection = Database::getInstance($config)->connection();
        $requiredTables = ['users', 'instances', 'logs'];
        foreach ($requiredTables as $table) {
            $tableCheck = $connection->query("SHOW TABLES LIKE '{$table}'")->fetch();
            if (!$tableCheck) {
                header('Location: /install');
                exit;
            }
        }
    } catch (Throwable $exception) {
        header('Location: /install');
        exit;
    }
}

if (!$isInstallRoute && !$envReady) {
    header('Location: /install');
    exit;
}

$router->get('/', function () {
    header('Location: /dashboard');
    exit;
});

$router->get('/install', [InstallController::class, 'show']);
$router->post('/install/connect', [InstallController::class, 'connect']);
$router->post('/install/create-db', [InstallController::class, 'createDatabase']);
$router->post('/install/write-env', [InstallController::class, 'writeEnv']);
$router->post('/install/create-tables', [InstallController::class, 'createTables']);
$router->post('/install/seed', [InstallController::class, 'seed']);

$router->get('/login', [AuthController::class, 'showLogin']);
$router->post('/login', [AuthController::class, 'login']);
$router->post('/logout', [AuthController::class, 'logout']);

$router->get('/dashboard', [InstanceController::class, 'dashboard']);
$router->get('/logs', [LogController::class, 'index']);

$router->get('/api/instances', [InstanceController::class, 'listInstances']);
$router->post('/api/instances', [InstanceController::class, 'createInstance']);
$router->post('/api/instances/{id}/delete', [InstanceController::class, 'deleteInstance']);
$router->get('/api/instances/{id}/connect', [InstanceController::class, 'connectInstance']);
$router->get('/api/instances/{id}/status', [InstanceController::class, 'statusInstance']);
$router->get('/api/instances/{id}/unread', [InstanceController::class, 'unreadCount']);
$router->post('/api/instances/{id}/test-message', [InstanceController::class, 'testMessage']);

$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
