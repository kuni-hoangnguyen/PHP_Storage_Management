<?php
$batches = isset($batches) && is_array($batches) ? $batches : [];
?>

<div class="container py-4">
		<nav aria-label="breadcrumb">
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="/admin/index">Admin Dashboard</a></li>
			<li class="breadcrumb-item active" aria-current="page">Lô hàng</li>
		</ol>
	</nav>
	<h1 class="h3 mb-3">Admin - Batches</h1>

	<div class="card shadow-sm mb-3">
		<div class="card-body">
			<form class="row g-2" method="get" action="/admin/batches">
				<div class="col-md-4">
					<input type="text" name="batch_code" class="form-control" placeholder="Mã lô"
						value="<?php echo htmlspecialchars((string) ($_GET['batch_code'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
				</div>
				<div class="col-md-3">
					<input type="text" name="supplier_code" class="form-control" placeholder="Mã nhà cung cấp"
						value="<?php echo htmlspecialchars((string) ($_GET['supplier_code'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
				</div>
				<div class="col-md-3">
					<select name="status" class="form-control">
						<option value="">Tất cả trạng thái</option>
						<?php foreach ($statusMap as $key => $label): ?>
							<option value="<?php echo htmlspecialchars((string) $key, ENT_QUOTES, 'UTF-8'); ?>" <?php echo ($key === ($_GET['status'] ?? '')) ? 'selected' : ''; ?>>
								<?php echo htmlspecialchars((string) $label, ENT_QUOTES, 'UTF-8'); ?>
							</option>
						<?php endforeach; ?>
					</select>
				</div>
				<div class="col-md-2 d-grid">
					<button class="btn btn-outline-primary" type="submit">Lọc</button>
				</div>
			</form>
		</div>
	</div>

	<div class="card shadow-sm">
		<div class="table-responsive">
			<table class="table table-striped table-hover mb-0 align-middle">
				<thead class="table-light">
					<tr>
						<th>Mã lô</th>
						<th>Nhà cung cấp</th>
						<th>Sản phẩm</th>
						<th>Ngày nhập</th>
						<th>OK</th>
						<th>NG</th>
						<th>Tổng số đơn vị</th>
						<th>Trạng thái</th>
						<th class="text-end">Thao tác</th>
					</tr>
				</thead>
				<tbody>
					<?php if ($batches === []): ?>
					<tr>
						<td colspan="8" class="text-center text-muted py-4">Chưa có dữ liệu lô hàng nào.</td>
					</tr>
					<?php else: ?>
					<?php foreach ($batches as $row): ?>
					<?php
                        $statusKey  = (string) ($row['status'] ?? '');
                        $statusMeta = $batchStatusMap[$statusKey] ?? ['label' => $statusKey, 'badgeClass' => 'bg-secondary'];
                    ?>
					<tr>
						<td><?php echo htmlspecialchars((string) ($row['batch_code'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></td>
						<td><?php echo htmlspecialchars((string) ($row['supplier_code'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></td>
						<td><?php echo htmlspecialchars((string) ($row['product_name'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></td>
						<td><?php echo htmlspecialchars((string) ($row['import_date'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></td>
						<td class="text-success fw-semibold"><?php echo htmlspecialchars((string) ($row['ok_units'] ?? '0'), ENT_QUOTES, 'UTF-8'); ?></td>
						<td class="text-danger fw-semibold"><?php echo htmlspecialchars((string) ($row['ng_units'] ?? '0'), ENT_QUOTES, 'UTF-8'); ?></td>
						<td><?php echo htmlspecialchars((string) ($row['total_units'] ?? '0'), ENT_QUOTES, 'UTF-8'); ?></td>
						<td><span class="badge <?php echo htmlspecialchars((string) $statusMeta['badgeClass'], ENT_QUOTES, 'UTF-8'); ?> rounded-pill"><?php echo htmlspecialchars((string) $statusMeta['label'], ENT_QUOTES, 'UTF-8'); ?></span></td>
						<td class="text-end">
							<a class="btn btn-sm btn-outline-primary" href="/admin/batch_detail?batch_code=<?php echo urlencode((string) ($row['batch_code'] ?? '')); ?>">Chi tiết </a>
						</td>
					</tr>
					<?php endforeach; ?>
					<?php endif; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>
