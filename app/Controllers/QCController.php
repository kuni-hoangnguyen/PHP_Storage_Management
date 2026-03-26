<?php
declare (strict_types = 1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;

final class QCController extends Controller
{
    public function index(): void
    {
        $this->view('qc/index', ['title' => 'Quality Control']);
    }

    public function batchList(): void
    {
        $pdo  = Database::getInstance();
        $stmt = $pdo->prepare(
            'SELECT
                b.batch_code,
                b.supplier_code,
                b.product_type,
                b.import_date,
                b.status,
                p.product_name,
                SUM(total_units) AS total_units
            FROM batches b
            JOIN product_types p ON b.product_type = p.product_code
            LEFT JOIN boxes ON b.batch_code = boxes.batch_code
            WHERE b.status IN ("pending_qc", "in_progress")
            GROUP BY
                b.batch_code,
                b.supplier_code,
                b.product_type,
                b.import_date,
                b.status,
                p.product_name
            ORDER BY b.import_date DESC;'
        );
        $stmt2 = $pdo->prepare(
            'SELECT
                b.batch_code,
                b.supplier_code,
                b.product_type,
                b.import_date,
                b.status,
                p.product_name,
                r.ok_units,
                r.ng_units
            FROM batches b
            JOIN product_types p ON b.product_type = p.product_code
            JOIN qc_results r ON b.batch_code = r.batch_code
            WHERE b.status IN ("completed")
            GROUP BY
                b.batch_code,
                b.supplier_code,
                b.product_type,
                b.import_date,
                b.status,
                p.product_name,
                r.ok_units,
                r.ng_units
            ORDER BY b.import_date DESC;'
        );
        $stmt3 = $pdo->prepare(
            'SELECT
                b.batch_code,
                b.supplier_code,
                b.product_type,
                b.import_date,
                b.status,
                p.product_name,
                r.ok_units,
                r.ng_units
            FROM batches b
            JOIN product_types p ON b.product_type = p.product_code
            JOIN qc_results r ON b.batch_code = r.batch_code
            WHERE b.status IN ("rejected")
            GROUP BY
                b.batch_code,
                b.supplier_code,
                b.product_type,
                b.import_date,
                b.status,
                p.product_name,
                r.ok_units,
                r.ng_units
            ORDER BY b.import_date DESC;'
        );
        $stmt->execute();
        $pendingBatches = $stmt->fetchAll();
        $stmt2->execute();
        $completedBatches = $stmt2->fetchAll();
        $stmt3->execute();
        $rejectedBatches = $stmt3->fetchAll();

        $this->view('qc/batch_list', [
            'title'            => 'Batch List',
            'pendingBatches'   => $pendingBatches,
            'completedBatches' => $completedBatches,
            'rejectedBatches'  => $rejectedBatches,
        ]);
    }

    public function inspectBatch(): void
    {
        $batchCode = trim((string) ($_GET['batch_code'] ?? ''));

        $pdo = Database::getInstance();

        $batchStmt = $pdo->prepare(
            'SELECT b.*,
            s.supplier_name,
            p.product_name
            FROM batches b
            JOIN suppliers s ON b.supplier_code = s.supplier_code
            JOIN product_types p ON b.product_type = p.product_code
            WHERE batch_code = :batch_code
            LIMIT 1'
        );
        $boxesStmt  = $pdo->prepare('SELECT * FROM boxes WHERE batch_code = :batch_code ORDER BY box_code ASC');
        $defectStmt = $pdo->query('SELECT * FROM defect_types');
        $statusStmt =$pdo->prepare('UPDATE batches SET status = :status WHERE batch_code = :batch_code');

        $batchStmt->execute(['batch_code' => $batchCode]);
        $boxesStmt->execute(['batch_code' => $batchCode]);
        $statusStmt->execute(['status' => 'in_progress', 'batch_code' => $batchCode]);

        $batch     = $batchStmt->fetch();
        $boxes     = $boxesStmt->fetchAll();
        $box_count = count($boxes);
        $defects   = $defectStmt->fetchAll();

        $this->view('qc/inspect', [
            'title'     => 'Inspect Batch',
            'batch'     => $batch,
            'boxes'     => $boxes,
            'box_count' => $box_count,
            'defects'   => $defects,
            'batchCode' => $batchCode,
        ]);
    }

    public function submitInspection(): void
    {
        $batchCode = trim((string) ($_POST['batch_code'] ?? ''));
        $okUnits   = (int) ($_POST['ok_units'] ?? 0);
        $ngUnits   = (int) ($_POST['ng_units'] ?? 0);
        $defects   = $_POST['defects'] ?? [];

        if ($batchCode === '') {
            http_response_code(422);
            die('batch_code là bắt buộc.');
        }

        $pdo             = Database::getInstance();
        $batchExistsStmt = $pdo->prepare(
            'SELECT 1
            FROM batches
            WHERE batch_code = :batch_code
            LIMIT 1');
        $defectStmt = $pdo->prepare(
            'INSERT INTO defect_records (batch_code, defect_type_id, qty_units)
            VALUES (:batch_code, :defect_type_id, :qty_units)');
        $resultsStmt = $pdo->prepare(
            'INSERT INTO qc_results (batch_code, ok_units, ng_units, inspected_by)
            VALUES (:batch_code, :ok_units, :ng_units, :inspected_by)');
        $updateBatchStmt = $pdo->prepare(
            'UPDATE batches
            SET status = :status
            WHERE batch_code = :batch_code');

        $batchExistsStmt->execute(['batch_code' => $batchCode]);
        if ($batchExistsStmt->fetch() === false) {
            http_response_code(422);
            die('batch_code không tồn tại.');
        }

        try {
            $pdo->beginTransaction();

            foreach ($defects as $defect) {
                $defect_type_id = (int) ($defect['defect_type_id'] ?? 0);
                $qty_units      = (int) ($defect['qty_units'] ?? 0);

                if ($defect_type_id > 0 && $qty_units > 0) {
                    $defectStmt->execute([
                        'batch_code'     => $batchCode,
                        'defect_type_id' => $defect_type_id,
                        'qty_units'      => $qty_units,
                    ]);
                }
            }

            $resultsStmt->execute([
                'batch_code'   => $batchCode,
                'ok_units'     => $okUnits,
                'ng_units'     => $ngUnits,
                'inspected_by' => $_SESSION['user_id'] ?? null,
            ]);

            $newStatus = ($ngUnits / ($okUnits + $ngUnits) >= 0.3) ? 'rejected' : 'completed';
            $updateBatchStmt->execute([
                'status'     => $newStatus,
                'batch_code' => $batchCode,
            ]);

            $pdo->commit();

            header('Location: /qc/batches');
            exit;
        } catch (\Exception $e) {
            $pdo->rollBack();
            die('Error processing inspection: ' . htmlspecialchars((string) $e->getMessage(), ENT_QUOTES, 'UTF-8'));
        }
    }

    public function viewResult(): void
    {
        $batchCode = trim((string) ($_GET['batch_code'] ?? ''));

        $pdo       = Database::getInstance();
        $batchStmt = $pdo->prepare(
            'SELECT b.*,
            s.supplier_name,
            p.product_name,
            r.ok_units,
            r.ng_units,
            u.username as inspected_by
            FROM batches b
            JOIN suppliers s ON b.supplier_code = s.supplier_code
            JOIN product_types p ON b.product_type = p.product_code
            JOIN qc_results r ON b.batch_code = r.batch_code
            JOIN users u ON r.inspected_by = u.id
            WHERE b.batch_code = :batch_code
            LIMIT 1'
        );
        $defectsStmt = $pdo->prepare(
            'SELECT d.name AS defect_name, dr.qty_units
            FROM defect_records dr
            JOIN defect_types d ON dr.defect_type_id = d.defect_type_id
            WHERE dr.batch_code = :batch_code'
        );
        $batchStmt->execute(['batch_code' => $batchCode]);
        $batch   = $batchStmt->fetch();
        $defects = [];
        if ($batch) {
            $defectsStmt->execute(['batch_code' => $batchCode]);
            $defects = $defectsStmt->fetchAll();
        }

        $this->view('qc/qc_result', [
            'title'   => 'QC Result',
            'batch'   => $batch,
            'defects' => $defects,
        ]);
    }
}
