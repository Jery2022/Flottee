<?php
require_once '../models/reservationModel.php';

function handleCreateReservation($pdo, $user_id, $data) {
    if (!isset($data['vehicle_id'], $data['start_date'], $data['end_date'])) {
        http_response_code(400);
        header('Content-Type: application/json');
        echo json_encode(["message" => "Champs manquants"]);
        return;
    }

    $success = insertReservation(
        $pdo,
        $user_id,
        $data['vehicle_id'],
        $data['start_date'],
        $data['end_date']
    );

    if ($success) {
        http_response_code(201);
        header('Content-Type: application/json');
        echo json_encode(["message" => "Réservation enregistrée"]);
    } else {
        http_response_code(500);
        header('Content-Type: application/json');
        echo json_encode(["message" => "Erreur lors de l'enregistrement"]);
    }
}

function handleGetReservations($pdo, $user_id) {
    $reservations = getReservations($pdo, $user_id);
    http_response_code(200);
    header('Content-Type: application/json');
    echo json_encode($reservations);
}

function handleUpdateReservation($pdo, $reservation_id, $data) {
    if (!isset($data['start_date'], $data['end_date'])) {
        http_response_code(400);
        header('Content-Type: application/json');
        echo json_encode(["message" => "Champs manquants"]);
        return;
    }

    $success = updateReservation(
        $pdo,
        $reservation_id,
        $data['start_date'],
        $data['end_date']
    );

    if ($success) {
        http_response_code(200);
        header('Content-Type: application/json');
        echo json_encode(["message" => "Réservation mise à jour"]);
    } else {
        http_response_code(500);
        header('Content-Type: application/json');
        echo json_encode(["message" => "Erreur lors de la mise à jour"]);
    }
}

function handleDeleteReservation($pdo, $reservation_id) {
    $success = deleteReservation($pdo, $reservation_id);
    if ($success) {
        http_response_code(204);
        header('Content-Type: application/json');
        echo json_encode(["message" => "Réservation supprimée"]);
    } else {
        http_response_code(500);
        header('Content-Type: application/json');
        echo json_encode(["message" => "Erreur lors de la suppression"]);
    }
}

