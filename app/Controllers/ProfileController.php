<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Models\User;

final class ProfileController extends Controller
{
    public function index(): void
    {
        $this->requireAuth();

        $db = Database::getInstance($this->config);
        $userModel = new User($db);
        $user = $userModel->findById((int) $_SESSION['user_id']);

        $this->view('profile/index', [
            'pageTitle' => 'Meu Perfil',
            'user' => $user,
            'userName' => $_SESSION['user_name'] ?? 'Usuário',
        ]);
    }

    public function update(): void
    {
        $this->requireAuth();

        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');

        if ($name === '' || $email === '') {
            $this->json(['success' => false, 'message' => 'Nome e e-mail são obrigatórios.'], 422);
            return;
        }

        $db = Database::getInstance($this->config);
        $userModel = new User($db);
        $userModel->updateProfile((int) $_SESSION['user_id'], $name, $email);

        $_SESSION['user_name'] = $name;

        $this->json(['success' => true, 'message' => 'Perfil atualizado.']);
    }

    public function updatePassword(): void
    {
        $this->requireAuth();

        $password = $_POST['password'] ?? '';
        if ($password === '') {
            $this->json(['success' => false, 'message' => 'Informe a nova senha.'], 422);
            return;
        }

        $db = Database::getInstance($this->config);
        $userModel = new User($db);
        $userModel->updatePassword((int) $_SESSION['user_id'], $password);

        $this->json(['success' => true, 'message' => 'Senha atualizada.']);
    }
}
