<?php

declare (strict_types = 1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;

final class WarehouseController extends Controller
{
    public function index(): void
    {
        $this->view('warehouse/index', ['title' => 'Warehouse Management']);
    }

    public function createBatch(): void
    {
        $pdo = Database::getInstance();

        $supplierStmt = $pdo->query('SELECT supplier_code, supplier_name FROM suppliers ORDER BY supplier_name ASC');
        $productStmt  = $pdo->query('SELECT product_code, product_name FROM product_types ORDER BY product_name ASC');

        $suppliers     = $supplierStmt->fetchAll();
        $product_types = $productStmt->fetchAll();

        $this->view('warehouse/batch_create', ['title' => 'Create New Batch', 'suppliers' => $suppliers, 'product_types' => $product_types]);
    }

    public function storeBatch(): void
    {
        $supplierCode = $_POST['supplier_code'] ?? '';
        $productType  = $_POST['product_type'] ?? '';
        $importDate   = $_POST['import_date'] ?? '';

        // Validate input

        //Generate batch code
        $pdo  = Database::getInstance();
        $stmt = $pdo->prepare('SELECT COUNT(*) as count from batches WHERE import_date = :import_date');
        $stmt->execute(['import_date' => $importDate]);
        $batchNum = $stmt->fetch();

        $batchCode = sprintf('Q%d-%s-%s', $batchNum['count'] + 1, date('Ymd', strtotime($importDate)), $supplierCode);

        // Insert into database
        $stmt = $pdo->prepare('INSERT INTO batches (batch_code, supplier_code, product_type, import_date) VALUES (:batch_code, :supplier_code, :product_type, :import_date)');
        $stmt->execute([
            'batch_code'    => $batchCode,
            'supplier_code' => $supplierCode,
            'product_type'  => $productType,
            'import_date'   => $importDate,
        ]);

        //Redirect to box add page with batch code
        header('Location: /warehouse/box_add?batch_code=' . urlencode($batchCode));
        exit;
    }

    public function showAddBoxForm(): void
    {
        $batchCode = trim((string) ($_GET['batch_code'] ?? ''));
        $oldInput  = $_SESSION['old_input'] ?? [];
        $error     = $_SESSION['form_error'] ?? null;

        unset($_SESSION['old_input'], $_SESSION['form_error']);

        $pdo       = Database::getInstance();
        $batchStmt = $pdo->prepare(
            'SELECT
        b.*,
        s.supplier_name,
        p.product_name
        FROM batches b
        JOIN suppliers s ON b.supplier_code = s.supplier_code
        JOIN product_types p ON b.product_type = p.product_code
        WHERE b.batch_code = :batch_code
        LIMIT 1');
        $boxesStmt = $pdo->prepare(
            'SELECT *
            FROM boxes
            WHERE batch_code = :batch_code
            ORDER BY box_code ASC');
        $batchStmt->execute(['batch_code' => $batchCode]);
        $boxesStmt->execute(['batch_code' => $batchCode]);
        $batch = $batchStmt->fetch();
        $boxes = $boxesStmt->fetchAll();

        $this->view('warehouse/box_add', [
            'title'    => 'Add Box to Batch',
            'batch'    => $batch,
            'oldInput' => is_array($oldInput) ? $oldInput : [],
            'error'    => is_string($error) ? $error : null,
            'boxes'    => $boxes,
        ]);
    }

    public function showBatchesList(): void
    {
        $pdo     = Database::getInstance();
        $perPage = 10;
        $page    = max(1, (int) ($_GET['page'] ?? '1'));
        $offset  = ($page - 1) * $perPage;

        $countStmt  = $pdo->query('SELECT COUNT(*) FROM batches');
        $totalRows  = (int) $countStmt->fetchColumn();
        $totalPages = max(1, (int) ceil($totalRows / $perPage));

        if ($page > $totalPages) {
            $page   = $totalPages;
            $offset = ($page - 1) * $perPage;
            exit;
        }

        $stmt = $pdo->prepare(
            'SELECT
                b.batch_code,
                b.supplier_code,
                b.product_type,
                b.import_date,
                b.status,
                s.supplier_name,
                p.product_name,
                COUNT(bx.box_code) AS box_count,
                COALESCE(SUM(bx.total_units), 0) AS total_units
            FROM batches b
            JOIN suppliers s ON b.supplier_code = s.supplier_code
            JOIN product_types p ON b.product_type = p.product_code
            LEFT JOIN boxes bx ON b.batch_code = bx.batch_code
            GROUP BY
                b.batch_code,
                b.supplier_code,
                b.product_type,
                b.import_date,
                b.status,
                s.supplier_name,
                p.product_name
            ORDER BY b.created_at DESC
            LIMIT :limit OFFSET :offset'
        );
        $stmt->execute([
            'limit'  => $perPage,
            'offset' => $offset,
        ]);
        $batches = $stmt->fetchAll();

        $this->view('warehouse/batch_list', [
            'title'      => 'Batch List',
            'batches'    => $batches,
            'page'       => $page,
            'totalPages' => $totalPages,
            'totalRows'  => $totalRows,
            'perPage'    => $perPage,
        ]);
    }

    public function showDetail(): void
    {
        $batchCode = trim((string) ($_GET['batch_code'] ?? ''));

        $pdo = Database::getInstance();

        $batchStmt = $pdo->prepare(
            'SELECT
        b.*,
        s.supplier_name,
        p.product_name
        FROM batches b
        JOIN suppliers s ON b.supplier_code = s.supplier_code
        JOIN product_types p ON b.product_type = p.product_code
        WHERE b.batch_code = :batch_code
        LIMIT 1');
        $boxesStmt = $pdo->prepare('SELECT * FROM boxes WHERE batch_code = :batch_code ORDER BY box_code ASC');

        $batchStmt->execute(['batch_code' => $batchCode]);
        $boxesStmt->execute(['batch_code' => $batchCode]);

        $batch = $batchStmt->fetch();
        $boxes = $boxesStmt->fetchAll();

        $totalUnits = 0;
        foreach ($boxes as $box) {
            $totalUnits += (int) $box['total_units'];
        }

        $this->view('warehouse/batch_detail', [
            'title'      => 'Batch Detail',
            'batch'      => $batch,
            'boxes'      => $boxes,
            'totalUnits' => $totalUnits,
        ]);
    }

    public function updateBatchStatus(): void
    {
        $batchCode  = trim((string) ($_GET['batch_code'] ?? ''));
        $pdo        = Database::getInstance();
        $updateStmt = $pdo->prepare('UPDATE batches SET status = :status WHERE batch_code = :batch_code');
        $updateStmt->execute([
            'status'     => 'pending_qc',
            'batch_code' => $batchCode,
        ]);
        header('Location: /warehouse/detail?batch_code=' . urlencode($batchCode));
        exit;
    }

    public function addBox(): void
    {
        $batchCode = trim((string) ($_POST['batch_code'] ?? ''));
        $boxes     = $_POST['boxes'] ?? [];

        if ($batchCode === '' || ! is_array($boxes) || $boxes === []) {
            $this->redirectBackToBoxForm($batchCode, $_POST, 'Dữ liệu không hợp lệ.');
        }

        $pdo = Database::getInstance();

        $batchExistsStmt = $pdo->prepare('SELECT 1 FROM batches WHERE batch_code = :batch_code LIMIT 1');
        $batchExistsStmt->execute(['batch_code' => $batchCode]);
        if ($batchExistsStmt->fetch() === false) {
            $this->redirectBackToBoxForm($batchCode, $_POST, 'Lô hàng không tồn tại.');
        }

        $seenBoxCodes  = [];
        $preparedBoxes = [];

        foreach ($boxes as $index => $box) {
            if (! is_array($box)) {
                $this->redirectBackToBoxForm($batchCode, $_POST, 'Dữ liệu không hợp lệ.');
            }

            $boxCode  = trim((string) ($box['box_code'] ?? ''));
            $trayRaw  = trim((string) ($box['tray_count'] ?? ''));
            $unitRaw  = trim((string) ($box['unit_per_tray'] ?? ''));
            $totalRaw = trim((string) ($box['total_units'] ?? ''));

            if ($boxCode === '') {
                $this->redirectBackToBoxForm($batchCode, $_POST, 'Mã thùng không được để trống.');
            }

            $normalizedCode = strtoupper($boxCode);
            if (isset($seenBoxCodes[$normalizedCode])) {
                $this->redirectBackToBoxForm($batchCode, $_POST, 'Mã thùng bị trùng trong form: ' . $boxCode . '.');
            }
            $seenBoxCodes[$normalizedCode] = true;

            $tray  = $this->toPositiveIntOrNull($trayRaw);
            $unit  = $this->toPositiveIntOrNull($unitRaw);
            $total = $this->toPositiveIntOrNull($totalRaw);

            if ($total === null && ($tray === null || $unit === null)) {
                $this->redirectBackToBoxForm($batchCode, $_POST, 'Nếu không nhập tổng sản phẩm thì phải nhập số khay và số sản phẩm/khay (dòng ' . ((int) $index + 1) . ').');
            }

            if ($total === null) {
                $total = $tray * $unit;
            }

            $preparedBoxes[] = [
                'batch_code'    => $batchCode,
                'box_code'      => $boxCode,
                'total_units'   => $total,
                'tray_count'    => $tray,
                'unit_per_tray' => $unit,
            ];
        }

        $allBoxCodes  = array_column($preparedBoxes, 'box_code');
        $placeholders = implode(', ', array_fill(0, count($allBoxCodes), '?'));
        $existsStmt   = $pdo->prepare('SELECT box_code, batch_code FROM boxes WHERE box_code IN (' . $placeholders . ')');
        $existsStmt->execute($allBoxCodes);
        $existingRows = $existsStmt->fetchAll();

        $existingByBoxCode = [];
        foreach ($existingRows as $existingRow) {
            $existingKey                       = strtoupper((string) ($existingRow['box_code'] ?? ''));
            $existingByBoxCode[$existingKey] = (string) ($existingRow['batch_code'] ?? '');
        }

        $conflictingCodes = [];
        $insertRows       = [];
        $updateRows       = [];

        foreach ($preparedBoxes as $row) {
            $rowKey        = strtoupper((string) $row['box_code']);
            $existingBatch = $existingByBoxCode[$rowKey] ?? null;

            if ($existingBatch === null) {
                $insertRows[] = $row;
                continue;
            }

            if ($existingBatch !== $batchCode) {
                $conflictingCodes[] = (string) $row['box_code'];
                continue;
            }

            $updateRows[] = $row;
        }

        if ($conflictingCodes !== []) {
            $this->redirectBackToBoxForm(
                $batchCode,
                $_POST,
                'Mã thùng đã tồn tại ở lô khác: ' . implode(', ', array_map('strval', $conflictingCodes)) . '.'
            );
        }

        $insertStmt = $pdo->prepare(
            'INSERT INTO boxes (batch_code, box_code, total_units, tray_count, unit_per_tray) VALUES (:batch_code, :box_code, :total_units, :tray_count, :unit_per_tray)'
        );
        $updateStmt = $pdo->prepare(
            'UPDATE boxes SET total_units = :total_units, tray_count = :tray_count, unit_per_tray = :unit_per_tray WHERE batch_code = :batch_code AND box_code = :box_code'
        );

        $pdo->beginTransaction();
        try {
            foreach ($updateRows as $row) {
                $updateStmt->execute($row);
            }

            foreach ($insertRows as $row) {
                $insertStmt->execute($row);
            }
            $pdo->commit();
        } catch (\Throwable $exception) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $this->redirectBackToBoxForm($batchCode, $_POST, 'Đã có lỗi xảy ra khi lưu dữ liệu. Vui lòng thử lại.');
        }

        header('Location: /warehouse/batches');
        exit;
    }

    private function toPositiveIntOrNull(string $value): ?int
    {
        if ($value === '') {
            return null;
        }

        if (! preg_match('/^[0-9]+$/', $value)) {
            return null;
        }

        $number = (int) $value;
        return $number > 0 ? $number : null;
    }

    /**
     * @param array<string, mixed> $oldInput
     */
    private function redirectBackToBoxForm(string $batchCode, array $oldInput, string $message): void
    {
        $_SESSION['old_input']  = $oldInput;
        $_SESSION['form_error'] = $message;

        header('Location: /warehouse/box_add?batch_code=' . urlencode($batchCode));
        exit;

    }
}
