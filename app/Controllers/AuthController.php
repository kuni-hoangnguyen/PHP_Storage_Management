<?php

declare (strict_types = 1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;

final class AuthController extends Controller
{
    public function getLoginForm(): void
    {
        $this->view('auth/login', ['title' => 'Login']);
    }

    public function postLoginForm(): void
    {
        $username = trim((string) ($_POST['username'] ?? ''));
        $password = trim((string) ($_POST['password'] ?? ''));

        $pdo  = Database::getInstance();
        $stmt = $pdo->prepare('SELECT * from users WHERE username = :username LIMIT 1');
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            session_start();
            $_SESSION['user_id']  = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role']     = $user['role'];

            $menuByRole = [
                'warehouse_staff' => [
                    ['/warehouse/index'],
                ],
                'qc_staff'        => [
                    ['/qc/index'],
                ],
                'manager'         => [
                    ['/manager/index'],
                ],
                'admin'           => [
                    // ['/admin/index']
                    ['/'],
                ],
            ];

            header('Location: ' . ($menuByRole[$_SESSION['role'] ?? ''][0][0] ?? '/'));
            exit;
        } else {
            $this->view('auth/login', [
                'title' => 'Login',
                'error' => 'Sai tên đăng nhập hoặc mật khẩu',
            ]);
        }
    }

    public function logout(): void
    {
        session_destroy();
        header('Location: /');
        exit;
    }
}
