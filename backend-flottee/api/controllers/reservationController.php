<?php

namespace App\Controllers;

use Core\Response;
use App\Models\ReservationModel;
use App\Helpers\AuthMiddleware;

class reservationController
{
    protected ReservationModel $model;

    public function __construct()
    {
        $this->model = new ReservationModel();
    }

    public function index()
    {
        AuthMiddleware::requireRole('admin');

        $filters = [
            'vehicle' => $_GET['vehicle'] ?? null,
            'user' => $_GET['user'] ?? null,
            'status' => $_GET['status'] ?? null,
        ];
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $perPage = isset($_GET['perPage']) ? (int)$_GET['perPage'] : 10;

        $data = $this->model->getPaginated($filters, $page, $perPage);

        Response::json(['status' => 'success', 'data' => $data]);
    }

    public function show($id)
    {
        // Implementation for showing a single reservation
    }

    public function store()
    {
        AuthMiddleware::requireRole('admin');

        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['vehicle_id'], $data['user_id'], $data['start_date'], $data['end_date'], $data['status'])) {
            Response::error('Données manquantes', 400);
            return;
        }

        $newReservationId = $this->model->create($data);

        if ($newReservationId) {
            $newReservation = $this->model->getById($newReservationId);
            Response::json(['status' => 'success', 'data' => $newReservation]);
        } else {
            Response::error('Échec de la création de la réservation', 500);
        }
    }

    public function update($id)
    {
        AuthMiddleware::requireRole('admin');

        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['vehicle_id'], $data['user_id'], $data['start_date'], $data['end_date'], $data['status'])) {
            Response::error('Données manquantes', 400);
            return;
        }

        $success = $this->model->update($id, $data);

        if ($success) {
            $updatedReservation = $this->model->getById($id);
            Response::json(['status' => 'success', 'data' => $updatedReservation]);
        } else {
            Response::error('Échec de la mise à jour', 500);
        }
    }

    public function destroy($id)
    {
        AuthMiddleware::requireRole('admin');

        $success = $this->model->delete($id);

        if ($success) {
            Response::json(['status' => 'success', 'message' => 'Réservation supprimée avec succès']);
        } else {
            Response::error('Échec de la suppression', 500);
        }
    }
}
