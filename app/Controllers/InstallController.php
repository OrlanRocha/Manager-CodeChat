<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use PDO;
use PDOException;

final class InstallController extends Controller
{
    public function show(): void
    {
        $this->view('install/index', [
            'pageTitle' => 'Instalação',
            'isAuthPage' => true,
        ]);
    }

    public function connect(): void
    {
        $data = $this->sanitizeInstallData($_POST);

        if ($data['host'] === '' || $data['name'] === '' || $data['user'] === '') {
            $this->json(['success' => false, 'message' => 'Preencha host, banco e usuário.'], 422);
            return;
        }

        try {
            new PDO(
                sprintf('mysql:host=%s;charset=%s', $data['host'], $data['charset']),
                $data['user'],
                $data['pass'],
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
        } catch (PDOException $exception) {
            $this->json(['success' => false, 'message' => 'Não foi possível conectar ao MySQL.'], 422);
            return;
        }

        $_SESSION['install'] = $data;

        $this->json(['success' => true, 'message' => 'Conexão validada com sucesso.']);
    }

    public function createDatabase(): void
    {
        $data = $this->getInstallData();
        if (!$data) {
            $this->json(['success' => false, 'message' => 'Execute o passo de conexão primeiro.'], 422);
            return;
        }

        try {
            $pdo = new PDO(
                sprintf('mysql:host=%s;charset=%s', $data['host'], $data['charset']),
                $data['user'],
                $data['pass'],
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
            $pdo->exec(sprintf(
                'CREATE DATABASE IF NOT EXISTS `%s` CHARACTER SET %s COLLATE %s_unicode_ci',
                $data['name'],
                $data['charset'],
                $data['charset']
            ));
        } catch (PDOException $exception) {
            $this->json(['success' => false, 'message' => 'Falha ao criar o banco de dados.'], 422);
            return;
        }

        $this->json(['success' => true, 'message' => 'Banco criado com sucesso.']);
    }

    public function writeEnv(): void
    {
        $data = $this->getInstallData();
        if (!$data) {
            $this->json(['success' => false, 'message' => 'Execute o passo de conexão primeiro.'], 422);
            return;
        }

        $envContent = sprintf(
            "DB_HOST=%s\nDB_NAME=%s\nDB_USER=%s\nDB_PASS=%s\nDB_CHARSET=%s\nAPI_BASE_URL=%s\nAPI_TIMEOUT=%d\n",
            $data['host'],
            $data['name'],
            $data['user'],
            $data['pass'],
            $data['charset'],
            $data['api_url'],
            $data['timeout']
        );

        $envPath = $this->config['app']['base_path'] . '/.env';
        if (file_put_contents($envPath, $envContent) === false) {
            $this->json(['success' => false, 'message' => 'Não foi possível criar o .env.'], 500);
            return;
        }

        $this->json(['success' => true, 'message' => 'Arquivo .env criado.']);
    }

    public function createTables(): void
    {
        $data = $this->getInstallData();
        if (!$data) {
            $this->json(['success' => false, 'message' => 'Execute o passo de conexão primeiro.'], 422);
            return;
        }

        try {
            $pdo = new PDO(
                sprintf('mysql:host=%s;dbname=%s;charset=%s', $data['host'], $data['name'], $data['charset']),
                $data['user'],
                $data['pass'],
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
            $this->runSqlFile($pdo, $this->config['app']['base_path'] . '/database.sql');
        } catch (PDOException $exception) {
            $this->json(['success' => false, 'message' => 'Erro ao criar as tabelas.'], 500);
            return;
        }

        $this->json(['success' => true, 'message' => 'Tabelas criadas.']);
    }

    public function seed(): void
    {
        $data = $this->getInstallData();
        if (!$data) {
            $this->json(['success' => false, 'message' => 'Execute o passo de conexão primeiro.'], 422);
            return;
        }

        $seedPath = $this->config['app']['base_path'] . '/seeds.sql';
        if (!file_exists($seedPath)) {
            $this->json(['success' => false, 'message' => 'Seeds não encontrados.'], 404);
            return;
        }

        try {
            $pdo = new PDO(
                sprintf('mysql:host=%s;dbname=%s;charset=%s', $data['host'], $data['name'], $data['charset']),
                $data['user'],
                $data['pass'],
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
            $this->runSqlFile($pdo, $seedPath);
        } catch (PDOException $exception) {
            $this->json(['success' => false, 'message' => 'Erro ao importar seeds.'], 500);
            return;
        }

        $this->json(['success' => true, 'message' => 'Seeds importados.']);
    }

    private function sanitizeInstallData(array $input): array
    {
        return [
            'host' => trim($input['db_host'] ?? 'localhost'),
            'name' => trim($input['db_name'] ?? 'codechat_manager'),
            'user' => trim($input['db_user'] ?? ''),
            'pass' => trim($input['db_pass'] ?? ''),
            'charset' => trim($input['db_charset'] ?? 'utf8mb4'),
            'api_url' => trim($input['api_url'] ?? 'http://localhost:8084'),
            'timeout' => (int) ($input['api_timeout'] ?? 20),
        ];
    }

    private function getInstallData(): ?array
    {
        return $_SESSION['install'] ?? null;
    }

    private function runSqlFile(PDO $pdo, string $path): void
    {
        $sql = file_get_contents($path);
        if ($sql === false) {
            throw new PDOException('Arquivo SQL não encontrado.');
        }

        $statements = array_filter(array_map('trim', explode(';', $sql)));
        foreach ($statements as $statement) {
            $pdo->exec($statement);
        }
    }
}
