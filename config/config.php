<?php
/**
 * Configuração central do CodeChat Manager.
 * Ajuste os valores conforme o seu ambiente.
 */

declare(strict_types=1);

return [
    'db' => [
        'host' => 'localhost',
        'name' => 'codechat_manager',
        'user' => 'root',
        'pass' => '',
        'charset' => 'utf8mb4',
    ],
    'api' => [
        // URL base da CodeChat API (ajuste a porta conforme seu container).
        'base_url' => 'http://localhost:8084',
        // Timeout padrão das requisições (em segundos).
        'timeout' => 20,
    ],
    'app' => [
        'base_path' => dirname(__DIR__),
    ],
];
