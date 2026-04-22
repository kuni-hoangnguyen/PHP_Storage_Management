<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white">
            <h1 class="h3 mb-0">Nhập Thùng Cho Lô Hàng</h1>
        </div>
        <div class="card-body">
            <?php if (isset($batch)): ?>
            <div class="row g-3 mb-4 border-bottom pb-3">
                <div class="col-md-3">
                    <div class="text-muted small">Mã lô</div>
                    <div class="fw-bold">
                        <?php echo htmlspecialchars((string) $batch['batch_code'], ENT_QUOTES, 'UTF-8'); ?></div>
                </div>
                <div class="col-md-3">
                    <div class="text-muted small">Nhà cung cấp</div>
                    <div class="fw-bold">
                        <?php echo htmlspecialchars(sprintf('%s - %s', (string) ($batch['supplier_code'] ?? ''), (string) ($batch['supplier_name'] ?? '')), ENT_QUOTES, 'UTF-8'); ?>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-muted small">Loại sản phẩm</div>
                    <div class="fw-bold">
                        <?php echo htmlspecialchars((string) $batch['product_name'], ENT_QUOTES, 'UTF-8'); ?></div>
                </div>
                <div class="col-md-3">
                    <div class="text-muted small">Ngày nhập</div>
                    <div class="fw-bold">
                        <?php echo htmlspecialchars((string) $batch['import_date'], ENT_QUOTES, 'UTF-8'); ?></div>
                </div>
            </div>
            <?php endif; ?>

            <?php
                $oldInput = isset($oldInput) && is_array($oldInput) ? $oldInput : [];
                $oldBoxes = isset($oldInput['boxes']) && is_array($oldInput['boxes']) ? $oldInput['boxes'] : [];
                $existingBoxes = isset($boxes) && is_array($boxes) ? $boxes : [];

                $initialBoxes = $oldBoxes !== [] ? $oldBoxes : $existingBoxes;
                $boxCount = count($initialBoxes) > 0 ? count($initialBoxes) : 1;
            ?>

            <?php if (isset($error)): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo htmlspecialchars((string) $error, ENT_QUOTES, 'UTF-8'); ?>
            </div>
            <?php endif; ?>

            <form id="box-form" action="/warehouse/box_add" method="post"
                data-initial-boxes="<?php echo htmlspecialchars((string) json_encode($initialBoxes, JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8'); ?>">
                <input type="hidden" name="batch_code"
                    value="<?php echo htmlspecialchars((string) ($batch['batch_code'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">

                <div class="row g-3 align-items-end mb-4">
                    <div class="col-md-6">
                        <label class="form-label" for="box_count">Số thùng cần nhập</label>
                        <input class="form-control" type="number" id="box_count" min="1" max="200"
                            value="<?php echo htmlspecialchars((string) $boxCount, ENT_QUOTES, 'UTF-8'); ?>" required>
                    </div>
                    <div class="col-md-6">
                        <button class="btn btn-secondary w-100" type="button" id="generate-btn">Tạo form</button>
                    </div>
                </div>

                <hr class="mb-4">

                <div id="boxes-container"></div>

                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-primary">Lưu danh sách thùng</button>
                    <a class="btn btn-outline-secondary" href="/warehouse/batches">Quay lại</a>
                </div>
            </form>
        </div>
    </div>
</div>

