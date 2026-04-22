<?php
$qcResults = isset($qcResults) && is_array($qcResults) ? $qcResults : [];
?>

<div class="container py-4">
		<nav aria-label="breadcrumb">
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="/admin/index">Admin Dashboard</a></li>
			<li class="breadcrumb-item active" aria-current="page">Kết quả QC</li>
		</ol>
	</nav>
	<h1 class="h3 mb-3">Kết quả QC</h1>

	<div class="card shadow-sm mb-3">
		<div class="card-body">
			<form class="row g-2" method="get" action="/admin/qc_results">
				<div class="col-md-10">
					<input type="text" name="batch_code" class="form-control" placeholder="Tìm theo mã lô"
						value="<?php echo htmlspecialchars((string) ($_GET['batch_code'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
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
						<th>ID</th>
						<th>Mã lô</th>
						<th>OK</th>
						<th>NG</th>
						<th>Người kiểm tra</th>
						<th>Thời điểm kiểm tra</th>
						<th class="text-end">Thao tác</th>
					</tr>
				</thead>
				<tbody>
					<?php if ($qcResults === []): ?>
					<tr>
						<td colspan="7" class="text-center text-muted py-4">Chưa có dữ liệu.</td>
					</tr>
					<?php else: ?>
					<?php foreach ($qcResults as $row): ?>
					<tr>
						<td><?php echo htmlspecialchars((string) ($row['result_id'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></td>
						<td><?php echo htmlspecialchars((string) ($row['batch_code'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></td>
						<td class="fw-semibold text-success"><?php echo htmlspecialchars((string) ($row['ok_units'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></td>
						<td class="fw-semibold text-danger"><?php echo htmlspecialchars((string) ($row['ng_units'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></td>
						<td><?php echo htmlspecialchars((string) ($row['inspector_name'] ?? $row['inspected_by'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></td>
						<td><?php echo htmlspecialchars((string) ($row['inspected_at'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></td>
						<td class="text-end">
							<a class="btn btn-sm btn-outline-primary" href="/admin/qc_result_save?id=<?php echo urlencode((string) ($row['result_id'] ?? '')); ?>">Sửa</a>
						</td>
					</tr>
					<?php endforeach; ?>
					<?php endif; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>
