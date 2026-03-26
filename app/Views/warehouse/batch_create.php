<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h1 class="h4 mb-0">Tạo Lô Hàng Mới</h1>
                </div>
                <div class="card-body">
                    <p class="card-text text-muted mb-4">Nhập thông tin lô để bắt đầu quá trình nhập kho.</p>

                    <?php if (isset($error)): ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo htmlspecialchars((string) $error, ENT_QUOTES, 'UTF-8'); ?>
                    </div>
                    <?php endif; ?>

                    <form action="/warehouse/create" method="post">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label" for="supplier_code">Nguồn hàng</label>
                                <select class="form-select" id="supplier_code" name="supplier_code" required>
                                    <option value="">-- Chọn nguồn --</option>
                                    <?php foreach ($suppliers as $row): ?>
                                    <option value="<?php echo htmlspecialchars((string) $row['supplier_code'], ENT_QUOTES, 'UTF-8'); ?>">
                                        <?php echo htmlspecialchars(sprintf('%s - %s', (string) ($row['supplier_code'] ?? ''), (string) ($row['supplier_name'] ?? '')), ENT_QUOTES, 'UTF-8'); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label" for="product_type">Loại sản phẩm</label>
                                <select class="form-select" id="product_type" name="product_type" required>
                                    <option value="">-- Chọn loại --</option>
                                    <?php foreach ($product_types as $row): ?>
                                    <option value="<?php echo htmlspecialchars((string) $row['product_code'], ENT_QUOTES, 'UTF-8'); ?>">
                                        <?php echo htmlspecialchars((string) $row['product_name'], ENT_QUOTES, 'UTF-8'); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label" for="import_date">Ngày nhập kho</label>
                                <input class="form-control" type="date" id="import_date" name="import_date" required>
                            </div>

                            <div class="col-12 mt-4">
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">Tạo lô hàng</button>
                                    <a class="btn btn-outline-secondary" href="/warehouse/batches">Quay lại danh sách</a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>