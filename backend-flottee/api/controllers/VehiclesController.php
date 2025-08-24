<?php

namespace App\Controllers;

use Core\Response;
use App\Models\VehiclesModel;
use App\Helpers\AuthMiddleware;

class VehiclesController
{
    protected VehiclesModel $model;

    public function __construct()
    {
        $this->model = new VehiclesModel();
    }

    public function index()
    {
        AuthMiddleware::requireRole('admin');

        try {
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
        } catch (\Exception $e) {
            // En-tête de réponse HTTP pour indiquer une erreur de serveur
            http_response_code(500);
            // Réponse JSON avec le message d'erreur pour le débogage
            Response::json([
                'status' => 'error',
                'message' => 'Une erreur interne est survenue.',
                'error' => $e->getMessage() // Message d'erreur détaillé
            ]);
        }
    }

    // Keep other methods like show, store, update, destroy as needed,
    // adapting them from UsersController. For now, the index method is the priority.
}
