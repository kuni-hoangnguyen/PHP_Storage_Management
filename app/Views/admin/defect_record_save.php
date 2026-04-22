<?php
$defectRecord = isset($defectRecord) && is_array($defectRecord) ? $defectRecord : [];
$defectTypes = isset($defectTypes) && is_array($defectTypes) ? $defectTypes : [];
$errors = isset($errors) && is_array($errors) ? $errors : [];
$old = isset($old) && is_array($old) ? $old : [];

$defectId = (string) ($old['defect_id'] ?? $defectRecord['defect_id'] ?? '');
$batchCode = (string) ($old['batch_code'] ?? $defectRecord['batch_code'] ?? '');
$defectTypeId = (string) ($old['defect_type_id'] ?? $defectRecord['defect_type_id'] ?? '');
$qtyUnits = (string) ($old['qty_units'] ?? $defectRecord['qty_units'] ?? '');
?>

<div class="container py-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/admin/index">Admin Dashboard</a></li>
            <li class="breadcrumb-item"><a href="/admin/defect_records">Defect Records</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit</li>
        </ol>
    </nav>

    <div class="card shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h1 class="h4 mb-0">Edit Defect Record #<?php echo htmlspecialchars($defectId, ENT_QUOTES, 'UTF-8'); ?></h1>
            <a class="btn btn-outline-secondary btn-sm" href="/admin/defect_records">Back</a>
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

            <form method="post" action="/admin/defect_record_save" class="row g-3">
                <input type="hidden" name="defect_id" value="<?php echo htmlspecialchars($defectId, ENT_QUOTES, 'UTF-8'); ?>">

                <div class="col-md-6">
                    <label class="form-label" for="batch_code">Batch code</label>
                    <input class="form-control" id="batch_code" name="batch_code" type="text" required value="<?php echo htmlspecialchars($batchCode, ENT_QUOTES, 'UTF-8'); ?>">
                </div>

                <div class="col-md-6">
                    <label class="form-label" for="defect_type_id">Defect type</label>
                    <select class="form-select" id="defect_type_id" name="defect_type_id" required>
                        <option value="">-- Chon loai loi --</option>
                        <?php foreach ($defectTypes as $type): ?>
                        <?php $typeId = (string) ($type['defect_type_id'] ?? ''); ?>
                        <option value="<?php echo htmlspecialchars($typeId, ENT_QUOTES, 'UTF-8'); ?>" <?php echo $defectTypeId === $typeId ? 'selected' : ''; ?>><?php echo htmlspecialchars((string) ($type['name'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label" for="qty_units">Qty units</label>
                    <input class="form-control" id="qty_units" name="qty_units" type="number" min="1" required value="<?php echo htmlspecialchars($qtyUnits, ENT_QUOTES, 'UTF-8'); ?>">
                </div>

                <div class="col-12 d-flex gap-2 mt-2">
                    <button class="btn btn-primary" type="submit">Save</button>
                    <a class="btn btn-outline-secondary" href="/admin/defect_records">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
