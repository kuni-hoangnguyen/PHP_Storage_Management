<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2 mb-0">Báo Cáo QC Lô Hàng</h1>
    </div>

    <?php
        $filterQuery = is_array($filterQuery ?? null) ? $filterQuery : [];
        $code = (string) ($filterQuery['code'] ?? '');
        $supplierCode = (string) ($filterQuery['supplier_code'] ?? '');
        $productType = (string) ($filterQuery['product_type'] ?? '');
        $status = (string) ($filterQuery['status'] ?? 'completed');
        $fromDate = (string) ($filterQuery['from_date'] ?? '');
        $toDate = (string) ($filterQuery['to_date'] ?? '');

        $paginationQuery = [
            'code' => $code,
            'supplier_code' => $supplierCode,
            'product_type' => $productType,
            'status' => $status,
            'from_date' => $fromDate,
            'to_date' => $toDate,
        ];
    ?>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="get" action="/manager/batches" class="row g-3 align-items-end">
                <div class="col-md-2">
                    <label class="form-label" for="code">Mã lô</label>
                    <input class="form-control" type="text" id="code" name="code" value="<?php echo htmlspecialchars($code, ENT_QUOTES, 'UTF-8'); ?>" placeholder="VD: Q1-2026...">
                </div>
                <div class="col-md-2">
                    <label class="form-label" for="from_date">Từ ngày</label>
                    <input class="form-control" type="date" id="from_date" name="from_date" value="<?php echo htmlspecialchars($fromDate, ENT_QUOTES, 'UTF-8'); ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label" for="to_date">Đến ngày</label>
                    <input class="form-control" type="date" id="to_date" name="to_date" value="<?php echo htmlspecialchars($toDate, ENT_QUOTES, 'UTF-8'); ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label" for="supplier_code">Nhà cung cấp</label>
                    <select class="form-select" id="supplier_code" name="supplier_code">
                        <option value="">Tất cả</option>
                        <?php foreach (($suppliers ?? []) as $supplier): ?>
                        <?php $code = (string) ($supplier['supplier_code'] ?? ''); ?>
                        <option value="<?php echo htmlspecialchars($code, ENT_QUOTES, 'UTF-8'); ?>" <?php echo $code === $supplierCode ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars(sprintf('%s - %s', $code, (string) ($supplier['supplier_name'] ?? '')), ENT_QUOTES, 'UTF-8'); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label" for="product_type">Loại sản phẩm</label>
                    <select class="form-select" id="product_type" name="product_type">
                        <option value="">Tất cả</option>
                        <?php foreach (($productTypes ?? []) as $product): ?>
                        <?php $productCode = (string) ($product['product_code'] ?? ''); ?>
                        <option value="<?php echo htmlspecialchars($productCode, ENT_QUOTES, 'UTF-8'); ?>" <?php echo $productCode === $productType ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars((string) ($product['product_name'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label" for="status">Trạng thái</label>
                    <select class="form-select" id="status" name="status">
                        <option value="all" <?php echo $status === 'all' ? 'selected' : ''; ?>>Tất cả</option>
                        <option value="completed" <?php echo $status === 'completed' ? 'selected' : ''; ?>>Hoàn tất</option>
                        <option value="rejected" <?php echo $status === 'rejected' ? 'selected' : ''; ?>>Từ chối</option>
                    </select>
                </div>
                <div class="col-md-6 d-grid gap-2">
                    <button type="submit" class="btn btn-primary">Lọc</button>
                </div>
                <div class="col-md-6 d-grid gap-2">
                    <a href="/manager/batches" class="btn btn-outline-secondary">Đặt lại</a>
            </form>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small">Tổng lô</div>
                    <div class="h4 mb-0"><?php echo (int) ($completedTotalRows ?? 0); ?></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small">Tổng OK</div>
                    <div class="h4 mb-0 text-success"><?php echo (int) ($totalOkUnits ?? 0); ?></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small">Tổng NG</div>
                    <div class="h4 mb-0 text-danger"><?php echo (int) ($totalNgUnits ?? 0); ?></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small">Tỷ lệ NG</div>
                    <div class="h4 mb-0"><?php echo htmlspecialchars((string) ($ngRate ?? 0), ENT_QUOTES, 'UTF-8'); ?>%</div>
                </div>
            </div>
        </div>
    </div>

    <h2 class="h4 mt-4 mb-3 text-success border-bottom pb-2">Danh sách lô theo bộ lọc</h2>
    <?php if (empty($completedBatches)): ?>
    <div class="alert alert-light border text-center text-muted" role="alert">
        Không có lô hàng đã kiểm định.
    </div>
    <?php else: ?>
    <div class="text-muted small mb-2">
        Tổng bản ghi: <?php echo (int) ($completedTotalRows ?? count($completedBatches)); ?>
    </div>
    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Mã lô</th>
                        <th>Sản phẩm</th>
                        <th>Nhà cung cấp</th>
                        <th>Ngày nhập</th>
                        <th>OK</th>
                        <th>NG</th>
                        <th>Tỉ lệ đạt</th>
                        <th>Trạng thái</th>
                        <th class="text-end">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($completedBatches as $batch): ?>
                    <?php
                        $ok         = (int) ($batch['ok_units'] ?? 0);
                        $ng         = (int) ($batch['ng_units'] ?? 0);
                        $total      = $ok + $ng;
                        $ratio      = $total > 0 ? round(($ok / $total) * 100, 2) : 0;
                        $statusKey  = (string) ($batch['status'] ?? '');
                        $statusMeta = $batchStatusMap[$statusKey] ?? ['label' => $statusKey, 'badgeClass' => 'bg-secondary'];
                    ?>
                    <tr>
                        <td class="fw-bold">
                            <?php echo htmlspecialchars((string) $batch['batch_code'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars((string) $batch['product_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars((string) ($batch['supplier_name'] ?? ($batch['supplier_code'] ?? '')), ENT_QUOTES, 'UTF-8'); ?>
                        </td>
                        <td><?php echo htmlspecialchars((string) $batch['import_date'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td class="text-success fw-semibold"><?php echo htmlspecialchars((string) $ok, ENT_QUOTES, 'UTF-8'); ?></td>
                        <td class="text-danger fw-semibold"><?php echo htmlspecialchars((string) $ng, ENT_QUOTES, 'UTF-8'); ?></td>
                        <td>
                            <div class="progress" style="height: 20px;">
                                <div class="progress-bar bg-success" role="progressbar"
                                    style="width: <?php echo $ratio; ?>%;" aria-valuenow="<?php echo $ratio; ?>"
                                    aria-valuemin="0" aria-valuemax="100"><?php echo $ratio; ?>%</div>
                            </div>
                        </td>
                        <td><span
                                class="badge <?php echo htmlspecialchars((string) $statusMeta['badgeClass'], ENT_QUOTES, 'UTF-8'); ?> rounded-pill"><?php echo htmlspecialchars((string) $statusMeta['label'], ENT_QUOTES, 'UTF-8'); ?></span>
                        </td>
                        <td class="text-end"><a class="btn btn-outline-secondary btn-sm"
                                href="/manager/detail?batch_code=<?php echo urlencode((string) $batch['batch_code']); ?>">Xem
                                chi tiết</a></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>
    <?php if (($completedTotalPages ?? 1) > 1): ?>
<nav class="mt-3 justify-content-end d-flex">
    <ul class="pagination mb-0">
        <?php
            $prev = max(1, (int)$completedPage - 1);
            $next = min((int)$completedTotalPages, (int)$completedPage + 1);
            $prevQuery = $paginationQuery;
            $prevQuery['page'] = $prev;
            $nextQuery = $paginationQuery;
            $nextQuery['page'] = $next;
        ?>
        <li class="page-item <?php echo ((int)$completedPage <= 1) ? 'disabled' : ''; ?>">
            <a class="page-link" href="?<?php echo htmlspecialchars(http_build_query($prevQuery), ENT_QUOTES, 'UTF-8'); ?>">Trước</a>
        </li>
        <li class="page-item disabled"><span class="page-link"><?php echo (int)$completedPage; ?> / <?php echo (int)$completedTotalPages; ?></span></li>
        <li class="page-item <?php echo ((int)$completedPage >= (int)$completedTotalPages) ? 'disabled' : ''; ?>">
            <a class="page-link" href="?<?php echo htmlspecialchars(http_build_query($nextQuery), ENT_QUOTES, 'UTF-8'); ?>">Sau</a>
        </li>
    </ul>
</nav>
<?php endif; ?>
</div>