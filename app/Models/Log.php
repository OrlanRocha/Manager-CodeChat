<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use Throwable;

final class Log
{
    private Database $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    public function record(string $level, string $context, string $message, array $payload = []): void
    {
        try {
            $stmt = $this->db->connection()->prepare(
                'INSERT INTO logs (level, context, message, payload) VALUES (:level, :context, :message, :payload)'
            );
            $stmt->execute([
                'level' => $level,
                'context' => $context,
                'message' => $message,
                'payload' => $payload ? json_encode($payload, JSON_UNESCAPED_UNICODE) : null,
            ]);
        } catch (Throwable $exception) {
            // Evita quebrar o fluxo da aplicação em caso de falha no log.
        }
    }

    public function latest(int $limit = 100): array
    {
        $stmt = $this->db->connection()->prepare('SELECT * FROM logs ORDER BY created_at DESC LIMIT :limit');
        $stmt->bindValue('limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}
