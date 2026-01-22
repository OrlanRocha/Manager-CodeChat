<?php
/**
 * Configuração central do CodeChat Manager.
 * Ajuste os valores conforme o seu ambiente.
 */

declare(strict_types=1);

$basePath = dirname(__DIR__);
$envPath = $basePath . '/.env';

$env = [];
if (file_exists($envPath)) {
    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (str_starts_with(trim($line), '#')) {
            continue;
        }
        [$key, $value] = array_pad(explode('=', $line, 2), 2, null);
        if ($key !== null && $value !== null) {
            $env[trim($key)] = trim($value, " \t\n\r\0\x0B\"");
        }
    }
}

return [
    'db' => [
        'host' => $env['DB_HOST'] ?? 'localhost',
        'name' => $env['DB_NAME'] ?? 'codechat_manager',
        'user' => $env['DB_USER'] ?? 'root',
        'pass' => $env['DB_PASS'] ?? '',
        'charset' => $env['DB_CHARSET'] ?? 'utf8mb4',
    ],
    'api' => [
        // URL base da CodeChat API (ajuste a porta conforme seu container).
        'base_url' => $env['API_BASE_URL'] ?? 'http://localhost:8084',
        // Timeout padrão das requisições (em segundos).
        'timeout' => (int) ($env['API_TIMEOUT'] ?? 20),
        // Chave global da CodeChat API.
        'api_key' => $env['API_KEY'] ?? '',
        // Token JWT para autenticação.
        'api_jwt' => $env['API_JWT'] ?? '',
        // Integração alvo (ex.: evolution-api).
        'integration' => $env['API_INTEGRATION'] ?? 'codechat',
    ],
    'app' => [
        'base_path' => $basePath,
    ],
];
