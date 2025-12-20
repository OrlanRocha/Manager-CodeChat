<?php

declare(strict_types=1);

namespace App\Core;

abstract class Controller
{
    protected array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    protected function view(string $view, array $data = []): void
    {
        extract($data, EXTR_SKIP);

        $viewPath = $this->config['app']['base_path'] . '/app/Views/' . $view . '.php';
        $layoutPath = $this->config['app']['base_path'] . '/app/Views/layouts/main.php';

        if (!file_exists($viewPath)) {
            http_response_code(500);
            echo 'View não encontrada.';
            return;
        }

        require $layoutPath;
    }

    protected function json(array $payload, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($payload, JSON_UNESCAPED_UNICODE);
    }

    protected function requireAuth(): void
    {
        if (!isset($_SESSION['user_id'])) {
            if ($this->isApiRequest()) {
                $this->json([
                    'success' => false,
                    'message' => 'Sessão expirada. Faça login novamente.',
                ], 401);
                exit;
            }

            header('Location: /login');
            exit;
        }
    }

    protected function isApiRequest(): bool
    {
        $path = '/' . trim(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/', '/');
        $accept = $_SERVER['HTTP_ACCEPT'] ?? '';

        return str_starts_with($path, '/instances')
            || str_starts_with($path, '/users')
            || str_starts_with($path, '/myprofile')
            || str_contains($accept, 'application/json');
    }

    protected function isAdmin(): bool
    {
        return ($_SESSION['user_role'] ?? 'user') === 'admin';
    }
}
