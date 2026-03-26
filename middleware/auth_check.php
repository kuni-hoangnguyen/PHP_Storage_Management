<?php
declare (strict_types = 1);

function redirect(string $path): void
{
    header('Location: ' . $path);
    exit;
}

function isLoggedIn(): bool
{
    return isset($_SESSION['user_id']) && is_int($_SESSION['user_id']);
}

function currentRole(): ?string
{
    $role = $_SESSION['role'] ?? null;
    return is_string($role) ? $role : null;
}

function redirectByRole(string $role): void
{
    $map = [
        'warehouse_staff' => '/warehouse/index',
        'qc_staff'        => '/qc/index',
        'manager'         => '/manager/index',
        // 'admin'           => '/admin/index',
        'admin'           => '/',
    ];

    redirect($map[$role] ?? '/');
}

function requireGuest(): void
{
    if (isLoggedIn()) {
        $role = currentRole();
        if ($role !== null) {
            redirectByRole($role);
        }
        redirect('/');
    }
}

function requireAuth(): void
{
    if (! isLoggedIn()) {
        redirect('/login');
    }
}

function authorize(array $allowedRoles): void
{
    requireAuth();

    $role = currentRole();
    if ($role === null || ! in_array($role, $allowedRoles, true)) {
        http_response_code(403);
        echo 'Bạn không có quyền truy cập trang này.';
        exit;
    }
}

function enforceAccess(string $uri): void
{
    // Public routes
    if ($uri === '/' || $uri === '/login') {
        if ($uri === '/login') {
            requireGuest();
        }
        return;
    }

    // Must login for all other routes
    requireAuth();

    $role = currentRole();
    if ($role === 'admin') {
        return; // admin bypass
    }

    if (str_starts_with($uri, '/warehouse/')) {
        authorize(['warehouse_staff']);
        return;
    }

    if (str_starts_with($uri, '/qc/')) {
        authorize(['qc_staff']);
        return;
    }

    if (str_starts_with($uri, '/manager/')) {
        authorize(['manager']);
        return;
    }

    if (str_starts_with($uri, '/admin/')) {
        authorize(['admin']);
        return;
    }
}
