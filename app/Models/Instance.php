<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class Instance
{
    private Database $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    public function all(): array
    {
        $stmt = $this->db->connection()->query('SELECT * FROM instances ORDER BY created_at DESC');
        return $stmt->fetchAll();
    }

    public function create(string $name, ?string $description = null, ?string $apiKey = null, ?string $webhookUrl = null): int
    {
        $stmt = $this->db->connection()->prepare(
            'INSERT INTO instances (instance_name, description, api_key, webhook_url, status) VALUES (:name, :description, :api_key, :webhook, :status)'
        );
        $stmt->execute([
            'name' => $name,
            'description' => $description,
            'api_key' => $apiKey,
            'webhook' => $webhookUrl,
            'status' => 'pending',
        ]);

        return (int) $this->db->connection()->lastInsertId();
    }

    public function delete(int $id): void
    {
        $stmt = $this->db->connection()->prepare('DELETE FROM instances WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->connection()->prepare('SELECT * FROM instances WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $instance = $stmt->fetch();

        return $instance ?: null;
    }

    public function updateStatus(int $id, string $status): void
    {
        $stmt = $this->db->connection()->prepare('UPDATE instances SET status = :status WHERE id = :id');
        $stmt->execute([
            'status' => $status,
            'id' => $id,
        ]);
    }
}
