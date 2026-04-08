<?php

declare (strict_types = 1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;

final class ManagerController extends Controller
{
    public function index(): void
    {
        $this->view('manager/index', ['title' => 'Manager Dashboard']);
    }

    public function showBatchesList(): void
    {
        $pdo = Database::getInstance();

        $perPage = 10;
        $page    = max(1, (int) ($_GET['page'] ?? 1));

        $supplierCode = trim((string) ($_GET['supplier_code'] ?? ''));
        $productType  = trim((string) ($_GET['product_type'] ?? ''));
        $status       = trim((string) ($_GET['status'] ?? 'all'));
        $code         = trim((string) ($_GET['code'] ?? ''));
        $fromDate     = trim((string) ($_GET['from_date'] ?? ''));
        $toDate       = trim((string) ($_GET['to_date'] ?? ''));

        $allowedStatuses = ['all', 'completed', 'rejected'];
        if (! in_array($status, $allowedStatuses, true)) {
            $status = 'completed';
        }

        $whereParts = [];
        $params     = [];

        if ($status === 'all') {
            $whereParts[] = 'b.status IN ("completed", "rejected")';
        } else {
            $whereParts[]     = 'b.status = :status';
            $params['status'] = $status;
        }

        if ($code !== '') {
            $whereParts[]   = 'b.batch_code LIKE :code';
            $params['code'] = '%' . $code . '%';
        }

        if ($supplierCode !== '') {
            $whereParts[]            = 'b.supplier_code = :supplier_code';
            $params['supplier_code'] = $supplierCode;
        }

        if ($productType !== '') {
            $whereParts[]           = 'b.product_type = :product_type';
            $params['product_type'] = $productType;
        }

        if ($fromDate !== '') {
            $whereParts[]        = 'b.import_date >= :from_date';
            $params['from_date'] = $fromDate;
        }

        if ($toDate !== '') {
            $whereParts[]      = 'b.import_date <= :to_date';
            $params['to_date'] = $toDate;
        }

        $whereSql = $whereParts !== [] ? 'WHERE ' . implode(' AND ', $whereParts) : '';

        $suppliers    = $pdo->query('SELECT supplier_code, supplier_name FROM suppliers ORDER BY supplier_name ASC')->fetchAll();
        $productTypes = $pdo->query('SELECT product_code, product_name FROM product_types ORDER BY product_name ASC')->fetchAll();

        $countStmt = $pdo->prepare(
            'SELECT COUNT(*)
            FROM batches b
            JOIN qc_results r ON b.batch_code = r.batch_code
            ' . $whereSql
        );
        $countStmt->execute($params);
        $totalRows  = (int) $countStmt->fetchColumn();
        $totalPages = max(1, (int) ceil($totalRows / $perPage));

        if ($page > $totalPages) {
            $page = $totalPages;
        }

        $offset = ($page - 1) * $perPage;

        $statsStmt = $pdo->prepare(
            'SELECT
                COUNT(*) AS total_batches,
                COALESCE(SUM(r.ok_units), 0) AS total_ok_units,
                COALESCE(SUM(r.ng_units), 0) AS total_ng_units
            FROM batches b
            JOIN qc_results r ON b.batch_code = r.batch_code
            ' . $whereSql
        );
        $statsStmt->execute($params);
        $stats = $statsStmt->fetch();

        $totalOkUnits = (int) ($stats['total_ok_units'] ?? 0);
        $totalNgUnits = (int) ($stats['total_ng_units'] ?? 0);
        $grandTotal   = $totalOkUnits + $totalNgUnits;
        $ngRate       = $grandTotal > 0 ? round(($totalNgUnits / $grandTotal) * 100, 2) : 0.0;

        $stmt = $pdo->prepare(
            'SELECT
                b.batch_code,
                b.supplier_code,
                s.supplier_name,
                b.product_type,
                b.import_date,
                b.updated_at,
                b.status,
                p.product_name,
                r.ok_units,
                r.ng_units
            FROM batches b
            JOIN suppliers s ON b.supplier_code = s.supplier_code
            JOIN product_types p ON b.product_type = p.product_code
            JOIN qc_results r ON b.batch_code = r.batch_code
            ' . $whereSql . '
            ORDER BY b.updated_at DESC
            LIMIT :limit OFFSET :offset'
        );

        foreach ($params as $key => $value) {
            $stmt->bindValue(':' . $key, (string) $value, \PDO::PARAM_STR);
        }
        $stmt->bindValue(':limit', $perPage, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();
        $completedBatches = $stmt->fetchAll();

        $filterQuery = [
            'code'          => $code,
            'supplier_code' => $supplierCode,
            'product_type'  => $productType,
            'status'        => $status,
            'from_date'     => $fromDate,
            'to_date'       => $toDate,
        ];

        $this->view('manager/batch_list', [
            'title'               => 'Danh sách lô hàng',
            'completedBatches'    => $completedBatches,
            'completedPage'       => $page,
            'completedTotalPages' => $totalPages,
            'completedTotalRows'  => $totalRows,
            'suppliers'           => $suppliers,
            'productTypes'        => $productTypes,
            'filterQuery'         => $filterQuery,
            'totalOkUnits'        => $totalOkUnits,
            'totalNgUnits'        => $totalNgUnits,
            'ngRate'              => $ngRate,
        ]);
    }

    public function viewDetail(): void
    {
        $batchCode = trim((string) ($_GET['batch_code'] ?? ''));

        $pdo = Database::getInstance();

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
            LEFT JOIN users u ON r.inspected_by = u.id
            WHERE b.batch_code = :batch_code
            LIMIT 1'
        );

        $defectsStmt = $pdo->prepare(
            'SELECT d.name AS defect_name, dr.qty_units
            FROM defect_records dr
            JOIN defect_types d ON dr.defect_type_id = d.defect_type_id
            WHERE dr.batch_code = :batch_code'
        );

        $boxesStmt = $pdo->prepare(
            'SELECT box_code, tray_count, unit_per_tray, total_units
            FROM boxes
            WHERE batch_code = :batch_code
            ORDER BY box_code ASC'
        );

        $batchStmt->execute(['batch_code' => $batchCode]);
        $batch = $batchStmt->fetch();

        $defects = [];
        $boxes   = [];
        if ($batch) {
            $defectsStmt->execute(['batch_code' => $batchCode]);
            $defects = $defectsStmt->fetchAll();

            $boxesStmt->execute(['batch_code' => $batchCode]);
            $boxes = $boxesStmt->fetchAll();

        }

        $this->view('manager/detail', [
            'title'   => 'Chi tiết kiểm định',
            'batch'   => $batch,
            'defects' => $defects,
            'boxes'   => $boxes,
        ]);
    }
}
