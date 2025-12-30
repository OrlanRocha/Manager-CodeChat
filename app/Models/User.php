<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class User
{
    private Database $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->connection()->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        return $user ?: null;
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->connection()->prepare('SELECT * FROM users WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        $user = $stmt->fetch();

        return $user ?: null;
    }

    public function all(): array
    {
        $stmt = $this->db->connection()->query('SELECT * FROM users ORDER BY created_at DESC');
        return $stmt->fetchAll();
    }

    public function create(string $name, string $email, string $password, string $role = 'user'): int
    {
        $stmt = $this->db->connection()->prepare(
            'INSERT INTO users (name, email, password, role, status) VALUES (:name, :email, :password, :role, :status)'
        );
        $stmt->execute([
            'name' => $name,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'role' => $role,
            'status' => 'active',
        ]);

        return (int) $this->db->connection()->lastInsertId();
    }

    public function updateProfile(int $id, string $name, string $email): void
    {
        $stmt = $this->db->connection()->prepare(
            'UPDATE users SET name = :name, email = :email WHERE id = :id'
        );
        $stmt->execute([
            'name' => $name,
            'email' => $email,
            'id' => $id,
        ]);
    }

    public function updatePassword(int $id, string $password): void
    {
        $stmt = $this->db->connection()->prepare(
            'UPDATE users SET password = :password WHERE id = :id'
        );
        $stmt->execute([
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'id' => $id,
        ]);
    }

    public function updateStatus(int $id, string $status): void
    {
        $stmt = $this->db->connection()->prepare(
            'UPDATE users SET status = :status WHERE id = :id'
        );
        $stmt->execute([
            'status' => $status,
            'id' => $id,
        ]);
    }

    public function updateRole(int $id, string $role): void
    {
        $stmt = $this->db->connection()->prepare(
            'UPDATE users SET role = :role WHERE id = :id'
        );
        $stmt->execute([
            'role' => $role,
            'id' => $id,
        ]);
    }

    public function delete(int $id): void
    {
        $stmt = $this->db->connection()->prepare('DELETE FROM users WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }
}
