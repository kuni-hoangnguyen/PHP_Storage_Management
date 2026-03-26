<?php

declare (strict_types = 1);

return [
    'GET'  => [
        '/'                        => ['HomeController', 'index'],
        '/login'                   => ['AuthController', 'getLoginForm'],
        '/logout'                  => ['AuthController', 'logout'],

        '/warehouse/index'         => ['WarehouseController', 'index'],
        '/warehouse/create'        => ['WarehouseController', 'createBatch'],
        '/warehouse/box_add'       => ['WarehouseController', 'showAddBoxForm'],
        '/warehouse/batches'       => ['WarehouseController', 'showBatchesList'],
        '/warehouse/detail'        => ['WarehouseController', 'showDetail'],
        '/warehouse/update_status' => ['WarehouseController', 'updateBatchStatus'],

        '/qc/index'                => ['QCController', 'index'],
        '/qc/batches'              => ['QCController', 'batchList'],
        '/qc/inspect'              => ['QCController', 'inspectBatch'],
        '/qc/result'              => ['QCController', 'viewResult'],

        '/manager/index'           => ['ManagerController', 'index'],

        '/admin/index'             => ['AdminController', 'index'],

    ],
    'POST' => [
        '/login'             => ['AuthController', 'postLoginForm'],

        '/warehouse/create'  => ['WarehouseController', 'storeBatch'],
        '/warehouse/box_add' => ['WarehouseController', 'addBox'],

        '/qc/inspect'       => ['QCController', 'submitInspection'],
    ],
];
