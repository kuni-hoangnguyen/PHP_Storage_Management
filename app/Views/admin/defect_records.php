<?php
$defectRecords = isset($defectRecords) && is_array($defectRecords) ? $defectRecords : [];
?>

<div class="container py-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/admin/index">Admin Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Defect Records</li>
        </ol>
    </nav>

    <h1 class="h3 mb-3">Admin - Defect Records</h1>

    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <form class="row g-2" method="get" action="/admin/defect_records">
                <div class="col-md-10">
                    <input type="text" name="batch_code" class="form-control" placeholder="Filter by batch code"
                        value="<?php echo htmlspecialchars((string) ($_GET['batch_code'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
                </div>
                <div class="col-md-2 d-grid">
                    <button class="btn btn-outline-primary" type="submit">Loc</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-striped table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Batch code</th>
                        <th>Defect type</th>
                        <th>Qty units</th>
                        <th>Created at</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($defectRecords === []): ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">Chua co du lieu defect_records.</td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($defectRecords as $row): ?>
                    <tr>
                        <td><?php echo htmlspecialchars((string) ($row['defect_id'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars((string) ($row['batch_code'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars((string) ($row['defect_name'] ?? $row['defect_type_id'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars((string) ($row['qty_units'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars((string) ($row['created_at'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></td>
                        <td class="text-end">
                            <a class="btn btn-sm btn-outline-primary" href="/admin/defect_record_save?id=<?php echo urlencode((string) ($row['defect_id'] ?? '')); ?>">Edit</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
