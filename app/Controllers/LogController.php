<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Models\Log;

final class LogController extends Controller
{
    public function index(): void
    {
        $this->requireAuth();

        $db = Database::getInstance($this->config);
        $logModel = new Log($db);
        $logs = $logModel->latest(200);

        $this->view('logs/index', [
            'pageTitle' => 'Logs',
            'logs' => $logs,
            'userName' => $_SESSION['user_name'] ?? 'Usuário',
        ]);
    }
}
