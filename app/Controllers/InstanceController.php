<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Models\Instance;
use App\Core\Logger;

final class InstanceController extends Controller
{
    private ?Logger $logger = null;

    public function dashboard(): void
    {
        $this->requireAuth();

        $db = Database::getInstance($this->config);
        $instanceModel = new Instance($db);
        $instances = $instanceModel->allByUser((int) $_SESSION['user_id'], $this->isAdmin());

        $connectedCount = 0;
        foreach ($instances as $instance) {
            if ($instance['status'] === 'connected') {
                $connectedCount++;
            }
        }

        $this->view('instances/index', [
            'pageTitle' => 'Dashboard',
            'instances' => $instances,
            'connectedCount' => $connectedCount,
            'userName' => $_SESSION['user_name'] ?? 'Usuário',
        ]);
    }

    public function listInstances(): void
    {
        $this->requireAuth();

        $db = Database::getInstance($this->config);
        $instanceModel = new Instance($db);

        $instances = $instanceModel->allByUser((int) $_SESSION['user_id'], $this->isAdmin());
        foreach ($instances as $index => $instance) {
            $apiResponse = $this->callCodeChatApi(
                '/instance/fetchInstances',
                'GET',
                [],
                $instance['api_key'] ?: null
            );
            if ($apiResponse['success']) {
                $statusRaw = $this->extractInstanceStatus($apiResponse['data'], $instance['instance_name']);
                if ($statusRaw) {
                    $status = $this->mapConnectionStatus((string) $statusRaw);
                    $instances[$index]['status'] = $status;
                    $instanceModel->updateStatus((int) $instance['id'], $status);
                }
            }
            unset($instances[$index]['api_key']);
        }

        $this->json([
            'success' => true,
            'data' => $instances,
        ]);
    }

    public function createInstance(): void
    {
        $this->requireAuth();

        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        if ($name === '') {
            $this->json(['success' => false, 'message' => 'Informe o nome da instância.'], 422);
            return;
        }

        $db = Database::getInstance($this->config);
        $instanceModel = new Instance($db);
        $id = $instanceModel->create((int) $_SESSION['user_id'], $name, $description !== '' ? $description : null);

        $payload = [
            'instanceName' => $name,
            'qrcode' => true,
        ];
        $integration = $this->config['api']['integration'] ?? '';
        if ($integration !== '') {
            $payload['integration'] = $integration;
        }

        $apiResponse = $this->callCodeChatApi('/instance/create', 'POST', $payload);

        if (!$apiResponse['success']) {
            $instanceModel->delete($id);
            $this->json([
                'success' => false,
                'message' => $apiResponse['message'],
            ], 502);
            return;
        }

        $token = $apiResponse['data']['hash']
            ?? $apiResponse['data']['apikey']
            ?? $apiResponse['data']['Auth']['token']
            ?? $apiResponse['data']['auth']['token']
            ?? $apiResponse['data']['token']
            ?? null;
        if ($token) {
            $instanceModel->updateToken($id, $token);
        }

        $qrCode = $apiResponse['data']['qrcode']['base64']
            ?? $apiResponse['data']['base64']
            ?? $apiResponse['data']['qr']
            ?? $apiResponse['data']['qrCode']
            ?? $apiResponse['data']['qrcode']
            ?? null;

        $this->json([
            'success' => true,
            'message' => 'Instância criada com sucesso.',
            'data' => [
                'id' => $id,
                'instance_name' => $name,
                'description' => $description !== '' ? $description : null,
                'token' => $token,
                'qr' => $qrCode,
                'status' => 'pending',
            ],
        ]);
    }

    public function deleteInstance(int $id): void
    {
        $this->requireAuth();

        $db = Database::getInstance($this->config);
        $instanceModel = new Instance($db);
        $instance = $instanceModel->findForUser($id, (int) $_SESSION['user_id'], $this->isAdmin());

        if (!$instance) {
            $this->json(['success' => false, 'message' => 'Instância não encontrada.'], 404);
            return;
        }

        $apiResponse = $this->callCodeChatApi(
            sprintf('/instance/delete/%s', urlencode($instance['instance_name'])),
            'DELETE'
        );

        if (!$apiResponse['success']) {
            $this->json([
                'success' => false,
                'message' => $apiResponse['message'],
            ], 502);
            return;
        }

        $instanceModel->delete($id);

        $this->json([
            'success' => true,
            'message' => 'Instância removida com sucesso.',
        ]);
    }

    public function connectInstance(int $id): void
    {
        $this->requireAuth();

        $db = Database::getInstance($this->config);
        $instanceModel = new Instance($db);
        $instance = $instanceModel->findForUser($id, (int) $_SESSION['user_id'], $this->isAdmin());

        if (!$instance) {
            $this->json(['success' => false, 'message' => 'Instância não encontrada.'], 404);
            return;
        }

        $apiResponse = $this->callCodeChatApi(
            sprintf('/instance/connect/%s', urlencode($instance['instance_name'])),
            'GET'
        );

        if (!$apiResponse['success']) {
            $this->json([
                'success' => false,
                'message' => $apiResponse['message'],
            ], 502);
            return;
        }

        $qrCode = $apiResponse['data']['qrcode']['base64']
            ?? $apiResponse['data']['base64']
            ?? $apiResponse['data']['qr']
            ?? $apiResponse['data']['qrCode']
            ?? $apiResponse['data']['qrcode']
            ?? null;

        $this->json([
            'success' => true,
            'data' => [
                'qr' => $qrCode,
            ],
        ]);
    }

    public function statusInstance(int $id): void
    {
        $this->requireAuth();

        $db = Database::getInstance($this->config);
        $instanceModel = new Instance($db);
        $instance = $instanceModel->findForUser($id, (int) $_SESSION['user_id'], $this->isAdmin());

        if (!$instance) {
            $this->json(['success' => false, 'message' => 'Instância não encontrada.'], 404);
            return;
        }

        $apiResponse = $this->callCodeChatApi(
            sprintf('/instance/connectionState/%s', urlencode($instance['instance_name'])),
            'GET'
        );

        if (!$apiResponse['success']) {
            $fallback = $this->callCodeChatApi(
                '/instance/fetchInstances',
                'GET',
                [],
                $instance['api_key'] ?: null
            );
            if ($fallback['success']) {
                $statusRaw = $this->extractInstanceStatus($fallback['data'], $instance['instance_name']);
                $status = $this->mapConnectionStatus((string) ($statusRaw ?? 'disconnected'));
                $instanceModel->updateStatus($id, $status);

                $this->json([
                    'success' => true,
                    'data' => [
                        'status' => $status,
                    ],
                ]);
                return;
            }
        }

        if (!$apiResponse['success']) {
            $this->json([
                'success' => false,
                'message' => $apiResponse['message'],
            ], 502);
            return;
        }

        $statusRaw = $this->extractConnectionStatus($apiResponse['data']);
        $status = $this->mapConnectionStatus($statusRaw);
        $instanceModel->updateStatus($id, $status);

        $this->json([
            'success' => true,
            'data' => [
                'status' => $status,
            ],
        ]);
    }

    public function unreadCount(int $id): void
    {
        $this->requireAuth();

        $db = Database::getInstance($this->config);
        $instanceModel = new Instance($db);
        $instance = $instanceModel->findForUser($id, (int) $_SESSION['user_id'], $this->isAdmin());

        if (!$instance) {
            $this->json(['success' => false, 'message' => 'Instância não encontrada.'], 404);
            return;
        }

        $count = 0;

        $this->json([
            'success' => true,
            'data' => [
                'count' => $count,
            ],
            'message' => 'Contagem indisponível. Configure um endpoint dedicado.',
        ]);
    }

    public function testMessage(int $id): void
    {
        $this->requireAuth();

        $to = trim($_POST['to'] ?? '');
        $message = trim($_POST['message'] ?? '');

        if ($to === '' || $message === '') {
            $this->json(['success' => false, 'message' => 'Informe o número e a mensagem.'], 422);
            return;
        }

        $db = Database::getInstance($this->config);
        $instanceModel = new Instance($db);
        $instance = $instanceModel->findForUser($id, (int) $_SESSION['user_id'], $this->isAdmin());

        if (!$instance) {
            $this->json(['success' => false, 'message' => 'Instância não encontrada.'], 404);
            return;
        }

        $apiResponse = $this->callCodeChatApi(
            sprintf('/message/sendText/%s', urlencode($instance['instance_name'])),
            'POST',
            [
                'number' => $to,
                'text' => $message,
            ]
        );

        if (!$apiResponse['success']) {
            $this->json([
                'success' => false,
                'message' => $apiResponse['message'],
            ], 502);
            return;
        }

        $this->json([
            'success' => true,
            'message' => 'Mensagem enviada com sucesso.',
        ]);
    }

    private function callCodeChatApi(string $path, string $method, array $payload = [], ?string $instanceToken = null): array
    {
        $baseUrl = rtrim($this->config['api']['base_url'], '/');
        $url = $baseUrl . $path;

        $ch = curl_init($url);
        $headers = ['Accept: application/json'];
        $apiKey = $this->config['api']['api_key'] ?? '';
        if ($apiKey === '') {
            return [
                'success' => false,
                'message' => 'Chave global da API não configurada.',
            ];
        }
        $headers[] = 'apikey: ' . ($instanceToken ?: $apiKey);

        if (in_array($method, ['POST', 'PUT', 'PATCH'], true)) {
            $headers[] = 'Content-Type: application/json';
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        }

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_TIMEOUT => (int) $this->config['api']['timeout'],
            CURLOPT_HTTPHEADER => $headers,
        ]);

        $response = curl_exec($ch);
        $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($response === false) {
            $this->logger()->error('codechat_api', 'Falha de conexão com a API.', [
                'path' => $path,
                'method' => $method,
                'payload' => $payload,
                'error' => $curlError,
            ]);
            return [
                'success' => false,
                'message' => 'Não foi possível conectar à CodeChat API: ' . ($curlError ?: 'erro desconhecido.'),
            ];
        }

        $data = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->logger()->error('codechat_api', 'Resposta inválida da API.', [
                'path' => $path,
                'method' => $method,
                'payload' => $payload,
                'response' => $response,
            ]);
            return [
                'success' => false,
                'message' => 'Resposta inválida da CodeChat API (HTTP ' . $status . ').',
            ];
        }

        if ($status >= 400) {
            $this->logger()->error('codechat_api', 'Erro retornado pela API.', [
                'path' => $path,
                'method' => $method,
                'status' => $status,
                'payload' => $payload,
                'response' => $data,
            ]);
            if ($status === 401) {
                return [
                    'success' => false,
                    'message' => 'Não autorizado (HTTP 401). Verifique API_KEY e o JWT da instância.',
                    'data' => $data,
                ];
            }
            return [
                'success' => false,
                'message' => $this->extractApiMessage($data, $status),
                'data' => $data,
            ];
        }

        $this->logger()->info('codechat_api', 'Chamada realizada com sucesso.', [
            'path' => $path,
            'method' => $method,
            'status' => $status,
            'payload' => $payload,
        ]);

        return [
            'success' => true,
            'data' => $data,
        ];
    }

    private function logger(): Logger
    {
        if ($this->logger === null) {
            $this->logger = new Logger(Database::getInstance($this->config), $this->config);
        }

        return $this->logger;
    }

    private function mapConnectionStatus(string $status): string
    {
        $normalized = strtolower($status);
        return match ($normalized) {
            'open', 'online', 'connected' => 'connected',
            'connecting', 'pending' => 'pending',
            'close', 'closed', 'offline', 'disconnected' => 'disconnected',
            default => 'disconnected',
        };
    }

    private function extractInstanceStatus($data, string $instanceName): ?string
    {
        if (isset($data['connectionStatus'])) {
            return (string) $data['connectionStatus'];
        }
        if (isset($data['state'])) {
            return (string) $data['state'];
        }
        if (isset($data['status'])) {
            return (string) $data['status'];
        }
        if (is_array($data)) {
            foreach ($data as $item) {
                if (!is_array($item)) {
                    continue;
                }
                $name = $item['instanceName'] ?? $item['name'] ?? $item['instance_name'] ?? null;
                if ($name === $instanceName) {
                    return (string) ($item['connectionStatus'] ?? $item['state'] ?? $item['status'] ?? null);
                }
            }
        }

        return null;
    }

    private function extractConnectionStatus(array $data): string
    {
        return (string) ($data['state'] ?? $data['status'] ?? $data['connectionStatus'] ?? 'disconnected');
    }

    private function extractApiMessage(array $data, int $status): string
    {
        if (isset($data['message'])) {
            if (is_array($data['message'])) {
                return implode(' | ', $data['message']);
            }
            return (string) $data['message'];
        }

        if (isset($data['response']['message'])) {
            if (is_array($data['response']['message'])) {
                return implode(' | ', $data['response']['message']);
            }
            return (string) $data['response']['message'];
        }

        return 'Erro ao processar requisição na CodeChat API (HTTP ' . $status . ').';
    }
}
