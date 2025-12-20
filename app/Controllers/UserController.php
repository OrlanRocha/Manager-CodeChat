<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Models\User;

final class UserController extends Controller
{
    public function index(): void
    {
        $this->requireAuth();
        $this->requireAdmin();

        $db = Database::getInstance($this->config);
        $userModel = new User($db);
        $users = $userModel->all();

        $this->view('users/index', [
            'pageTitle' => 'Usuários',
            'users' => $users,
            'userName' => $_SESSION['user_name'] ?? 'Usuário',
        ]);
    }

    public function list(): void
    {
        $this->requireAuth();
        $this->requireAdmin();

        $db = Database::getInstance($this->config);
        $userModel = new User($db);

        $users = $userModel->all();
        foreach ($users as $index => $user) {
            unset($users[$index]['password']);
        }

        $this->json([
            'success' => true,
            'data' => $users,
        ]);
    }

    public function create(): void
    {
        $this->requireAuth();
        $this->requireAdmin();

        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $role = $_POST['role'] ?? 'user';

        if ($name === '' || $email === '' || $password === '') {
            $this->json(['success' => false, 'message' => 'Preencha nome, e-mail e senha.'], 422);
            return;
        }

        $db = Database::getInstance($this->config);
        $userModel = new User($db);
        $id = $userModel->create($name, $email, $password, $role);

        $this->json([
            'success' => true,
            'message' => 'Usuário criado com sucesso.',
            'data' => ['id' => $id],
        ]);
    }

    public function update(int $id): void
    {
        $this->requireAuth();
        $this->requireAdmin();

        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $role = $_POST['role'] ?? 'user';
        $status = $_POST['status'] ?? 'active';

        if ($name === '' || $email === '') {
            $this->json(['success' => false, 'message' => 'Nome e e-mail são obrigatórios.'], 422);
            return;
        }

        $db = Database::getInstance($this->config);
        $userModel = new User($db);
        $userModel->updateProfile($id, $name, $email);
        $userModel->updateRole($id, $role);
        $userModel->updateStatus($id, $status);

        $this->json([
            'success' => true,
            'message' => 'Usuário atualizado.',
        ]);
    }

    public function updatePassword(int $id): void
    {
        $this->requireAuth();
        $this->requireAdmin();

        $password = $_POST['password'] ?? '';
        if ($password === '') {
            $this->json(['success' => false, 'message' => 'Informe a nova senha.'], 422);
            return;
        }

        $db = Database::getInstance($this->config);
        $userModel = new User($db);
        $userModel->updatePassword($id, $password);

        $this->json([
            'success' => true,
            'message' => 'Senha atualizada.',
        ]);
    }

    public function delete(int $id): void
    {
        $this->requireAuth();
        $this->requireAdmin();

        $db = Database::getInstance($this->config);
        $userModel = new User($db);
        $userModel->delete($id);

        $this->json([
            'success' => true,
            'message' => 'Usuário removido.',
        ]);
    }

    private function requireAdmin(): void
    {
        if (($_SESSION['user_role'] ?? 'user') !== 'admin') {
            if ($this->isApiRequest()) {
                $this->json(['success' => false, 'message' => 'Acesso restrito.'], 403);
                exit;
            }

            header('Location: /dashboard');
            exit;
        }
    }
}
