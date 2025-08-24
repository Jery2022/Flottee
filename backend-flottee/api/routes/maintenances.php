<?php
// backend-flottee/api/routes/maintenances.php

require_once __DIR__ . '/../controllers/MaintenanceController.php';

$controller = new MaintenanceController();

$request_method = $_SERVER["REQUEST_METHOD"];

switch ($request_method) {
    case 'GET':
        if (!empty($_GET["id"])) {
            $id = intval($_GET["id"]);
            $response = $controller->getMaintenanceById($id);
            echo json_encode($response);
        } else {
            $response = $controller->getAllMaintenances();
            echo json_encode($response);
        }
        break;
    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);
        $response = $controller->createMaintenance($data);
        echo json_encode($response);
        break;
    case 'PUT':
        $id = intval($_GET["id"]);
        $data = json_decode(file_get_contents("php://input"), true);
        $response = $controller->updateMaintenance($id, $data);
        echo json_encode($response);
        break;
    case 'DELETE':
        $id = intval($_GET["id"]);
        $response = $controller->deleteMaintenance($id);
        echo json_encode($response);
        break;
    default:
        header("HTTP/1.0 405 Method Not Allowed");
        break;
}
