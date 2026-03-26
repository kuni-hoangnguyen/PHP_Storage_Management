<div class="container py-4">
    <div class="row mb-4">
    </div>

    <?php if ($isLoggedIn): ?>
    <?php
        $role = (string) ($authUser['role'] ?? '');

        $menuByRole = [
            'warehouse_staff' => [
                ['/warehouse/index', 'Quản lý kho'],
            ],
            'qc_staff'        => [
                ['/qc/index', 'Kiểm soát chất lượng'],
            ],
            'manager'         => [
                ['/manager/index', 'Quản lý'],
            ],
            'admin'           => [
                ['/warehouse/index', 'Quản lý kho'],
                ['/qc/index', 'Kiểm soát chất lượng'],
                ['/manager/index', 'Quản lý'],
                ['/admin/index', 'Admin'],
            ],
        ];
    ?>

    <div class="row g-4 mb-5">
        <div class="col-md-3">
            <div class="card h-100 shadow-sm">
                <div class="card-body text-center">
                    <h5 class="card-title text-muted">User ID</h5>
                    <p class="card-text display-6">#<?php echo htmlspecialchars((string) $authUser['id'], ENT_QUOTES, 'UTF-8'); ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100 shadow-sm">
                <div class="card-body text-center">
                    <h5 class="card-title text-muted">Username</h5>
                    <p class="card-text fw-bold fs-4"><?php echo htmlspecialchars((string) $authUser['username'], ENT_QUOTES, 'UTF-8'); ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100 shadow-sm">
                <div class="card-body text-center">
                    <h5 class="card-title text-muted">Role</h5>
                    <p class="card-text"><span class="badge bg-info text-dark"><?php echo htmlspecialchars((string) $authUser['role'], ENT_QUOTES, 'UTF-8'); ?></span></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100 shadow-sm">
                <div class="card-body text-center">
                    <h5 class="card-title text-muted">Session</h5>
                    <p class="card-text text-success fw-bold">Active</p>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex gap-2 justify-content-center">
        <?php foreach ($menuByRole[$role] ?? [] as [$href, $label]): ?>
        <a class="btn btn-primary btn-lg" href="<?php echo htmlspecialchars($href, ENT_QUOTES, 'UTF-8'); ?>">
            <?php echo htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?>
        </a>
        <?php endforeach; ?>
        <a class="btn btn-secondary btn-lg" href="/logout">Logout</a>
    </div>
    <?php else: ?>
    <div class="alert alert-warning text-center" role="alert">
        Bạn chưa đăng nhập. Vui lòng truy cập trang đăng nhập để tiếp tục.
    </div>
    <div class="d-flex justify-content-center">
        <a class="btn btn-primary btn-lg" href="/login">Đăng nhập</a>
    </div>
    <?php endif; ?>
</div>