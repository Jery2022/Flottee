<?php
// backend-flottee/api/controllers/MaintenanceController.php

namespace App\Controllers;

use Core\Response;
use App\Models\MaintenanceModel;
use App\Helpers\AuthMiddleware;

class MaintenanceController
{
    private $maintenanceModel;

    public function __construct()
    {
        $this->maintenanceModel = new MaintenanceModel();
    }

    public function index()
    {
        AuthMiddleware::requireRole('admin');

        try {
            $filters = [
                'vehicle_id' => $_GET['vehicle_id'] ?? null,
                'type' => $_GET['type'] ?? null,
                'status' => $_GET['status'] ?? null,
                'severity' => $_GET['severity'] ?? null,
            ];
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $perPage = isset($_GET['perPage']) ? (int)$_GET['perPage'] : 10;

            $data = $this->maintenanceModel->getPaginated($filters, $page, $perPage);

            Response::json(['status' => 'success', 'data' => $data]);
        } catch (\Exception $e) {
            http_response_code(500);
            Response::json([
                'status' => 'error',
                'message' => 'Une erreur interne est survenue.',
                'error' => $e->getMessage()
            ]);
        }
    }

    public function show($id)
    {
        AuthMiddleware::requireRole('admin');
        $maintenance = $this->maintenanceModel->getById($id);
        if ($maintenance) {
            Response::json(['status' => 'success', 'data' => $maintenance]);
        } else {
            http_response_code(404);
            Response::json(['status' => 'error', 'message' => 'Maintenance non trouvée.']);
        }
    }

    public function store()
    {
        AuthMiddleware::requireRole('admin');
        $data = json_decode(file_get_contents("php://input"), true);

        if ($this->maintenanceModel->create($data)) {
            Response::json(['status' => 'success', 'message' => 'Maintenance créée avec succès.']);
        } else {
            http_response_code(500);
            Response::json(['status' => 'error', 'message' => 'Erreur lors de la création de la maintenance.']);
        }
    }

    public function update($id)
    {
        AuthMiddleware::requireRole('admin');
        $data = json_decode(file_get_contents("php://input"), true);

        if ($this->maintenanceModel->update($id, $data)) {
            Response::json(['status' => 'success', 'message' => 'Maintenance mise à jour avec succès.']);
        } else {
            http_response_code(500);
            Response::json(['status' => 'error', 'message' => 'Erreur lors de la mise à jour de la maintenance.']);
        }
    }

    public function destroy($id)
    {
        AuthMiddleware::requireRole('admin');
        if ($this->maintenanceModel->delete($id)) {
            Response::json(['status' => 'success', 'message' => 'Maintenance supprimée avec succès.']);
        } else {
            http_response_code(500);
            Response::json(['status' => 'error', 'message' => 'Erreur lors de la suppression de la maintenance.']);
        }
    }
}
