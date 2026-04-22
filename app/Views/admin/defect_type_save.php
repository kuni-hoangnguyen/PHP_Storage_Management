<?php
$defectType = isset($defectType) && is_array($defectType) ? $defectType : [];
$errors = isset($errors) && is_array($errors) ? $errors : [];
$old = isset($old) && is_array($old) ? $old : [];

$defectTypeId = (string) ($old['defect_type_id'] ?? $defectType['defect_type_id'] ?? $_GET['id'] ?? '');
$isEdit = $defectTypeId !== '';

$name = (string) ($old['name'] ?? $defectType['name'] ?? '');
$description = (string) ($old['description'] ?? $defectType['description'] ?? '');
$isActive = (string) ($old['is_active'] ?? $defectType['is_active'] ?? '1');
?>

<div class="container py-4">
	<nav aria-label="breadcrumb">
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="/admin/index">Admin Dashboard</a></li>
			<li class="breadcrumb-item"><a href="/admin/crud">Danh mục chung</a></li>
			<li class="breadcrumb-item active" aria-current="page"><?php echo $isEdit ? 'Cập nhật loại lỗi' : 'Thêm loại lỗi'; ?></li>
		</ol>
	</nav>

	<div class="card shadow-sm">
		<div class="card-header bg-white d-flex justify-content-between align-items-center">
			<h1 class="h4 mb-0"><?php echo $isEdit ? 'Cập nhật loại lỗi' : 'Thêm loại lỗi mới'; ?></h1>
			<a class="btn btn-outline-secondary btn-sm" href="/admin/defect_types">Quay lại danh sách</a>
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

			<form method="post" action="/admin/defect_type_save" class="row g-3">
				<input type="hidden" name="defect_type_id" value="<?php echo htmlspecialchars($defectTypeId, ENT_QUOTES, 'UTF-8'); ?>">

				<div class="col-12">
					<label class="form-label" for="name">Tên loại lỗi</label>
					<input class="form-control" id="name" name="name" type="text" required
						value="<?php echo htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?>">
				</div>

				<div class="col-12">
					<label class="form-label" for="description">Mô tả</label>
					<textarea class="form-control" id="description" name="description" rows="4"><?php echo htmlspecialchars($description, ENT_QUOTES, 'UTF-8'); ?></textarea>
				</div>

				<div class="col-md-6">
					<label class="form-label" for="is_active">Trạng thái hiển thị </label>
					<select class="form-select" id="is_active" name="is_active">
						<option value="1" <?php echo $isActive === '1' ? 'selected' : ''; ?>>Hiển thị</option>
						<option value="0" <?php echo $isActive === '0' ? 'selected' : ''; ?>>Ẩn</option>
					</select>
				</div>

				<div class="col-12 d-flex gap-2 mt-2">
					<button type="submit" class="btn btn-primary"><?php echo $isEdit ? 'Lưu thay đổi' : 'Tạo loại lỗi'; ?></button>
					<a class="btn btn-outline-secondary" href="/admin/defect_types">Hủy</a>
				</div>
			</form>
		</div>
	</div>
</div>
