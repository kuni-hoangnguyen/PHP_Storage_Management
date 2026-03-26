<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2 mb-0">Danh Sách Lô Hàng QC</h1>
    </div>

    <h2 class="h4 mt-4 mb-3 text-primary border-bottom pb-2">Lô Chờ Kiểm Định</h2>
    <?php if (empty($pendingBatches)): ?>
    <div class="alert alert-light border text-center text-muted" role="alert">
        Không có lô hàng nào đang chờ QC.
    </div>
    <?php else: ?>
    <div class="card shadow-sm mb-5">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Mã lô</th>
                        <th>Sản phẩm</th>
                        <th>Nhà cung cấp</th>
                        <th>Ngày nhập</th>
                        <th>Số lượng</th>
                        <th>Trạng thái</th>
                        <th class="text-end">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pendingBatches as $batch): ?>
                    <tr>
                        <td class="fw-bold"><?php echo htmlspecialchars((string) $batch['batch_code'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars((string) $batch['product_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars((string) ($batch['supplier_code'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars((string) $batch['import_date'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars((string) $batch['total_units'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><span class="badge bg-warning text-dark rounded-pill"><?php echo htmlspecialchars((string) $batch['status'], ENT_QUOTES, 'UTF-8'); ?></span></td>
                        <td class="text-end">
                            <a class="btn btn-primary btn-sm" href="/qc/inspect?batch_code=<?php echo urlencode((string) $batch['batch_code']); ?>">
                                <span class="fw-bold">+</span> Tiến hành QC
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>

    <h2 class="h4 mt-4 mb-3 text-success border-bottom pb-2">Lô Đã Kiểm Định</h2>
    <?php if (empty($completedBatches)): ?>
    <div class="alert alert-light border text-center text-muted" role="alert">
        Không có lô hàng đã kiểm định.
    </div>
    <?php else: ?>
    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Mã lô</th>
                        <th>Sản phẩm</th>
                        <th>Nhà cung cấp</th>
                        <th>Ngày nhập</th>
                        <th>Tỉ lệ đạt</th>
                        <th>Trạng thái</th>
                        <th class="text-end">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($completedBatches as $batch): ?>
                    <?php
                        $ok = (int) ($batch['ok_units'] ?? 0);
                        $ng = (int) ($batch['ng_units'] ?? 0);
                        $total = $ok + $ng;
                        $ratio = $total > 0 ? round(($ok / $total) * 100, 2) : 0;
                    ?>
                    <tr>
                        <td class="fw-bold"><?php echo htmlspecialchars((string) $batch['batch_code'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars((string) $batch['product_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars((string) ($batch['supplier_code'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars((string) $batch['import_date'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td>
                            <div class="progress" style="height: 20px;">
                                <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo $ratio; ?>%;" aria-valuenow="<?php echo $ratio; ?>" aria-valuemin="0" aria-valuemax="100"><?php echo $ratio; ?>%</div>
                            </div>
                        </td>
                        <td><span class="badge bg-success rounded-pill"><?php echo htmlspecialchars((string) $batch['status'], ENT_QUOTES, 'UTF-8'); ?></span></td>
                        <td class="text-end"><a class="btn btn-outline-secondary btn-sm" href="/qc/result?batch_code=<?php echo urlencode((string) $batch['batch_code']); ?>">Xem chi tiết</a></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>

    <h2 class="h4 mt-4 mb-3 text-danger border-bottom pb-2">Lô Đã Từ Chối</h2>
    <?php if (empty($rejectedBatches)): ?>
    <div class="alert alert-light border text-center text-muted" role="alert">
        Không có lô hàng đã kiểm định.
    </div>
    <?php else: ?>
    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Mã lô</th>
                        <th>Sản phẩm</th>
                        <th>Nhà cung cấp</th>
                        <th>Ngày nhập</th>
                        <th>Tỉ lệ đạt</th>
                        <th>Trạng thái</th>
                        <th class="text-end">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rejectedBatches as $batch): ?>
                    <?php
                        $ok = (int) ($batch['ok_units'] ?? 0);
                        $ng = (int) ($batch['ng_units'] ?? 0);
                        $total = $ok + $ng;
                        $ratio = $total > 0 ? round(($ok / $total) * 100, 2) : 0;
                    ?>
                    <tr>
                        <td class="fw-bold"><?php echo htmlspecialchars((string) $batch['batch_code'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars((string) $batch['product_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars((string) ($batch['supplier_code'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars((string) $batch['import_date'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td>
                            <div class="progress" style="height: 20px;">
                                <div class="progress-bar bg-danger" role="progressbar" style="width: <?php echo $ratio; ?>%;" aria-valuenow="<?php echo $ratio; ?>" aria-valuemin="0" aria-valuemax="100"><?php echo $ratio; ?>%</div>
                            </div>
                        </td>
                        <td><span class="badge bg-danger rounded-pill"><?php echo htmlspecialchars((string) $batch['status'], ENT_QUOTES, 'UTF-8'); ?></span></td>
                        <td class="text-end"><a class="btn btn-outline-secondary btn-sm" href="/qc/result?batch_code=<?php echo urlencode((string) $batch['batch_code']); ?>">Xem chi tiết</a></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>
</div>