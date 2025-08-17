<?php
require_once __DIR__ . '/../models/vehiclesModel.php';

function handleGetVehicles($id = null) {
    header('Content-Type: application/json');
    echo json_encode($id ? getVehicleById($id) : getAllVehicles());
}

function handleCreateVehicle($data) {
    header('Content-Type: application/json');
    echo json_encode(['success' => createVehicle($data)]);
}

function handleUpdateVehicle($id, $data) {
    header('Content-Type: application/json');
    echo json_encode(['success' => updateVehicle($id, $data)]);
}

function handleDeleteVehicle($id) {
    header('Content-Type: application/json');
    echo json_encode(['success' => deleteVehicle($id)]);
}
