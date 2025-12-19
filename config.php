<?php
/**
 * Configuração central do CodeChat Manager.
 * Ajuste os valores conforme o seu ambiente.
 */

declare(strict_types=1);

/**
 * Retorna uma instância PDO reutilizável.
 */
function db(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $dbHost = 'localhost';
    $dbName = 'codechat_manager';
    $dbUser = 'root';
    $dbPass = '';
    $dbCharset = 'utf8mb4';

    $dsn = "mysql:host={$dbHost};dbname={$dbName};charset={$dbCharset}";

    $pdo = new PDO($dsn, $dbUser, $dbPass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    return $pdo;
}

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
];
