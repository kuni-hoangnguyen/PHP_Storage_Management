<?php

declare (strict_types = 1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;

final class AdminController extends Controller
{
    public function index(): void
    {
        $this->view('admin/index', ['title' => 'Admin Dashboard']);
    }

    public function manageUsers(): void
    {
        $rolemap = [
            ''                => 'Tất cả vai trò',
            'admin'           => 'Admin',
            'manager'         => 'Quản lý',
            'warehouse_staff' => 'Nhân viên kho',
            'qc_staff'        => 'Nhân viên kiểm tra chất lượng',
        ];

        $keyword = isset($_GET['keyword']) ? trim((string) $_GET['keyword']) : '';
        $role    = isset($_GET['role']) ? trim((string) $_GET['role']) : '';
        $active  = isset($_GET['active']) ? trim((string) $_GET['active']) : '';

        $whereParts = [];
        $params     = [];
        if (isset($keyword) && $keyword !== '') {
            $whereParts[]      = '(username LIKE :keyword)';
            $params['keyword'] = '%' . $keyword . '%';
        }
        if (isset($role) && $role !== '') {
            $whereParts[]   = 'role = :role';
            $params['role'] = trim((string) $role);
        }
        if (isset($active) && $active !== '') {
            $whereParts[]     = 'is_active = :active';
            $params['active'] = trim((string) $active);
        }

        $pdo = Database::getInstance();

        $whereSql = $whereParts !== [] ? ' WHERE ' . implode(' AND ', $whereParts) : '';
        $sql      = 'SELECT id, username, role, is_active FROM users' . $whereSql . ' ORDER BY id DESC';

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $users = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $this->view('admin/users', [
            'title'   => 'Manage Users',
            'users'   => $users,
            'rolemap' => $rolemap,
        ]);
    }

    public function saveUserForm(): void
    {
        $pdo     = Database::getInstance();
        $user_id = isset($_GET['id']) ? (int) $_GET['id'] : null;

        $user = null;
        if ($user_id !== null && $user_id > 0) {
            $stmt = $pdo->prepare('SELECT id, username, role, is_active FROM users WHERE id = :id');
            $stmt->execute(['id' => $user_id]);
            $user = $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
        }

        $this->view('admin/user_save', [
            'title'   => 'Add/Edit User',
            'user_id' => $user_id,
            'user'    => $user,
        ]);
    }

    public function saveUser(): void
    {
        $pdo = Database::getInstance();

        $user_id  = isset($_POST['user_id']) && $_POST['user_id'] !== '' ? (int) $_POST['user_id'] : null;
        $username = trim((string) ($_POST['username'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');
        $role     = (string) ($_POST['role'] ?? '');
        $isActive = isset($_POST['is_active']) && (string) $_POST['is_active'] === '0' ? 0 : 1;

        $allowedRoles = ['admin', 'manager', 'warehouse_staff', 'qc_staff'];
        $isEdit       = $user_id !== null && $user_id > 0;

        $errors = [];

        if ($username == '') {
            $errors[] = 'Tên đăng nhập không được để trống.';
        }

        if (! in_array($role, $allowedRoles, true)) {
            $errors[] = 'Vai trò không hợp lệ.';
        }

        if (! $isEdit && $password === '') {
            $errors[] = 'Mật khẩu là bắt buộc khi tạo mới tài khoản.';
        }

        if ($isEdit && isset($_SESSION['user_id']) && (int) $_SESSION['user_id'] === $user_id && $isActive === 0) {
            $errors[] = 'Bạn không thể tự vô hiệu hóa chính mình.';
        }

        $existingUser = null;
        if ($isEdit) {
            $findStmt = $pdo->prepare('SELECT id, username, role, is_active FROM users WHERE id = :id');
            $findStmt->execute(['id' => $user_id]);
            $existingUser = $findStmt->fetch(\PDO::FETCH_ASSOC) ?: null;
            if ($existingUser === null) {
                $errors[] = 'Tài khoản không tồn tại.';
            }
        }

        $usernameCheckStmt = $pdo->prepare('SELECT id FROM users WHERE username = :username LIMIT 1');
        $usernameCheckStmt->execute(['username' => $username]);
        $usernameOwner = $usernameCheckStmt->fetchColumn();
        if ($usernameOwner !== false && (! $isEdit || (int) $usernameOwner !== $user_id)) {
            $errors[] = 'Tên đăng nhập đã tồn tại.';
        }

        if ($errors !== []) {
            $this->view('admin/user_save', [
                'title'  => 'Add/Edit User',
                'user'   => $existingUser,
                'old'    => [
                    'user_id'   => $user_id !== null ? (string) $user_id : '',
                    'username'  => $username,
                    'role'      => $role,
                    'is_active' => (string) $isActive,
                ],
                'errors' => $errors,
            ]);
            return;
        }

        $passwordHash = $password !== '' ? password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]) : null;

        if ($isEdit) {
            if ($passwordHash !== null) {
                $stmt = $pdo->prepare('UPDATE users SET username = :username, password_hash = :password_hash, role = :role, is_active = :is_active WHERE id = :id');
                $stmt->execute([
                    'username'      => $username,
                    'password_hash' => $passwordHash,
                    'role'          => $role,
                    'is_active'     => $isActive,
                    'id'            => $user_id,
                ]);
            } else {
                $stmt = $pdo->prepare('UPDATE users SET username = :username, role = :role, is_active = :is_active WHERE id = :id');
                $stmt->execute([
                    'username'  => $username,
                    'role'      => $role,
                    'is_active' => $isActive,
                    'id'        => $user_id,
                ]);
            }
        } else {
            $stmt = $pdo->prepare('INSERT INTO users (username, password_hash, role, is_active) VALUES (:username, :password_hash, :role, :is_active)');
            $stmt->execute([
                'username'      => $username,
                'password_hash' => $passwordHash,
                'role'          => $role,
                'is_active'     => $isActive,
            ]);
        }

        $pdo->commit();
        header('Location: /admin/users');
        exit;
    }

    public function manageCrud(): void
    {
        $pdo = Database::getInstance();

        $defectTypes = $pdo->query('SELECT defect_type_id, name, description, is_active FROM defect_types WHERE deleted_at IS NULL ORDER BY defect_type_id ASC')->fetchAll(\PDO::FETCH_ASSOC);
        $productTypes = $pdo->query('SELECT product_type_id, product_code, product_name, is_active FROM product_types WHERE deleted_at IS NULL ORDER BY product_type_id ASC')->fetchAll(\PDO::FETCH_ASSOC);
        $suppliers = $pdo->query('SELECT supplier_id, supplier_code, supplier_name, is_active FROM suppliers WHERE deleted_at IS NULL ORDER BY supplier_id ASC')->fetchAll(\PDO::FETCH_ASSOC);

        $this->view('admin/crud', [
            'title'       => 'Manage Catalogs',
            'defectTypes' => $defectTypes,
            'productTypes'=> $productTypes,
            'suppliers'   => $suppliers,
        ]);
    }

    public function saveDefectTypeForm(): void
    {
        $pdo            = Database::getInstance();
        $defect_type_id = isset($_GET['id']) ? (int) $_GET['id'] : null;
        $defect_type    = null;

        if ($defect_type_id !== null && $defect_type_id > 0) {
            $stmt = $pdo->prepare('SELECT defect_type_id, name, description, is_active FROM defect_types WHERE defect_type_id = :id AND deleted_at IS NULL');
            $stmt->execute(['id' => $defect_type_id]);
            $defect_type = $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
        }

        $this->view('admin/defect_type_save', [
            'title'      => 'Add/Edit Defect Type',
            'defectType' => $defect_type,
        ]);

    }

    public function saveDefectType(): void
    {
        $pdo = Database::getInstance();

        $defect_type_id = isset($_POST['defect_type_id']) && $_POST['defect_type_id'] !== '' ? (int) $_POST['defect_type_id'] : null;
        $name           = trim((string) ($_POST['name'] ?? ''));
        $description    = trim((string) ($_POST['description'] ?? ''));
        $isActive       = isset($_POST['is_active']) && (string) $_POST['is_active'] === '0' ? 0 : 1;

        $allowedStatuses = [0, 1];
        $isEdit          = $defect_type_id !== null && $defect_type_id > 0;

        $errors = [];

        if ($name == '') {
            $errors[] = 'Tên loại lỗi không được để trống.';
        }

        if (! in_array($isActive, $allowedStatuses, true)) {
            $errors[] = 'Trạng thái hiển thị không hợp lệ.';
        }

        if ($isEdit) {
            $findStmt = $pdo->prepare('SELECT defect_type_id, name, description, is_active FROM defect_types WHERE defect_type_id = :id AND deleted_at IS NULL');
            $findStmt->execute(['id' => $defect_type_id]);
            $existingDefectType = $findStmt->fetch(\PDO::FETCH_ASSOC) ?: null;
            if ($existingDefectType === null) {
                $errors[] = 'Loại lỗi không tồn tại.';
            }
        }

        if ($errors !== []) {
            $this->view('admin/defect_type_save', [
                'title'      => 'Add/Edit Defect Type',
                'defectType' => isset($existingDefectType) ? $existingDefectType : null,
                'old'        => [
                    'defect_type_id' => $defect_type_id !== null ? (string) $defect_type_id : '',
                    'name'           => $name,
                    'description'    => $description,
                    'is_active'      => (string) $isActive,
                ],
                'errors'     => $errors,
            ]);
            return;
        }

        if ($isEdit) {
            $stmt = $pdo->prepare('UPDATE defect_types SET name = :name, description = :description, is_active = :is_active WHERE defect_type_id = :id');
            $stmt->execute([
                'name'        => $name,
                'description' => $description,
                'is_active'   => $isActive,
                'id'          => $defect_type_id,
            ]);
        } else {
            $stmt = $pdo->prepare('INSERT INTO defect_types (name, description, is_active) VALUES (:name, :description, :is_active)');
            $stmt->execute([
                'name'        => $name,
                'description' => $description,
                'is_active'   => $isActive,
            ]);
        }

        header('Location: /admin/crud');
        exit;
    }

    public function deleteDefectType(): void
    {
        $id   = isset($_POST['id']) ? (int) $_POST['id'] : null;
        $pdo  = Database::getInstance();
        $stmt = $pdo->prepare('UPDATE defect_types SET deleted_at = NOW(), is_active = 0 WHERE defect_type_id = :id');
        $stmt->execute(['id' => $id]);
        header('Location: /admin/crud');
        exit;
    }

    public function saveProductTypeForm(): void
    {
        $pdo = Database::getInstance();
        $id = isset($_GET['id']) ? (int) $_GET['id'] : null;
        $productType = null;

        if ($id !== null && $id > 0) {
            $stmt = $pdo->prepare('SELECT product_type_id, product_code, product_name, is_active FROM product_types WHERE product_type_id = :id AND deleted_at IS NULL');
            $stmt->execute(['id' => $id]);
            $productType = $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
        }

        $this->view('admin/product_type_save', [
            'title' => 'Add/Edit Product Type',
            'productType' => $productType,
        ]);
    }

    public function saveProductType(): void
    {
        $pdo = Database::getInstance();

        $id = isset($_POST['product_type_id']) && $_POST['product_type_id'] !== '' ? (int) $_POST['product_type_id'] : null;
        $code = trim((string) ($_POST['product_code'] ?? ''));
        $name = trim((string) ($_POST['product_name'] ?? ''));
        $isActive = isset($_POST['is_active']) && (string) $_POST['is_active'] === '0' ? 0 : 1;
        $isEdit = $id !== null && $id > 0;

        $errors = [];
        if ($code === '') {
            $errors[] = 'Mã loại sản phẩm không được để trống.';
        }
        if ($name === '') {
            $errors[] = 'Tên loại sản phẩm không được để trống.';
        }

        $existing = null;
        if ($isEdit) {
            $stmt = $pdo->prepare('SELECT product_type_id, product_code, product_name, is_active FROM product_types WHERE product_type_id = :id AND deleted_at IS NULL');
            $stmt->execute(['id' => $id]);
            $existing = $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
            if ($existing === null) {
                $errors[] = 'Loại sản phẩm không tồn tại.';
            }
        }

        $dupStmt = $pdo->prepare('SELECT product_type_id FROM product_types WHERE product_code = :code LIMIT 1');
        $dupStmt->execute(['code' => $code]);
        $owner = $dupStmt->fetchColumn();
        if ($owner !== false && (!$isEdit || (int) $owner !== $id)) {
            $errors[] = 'Mã loại sản phẩm đã tồn tại.';
        }

        if ($errors !== []) {
            $this->view('admin/product_type_save', [
                'title' => 'Add/Edit Product Type',
                'productType' => $existing,
                'old' => [
                    'product_type_id' => $id !== null ? (string) $id : '',
                    'product_code' => $code,
                    'product_name' => $name,
                    'is_active' => (string) $isActive,
                ],
                'errors' => $errors,
            ]);
            return;
        }

        if ($isEdit) {
            $stmt = $pdo->prepare('UPDATE product_types SET product_code = :product_code, product_name = :product_name, is_active = :is_active WHERE product_type_id = :id');
            $stmt->execute([
                'product_code' => $code,
                'product_name' => $name,
                'is_active' => $isActive,
                'id' => $id,
            ]);
        } else {
            $stmt = $pdo->prepare('INSERT INTO product_types (product_code, product_name, is_active) VALUES (:product_code, :product_name, :is_active)');
            $stmt->execute([
                'product_code' => $code,
                'product_name' => $name,
                'is_active' => $isActive,
            ]);
        }

        header('Location: /admin/crud');
        exit;
    }

    public function deleteProductType(): void
    {
        $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
        if ($id > 0) {
            $pdo = Database::getInstance();
            $stmt = $pdo->prepare('UPDATE product_types SET is_active = 0, deleted_at = NOW() WHERE product_type_id = :id');
            $stmt->execute(['id' => $id]);
        }
        header('Location: /admin/crud');
        exit;
    }

    public function saveSupplierForm(): void
    {
        $pdo = Database::getInstance();
        $id = isset($_GET['id']) ? (int) $_GET['id'] : null;
        $supplier = null;

        if ($id !== null && $id > 0) {
            $stmt = $pdo->prepare('SELECT supplier_id, supplier_code, supplier_name, is_active FROM suppliers WHERE supplier_id = :id');
            $stmt->execute(['id' => $id]);
            $supplier = $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
        }

        $this->view('admin/supplier_save', [
            'title' => 'Add/Edit Supplier',
            'supplier' => $supplier,
        ]);
    }

    public function saveSupplier(): void
    {
        $pdo = Database::getInstance();

        $id = isset($_POST['supplier_id']) && $_POST['supplier_id'] !== '' ? (int) $_POST['supplier_id'] : null;
        $code = trim((string) ($_POST['supplier_code'] ?? ''));
        $name = trim((string) ($_POST['supplier_name'] ?? ''));
        $isActive = isset($_POST['is_active']) && (string) $_POST['is_active'] === '0' ? 0 : 1;
        $isEdit = $id !== null && $id > 0;

        $errors = [];
        if ($code === '') {
            $errors[] = 'Mã nhà cung cấp không được để trống.';
        }
        if ($name === '') {
            $errors[] = 'Tên nhà cung cấp không được để trống.';
        }

        $existing = null;
        if ($isEdit) {
            $stmt = $pdo->prepare('SELECT supplier_id, supplier_code, supplier_name, is_active FROM suppliers WHERE supplier_id = :id AND deleted_at IS NULL');
            $stmt->execute(['id' => $id]);
            $existing = $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
            if ($existing === null) {
                $errors[] = 'Nhà cung cấp không tồn tại.';
            }
        }

        $dupStmt = $pdo->prepare('SELECT supplier_id FROM suppliers WHERE supplier_code = :code LIMIT 1');
        $dupStmt->execute(['code' => $code]);
        $owner = $dupStmt->fetchColumn();
        if ($owner !== false && (!$isEdit || (int) $owner !== $id)) {
            $errors[] = 'Mã nhà cung cấp đã tồn tại.';
        }

        if ($errors !== []) {
            $this->view('admin/supplier_save', [
                'title' => 'Add/Edit Supplier',
                'supplier' => $existing,
                'old' => [
                    'supplier_id' => $id !== null ? (string) $id : '',
                    'supplier_code' => $code,
                    'supplier_name' => $name,
                    'is_active' => (string) $isActive,
                ],
                'errors' => $errors,
            ]);
            return;
        }

        if ($isEdit) {
            $stmt = $pdo->prepare('UPDATE suppliers SET supplier_code = :supplier_code, supplier_name = :supplier_name, is_active = :is_active WHERE supplier_id = :id');
            $stmt->execute([
                'supplier_code' => $code,
                'supplier_name' => $name,
                'is_active' => $isActive,
                'id' => $id,
            ]);
        } else {
            $stmt = $pdo->prepare('INSERT INTO suppliers (supplier_code, supplier_name, is_active) VALUES (:supplier_code, :supplier_name, :is_active)');
            $stmt->execute([
                'supplier_code' => $code,
                'supplier_name' => $name,
                'is_active' => $isActive,
            ]);
        }

        header('Location: /admin/crud');
        exit;
    }

    public function deleteSupplier(): void
    {
        $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
        if ($id > 0) {
            $pdo = Database::getInstance();
            $stmt = $pdo->prepare('UPDATE suppliers SET is_active = 0, deleted_at = NOW() WHERE supplier_id = :id');
            $stmt->execute(['id' => $id]);
        }
        header('Location: /admin/crud');
        exit;
    }

    public function manageBatches(): void
    {
        $batchCode    = trim((string) ($_GET['batch_code'] ?? ''));
        $supplierCode = trim((string) ($_GET['supplier_code'] ?? ''));
        $status       = trim((string) ($_GET['status'] ?? ''));

        $statusMap = [
            'new'         => 'Mới',
            'pending_qc'  => 'Chờ QC',
            'in_progress' => 'Đang xử lý',
            'completed'   => 'Hoàn thành',
            'rejected'    => 'Bị từ chối',
        ];

        $whereParts = [];
        $params     = [];

        if ($batchCode !== '') {
            $whereParts[]         = 'b.batch_code LIKE :batch_code';
            $params['batch_code'] = '%' . $batchCode . '%';
        }
        if ($supplierCode !== '') {
            $whereParts[]            = 'b.supplier_code = :supplier_code';
            $params['supplier_code'] = $supplierCode;
        }
        if ($status !== '') {
            $whereParts[]     = 'b.status = :status';
            $params['status'] = $status;
        }

        $whereSql = $whereParts !== [] ? ' WHERE ' . implode(' AND ', $whereParts) : '';

        $sql = "
            SELECT
                b.batch_id,
                b.batch_code,
                b.supplier_code,
                pt.product_name AS product_name,
                b.import_date,
                b.status,
                qc.ok_units,
                qc.ng_units,
                COALESCE(SUM(bx.total_units), 0) AS total_units
            FROM batches b
            LEFT JOIN boxes bx ON bx.batch_code = b.batch_code
            LEFT JOIN product_types pt ON pt.product_code = b.product_type
            LEFT JOIN qc_results qc ON qc.batch_code = b.batch_code
            {$whereSql}
            GROUP BY b.batch_id, b.batch_code, b.supplier_code, pt.product_name, b.import_date, b.status, qc.ok_units, qc.ng_units
            ORDER BY b.import_date DESC, b.batch_id DESC
        ";

        $pdo  = Database::getInstance();
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $batches = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $this->view('admin/batches', [
            'title'     => 'Manage Batches',
            'batches'   => $batches,
            'statusMap' => $statusMap,
        ]);
    }

    public function batchDetail(): void
    {
        $statusMap = [
            'new'         => 'Mới',
            'pending_qc'  => 'Chờ QC',
            'in_progress' => 'Đang xử lý',
            'completed'   => 'Hoàn thành',
            'rejected'    => 'Bị từ chối',
        ];
        $batchCode = trim((string) ($_GET['batch_code'] ?? ''));
        if ($batchCode === '') {
            header('Location: /admin/batches');
            exit;
        }

        $pdo = Database::getInstance();

        $batchStmt = $pdo->prepare('SELECT batch_id, batch_code, supplier_code, product_type, import_date, status FROM batches WHERE batch_code = :batch_code LIMIT 1');
        $batchStmt->execute(['batch_code' => $batchCode]);
        $batch = $batchStmt->fetch(\PDO::FETCH_ASSOC);

        if ($batch === false) {
            header('Location: /admin/batches');
            exit;
        }

        $boxesStmt = $pdo->prepare('SELECT box_id, batch_code, box_code, tray_count, unit_per_tray, total_units FROM boxes WHERE batch_code = :batch_code ORDER BY box_id ASC');
        $boxesStmt->execute(['batch_code' => $batchCode]);
        $boxes = $boxesStmt->fetchAll(\PDO::FETCH_ASSOC);

        $supplierStmt = $pdo->query('SELECT supplier_code, supplier_name FROM suppliers');
        $suppliers    = $supplierStmt->fetchAll(\PDO::FETCH_ASSOC);

        $productTypeStmt = $pdo->query('SELECT product_code, product_name FROM product_types');
        $productTypes    = $productTypeStmt->fetchAll(\PDO::FETCH_ASSOC);

        $this->view('admin/batch_detail', [
            'title'   => 'Batch Detail',
            'batch'   => $batch,
            'boxes'   => $boxes,
            'statusMap'=> $statusMap,
            'suppliers' => $suppliers,
            'productTypes' => $productTypes,
            'message' => (string) ($_GET['message'] ?? ''),
        ]);
    }

    public function saveBatch(): void
    {
        $batchCode = trim((string) ($_POST['batch_code'] ?? ''));
        if ($batchCode === '') {
            header('Location: /admin/batches');
            exit;
        }

        $supplierCode = trim((string) ($_POST['supplier_code'] ?? ''));
        $productType  = trim((string) ($_POST['product_type'] ?? ''));
        $importDate   = trim((string) ($_POST['import_date'] ?? ''));
        $status       = trim((string) ($_POST['status'] ?? ''));
        $boxes        = isset($_POST['boxes']) && is_array($_POST['boxes']) ? $_POST['boxes'] : [];

        $allowedSuppliers = ['VSD', 'HDB'];
        $allowedTypes     = ['watch_strap', 'watch_face'];
        $allowedStatuses  = ['new', 'pending_qc', 'in_progress', 'completed', 'rejected'];

        if (! in_array($supplierCode, $allowedSuppliers, true) || ! in_array($productType, $allowedTypes, true) || ! in_array($status, $allowedStatuses, true) || $importDate === '') {
            header('Location: /admin/batch_detail?batch_code=' . urlencode($batchCode));
            exit;
        }

        $pdo = Database::getInstance();
        $pdo->beginTransaction();
        try {
            $batchStmt = $pdo->prepare('UPDATE batches SET supplier_code = :supplier_code, product_type = :product_type, import_date = :import_date, status = :status WHERE batch_code = :batch_code');
            $batchStmt->execute([
                'supplier_code' => $supplierCode,
                'product_type'  => $productType,
                'import_date'   => $importDate,
                'status'        => $status,
                'batch_code'    => $batchCode,
            ]);

            $boxStmt = $pdo->prepare('UPDATE boxes SET tray_count = :tray_count, unit_per_tray = :unit_per_tray, total_units = :total_units WHERE box_id = :box_id AND batch_code = :batch_code');
            foreach ($boxes as $box) {
                $boxId = isset($box['box_id']) ? (int) $box['box_id'] : 0;
                if ($boxId <= 0) {
                    continue;
                }

                $trayCount   = isset($box['tray_count']) && $box['tray_count'] !== '' ? max(0, (int) $box['tray_count']) : null;
                $unitPerTray = isset($box['unit_per_tray']) && $box['unit_per_tray'] !== '' ? max(0, (int) $box['unit_per_tray']) : null;
                $totalUnits  = isset($box['total_units']) ? max(1, (int) $box['total_units']) : 1;

                $boxStmt->execute([
                    'tray_count'    => $trayCount,
                    'unit_per_tray' => $unitPerTray,
                    'total_units'   => $totalUnits,
                    'box_id'        => $boxId,
                    'batch_code'    => $batchCode,
                ]);
            }

            $pdo->commit();
            header('Location: /admin/batch_detail?batch_code=' . urlencode($batchCode) . '&message=' . urlencode('Saved'));
            exit;
        } catch (\Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    public function manageBoxes(): void
    {
        $this->view('admin/boxes', ['title' => 'Manage Boxes']);
    }

    public function manageQCResults(): void
    {
        $batchCode = trim((string) ($_GET['batch_code'] ?? ''));

        $whereSql = '';
        $params   = [];
        if ($batchCode !== '') {
            $whereSql             = ' WHERE q.batch_code LIKE :batch_code';
            $params['batch_code'] = '%' . $batchCode . '%';
        }

        $pdo  = Database::getInstance();
        $stmt = $pdo->prepare(
            'SELECT q.result_id, q.batch_code, q.ok_units, q.ng_units, q.inspected_by, q.inspected_at, u.username AS inspector_name
             FROM qc_results q
             LEFT JOIN users u ON u.id = q.inspected_by' . $whereSql . '
             ORDER BY q.result_id DESC'
        );
        $stmt->execute($params);
        $qcResults = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $this->view('admin/qc_results', [
            'title'     => 'Manage QC Results',
            'qcResults' => $qcResults,
        ]);
    }

    public function saveQCResultForm(): void
    {
        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        if ($id <= 0) {
            header('Location: /admin/qc_results');
            exit;
        }

        $pdo  = Database::getInstance();
        $stmt = $pdo->prepare('SELECT result_id, batch_code, ok_units, ng_units, inspected_by, inspected_at FROM qc_results WHERE result_id = :id');
        $stmt->execute(['id' => $id]);
        $qcResult = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($qcResult === false) {
            header('Location: /admin/qc_results');
            exit;
        }

        $users = $pdo->query("SELECT id, username FROM users WHERE role IN ('admin', 'qc_staff', 'manager') AND is_active = 1 ORDER BY username ASC")->fetchAll(\PDO::FETCH_ASSOC);
        $defectTypes = $pdo->query('SELECT defect_type_id, name FROM defect_types WHERE is_active = 1 AND deleted_at IS NULL ORDER BY name ASC')->fetchAll(\PDO::FETCH_ASSOC);
        $defectStmt = $pdo->prepare('SELECT defect_id, defect_type_id, qty_units FROM defect_records WHERE batch_code = :batch_code ORDER BY defect_id ASC');
        $defectStmt->execute(['batch_code' => (string) $qcResult['batch_code']]);
        $defects = $defectStmt->fetchAll(\PDO::FETCH_ASSOC);

        $totalUnitsStmt = $pdo->prepare('SELECT COALESCE(SUM(total_units), 0) AS total_units FROM boxes WHERE batch_code = :batch_code');
        $totalUnitsStmt->execute(['batch_code' => (string) $qcResult['batch_code']]);
        $batchTotalUnits = (int) ($totalUnitsStmt->fetchColumn() ?: 0);

        $this->view('admin/qc_result_save', [
            'title'    => 'Edit QC Result',
            'qcResult' => $qcResult,
            'users'    => $users,
            'defectTypes' => $defectTypes,
            'defects'  => $defects,
            'batchTotalUnits' => $batchTotalUnits,
            'errors'   => [],
            'old'      => [],
        ]);
    }

    public function saveQCResult(): void
    {
        $resultId = isset($_POST['result_id']) ? (int) $_POST['result_id'] : 0;
        if ($resultId <= 0) {
            header('Location: /admin/qc_results');
            exit;
        }

        $batchCode = trim((string) ($_POST['batch_code'] ?? ''));
        $inspectedBy = isset($_POST['inspected_by']) && $_POST['inspected_by'] !== '' ? (int) $_POST['inspected_by'] : null;
        $inspectedAtRaw = trim((string) ($_POST['inspected_at'] ?? ''));
        $inspectedAt = str_replace('T', ' ', $inspectedAtRaw);
        $defectsInput = isset($_POST['defects']) && is_array($_POST['defects']) ? $_POST['defects'] : [];

        $normalizedDefects = [];
        foreach ($defectsInput as $row) {
            if (!is_array($row)) {
                continue;
            }

            $defectTypeId = isset($row['defect_type_id']) ? (int) $row['defect_type_id'] : 0;
            $qtyUnits = isset($row['qty_units']) ? (int) $row['qty_units'] : 0;
            $defectId = isset($row['defect_id']) ? (int) $row['defect_id'] : 0;

            if ($defectTypeId <= 0 && $qtyUnits <= 0) {
                continue;
            }

            $normalizedDefects[] = [
                'defect_id' => $defectId,
                'defect_type_id' => $defectTypeId,
                'qty_units' => $qtyUnits,
            ];
        }

        $errors = [];
        if ($batchCode === '') {
            $errors[] = 'Batch code khong duoc de trong.';
        }
        if ($inspectedAtRaw === '') {
            $errors[] = 'Thoi gian kiem tra khong duoc de trong.';
        }

        foreach ($normalizedDefects as $index => $row) {
            if ((int) $row['defect_type_id'] <= 0) {
                $errors[] = 'Loai loi dong #' . ($index + 1) . ' khong hop le.';
            }
            if ((int) $row['qty_units'] <= 0) {
                $errors[] = 'So luong loi dong #' . ($index + 1) . ' phai lon hon 0.';
            }
        }

        $pdo = Database::getInstance();
        $defectTypes = $pdo->query('SELECT defect_type_id, name FROM defect_types WHERE is_active = 1 AND deleted_at IS NULL ORDER BY name ASC')->fetchAll(\PDO::FETCH_ASSOC);
        $users = $pdo->query("SELECT id, username FROM users WHERE role IN ('admin', 'qc_staff', 'manager') AND is_active = 1 ORDER BY username ASC")->fetchAll(\PDO::FETCH_ASSOC);

        $resultStmt = $pdo->prepare('SELECT result_id, batch_code, ok_units, ng_units, inspected_by, inspected_at FROM qc_results WHERE result_id = :id');
        $resultStmt->execute(['id' => $resultId]);
        $existingResult = $resultStmt->fetch(\PDO::FETCH_ASSOC);
        if ($existingResult === false) {
            header('Location: /admin/qc_results');
            exit;
        }

        if ($errors !== []) {
            $this->view('admin/qc_result_save', [
                'title'    => 'Edit QC Result',
                'qcResult' => $existingResult,
                'users'    => $users,
                'defectTypes' => $defectTypes,
                'defects'  => [],
                'batchTotalUnits' => 0,
                'errors'   => $errors,
                'old'      => [
                    'result_id'    => (string) $resultId,
                    'batch_code'   => $batchCode,
                    'inspected_by' => $inspectedBy !== null ? (string) $inspectedBy : '',
                    'inspected_at' => $inspectedAtRaw,
                    'defects'      => $normalizedDefects,
                ],
            ]);
            return;
        }

        $batchExistsStmt = $pdo->prepare('SELECT 1 FROM batches WHERE batch_code = :batch_code LIMIT 1');
        $batchExistsStmt->execute(['batch_code' => $batchCode]);
        if ($batchExistsStmt->fetchColumn() === false) {
            $this->view('admin/qc_result_save', [
                'title'    => 'Edit QC Result',
                'qcResult' => $existingResult,
                'users'    => $users,
                'defectTypes' => $defectTypes,
                'defects'  => [],
                'batchTotalUnits' => 0,
                'errors'   => ['Batch code khong ton tai.'],
                'old'      => [
                    'result_id'    => (string) $resultId,
                    'batch_code'   => $batchCode,
                    'inspected_by' => $inspectedBy !== null ? (string) $inspectedBy : '',
                    'inspected_at' => $inspectedAtRaw,
                    'defects'      => $normalizedDefects,
                ],
            ]);
            return;
        }

        $totalUnitsStmt = $pdo->prepare('SELECT COALESCE(SUM(total_units), 0) AS total_units FROM boxes WHERE batch_code = :batch_code');
        $totalUnitsStmt->execute(['batch_code' => $batchCode]);
        $batchTotalUnits = (int) ($totalUnitsStmt->fetchColumn() ?: 0);
        if ($batchTotalUnits <= 0) {
            $this->view('admin/qc_result_save', [
                'title'    => 'Edit QC Result',
                'qcResult' => $existingResult,
                'users'    => $users,
                'defectTypes' => $defectTypes,
                'defects'  => [],
                'batchTotalUnits' => 0,
                'errors'   => ['Batch nay khong co box/tong so luong de tinh ket qua QC.'],
                'old'      => [
                    'result_id'    => (string) $resultId,
                    'batch_code'   => $batchCode,
                    'inspected_by' => $inspectedBy !== null ? (string) $inspectedBy : '',
                    'inspected_at' => $inspectedAtRaw,
                    'defects'      => $normalizedDefects,
                ],
            ]);
            return;
        }

        $ngUnits = 0;
        foreach ($normalizedDefects as $row) {
            $ngUnits += max(0, (int) $row['qty_units']);
        }

        if ($ngUnits > $batchTotalUnits) {
            $this->view('admin/qc_result_save', [
                'title'    => 'Edit QC Result',
                'qcResult' => $existingResult,
                'users'    => $users,
                'defectTypes' => $defectTypes,
                'defects'  => [],
                'batchTotalUnits' => $batchTotalUnits,
                'errors'   => ['Tong so luong loi vuot qua tong san pham cua batch.'],
                'old'      => [
                    'result_id'    => (string) $resultId,
                    'batch_code'   => $batchCode,
                    'inspected_by' => $inspectedBy !== null ? (string) $inspectedBy : '',
                    'inspected_at' => $inspectedAtRaw,
                    'defects'      => $normalizedDefects,
                ],
            ]);
            return;
        }

        $okUnits = $batchTotalUnits - $ngUnits;

        $pdo->beginTransaction();
        try {
            // 1) Update defect records by batch.
            $deleteDefectsStmt = $pdo->prepare('DELETE FROM defect_records WHERE batch_code = :batch_code');
            $deleteDefectsStmt->execute(['batch_code' => $batchCode]);

            if ($normalizedDefects !== []) {
                $insertDefectStmt = $pdo->prepare('INSERT INTO defect_records (batch_code, defect_type_id, qty_units) VALUES (:batch_code, :defect_type_id, :qty_units)');
                foreach ($normalizedDefects as $row) {
                    $insertDefectStmt->execute([
                        'batch_code' => $batchCode,
                        'defect_type_id' => (int) $row['defect_type_id'],
                        'qty_units' => (int) $row['qty_units'],
                    ]);
                }
            }

            // 2) Update QC result.
            $updateQcStmt = $pdo->prepare('UPDATE qc_results SET batch_code = :batch_code, ok_units = :ok_units, ng_units = :ng_units, inspected_by = :inspected_by, inspected_at = :inspected_at WHERE result_id = :result_id');
            $updateQcStmt->execute([
                'batch_code'   => $batchCode,
                'ok_units'     => $okUnits,
                'ng_units'     => $ngUnits,
                'inspected_by' => $inspectedBy,
                'inspected_at' => $inspectedAt,
                'result_id'    => $resultId,
            ]);

            // 3) Update batch status.
            $newStatus = ($batchTotalUnits > 0 && ($ngUnits / $batchTotalUnits >= 0.3)) ? 'rejected' : 'completed';
            $updateBatchStmt = $pdo->prepare('UPDATE batches SET status = :status WHERE batch_code = :batch_code');
            $updateBatchStmt->execute([
                'status' => $newStatus,
                'batch_code' => $batchCode,
            ]);

            $pdo->commit();
        } catch (\Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }

        header('Location: /admin/qc_results');
        exit;
    }

    public function manageDefectRecords(): void
    {
        $batchCode = trim((string) ($_GET['batch_code'] ?? ''));

        $whereSql = '';
        $params   = [];
        if ($batchCode !== '') {
            $whereSql             = ' WHERE d.batch_code LIKE :batch_code';
            $params['batch_code'] = '%' . $batchCode . '%';
        }

        $pdo  = Database::getInstance();
        $stmt = $pdo->prepare(
            'SELECT d.defect_id, d.batch_code, d.defect_type_id, d.qty_units, d.created_at, t.name AS defect_name
             FROM defect_records d
             LEFT JOIN defect_types t ON t.defect_type_id = d.defect_type_id' . $whereSql . '
             ORDER BY d.defect_id DESC'
        );
        $stmt->execute($params);
        $defectRecords = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $this->view('admin/defect_records', [
            'title'         => 'Manage Defect Records',
            'defectRecords' => $defectRecords,
        ]);
    }

    public function saveDefectRecordForm(): void
    {
        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        if ($id <= 0) {
            header('Location: /admin/defect_records');
            exit;
        }

        $pdo  = Database::getInstance();
        $stmt = $pdo->prepare('SELECT defect_id, batch_code, defect_type_id, qty_units FROM defect_records WHERE defect_id = :id');
        $stmt->execute(['id' => $id]);
        $defectRecord = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($defectRecord === false) {
            header('Location: /admin/defect_records');
            exit;
        }

        $defectTypes = $pdo->query('SELECT defect_type_id, name FROM defect_types WHERE deleted_at IS NULL AND is_active = 1 ORDER BY name ASC')->fetchAll(\PDO::FETCH_ASSOC);

        $this->view('admin/defect_record_save', [
            'title'        => 'Edit Defect Record',
            'defectRecord' => $defectRecord,
            'defectTypes'  => $defectTypes,
            'errors'       => [],
            'old'          => [],
        ]);
    }

    public function saveDefectRecord(): void
    {
        $defectId = isset($_POST['defect_id']) ? (int) $_POST['defect_id'] : 0;
        if ($defectId <= 0) {
            header('Location: /admin/defect_records');
            exit;
        }

        $batchCode    = trim((string) ($_POST['batch_code'] ?? ''));
        $defectTypeId = isset($_POST['defect_type_id']) ? (int) $_POST['defect_type_id'] : 0;
        $qtyUnits     = isset($_POST['qty_units']) ? (int) $_POST['qty_units'] : 0;

        $errors = [];
        if ($batchCode === '') {
            $errors[] = 'Batch code khong duoc de trong.';
        }
        if ($defectTypeId <= 0) {
            $errors[] = 'Loai loi khong hop le.';
        }
        if ($qtyUnits <= 0) {
            $errors[] = 'So luong loi phai lon hon 0.';
        }

        $pdo = Database::getInstance();
        if ($errors !== []) {
            $stmt = $pdo->prepare('SELECT defect_id, batch_code, defect_type_id, qty_units FROM defect_records WHERE defect_id = :id');
            $stmt->execute(['id' => $defectId]);
            $defectRecord = $stmt->fetch(\PDO::FETCH_ASSOC) ?: [];
            $defectTypes  = $pdo->query('SELECT defect_type_id, name FROM defect_types WHERE deleted_at IS NULL AND is_active = 1 ORDER BY name ASC')->fetchAll(\PDO::FETCH_ASSOC);

            $this->view('admin/defect_record_save', [
                'title'        => 'Edit Defect Record',
                'defectRecord' => $defectRecord,
                'defectTypes'  => $defectTypes,
                'errors'       => $errors,
                'old'          => [
                    'defect_id'      => (string) $defectId,
                    'batch_code'     => $batchCode,
                    'defect_type_id' => (string) $defectTypeId,
                    'qty_units'      => (string) $qtyUnits,
                ],
            ]);
            return;
        }

        $stmt = $pdo->prepare('UPDATE defect_records SET batch_code = :batch_code, defect_type_id = :defect_type_id, qty_units = :qty_units WHERE defect_id = :defect_id');
        $stmt->execute([
            'batch_code'     => $batchCode,
            'defect_type_id' => $defectTypeId,
            'qty_units'      => $qtyUnits,
            'defect_id'      => $defectId,
        ]);

        header('Location: /admin/defect_records');
        exit;
    }

}
