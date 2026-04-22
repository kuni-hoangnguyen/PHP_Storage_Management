<?php
$defectTypes = isset($defectTypes) && is_array($defectTypes) ? $defectTypes : [];
$productTypes = isset($productTypes) && is_array($productTypes) ? $productTypes : [];
$suppliers = isset($suppliers) && is_array($suppliers) ? $suppliers : [];
?>

<div class="container py-4">
	<nav aria-label="breadcrumb">
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="/admin/index">Admin Dashboard</a></li>
			<li class="breadcrumb-item active" aria-current="page">Danh mục chung</li>
		</ol>
	</nav>

	<h1 class="h3 mb-3">CRUD danh mục chung</h1>

	<div class="card shadow-sm mb-4">
		<div class="card-header bg-white d-flex justify-content-between align-items-center">
			<h2 class="h5 mb-0">Loại lỗi</h2>
			<a href="/admin/defect_type_save" class="btn btn-primary btn-sm">Thêm loại lỗi</a>
		</div>
		<div class="table-responsive">
			<table class="table table-striped table-hover mb-0 align-middle">
				<thead class="table-light">
					<tr>
						<th>ID</th>
						<th>Tên loại lỗi</th>
						<th>Mô tả</th>
						<th>Trạng thái</th>
						<th class="text-end">Thao tác</th>
					</tr>
				</thead>
				<tbody>
					<?php if ($defectTypes === []): ?>
					<tr><td colspan="5" class="text-center text-muted py-4">Chưa có dữ liệu</td></tr>
					<?php else: ?>
					<?php foreach ($defectTypes as $row): ?>
					<tr>
						<td><?php echo htmlspecialchars((string) ($row['defect_type_id'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></td>
						<td><?php echo htmlspecialchars((string) ($row['name'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></td>
						<td><?php echo htmlspecialchars((string) ($row['description'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></td>
						<td>
							<?php $active = (int) ($row['is_active'] ?? 1); ?>
							<span class="badge <?php echo $active === 1 ? 'text-bg-success' : 'text-bg-secondary'; ?>"><?php echo $active === 1 ? 'Hiển thị' : 'Ẩn'; ?></span>
						</td>
						<td class="text-end">
							<a class="btn btn-sm btn-outline-primary" href="/admin/defect_type_save?id=<?php echo urlencode((string) ($row['defect_type_id'] ?? '')); ?>">Sửa</a>
							<form class="d-inline" method="post" action="/admin/defect_type_delete">
								<input type="hidden" name="id" value="<?php echo htmlspecialchars((string) ($row['defect_type_id'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
								<button class="btn btn-sm btn-outline-danger" type="submit">Xóa</button>
							</form>
						</td>
					</tr>
					<?php endforeach; ?>
					<?php endif; ?>
				</tbody>
			</table>
		</div>
	</div>

	<div class="card shadow-sm mb-4">
		<div class="card-header bg-white d-flex justify-content-between align-items-center">
			<h2 class="h5 mb-0">Loại sản phẩm</h2>
			<a href="/admin/product_type_save" class="btn btn-primary btn-sm">Thêm loại sản phẩm</a>
		</div>
		<div class="table-responsive">
			<table class="table table-striped table-hover mb-0 align-middle">
				<thead class="table-light">
					<tr>
						<th>ID</th>
						<th>Mã loại sản phẩm</th>
						<th>Tên loại sản phẩm</th>
						<th>Trạng thái</th>
						<th class="text-end">Thao tác</th>
					</tr>
				</thead>
				<tbody>
					<?php if ($productTypes === []): ?>
					<tr><td colspan="5" class="text-center text-muted py-4">Chưa có dữ liệu.</td></tr>
					<?php else: ?>
					<?php foreach ($productTypes as $row): ?>
					<tr>
						<td><?php echo htmlspecialchars((string) ($row['product_type_id'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></td>
						<td><?php echo htmlspecialchars((string) ($row['product_code'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></td>
						<td><?php echo htmlspecialchars((string) ($row['product_name'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></td>
						<td>
							<?php $active = (int) ($row['is_active'] ?? 1); ?>
							<span class="badge <?php echo $active === 1 ? 'text-bg-success' : 'text-bg-secondary'; ?>"><?php echo $active === 1 ? 'Hiển thị' : 'Ẩn'; ?></span>
						</td>
						<td class="text-end">
							<a class="btn btn-sm btn-outline-primary" href="/admin/product_type_save?id=<?php echo urlencode((string) ($row['product_type_id'] ?? '')); ?>">Sửa</a>
							<form class="d-inline" method="post" action="/admin/product_type_delete">
								<input type="hidden" name="id" value="<?php echo htmlspecialchars((string) ($row['product_type_id'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
								<button class="btn btn-sm btn-outline-danger" type="submit">Xóa</button>
							</form>
						</td>
					</tr>
					<?php endforeach; ?>
					<?php endif; ?>
				</tbody>
			</table>
		</div>
	</div>

	<div class="card shadow-sm">
		<div class="card-header bg-white d-flex justify-content-between align-items-center">
			<h2 class="h5 mb-0">Nhà cung cấp</h2>
			<a href="/admin/supplier_save" class="btn btn-primary btn-sm">Thêm nhà cung cấp</a>
		</div>
		<div class="table-responsive">
			<table class="table table-striped table-hover mb-0 align-middle">
				<thead class="table-light">
					<tr>
						<th>ID</th>
						<th>Mã nhà cung cấp</th>
						<th>Tên nhà cung cấp</th>
						<th>Trạng thái</th>
						<th class="text-end">Thao tác</th>
					</tr>
				</thead>
				<tbody>
					<?php if ($suppliers === []): ?>
					<tr><td colspan="5" class="text-center text-muted py-4">Chưa có dữ liệu</td></tr>
					<?php else: ?>
					<?php foreach ($suppliers as $row): ?>
					<tr>
						<td><?php echo htmlspecialchars((string) ($row['supplier_id'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></td>
						<td><?php echo htmlspecialchars((string) ($row['supplier_code'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></td>
						<td><?php echo htmlspecialchars((string) ($row['supplier_name'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></td>
						<td>
							<?php $active = (int) ($row['is_active'] ?? 1); ?>
							<span class="badge <?php echo $active === 1 ? 'text-bg-success' : 'text-bg-secondary'; ?>"><?php echo $active === 1 ? 'Hiển thị' : 'Ẩn'; ?></span>
						</td>
						<td class="text-end">
							<a class="btn btn-sm btn-outline-primary" href="/admin/supplier_save?id=<?php echo urlencode((string) ($row['supplier_id'] ?? '')); ?>">Sửa</a>
							<form class="d-inline" method="post" action="/admin/supplier_delete">
								<input type="hidden" name="id" value="<?php echo htmlspecialchars((string) ($row['supplier_id'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
								<button class="btn btn-sm btn-outline-danger" type="submit">Xóa</button>
							</form>
						</td>
					</tr>
					<?php endforeach; ?>
					<?php endif; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>
