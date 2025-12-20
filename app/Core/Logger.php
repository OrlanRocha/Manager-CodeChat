<?php

declare(strict_types=1);

namespace App\Core;

use App\Models\Log;
use DateTimeImmutable;

final class Logger
{
    private Database $db;
    private array $config;

    public function __construct(Database $db, array $config)
    {
        $this->db = $db;
        $this->config = $config;
    }

    public function info(string $context, string $message, array $payload = []): void
    {
        $this->write('info', $context, $message, $payload);
    }

    public function error(string $context, string $message, array $payload = []): void
    {
        $this->write('error', $context, $message, $payload);
    }

    private function write(string $level, string $context, string $message, array $payload = []): void
    {
        $logModel = new Log($this->db);
        $logModel->record($level, $context, $message, $payload);

        $timestamp = (new DateTimeImmutable())->format('Y-m-d H:i:s');
        $line = sprintf("[%s] %s.%s: %s %s\n", $timestamp, strtoupper($level), $context, $message, $payload ? json_encode($payload) : '');

        $logDir = $this->config['app']['base_path'] . '/storage/logs';
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }

        file_put_contents($logDir . '/app.log', $line, FILE_APPEND);
    }
}
