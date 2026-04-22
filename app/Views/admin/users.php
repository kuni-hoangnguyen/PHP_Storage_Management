<?php
$users = isset($users) && is_array($users) ? $users : [];
?>

<div class="container py-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/admin/index">Admin Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Người dùng</li>
        </ol>
    </nav>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Quản lý tài khoản</h1>
        <a class="btn btn-primary" href="/admin/user_save">Thêm người dùng</a>
    </div>

    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <form class="row g-2" method="get" action="/admin/users">
                <div class="col-md-4">
                    <input type="text" name="keyword" class="form-control" placeholder="Tìm theo tên đăng nhập"
                        value="<?php echo htmlspecialchars((string) ($_GET['keyword'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
                </div>
                <div class="col-md-3">
                    <select name="role" class="form-select">
                        <?php 
							foreach($rolemap as $value => $label) {
								$selected = $_GET['role'] === $value ? 'selected' : '';
								echo '<option value="' . htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8') . '" ' . $selected . '>' . htmlspecialchars((string) $label, ENT_QUOTES, 'UTF-8') . '</option>';
							}
						?>
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="active" class="form-select">
                        <option value="">Tất cả trạng thái</option>
                        <option value="1" <?php echo isset($_GET['active']) && $_GET['active'] === '1' ? 'selected' : ''; ?>>Đang hoạt động</option>
                        <option value="0" <?php echo isset($_GET['active']) && $_GET['active'] === '0' ? 'selected' : ''; ?>>Đã vô hiệu hóa</option>
                    </select>
                </div>
                <div class="col-md-2 d-grid">
                    <button class="btn btn-outline-primary" type="submit">Lọc</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-striped table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Tên đăng nhập</th>
                        <th>Vai trò</th>
                        <th>Trạng thái</th>
                        <th class="text-end">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($users === []): ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">Chưa có dữ liệu tài khoản.</td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($users as $row): ?>
                    <tr>
                        <td><?php echo htmlspecialchars((string) ($row['id'] ?? $row['user_id'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>
                        </td>
                        <td><?php echo htmlspecialchars((string) ($row['username'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><span
                                class="badge text-bg-secondary"><?php echo htmlspecialchars((string) ($rolemap[$row['role']] ?? ''), ENT_QUOTES, 'UTF-8'); ?></span>
                        </td>
                        <td>
                            <?php $active = (int) ($row['is_active'] ?? 0); ?>
                            <span class="badge <?php echo $active === 1 ? 'text-bg-success' : 'text-bg-danger'; ?>">
                                <?php echo $active === 1 ? 'Đang hoạt động' : 'Đã vô hiệu hóa'; ?>
                            </span>
                        </td>
                        <td class="text-end">
                            <a class="btn btn-sm btn-outline-primary"
                                href="/admin/user_save?id=<?php echo urlencode((string) ($row['id'] ?? '')); ?>">Sửa</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>