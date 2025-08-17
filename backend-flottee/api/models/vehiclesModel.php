<?php
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../config/db.php';

function getAllVehicles() {
    global $pdo;
    return $pdo->query("SELECT * FROM vehicles")->fetchAll();
}

function getVehicleById($id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM vehicles WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function createVehicle($data) {
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO vehicles (make, model, year, license_plate, status) VALUES (?, ?, ?, ?, ?)");
    return $stmt->execute([
        $data['make'], $data['model'], $data['year'],
        $data['license_plate'], $data['status'] ?? 'disponible'
    ]);
}

function updateVehicle($id, $data) {
    global $pdo;
    $fields = [];
    $values = [];

    foreach ($data as $key => $value) {
        $fields[] = "$key = ?";
        $values[] = $value;
    }

    $values[] = $id;
    $sql = "UPDATE vehicles SET " . implode(', ', $fields) . " WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute($values);
}

function deleteVehicle($id) {
    global $pdo;
    $stmt = $pdo->prepare("DELETE FROM vehicles WHERE id = ?");
    return $stmt->execute([$id]);
}
