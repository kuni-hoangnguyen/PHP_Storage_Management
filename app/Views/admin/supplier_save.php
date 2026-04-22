<?php
$supplier = isset($supplier) && is_array($supplier) ? $supplier : [];
$errors = isset($errors) && is_array($errors) ? $errors : [];
$old = isset($old) && is_array($old) ? $old : [];

$supplierId = (string) ($old['supplier_id'] ?? $supplier['supplier_id'] ?? $_GET['id'] ?? '');
$isEdit = $supplierId !== '';
$supplierCode = (string) ($old['supplier_code'] ?? $supplier['supplier_code'] ?? '');
$supplierName = (string) ($old['supplier_name'] ?? $supplier['supplier_name'] ?? '');
$isActive = (string) ($old['is_active'] ?? $supplier['is_active'] ?? '1');
?>

<div class="container py-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/admin/index">Admin Dashboard</a></li>
            <li class="breadcrumb-item"><a href="/admin/crud">Danh mục chung</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?php echo $isEdit ? 'Cập nhật nhà cung cấp' : 'Thêm nhà cung cấp'; ?></li>
        </ol>
    </nav>

    <div class="card shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h1 class="h4 mb-0"><?php echo $isEdit ? 'Cập nhật nhà cung cấp' : 'Thêm nhà cung cấp'; ?></h1>
            <a class="btn btn-outline-secondary btn-sm" href="/admin/crud">Quay lại</a>
        </div>

        <div class="card-body">
            <?php if ($errors !== []): ?>
            <div class="alert alert-danger" role="alert">
                <ul class="mb-0">
                    <?php foreach ($errors as $message): ?>
                    <li><?php echo htmlspecialchars((string) $message, ENT_QUOTES, 'UTF-8'); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>

            <form method="post" action="/admin/supplier_save" class="row g-3">
                <input type="hidden" name="supplier_id" value="<?php echo htmlspecialchars($supplierId, ENT_QUOTES, 'UTF-8'); ?>">

                <div class="col-md-6">
                    <label class="form-label" for="supplier_code">Mã nhà cung cấp</label>
                    <input class="form-control" id="supplier_code" name="supplier_code" type="text" required value="<?php echo htmlspecialchars($supplierCode, ENT_QUOTES, 'UTF-8'); ?>">
                </div>

                <div class="col-md-6">
                    <label class="form-label" for="supplier_name">Tên nhà cung cấp</label>
                    <input class="form-control" id="supplier_name" name="supplier_name" type="text" required value="<?php echo htmlspecialchars($supplierName, ENT_QUOTES, 'UTF-8'); ?>">
                </div>

                <div class="col-md-6">
                    <label class="form-label" for="is_active">Trạng thái</label>
                    <select class="form-select" id="is_active" name="is_active">
                        <option value="1" <?php echo $isActive === '1' ? 'selected' : ''; ?>>Hiển thị</option>
                        <option value="0" <?php echo $isActive === '0' ? 'selected' : ''; ?>>Ẩn</option>
                    </select>
                </div>

                <div class="col-12 d-flex gap-2 mt-2">
                    <button type="submit" class="btn btn-primary"><?php echo $isEdit ? 'Lưu thay đổi' : 'Tạo mới'; ?></button>
                    <a class="btn btn-outline-secondary" href="/admin/crud">Hủy</a>
                </div>
            </form>
        </div>
    </div>
</div>
