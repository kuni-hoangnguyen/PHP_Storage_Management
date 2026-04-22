<?php
$user = isset($user) && is_array($user) ? $user : [];
$errors = isset($errors) && is_array($errors) ? $errors : [];
$old = isset($old) && is_array($old) ? $old : [];

$userId = (string) ($old['user_id'] ?? $user['id'] ?? $user['user_id'] ?? $_GET['id'] ?? '');
$isEdit = $userId !== '';

$username = (string) ($old['username'] ?? $user['username'] ?? '');
$role = (string) ($old['role'] ?? $user['role'] ?? 'warehouse_staff');
$isActive = (string) ($old['is_active'] ?? $user['is_active'] ?? '1');
?>

<div class="container py-4">
	<nav aria-label="breadcrumb">
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="/admin/index">Admin Dashboard</a></li>
			<li class="breadcrumb-item"><a href="/admin/users">Người dùng</a></li>
			<li class="breadcrumb-item active" aria-current="page"><?php echo $isEdit ? 'Cập nhật' : 'Thêm mới'; ?></li>
		</ol>
	</nav>

	<div class="card shadow-sm">
		<div class="card-header bg-white d-flex justify-content-between align-items-center">
			<h1 class="h4 mb-0"><?php echo $isEdit ? 'Cập nhật tài khoản' : 'Thêm tài khoản mới'; ?></h1>
			<a class="btn btn-outline-secondary btn-sm" href="/admin/users">Quay lại danh sách</a>
		</div>

		<div class="card-body">
			<?php if ($errors !== []): ?>
			<div class="alert alert-danger" role="alert">
				<div class="fw-semibold mb-1">Vui lòng kiểm tra lại dữ liệu:</div>
				<ul class="mb-0">
					<?php foreach ($errors as $message): ?>
					<li><?php echo htmlspecialchars((string) $message, ENT_QUOTES, 'UTF-8'); ?></li>
					<?php endforeach; ?>
				</ul>
			</div>
			<?php endif; ?>

			<form method="post" action="/admin/user_save" class="row g-3">
				<input type="hidden" name="user_id" value="<?php echo htmlspecialchars($userId, ENT_QUOTES, 'UTF-8'); ?>">

				<div class="col-md-6">
					<label class="form-label" for="username">Tên đăng nhập</label>
					<input class="form-control" id="username" name="username" type="text" required
						value="<?php echo htmlspecialchars($username, ENT_QUOTES, 'UTF-8'); ?>">
				</div>

				<div class="col-md-6">
					<label class="form-label" for="password"><?php echo $isEdit ? 'Mật khẩu mới (Để trống nếu không đổi)' : 'Mật khẩu'; ?></label>
					<input class="form-control" id="password" name="password" type="password" <?php echo $isEdit ? '' : 'required'; ?>>
				</div>

				<div class="col-md-6">
					<label class="form-label" for="role">Vai trò</label>
					<select class="form-select" id="role" name="role" required>
						<option value="warehouse_staff" <?php echo $role === 'warehouse_staff' ? 'selected' : ''; ?>>Nhân viên kho</option>
						<option value="qc_staff" <?php echo $role === 'qc_staff' ? 'selected' : ''; ?>>Nhân viên QC</option>
						<option value="manager" <?php echo $role === 'manager' ? 'selected' : ''; ?>>Quản lý</option>
						<option value="admin" <?php echo $role === 'admin' ? 'selected' : ''; ?>>Admin</option>
					</select>
				</div>

				<div class="col-md-6">
					<label class="form-label" for="is_active">Trạng thái tài khoản</label>
					<select class="form-select" id="is_active" name="is_active">
						<option value="1" <?php echo $isActive === '1' ? 'selected' : ''; ?>>Đang hoạt động</option>
						<option value="0" <?php echo $isActive === '0' ? 'selected' : ''; ?>>Vô hiệu hóa</option>
					</select>
				</div>

				<div class="col-12 d-flex gap-2 mt-2">
					<button type="submit" class="btn btn-primary"><?php echo $isEdit ? 'Lưu thay đổi' : 'Tạo tài khoản'; ?></button>
					<a class="btn btn-outline-secondary" href="/admin/users">Hủy</a>
				</div>
			</form>
		</div>
	</div>
</div>
