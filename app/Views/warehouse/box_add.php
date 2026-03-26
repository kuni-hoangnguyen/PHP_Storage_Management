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
                <div class="col-md-3">
                    <div class="text-muted small">Ngày nhập</div>
                    <div class="fw-bold"><?php echo htmlspecialchars((string) $batch['import_date'], ENT_QUOTES, 'UTF-8'); ?></div>
                </div>
            </div>
            <?php endif; ?>

            <?php
                $oldInput = isset($oldInput) && is_array($oldInput) ? $oldInput : [];
                $oldBoxes = isset($oldInput['boxes']) && is_array($oldInput['boxes']) ? $oldInput['boxes'] : [];
                $oldCount = count($oldBoxes) > 0 ? count($oldBoxes) : 1;
            ?>

            <?php if (isset($error)): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo htmlspecialchars((string) $error, ENT_QUOTES, 'UTF-8'); ?>
            </div>
            <?php endif; ?>

            <form id="box-form" action="/warehouse/box_add" method="post">
                <input type="hidden" name="batch_code"
                    value="<?php echo htmlspecialchars((string) ($batch['batch_code'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">

                <div class="row g-3 align-items-end mb-4">
                    <div class="col-md-6">
                        <label class="form-label" for="box_count">Số thùng cần nhập</label>
                        <input class="form-control" type="number" id="box_count" min="1" max="200"
                            value="<?php echo htmlspecialchars((string) $oldCount, ENT_QUOTES, 'UTF-8'); ?>" required>
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

<script>
(function() {
    const oldBoxes = <?php echo json_encode($oldBoxes, JSON_UNESCAPED_UNICODE); ?>;
    const container = document.getElementById('boxes-container');
    const countInput = document.getElementById('box_count');
    const generateBtn = document.getElementById('generate-btn');
    let hydratedOldData = false;

    function isPositiveInt(value) {
        return Number.isInteger(value) && value > 0;
    }

    function parseIntStrict(raw) {
        if (!/^\d+$/.test(raw.trim())) {
            return null;
        }
        const n = Number(raw);
        return Number.isSafeInteger(n) ? n : null;
    }

    function syncRequiredAndTotal(block) {
        const totalInput = block.querySelector('[name$="[total_units]"]');
        const trayInput = block.querySelector('[name$="[tray_count]"]');
        const unitInput = block.querySelector('[name$="[unit_per_tray]"]');

        const totalRaw = totalInput.value.trim();
        const trayRaw = trayInput.value.trim();
        const unitRaw = unitInput.value.trim();

        const totalVal = totalRaw === '' ? null : parseIntStrict(totalRaw);
        const trayVal = trayRaw === '' ? null : parseIntStrict(trayRaw);
        const unitVal = unitRaw === '' ? null : parseIntStrict(unitRaw);

        if (isPositiveInt(trayVal) && isPositiveInt(unitVal)) {
            totalInput.value = String(trayVal * unitVal);
            totalInput.readOnly = true;

            trayInput.readOnly = false;
            unitInput.readOnly = false;

        } else if (isPositiveInt(totalVal)) {
            trayInput.readOnly = true;
            unitInput.readOnly = true;
            totalInput.readOnly = false;

        } else {
            trayInput.readOnly = false;
            unitInput.readOnly = false;
            totalInput.readOnly = false;
        }

    }

    function attachBoxEvents(block) {
        const totalInput = block.querySelector('[name$="[total_units]"]');
        const trayInput = block.querySelector('[name$="[tray_count]"]');
        const unitInput = block.querySelector('[name$="[unit_per_tray]"]');

        [totalInput, trayInput, unitInput].forEach((el) => {
            el.addEventListener('input', function() {
                syncRequiredAndTotal(block);
            });
            el.addEventListener('blur', function() {
                syncRequiredAndTotal(block);
            });
        });

        syncRequiredAndTotal(block);
    }

    function buildBoxBlock(index) {
        const no = index + 1;
        return `
            <div class="card mb-3 box-item shadow-sm">
                <div class="card-header bg-light py-2">
                    <h5 class="mb-0 fs-6 fw-bold">Thùng #${no}</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="box_code_${no}">Mã thùng</label>
                            <input class="form-control" type="text" id="box_code_${no}" name="boxes[${index}][box_code]" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="tray_count_${no}">Số khay</label>
                            <input class="form-control" type="number" id="tray_count_${no}" name="boxes[${index}][tray_count]" min="1" step="1">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="unit_per_tray_${no}">Số sản phẩm/khay</label>
                            <input class="form-control" type="number" id="unit_per_tray_${no}" name="boxes[${index}][unit_per_tray]" min="1" step="1">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="total_units_${no}">Tổng sản phẩm</label>
                            <input class="form-control" type="number" id="total_units_${no}" name="boxes[${index}][total_units]" min="1" step="1">
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    function applyOldData() {
        if (hydratedOldData) {
            return;
        }

        if (!Array.isArray(oldBoxes) || oldBoxes.length === 0) {
            hydratedOldData = true;
            return;
        }

        oldBoxes.forEach((box, index) => {
            const item = container.querySelectorAll('.box-item')[index];
            if (!item || typeof box !== 'object' || box === null) {
                return;
            }

            const boxCodeInput = item.querySelector('[name$="[box_code]"]');
            const totalInput = item.querySelector('[name$="[total_units]"]');
            const trayInput = item.querySelector('[name$="[tray_count]"]');
            const unitInput = item.querySelector('[name$="[unit_per_tray]"]');

            if (boxCodeInput) {
                boxCodeInput.value = String(box.box_code ?? '');
            }
            if (totalInput) {
                totalInput.value = String(box.total_units ?? '');
            }
            if (trayInput) {
                trayInput.value = String(box.tray_count ?? '');
            }
            if (unitInput) {
                unitInput.value = String(box.unit_per_tray ?? '');
            }

            syncRequiredAndTotal(item);
        });

        hydratedOldData = true;
    }

    function renderBoxes() {
        const count = Number(countInput.value || 0);
        if (!Number.isInteger(count) || count < 1) {
            container.innerHTML = '';
            return;
        }

        let html = '';
        for (let i = 0; i < count; i += 1) {
            html += buildBoxBlock(i);
        }
        container.innerHTML = html;

        container.querySelectorAll('.box-item').forEach(attachBoxEvents);
        applyOldData();
    }

    generateBtn.addEventListener('click', renderBoxes);

    renderBoxes();
})();
</script>