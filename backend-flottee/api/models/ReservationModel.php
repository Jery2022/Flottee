<?php

require_once __DIR__ . '/../functions/functions.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;

function insertReservation($pdo, $user_id, $vehicle_id, $start_date, $end_date) {
    $sql = "INSERT INTO reservations (user_id, vehicle_id, start_date, end_date) VALUES (?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([$user_id, $vehicle_id, $start_date, $end_date]);
}

function getReservations($pdo, $user_id = null) {
    if ($user_id) {
        $sql = "SELECT * FROM reservations WHERE user_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$user_id]);
    } else {
        $sql = "SELECT * FROM reservations";
        $stmt = $pdo->query($sql);
    }
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function updateReservation($pdo, $reservation_id, $start_date, $end_date) {
    $sql = "UPDATE reservations SET start_date = ?, end_date = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([$start_date, $end_date, $reservation_id]);
}

function deleteReservation($pdo, $reservation_id) {
    $sql = "DELETE FROM reservations WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([$reservation_id]);
}

function getReservationsByUser($pdo, $user_id) {
    $sql = "SELECT * FROM reservations WHERE user_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$user_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getReservationsByVehicle($pdo, $vehicle_id) {
    $sql = "SELECT * FROM reservations WHERE vehicle_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$vehicle_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getUserIdFromToken() {
    $jwt = getBearerToken();

    if (!$jwt) {
        return null;
    }
    
    try {
        $secretKey = $_ENV['JWT_SECRET_KEY'] ?: getenv('JWT_SECRET_KEY');
        $decoded = JWT::decode($jwt, new Key($secretKey, 'HS256'));
        return $decoded->data->user_id;
    } catch (Exception $e) {
        return null;
    }
}

function getUserDataFromToken() {
    $jwt = getBearerToken();

    if (!$jwt) {
        return null;
    }

    try {
        $secretKey = $_ENV['JWT_SECRET_KEY'] ?: getenv('JWT_SECRET_KEY');
        $decoded = JWT::decode($jwt, new Key($secretKey, 'HS256'));

        // Vérification de l'expiration
        if (isset($decoded->exp) && $decoded->exp < time()) {
            return null; // Token expiré
        }

        // Extraction des données utiles
        return [
            'user_id' => $decoded->data->user_id ?? null,
            'role'    => $decoded->data->role ?? 'user', // rôle par défaut
        ];
    } catch (ExpiredException $e) {
        // Token expiré
        return null;
    } catch (Exception $e) {
        // Token invalide ou autre erreur
        return null;
    }
}