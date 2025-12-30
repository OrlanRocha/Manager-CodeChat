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

    public function record(string $level, string $context, string $message, array $payload = [], ?int $userId = null, ?string $ipAddress = null): void
    {
        try {
            $stmt = $this->db->connection()->prepare(
                'INSERT INTO logs (level, context, message, payload, user_id, ip_address) VALUES (:level, :context, :message, :payload, :user_id, :ip_address)'
            );
            $stmt->execute([
                'level' => $level,
                'context' => $context,
                'message' => $message,
                'payload' => $payload ? json_encode($payload, JSON_UNESCAPED_UNICODE) : null,
                'user_id' => $userId,
                'ip_address' => $ipAddress,
            ]);
        } catch (Throwable $exception) {
            // Evita quebrar o fluxo da aplicação em caso de falha no log.
        }
    }

    public function latest(int $limit = 100): array
    {
        $stmt = $this->db->connection()->prepare(
            'SELECT logs.*, users.name AS user_name FROM logs LEFT JOIN users ON users.id = logs.user_id ORDER BY logs.created_at DESC LIMIT :limit'
        );
        $stmt->bindValue('limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}
