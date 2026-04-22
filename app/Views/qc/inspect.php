<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white">
            <h1 class="h3 mb-0">Kiểm Tra Lô Hàng</h1>
        </div>
        <div class="card-body">
            <?php if (isset($batch) && is_array($batch)): ?>
            <div class="row g-3 mb-4 border-bottom pb-3">
                <div class="col-md-2">
                    <div class="text-muted small">Mã lô</div>
                    <div class="fw-bold"><?php echo htmlspecialchars((string) $batch['batch_code'], ENT_QUOTES, 'UTF-8'); ?></div>
                </div>
                <div class="col-md-3">
                    <div class="text-muted small">Nhà cung cấp</div>
                    <div class="fw-bold"><?php echo htmlspecialchars(sprintf('%s - %s', (string) ($batch['supplier_code'] ?? ''), (string) ($batch['supplier_name'] ?? '')), ENT_QUOTES, 'UTF-8'); ?></div>
                </div>
                <div class="col-md-3">
                    <div class="text-muted small">Loại sản phẩm</div>
                    <div class="fw-bold"><?php echo htmlspecialchars((string) $batch['product_name'], ENT_QUOTES, 'UTF-8'); ?></div>
                </div>
                <div class="col-md-2">
                    <div class="text-muted small">Ngày nhập</div>
                    <div class="fw-bold"><?php echo htmlspecialchars((string) $batch['import_date'], ENT_QUOTES, 'UTF-8'); ?></div>
                </div>
                <div class="col-md-2">
                    <div class="text-muted small">Số thùng</div>
                    <div class="fw-bold"><?php echo htmlspecialchars((string) $box_count, ENT_QUOTES, 'UTF-8'); ?></div>
                </div>
            </div>
            <?php endif; ?>

            <?php if (isset($error)): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo htmlspecialchars((string) $error, ENT_QUOTES, 'UTF-8'); ?>
            </div>
            <?php endif; ?>

            <form id="box-form" action="/qc/inspect" method="post">
                <input type="hidden" name="batch_code"
                    value="<?php echo htmlspecialchars((string) ($batchCode ?? ($batch['batch_code'] ?? '')), ENT_QUOTES, 'UTF-8'); ?>">

                <div id="boxes-container">
                    <?php if (empty($boxes)): ?>
                    <div class="text-center py-5 text-muted bg-light rounded">
                        Không có thùng nào trong lô hàng này.
                    </div>
                    <?php else: ?>
                    <?php $batchTotalUnits = 0; ?>
                    <?php foreach ($boxes as $box): ?>
                    <?php $batchTotalUnits += (int) ($box['total_units'] ?? 0); ?>
                    <?php endforeach; ?>

                    <div class="qc-batch-card card mb-3 border-primary" data-total-units="<?php echo htmlspecialchars((string) $batchTotalUnits, ENT_QUOTES, 'UTF-8'); ?>">
                        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                            <span class="fw-bold fs-5">Ghi nhận lỗi</span>
                            <span class="badge bg-white text-primary fs-6">Tổng: <?php echo htmlspecialchars((string) $batchTotalUnits, ENT_QUOTES, 'UTF-8'); ?> SP</span>
                        </div>
                        <div class="card-body">
                            <div id="defect-list" class="mb-3"></div>

                            <button type="button" id="add-defect-btn" class="btn btn-outline-primary btn-sm mb-3">
                                <span class="fw-bold">+</span> Thêm lỗi
                            </button>

                            <template id="defect-template">
                                <div class="row g-2 mb-2 align-items-center defect-row">
                                    <div class="col-md-6">
                                        <select class="form-select defect-type" data-name="defects[__INDEX__][defect_type_id]" required>
                                            <option value="">-- Chọn loại lỗi --</option>
                                            <?php foreach ($defects as $defect): ?>
                                            <option value="<?php echo htmlspecialchars((string) $defect['defect_type_id'], ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars((string) $defect['name'], ENT_QUOTES, 'UTF-8'); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <input type="number" class="form-control defect-qty" data-name="defects[__INDEX__][qty_units]" min="1" max="<?php echo htmlspecialchars((string) $batchTotalUnits, ENT_QUOTES, 'UTF-8'); ?>" step="1" placeholder="Số sản phẩm lỗi">
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button" class="remove-defect-btn btn btn-danger w-100">Xóa</button>
                                    </div>
                                </div>
                            </template>

                            <hr>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-danger" for="ng-units">Tổng NG (Lỗi)</label>
                                    <input type="number" class="form-control" id="ng-units" name="ng_units" min="0" value="0" readonly style="background-color: #fee;">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-success" for="ok-units">Tổng OK (Đạt)</label>
                                    <input type="number" class="form-control" id="ok-units" name="ok_units" min="0" value="<?php echo htmlspecialchars((string) $batchTotalUnits, ENT_QUOTES, 'UTF-8'); ?>" readonly style="background-color: #eef;">
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <div id="qc-submit-error" class="alert alert-danger mt-3" style="display: none;"></div>

                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-primary">Lưu kết quả</button>
                    <a class="btn btn-outline-secondary" href="/qc/batches">Quay lại</a>
                </div>
            </form>
        </div>
    </div>
</div>

