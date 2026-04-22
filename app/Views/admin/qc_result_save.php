<?php
$qcResult = isset($qcResult) && is_array($qcResult) ? $qcResult : [];
$users = isset($users) && is_array($users) ? $users : [];
$defectTypes = isset($defectTypes) && is_array($defectTypes) ? $defectTypes : [];
$defects = isset($defects) && is_array($defects) ? $defects : [];
$batchTotalUnits = isset($batchTotalUnits) ? (int) $batchTotalUnits : 0;
$errors = isset($errors) && is_array($errors) ? $errors : [];
$old = isset($old) && is_array($old) ? $old : [];

$resultId = (string) ($old['result_id'] ?? $qcResult['result_id'] ?? '');
$batchCode = (string) ($old['batch_code'] ?? $qcResult['batch_code'] ?? '');
$inspectedBy = (string) ($old['inspected_by'] ?? $qcResult['inspected_by'] ?? '');
$inspectedAt = (string) ($old['inspected_at'] ?? $qcResult['inspected_at'] ?? '');
$formDefects = isset($old['defects']) && is_array($old['defects']) ? $old['defects'] : $defects;

$previewNgUnits = 0;
foreach ($formDefects as $item) {
    if (is_array($item)) {
        $previewNgUnits += max(0, (int) ($item['qty_units'] ?? 0));
    }
}
$previewOkUnits = max(0, $batchTotalUnits - $previewNgUnits);
$previewStatus = ($batchTotalUnits > 0 && ((float) $previewNgUnits / (float) $batchTotalUnits >= 0.3)) ? 'Từ chối' : 'Hoàn tất';
?>

<div class="container py-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/admin/index">Admin Dashboard</a></li>
            <li class="breadcrumb-item"><a href="/admin/qc_results">Kết quả QC</a></li>
            <li class="breadcrumb-item active" aria-current="page">Chỉnh sửa</li>
        </ol>
    </nav>

    <div class="card shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h1 class="h4 mb-0">Chỉnh sửa kết quả QC #<?php echo htmlspecialchars($resultId, ENT_QUOTES, 'UTF-8'); ?></h1>
            <a class="btn btn-outline-secondary btn-sm" href="/admin/qc_results">Quay lại</a>
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

            <form method="post" action="/admin/qc_result_save" class="row g-3">
                <input type="hidden" name="result_id" value="<?php echo htmlspecialchars($resultId, ENT_QUOTES, 'UTF-8'); ?>">

                <div class="col-md-6">
                    <label class="form-label" for="batch_code">Mã lô</label>
                    <input class="form-control" id="batch_code" name="batch_code" type="text" required readonly value="<?php echo htmlspecialchars($batchCode, ENT_QUOTES, 'UTF-8'); ?>">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Đơn vị QC</label>
                    <div class="border rounded px-3 py-2 bg-light small">
                        <div>Tổng sản phẩm lô: <strong id="batch-total-units"><?php echo htmlspecialchars((string) $batchTotalUnits, ENT_QUOTES, 'UTF-8'); ?></strong></div>
                        <div>OK tạm tính: <strong id="preview-ok-units"><?php echo htmlspecialchars((string) $previewOkUnits, ENT_QUOTES, 'UTF-8'); ?></strong></div>
                        <div>NG tạm tính: <strong id="preview-ng-units"><?php echo htmlspecialchars((string) $previewNgUnits, ENT_QUOTES, 'UTF-8'); ?></strong></div>
                        <div>
                            Trạng thái dự kiến: 
                            <strong id="preview-status">
                                <?php echo htmlspecialchars($previewStatus, ENT_QUOTES, 'UTF-8'); ?>
                            </strong>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="form-label" for="inspected_by">Kiểm tra bởi</label>
                    <select class="form-select" id="inspected_by" name="inspected_by">
                        <option value="">(NULL)</option>
                        <?php foreach ($users as $user): ?>
                        <?php $userId = (string) ($user['id'] ?? ''); ?>
                        <option value="<?php echo htmlspecialchars($userId, ENT_QUOTES, 'UTF-8'); ?>" <?php echo $inspectedBy === $userId ? 'selected' : ''; ?>><?php echo htmlspecialchars((string) ($user['username'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label" for="inspected_at">Thời điểm kiểm tra</label>
                    <input class="form-control" id="inspected_at" name="inspected_at" type="datetime-local" required
                        value="<?php echo htmlspecialchars(str_replace(' ', 'T', substr($inspectedAt, 0, 16)), ENT_QUOTES, 'UTF-8'); ?>">
                </div>

                <div class="col-12 mt-2">
                    <hr>
                    <h2 class="h6 mb-3">Bản ghi lỗi</h2>

                    <template id="defect-type-options-template">
                        <?php foreach ($defectTypes as $type): ?>
                        <option value="<?php echo htmlspecialchars((string) ($type['defect_type_id'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars((string) ($type['name'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></option>
                        <?php endforeach; ?>
                    </template>

                    <div id="defect-list" class="d-grid gap-2">
                        <?php if ($formDefects === []): ?>
                        <div class="row g-2 align-items-end defect-row">
                            <div class="col-md-7">
                                <label class="form-label">Loại lỗi</label>
                                <select class="form-select" name="defects[0][defect_type_id]">
                                    <option value="">-- Select --</option>
                                    <?php foreach ($defectTypes as $type): ?>
                                    <option value="<?php echo htmlspecialchars((string) ($type['defect_type_id'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars((string) ($type['name'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Số lượng đơn vị</label>
                                <input class="form-control" type="number" min="1" name="defects[0][qty_units]" value="">
                            </div>
                            <div class="col-md-2 d-grid">
                                <button type="button" class="btn btn-outline-danger remove-defect">Xóa</button>
                            </div>
                        </div>
                        <?php else: ?>
                        <?php foreach ($formDefects as $index => $defect): ?>
                        <?php
                        $currentDefectTypeId = (string) ($defect['defect_type_id'] ?? '');
                        $currentQty = (string) ($defect['qty_units'] ?? '');
                        $currentDefectId = (string) ($defect['defect_id'] ?? '');
                        ?>
                        <div class="row g-2 align-items-end defect-row">
                            <input type="hidden" name="defects[<?php echo (int) $index; ?>][defect_id]" value="<?php echo htmlspecialchars($currentDefectId, ENT_QUOTES, 'UTF-8'); ?>">
                            <div class="col-md-7">
                                <label class="form-label">Loại lỗi #<?php echo (int) ($index + 1); ?></label>
                                <select class="form-select" name="defects[<?php echo (int) $index; ?>][defect_type_id]">
                                    <option value="">-- Select --</option>
                                    <?php foreach ($defectTypes as $type): ?>
                                    <?php $typeId = (string) ($type['defect_type_id'] ?? ''); ?>
                                    <option value="<?php echo htmlspecialchars($typeId, ENT_QUOTES, 'UTF-8'); ?>" <?php echo $currentDefectTypeId === $typeId ? 'selected' : ''; ?>><?php echo htmlspecialchars((string) ($type['name'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Số lượng đơn vị</label>
                                <input class="form-control" type="number" min="1" name="defects[<?php echo (int) $index; ?>][qty_units]" value="<?php echo htmlspecialchars($currentQty, ENT_QUOTES, 'UTF-8'); ?>">
                            </div>
                            <div class="col-md-2 d-grid">
                                <button type="button" class="btn btn-outline-danger remove-defect">Xóa</button>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <button type="button" id="add-defect-row" class="btn btn-outline-primary btn-sm mt-3">Thêm loại lỗi</button>
                </div>
                
                <div class="col-12 d-flex gap-2 mt-2">
                    <button class="btn btn-primary" type="submit">Lưu</button>
                    <a class="btn btn-outline-secondary" href="/admin/qc_results">Hủy</a>
                </div>
            </form>
        </div>
    </div>
</div>
