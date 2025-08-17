<?php

use App\Controllers\DashboardController;

return [
    'GET' => function () {
        $controller = new DashboardController();
        $controller->index();
    },
];