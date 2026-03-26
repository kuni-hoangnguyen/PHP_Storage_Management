<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2 mb-0">Chi Tiết Lô Hàng</h1>
        <a class="btn btn-outline-secondary" href="/warehouse/batches">Quay Lại</a>
    </div>

    <?php if (empty($batch)): ?>
    <div class="alert alert-warning text-center" role="alert">Không tìm thấy lô hàng.</div>
    <?php else: ?>
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
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
                    <div class="fw-bold"><?php echo htmlspecialchars((string) $batch['supplier_name'], ENT_QUOTES, 'UTF-8'); ?></div>
                </div>
                <div class="col-md-3">
                    <div class="text-muted small">Ngày nhập</div>
                    <div class="fw-bold"><?php echo htmlspecialchars((string) $batch['import_date'], ENT_QUOTES, 'UTF-8'); ?></div>
                </div>
                <div class="col-md-3">
                    <div class="text-muted small">Trạng thái</div>
                    <span class="badge bg-secondary"><?php echo htmlspecialchars((string) $batch['status'], ENT_QUOTES, 'UTF-8'); ?></span>
                </div>
                <div class="col-md-3">
                    <div class="text-muted small">Số thùng</div>
                    <div class="fw-bold"><?php echo htmlspecialchars((string) count($boxes), ENT_QUOTES, 'UTF-8'); ?></div>
                </div>
            </div>
        </div>
    </div>

    <?php if ($batch['status'] === 'new'): ?>
    <div class="alert alert-info d-flex justify-content-between align-items-center mb-4" role="alert">
        <span>Vui lòng kiểm tra kỹ thông tin trước khi xác nhận. Sau khi xác nhận, lô hàng sẽ được chuyển sang QC.</span>
        <a class="btn btn-success" href="/warehouse/update_status?batch_code=<?php echo urlencode((string) $batch['batch_code']); ?>">
            <i class="bi bi-check-circle"></i> Xác nhận thông tin
        </a>
    </div>
    <?php endif; ?>

    <h3 class="h4 mb-3">Danh Sách Thùng</h3>
    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Mã thùng</th>
                        <th>Tổng sản phẩm</th>
                        <th>Số khay</th>
                        <th>Số sản phẩm/khay</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($boxes as $box): ?>
                    <tr>
                        <td><?php echo htmlspecialchars((string) $box['box_code'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars((string) $box['total_units'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars((string) $box['tray_count'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars((string) $box['unit_per_tray'], ENT_QUOTES, 'UTF-8'); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot class="table-light">
                    <tr>
                        <th class="text-end">Tổng cộng</th>
                        <th class="fw-bold"><?php echo htmlspecialchars((string) $totalUnits, ENT_QUOTES, 'UTF-8'); ?></th>
                        <th colspan="2"></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    <?php endif; ?>
</div>