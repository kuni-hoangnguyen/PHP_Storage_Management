// [UTIL] Convert input to safe non-negative integer.
function toNonNegativeInt(raw) {
	const value = String(raw ?? '').trim();
	if (!/^\d+$/.test(value)) {
		return 0;
	}
	const parsed = Number(value);
	return Number.isSafeInteger(parsed) && parsed >= 0 ? parsed : 0;
}

// [UTIL] Parse JSON string and ensure array fallback.
function parseJsonArray(raw, fallback) {
	try {
		const parsed = JSON.parse(raw || '[]');
		return Array.isArray(parsed) ? parsed : fallback;
	} catch (_error) {
		return fallback;
	}
}

// [WAREHOUSE] Initialize dynamic box form behavior.
function initWarehouseBoxAdd() {
	const container = document.getElementById('boxes-container');
	const countInput = document.getElementById('box_count');
	const generateBtn = document.getElementById('generate-btn');
	const form = document.getElementById('box-form');

	if (!container || !countInput || !generateBtn || !form) {
		return;
	}

	const initialBoxes = parseJsonArray(form.dataset.initialBoxes || '[]', []);
	let hydratedOldData = false;

	// [WAREHOUSE] Validate positive integer values.
	function isPositiveInt(value) {
		return Number.isInteger(value) && value > 0;
	}

	// [WAREHOUSE] Strict parser for integer-only text input.
	function parseIntStrict(raw) {
		const value = String(raw ?? '').trim();
		if (!/^\d+$/.test(value)) {
			return null;
		}
		const parsed = Number(value);
		return Number.isSafeInteger(parsed) ? parsed : null;
	}

	// [WAREHOUSE] Keep total_units synchronized with tray and unit inputs.
	function syncRequiredAndTotal(block) {
		const totalInput = block.querySelector('[name$="[total_units]"]');
		const trayInput = block.querySelector('[name$="[tray_count]"]');
		const unitInput = block.querySelector('[name$="[unit_per_tray]"]');

		if (!totalInput || !trayInput || !unitInput) {
			return;
		}

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
			return;
		}

		if (isPositiveInt(totalVal)) {
			trayInput.readOnly = true;
			unitInput.readOnly = true;
			totalInput.readOnly = false;
			return;
		}

		trayInput.readOnly = false;
		unitInput.readOnly = false;
		totalInput.readOnly = false;
	}

	// [WAREHOUSE] Bind recalculation listeners for each box block.
	function attachBoxEvents(block) {
		const totalInput = block.querySelector('[name$="[total_units]"]');
		const trayInput = block.querySelector('[name$="[tray_count]"]');
		const unitInput = block.querySelector('[name$="[unit_per_tray]"]');

		[totalInput, trayInput, unitInput].forEach(function (el) {
			if (!el) {
				return;
			}

			el.addEventListener('input', function () {
				syncRequiredAndTotal(block);
			});
			el.addEventListener('blur', function () {
				syncRequiredAndTotal(block);
			});
		});

		syncRequiredAndTotal(block);
	}

	// [WAREHOUSE] Build HTML markup for a single box input card.
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

	// [WAREHOUSE] Hydrate existing/old box values into rendered blocks.
	function applyInitialData() {
		if (hydratedOldData) {
			return;
		}

		if (!Array.isArray(initialBoxes) || initialBoxes.length === 0) {
			hydratedOldData = true;
			return;
		}

		initialBoxes.forEach(function (box, index) {
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

	// [WAREHOUSE] Render box blocks based on requested count.
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
		applyInitialData();
	}

	generateBtn.addEventListener('click', renderBoxes);
	renderBoxes();
}

// [QC] Initialize defect input and result calculation behavior.
function initQcInspect() {
	const form = document.querySelector('form#box-form[action="/qc/inspect"]');
	const batchCard = document.querySelector('.qc-batch-card');
	if (!form || !batchCard) {
		return;
	}

	// [QC] Recalculate NG/OK totals from current defect rows.
	function recalcBatch() {
		const totalUnits = toNonNegativeInt(batchCard.dataset.totalUnits);
		const ngInput = document.getElementById('ng-units');
		const okInput = document.getElementById('ok-units');
		const qtyInputs = document.querySelectorAll('#defect-list .defect-qty');

		if (!ngInput || !okInput) {
			return;
		}

		let rawNgUnits = 0;
		qtyInputs.forEach(function (qtyInput) {
			rawNgUnits += toNonNegativeInt(qtyInput.value);
		});

		batchCard.dataset.rawNgUnits = String(rawNgUnits);

		const maxNgUnits = totalUnits > 0 ? totalUnits : 0;
		const ngUnits = rawNgUnits > maxNgUnits ? maxNgUnits : rawNgUnits;

		ngInput.max = String(maxNgUnits);
		okInput.max = String(totalUnits);
		ngInput.value = String(ngUnits);
		okInput.value = String(totalUnits - ngUnits);
	}

	// [QC] Attach quantity/remove handlers for one defect row.
	function attachDefectEvents(row) {
		const qtyInput = row.querySelector('.defect-qty');
		const removeBtn = row.querySelector('.remove-defect-btn');
		if (!qtyInput || !removeBtn) {
			return;
		}

		qtyInput.addEventListener('input', recalcBatch);
		removeBtn.addEventListener('click', function () {
			row.remove();
			recalcBatch();
		});
	}

	// [QC] Append a new defect row from template with indexed names.
	function addDefectRow() {
		const list = document.getElementById('defect-list');
		const template = document.getElementById('defect-template');
		if (!list || !template) {
			return;
		}

		const index = list.querySelectorAll('.defect-row').length;
		const fragment = template.content.cloneNode(true);
		const row = fragment.querySelector('.defect-row');
		if (!row) {
			return;
		}

		const typeSelect = row.querySelector('.defect-type');
		const qtyInput = row.querySelector('.defect-qty');

		if (typeSelect) {
			typeSelect.name = String(typeSelect.dataset.name || '').replace('__INDEX__', String(index));
		}
		if (qtyInput) {
			qtyInput.name = String(qtyInput.dataset.name || '').replace('__INDEX__', String(index));
		}

		list.appendChild(row);
		attachDefectEvents(row);
		recalcBatch();
	}

	const addBtn = document.getElementById('add-defect-btn');
	if (addBtn) {
		addBtn.addEventListener('click', addDefectRow);
	}

	form.addEventListener('submit', function (event) {
		const errorEl = document.getElementById('qc-submit-error');
		const totalUnits = toNonNegativeInt(batchCard.dataset.totalUnits);
		const rawNgUnits = toNonNegativeInt(batchCard.dataset.rawNgUnits);

		if (rawNgUnits > totalUnits) {
			event.preventDefault();
			if (errorEl) {
				errorEl.textContent = 'Tổng số lượng lỗi vượt quá tổng số lượng của lô. Vui lòng kiểm tra lại.';
				errorEl.style.display = 'block';
			}
			return;
		}

		if (errorEl) {
			errorEl.textContent = '';
			errorEl.style.display = 'none';
		}
	});

	recalcBatch();
}

// [ADMIN] Initialize QC result editor (defects + realtime preview).
function initAdminQcResultSave() {
	const form = document.querySelector('form[action="/admin/qc_result_save"]');
	const list = document.getElementById('defect-list');
	const addBtn = document.getElementById('add-defect-row');
	const ngPreview = document.getElementById('preview-ng-units');
	const okPreview = document.getElementById('preview-ok-units');
	const statusPreview = document.getElementById('preview-status');
	const totalUnitsEl = document.getElementById('batch-total-units');
	const optionsTemplate = document.getElementById('defect-type-options-template');

	if (
		!form ||
		!list ||
		!addBtn ||
		!ngPreview ||
		!okPreview ||
		!statusPreview ||
		!totalUnitsEl ||
		!optionsTemplate
	) {
		return;
	}

	const totalUnits = toNonNegativeInt(totalUnitsEl.textContent || '0');

	function buildTypeOptions() {
		return optionsTemplate.innerHTML;
	}

	function reindexRows() {
		const rows = list.querySelectorAll('.defect-row');
		rows.forEach(function (row, index) {
			const idInput = row.querySelector('input[type="hidden"]');
			if (idInput) {
				idInput.name = `defects[${index}][defect_id]`;
			}

			const typeSelect = row.querySelector('select');
			if (typeSelect) {
				typeSelect.name = `defects[${index}][defect_type_id]`;
			}

			const qtyInput = row.querySelector('input[type="number"]');
			if (qtyInput) {
				qtyInput.name = `defects[${index}][qty_units]`;
			}
		});
	}

	function updatePreview() {
		const qtyInputs = list.querySelectorAll('input[type="number"]');
		let ngUnits = 0;

		qtyInputs.forEach(function (input) {
			ngUnits += toNonNegativeInt(input.value);
		});

		const okUnits = Math.max(0, totalUnits - ngUnits);
		const projectedStatus = totalUnits > 0 && ngUnits / totalUnits >= 0.3 ? 'Từ chối' : 'Hoàn tất';

		ngPreview.textContent = String(ngUnits);
		okPreview.textContent = String(okUnits);
		statusPreview.textContent = projectedStatus;
	}

	function bindRemove(btn) {
		btn.addEventListener('click', function () {
			const row = btn.closest('.defect-row');
			if (!row) {
				return;
			}

			row.remove();
			reindexRows();
			updatePreview();
		});
	}

	list.querySelectorAll('.remove-defect').forEach(bindRemove);

	list.addEventListener('input', function (event) {
		if (event.target && event.target.matches && event.target.matches('input, select')) {
			updatePreview();
		}
	});

	list.addEventListener('change', function (event) {
		if (event.target && event.target.matches && event.target.matches('input, select')) {
			updatePreview();
		}
	});

	addBtn.addEventListener('click', function () {
		const index = list.querySelectorAll('.defect-row').length;
		const wrapper = document.createElement('div');
		wrapper.className = 'row g-2 align-items-end defect-row';
		wrapper.innerHTML = `
			<input type="hidden" name="defects[${index}][defect_id]" value="">
			<div class="col-md-7">
				<label class="form-label">Loại lỗi</label>
				<select class="form-select" name="defects[${index}][defect_type_id]">
					<option value="">-- Select --</option>
					${buildTypeOptions()}
				</select>
			</div>
			<div class="col-md-3">
				<label class="form-label">Số lượng đơn vị</label>
				<input class="form-control" type="number" min="1" name="defects[${index}][qty_units]" value="">
			</div>
			<div class="col-md-2 d-grid">
				<button type="button" class="btn btn-outline-danger remove-defect">Xóa</button>
			</div>
		`;

		list.appendChild(wrapper);
		const removeBtn = wrapper.querySelector('.remove-defect');
		if (removeBtn) {
			bindRemove(removeBtn);
		}

		updatePreview();
	});

	updatePreview();
}

initWarehouseBoxAdd();
initQcInspect();
initAdminQcResultSave();
