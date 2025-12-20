<?php

declare(strict_types=1);

namespace App\Core;

final class Router
{
    private array $routes = [];
    private array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function get(string $path, callable|array $handler): void
    {
        $this->register('GET', $path, $handler);
    }

    public function post(string $path, callable|array $handler): void
    {
        $this->register('POST', $path, $handler);
    }

    public function dispatch(string $method, string $uri): void
    {
        $path = '/' . trim(parse_url($uri, PHP_URL_PATH) ?? '/', '/');

        try {
            foreach ($this->routes[$method] ?? [] as $route) {
                $pattern = $this->compilePattern($route['path']);
                if (preg_match($pattern, $path, $matches)) {
                    $params = array_filter(
                        $matches,
                        static fn ($key) => !is_int($key),
                        ARRAY_FILTER_USE_KEY
                    );

                    $this->invokeHandler($route['handler'], $params);
                    return;
                }
            }

            $this->renderError(404);
        } catch (\Throwable $exception) {
            $this->renderError(500, $exception->getMessage());
        }
    }

    private function register(string $method, string $path, callable|array $handler): void
    {
        $this->routes[$method][] = [
            'path' => $path,
            'handler' => $handler,
        ];
    }

    private function compilePattern(string $path): string
    {
        $pattern = preg_replace('#\{([a-zA-Z_][a-zA-Z0-9_-]*)\}#', '(?P<$1>[^/]+)', $path);
        $pattern = '#^' . rtrim($pattern ?? $path, '/') . '$#';

        return $pattern;
    }

    private function invokeHandler(callable|array $handler, array $params): void
    {
        if (is_array($handler)) {
            [$class, $method] = $handler;
            $controller = new $class($this->config);
            $controller->{$method}(...$this->castParams(array_values($params)));
            return;
        }

        $handler(...$this->castParams(array_values($params)));
    }

    private function castParams(array $params): array
    {
        return array_map(static function ($param) {
            if (is_string($param) && ctype_digit($param)) {
                return (int) $param;
            }
            return $param;
        }, $params);
    }

    private function renderError(int $code, ?string $detail = null): void
    {
        http_response_code($code);
        if ($this->isApiRequest()) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'success' => false,
                'message' => $code === 404 ? 'Rota não encontrada.' : 'Erro interno no servidor.',
                'detail' => $detail,
            ], JSON_UNESCAPED_UNICODE);
            return;
        }
        $path = $this->config['app']['base_path'] . '/app/Views/errors/' . $code . '.php';
        if (file_exists($path)) {
            require $path;
            return;
        }

        echo $code === 404 ? '404 - Página não encontrada.' : 'Erro interno.';
    }

    private function isApiRequest(): bool
    {
        $path = '/' . trim(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/', '/');
        $accept = $_SERVER['HTTP_ACCEPT'] ?? '';

        return str_starts_with($path, '/instances')
            || str_starts_with($path, '/users')
            || str_starts_with($path, '/myprofile')
            || str_contains($accept, 'application/json');
    }
}
