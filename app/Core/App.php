<?php

declare (strict_types = 1);

namespace App\Core;

final class App
{
    /** @var array<string, array<string, array<int, string>>> */
    private array $routes;

    public function __construct()
    {
        $this->routes = require BASE_PATH . '/routes/web.php';
    }

    public function run(): void
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $uri    = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';

        $normalizedUri = $this->normalizeUri($uri);
        require BASE_PATH . '/middleware/auth_check.php';
        enforceAccess($normalizedUri);
        $action = $this->routes[$method][$normalizedUri] ?? null;

        if ($action === null || count($action) !== 2) {
            $this->sendNotFound();
            return;
        }

        [$controllerName, $controllerMethod] = $action;
        $className                           = 'App\\Controllers\\' . $controllerName;

        if (! class_exists($className)) {
            $this->sendNotFound();
            return;
        }

        $controller = new $className();

        if (! method_exists($controller, $controllerMethod)) {
            $this->sendNotFound();
            return;
        }

        $controller->{$controllerMethod}();
    }

    private function normalizeUri(string $uri): string
    {
        $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/'));
        if ($scriptDir !== '/' && $scriptDir !== '.' && strpos($uri, $scriptDir) === 0) {
            $uri = substr($uri, strlen($scriptDir)) ?: '/';
        }

        $trimmed = trim($uri, '/');

        return $trimmed === '' ? '/' : '/' . $trimmed;
    }

    private function sendNotFound(): void
    {
        http_response_code(404);
        echo '404 - Page not found';
    }
}
