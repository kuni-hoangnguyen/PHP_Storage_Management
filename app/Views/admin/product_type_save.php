<?php
$productType = isset($productType) && is_array($productType) ? $productType : [];
$errors = isset($errors) && is_array($errors) ? $errors : [];
$old = isset($old) && is_array($old) ? $old : [];

$productTypeId = (string) ($old['product_type_id'] ?? $productType['product_type_id'] ?? $_GET['id'] ?? '');
$isEdit = $productTypeId !== '';
$productCode = (string) ($old['product_code'] ?? $productType['product_code'] ?? '');
$productName = (string) ($old['product_name'] ?? $productType['product_name'] ?? '');
$isActive = (string) ($old['is_active'] ?? $productType['is_active'] ?? '1');
?>

<div class="container py-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/admin/index">Admin Dashboard</a></li>
            <li class="breadcrumb-item"><a href="/admin/crud">Danh mục chung</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?php echo $isEdit ? 'Cập nhật loại sản phẩm' : 'Thêm loại sabr phẩm'; ?></li>
        </ol>
    </nav>

    <div class="card shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h1 class="h4 mb-0"><?php echo $isEdit ? 'Cap nhat loai san pham' : 'Them loai san pham'; ?></h1>
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

            <form method="post" action="/admin/product_type_save" class="row g-3">
                <input type="hidden" name="product_type_id" value="<?php echo htmlspecialchars($productTypeId, ENT_QUOTES, 'UTF-8'); ?>">

                <div class="col-md-6">
                    <label class="form-label" for="product_code">Mã loại sản phẩm</label>
                    <input class="form-control" id="product_code" name="product_code" type="text" required value="<?php echo htmlspecialchars($productCode, ENT_QUOTES, 'UTF-8'); ?>">
                </div>

                <div class="col-md-6">
                    <label class="form-label" for="product_name">Tên loại sản phẩm</label>
                    <input class="form-control" id="product_name" name="product_name" type="text" required value="<?php echo htmlspecialchars($productName, ENT_QUOTES, 'UTF-8'); ?>">
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
