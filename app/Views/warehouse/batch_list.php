<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2 mb-0">Danh Sách Lô Hàng</h1>
        <a class="btn btn-primary" href="/warehouse/create">
            <span>+</span> Tạo lô mới
        </a>
    </div>

    <?php if (empty($batches)): ?>
    <div class="alert alert-info text-center" role="alert">
        Chưa có lô hàng nào.
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
                        <th>Số thùng</th>
                        <th>Tổng số lượng</th>
                        <th>Trạng thái</th>
                        <th class="text-end">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($batches as $batch): ?>
                    <tr>
                        <td class="fw-bold text-primary"><?php echo htmlspecialchars((string) $batch['batch_code'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars((string) $batch['product_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars((string) ($batch['supplier_code'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars((string) $batch['import_date'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars((string) $batch['box_count'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars((string) $batch['total_units'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td>
                            <?php 
                                $statusClass = match($batch['status']) {
                                    'new' => 'bg-info text-dark',
                                    'pending_qc' => 'bg-warning text-dark',
                                    'in_progress' => 'bg-primary',
                                    'completed' => 'bg-success',
                                    'rejected' => 'bg-danger',
                                    default => 'bg-secondary'
                                };
                            ?>
                            <span class="badge <?php echo $statusClass; ?> rounded-pill"><?php echo htmlspecialchars((string) $batch['status'], ENT_QUOTES, 'UTF-8'); ?></span>
                        </td>
                        <td class="text-end">
                            <a class="btn btn-sm btn-outline-primary" href="/warehouse/detail?batch_code=<?php echo urlencode((string) $batch['batch_code']); ?>">Chi tiết</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>
</div>