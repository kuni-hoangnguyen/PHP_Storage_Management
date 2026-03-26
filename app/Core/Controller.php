<?php

declare (strict_types = 1);

namespace App\Core;

abstract class Controller
{
    /**
     * @param array<string, mixed> $data
     */
    protected function view(string $view, array $data = [], string $layout = 'main'): void
    {
        $viewFile   = APP_PATH . '/Views/' . $view . '.php';
        $layoutFile = APP_PATH . '/Views/layouts/' . $layout . '.php';

        if (! is_file($viewFile)) {
            throw new \RuntimeException('View not found: ' . $viewFile);
        }

        $shared = [
            'authUser'   => isset($_SESSION['user_id']) ?
            [
                'id'       => (int) $_SESSION['user_id'],
                'username' => is_string($_SESSION['username'] ?? null) ? $_SESSION['username'] : null,
                'role'     => is_string($_SESSION['role'] ?? null) ? $_SESSION['role'] : null,
            ] : null,
            'isLoggedIn' => isset($_SESSION['user_id']),
        ];

        extract(array_merge($shared, $data), EXTR_SKIP);

        ob_start();
        require $viewFile;
        $content = ob_get_clean();

        if (is_file($layoutFile)) {
            require $layoutFile;
            return;
        }

        echo $content;
    }

}
