<?php

declare (strict_types = 1);

return [
    'GET'  => [
        '/'                         => ['HomeController', 'index'],
        '/login'                    => ['AuthController', 'getLoginForm'],
        '/logout'                   => ['AuthController', 'logout'],

        '/warehouse/index'          => ['WarehouseController', 'index'],
        '/warehouse/create'         => ['WarehouseController', 'createBatch'],
        '/warehouse/box_add'        => ['WarehouseController', 'showAddBoxForm'],
        '/warehouse/batches'        => ['WarehouseController', 'showBatchesList'],
        '/warehouse/detail'         => ['WarehouseController', 'showDetail'],
        '/warehouse/update_status'  => ['WarehouseController', 'updateBatchStatus'],

        '/qc/index'                 => ['QCController', 'index'],
        '/qc/batches'               => ['QCController', 'batchList'],
        '/qc/inspect'               => ['QCController', 'inspectBatch'],
        '/qc/result'                => ['QCController', 'viewResult'],

        '/manager/index'            => ['ManagerController', 'index'],
        '/manager/batches'          => ['ManagerController', 'showBatchesList'],
        '/manager/detail'           => ['ManagerController', 'viewDetail'],

        '/admin/index'              => ['AdminController', 'index'],

        '/admin/users'              => ['AdminController', 'manageUsers'],
        '/admin/user_save'          => ['AdminController', 'saveUserForm'],

        '/admin/crud'               => ['AdminController', 'manageCrud'],
        '/admin/defect_type_save'   => ['AdminController', 'saveDefectTypeForm'],
        '/admin/product_type_save'  => ['AdminController', 'saveProductTypeForm'],
        '/admin/supplier_save'      => ['AdminController', 'saveSupplierForm'],

        '/admin/batches'            => ['AdminController', 'manageBatches'],
        '/admin/batch_detail'       => ['AdminController', 'batchDetail'],

        '/admin/qc_results'         => ['AdminController', 'manageQCResults'],
        '/admin/qc_result_save'     => ['AdminController', 'saveQCResultForm'],

        '/admin/defect_records'     => ['AdminController', 'manageDefectRecords'],
        '/admin/defect_record_save' => ['AdminController', 'saveDefectRecordForm'],

    ],
    'POST' => [
        '/login'                     => ['AuthController', 'postLoginForm'],

        '/warehouse/create'          => ['WarehouseController', 'storeBatch'],
        '/warehouse/box_add'         => ['WarehouseController', 'addBox'],

        '/qc/inspect'                => ['QCController', 'submitInspection'],

        '/admin/user_save'           => ['AdminController', 'saveUser'],

        '/admin/defect_type_save'    => ['AdminController', 'saveDefectType'],
        '/admin/defect_type_delete'  => ['AdminController', 'deleteDefectType'],

        '/admin/product_type_save'   => ['AdminController', 'saveProductType'],
        '/admin/product_type_delete' => ['AdminController', 'deleteProductType'],

        '/admin/supplier_save'       => ['AdminController', 'saveSupplier'],
        '/admin/supplier_delete'     => ['AdminController', 'deleteSupplier'],

        '/admin/batch_save'          => ['AdminController', 'saveBatch'],
        
        '/admin/qc_result_save'      => ['AdminController', 'saveQCResult'],
        '/admin/defect_record_save'  => ['AdminController', 'saveDefectRecord'],
    ],
];
