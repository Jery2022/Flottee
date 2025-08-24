<?php

namespace App\Controllers;

use Core\Response;
use App\Models\vehiclesModel;
use App\Helpers\AuthMiddleware;

class vehiclesController
{
    protected vehiclesModel $model;

    public function __construct()
    {
        $this->model = new vehiclesModel();
    }

    public function index()
    {
        AuthMiddleware::requireRole('admin');

        $filters = [
            'name' => $_GET['name'] ?? null,
            'type' => $_GET['type'] ?? null,
            'status' => $_GET['status'] ?? null,
            'visibility' => $_GET['visibility'] ?? 'visible',
        ];
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $perPage = isset($_GET['perPage']) ? (int)$_GET['perPage'] : 10;

        $data = $this->model->getPaginated($filters, $page, $perPage);

        Response::json(['status' => 'success', 'data' => $data]);
    }

    // Keep other methods like show, store, update, destroy as needed,
    // adapting them from UsersController. For now, the index method is the priority.
}
