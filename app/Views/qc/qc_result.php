<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2 mb-0">Kết Quả Kiểm Định</h1>
        <a class="btn btn-outline-secondary" href="/qc/batches">Quay lại</a>
    </div>

    <?php if (empty($batch)): ?>
    <div class="alert alert-warning text-center" role="alert">Không tìm thấy kết quả kiểm định.</div>
    <?php else: ?>
        <?php
            $statusKey = (string) ($batch['status'] ?? '');
            $statusMeta = $batchStatusMap[$statusKey] ?? ['label' => $statusKey, 'badgeClass' => 'bg-secondary'];
        ?>
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0">Thông Tin Chung</h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <div class="text-muted small">Mã lô</div>
                    <div class="fw-bold"><?php echo htmlspecialchars((string) $batch['batch_code'], ENT_QUOTES, 'UTF-8'); ?></div>
                </div>
                <div class="col-md-3">
                    <div class="text-muted small">Loại sản phẩm</div>
                    <div class="fw-bold"><?php echo htmlspecialchars((string) $batch['product_name'], ENT_QUOTES, 'UTF-8'); ?></div>
                </div>
                <div class="col-md-3">
                    <div class="text-muted small">Nhà cung cấp</div>
                    <div class="fw-bold"><?php echo htmlspecialchars(sprintf('%s - %s', (string) ($batch['supplier_code'] ?? ''), (string) ($batch['supplier_name'] ?? '')), ENT_QUOTES, 'UTF-8'); ?></div>
                </div>
                <div class="col-md-3">
                    <div class="text-muted small">Ngày nhập</div>
                    <div class="fw-bold"><?php echo htmlspecialchars((string) $batch['import_date'], ENT_QUOTES, 'UTF-8'); ?></div>
                </div>
                <div class="col-md-3">
                    <div class="text-muted small">Trạng thái</div>
                    <span class="badge <?php echo htmlspecialchars((string) $statusMeta['badgeClass'], ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars((string) $statusMeta['label'], ENT_QUOTES, 'UTF-8'); ?></span>
                </div>
                <div class="col-md-3">
                    <div class="text-muted small">Tổng OK (Đạt)</div>
                    <div class="fw-bold text-success"><?php echo htmlspecialchars((string) $batch['ok_units'], ENT_QUOTES, 'UTF-8'); ?></div>
                </div>
                <div class="col-md-3">
                    <div class="text-muted small">Tổng NG (Lỗi)</div>
                    <div class="fw-bold text-danger"><?php echo htmlspecialchars((string) $batch['ng_units'], ENT_QUOTES, 'UTF-8'); ?></div>
                </div>
                <?php if (!empty($batch['inspected_by'])): ?>
                <div class="col-md-3">
                    <div class="text-muted small">Người kiểm định</div>
                    <div class="fw-bold"><?php echo htmlspecialchars((string) $batch['inspected_by'], ENT_QUOTES, 'UTF-8'); ?></div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <h3 class="h4 mb-3">Thống Kê Lỗi</h3>
    <?php if (empty($defects)): ?>
    <div class="alert alert-success text-center" role="alert">Không có lỗi nào được ghi nhận (100% OK).</div>
    <?php else: ?>
    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Loại lỗi</th>
                        <th>Số lượng</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($defects as $defect): ?>
                    <tr>
                        <td><?php echo htmlspecialchars((string) $defect['defect_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td class="text-danger fw-bold"><?php echo htmlspecialchars((string) $defect['qty_units'], ENT_QUOTES, 'UTF-8'); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>
    <?php endif; ?>
</div>