<?php
$batch = isset($batch) && is_array($batch) ? $batch : [];
$boxes = isset($boxes) && is_array($boxes) ? $boxes : [];
$message = isset($message) ? (string) $message : '';
?>

<div class="container py-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/admin/index">Admin Dashboard</a></li>
            <li class="breadcrumb-item"><a href="/admin/batches">Lô hàng</a></li>
            <li class="breadcrumb-item active" aria-current="page">Chi tiết lô</li>
        </ol>
    </nav>

    <?php if ($message !== ''): ?>
    <div class="alert alert-success" role="alert">Lưu thành công.</div>
    <?php endif; ?>

    <div class="card shadow-sm mb-3">
        <div class="card-header bg-white">
            <h1 class="h4 mb-0">Lô: <?= htmlspecialchars((string) ($batch['batch_code'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></h1>
        </div>
        <div class="card-body">
            <form method="post" action="/admin/batch_save">
                <input type="hidden" name="batch_code" value="<?= htmlspecialchars((string) ($batch['batch_code'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">

                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label" for="supplier_code">Nhà cung cấp</label>
                        <select class="form-select" id="supplier_code" name="supplier_code" required>
                            <?php foreach ($suppliers as $option): ?>
                            <option value="<?= htmlspecialchars((string) $option['supplier_code'], ENT_QUOTES, 'UTF-8'); ?>" <?= (string) ($batch['supplier_code'] ?? '') === (string) $option['supplier_code'] ? 'selected' : ''; ?>><?= sprintf('%s - %s', $option['supplier_code'], htmlspecialchars((string) $option['supplier_name'], ENT_QUOTES, 'UTF-8')); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label" for="product_type">Loại sản phẩm</label>
                        <select class="form-select" id="product_type" name="product_type" required>
                            <?php foreach ($productTypes as $option): ?>
                            <option value="<?= htmlspecialchars((string) $option['product_code'], ENT_QUOTES, 'UTF-8'); ?>" <?= (string) ($batch['product_type'] ?? '') === (string) $option['product_code'] ? 'selected' : ''; ?>><?= htmlspecialchars((string) $option['product_name'], ENT_QUOTES, 'UTF-8'); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label" for="import_date">Ngày nhập</label>
                        <input class="form-control" type="date" id="import_date" name="import_date" required value="<?= htmlspecialchars((string) ($batch['import_date'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label" for="status">Trạng thái</label>
                        <select class="form-select" id="status" name="status" required>
                            <?php foreach ($statusMap as $key => $label): ?>
                            <option value="<?= htmlspecialchars((string) $key, ENT_QUOTES, 'UTF-8'); ?>" <?= (string) ($batch['status'] ?? '') === (string) $key ? 'selected' : ''; ?>><?= htmlspecialchars((string) $label, ENT_QUOTES, 'UTF-8'); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <hr>
                <h2 class="h6">Thùng</h2>

                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Mã thùng</th>
                                <th>Số khay</th>
                                <th>Sản phẩm/khay</th>
                                <th>Tổng số lượng</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($boxes === []): ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted">Không có thùng nào trong lô.</td>
                            </tr>
                            <?php else: ?>
                            <?php foreach ($boxes as $index => $box): ?>
                            <tr>
                                <td>
                                    <?= htmlspecialchars((string) ($box['box_id'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>
                                    <input type="hidden" name="boxes[<?= (int) $index; ?>][box_id]" value="<?= htmlspecialchars((string) ($box['box_id'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
                                </td>
                                <td><?= htmlspecialchars((string) ($box['box_code'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></td>
                                <td>
                                    <input class="form-control form-control-sm" type="number" min="0" name="boxes[<?= (int) $index; ?>][tray_count]" value="<?= htmlspecialchars((string) ($box['tray_count'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
                                </td>
                                <td>
                                    <input class="form-control form-control-sm" type="number" min="0" name="boxes[<?= (int) $index; ?>][unit_per_tray]" value="<?= htmlspecialchars((string) ($box['unit_per_tray'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
                                </td>
                                <td>
                                    <input class="form-control form-control-sm" type="number" min="1" name="boxes[<?= (int) $index; ?>][total_units]" value="<?= htmlspecialchars((string) ($box['total_units'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>" required>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Lưu</button>
                    <a class="btn btn-outline-secondary" href="/admin/batches">Quay lại</a>
                </div>
            </form>
        </div>
    </div>
</div>
