<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Models\User;

final class AuthController extends Controller
{
    public function showLogin(): void
    {
        if (isset($_SESSION['user_id'])) {
            header('Location: /dashboard');
            exit;
        }

        $this->view('auth/login', [
            'pageTitle' => 'Login',
            'isAuthPage' => true,
        ]);
    }

    public function login(): void
    {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($email === '' || $password === '') {
            $this->view('auth/login', [
                'pageTitle' => 'Login',
                'error' => 'Informe e-mail e senha.',
                'isAuthPage' => true,
            ]);
            return;
        }

        $db = Database::getInstance($this->config);
        $userModel = new User($db);
        $user = $userModel->findByEmail($email);

        if (!$user || !password_verify($password, $user['password'])) {
            $this->view('auth/login', [
                'pageTitle' => 'Login',
                'error' => 'Credenciais inválidas.',
                'isAuthPage' => true,
            ]);
            return;
        }
        if (($user['status'] ?? 'active') !== 'active') {
            $this->view('auth/login', [
                'pageTitle' => 'Login',
                'error' => 'Usuário bloqueado. Contate o administrador.',
                'isAuthPage' => true,
            ]);
            return;
        }

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_role'] = $user['role'] ?? 'user';

        header('Location: /dashboard');
        exit;
    }

    public function logout(): void
    {
        session_destroy();
        header('Location: /login');
        exit;
    }
}
