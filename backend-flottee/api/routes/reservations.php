<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: POST");

require_once '../config/db.php';
require_once '../helpers/jwtHelper.php';
require_once '../controllers/reservationController.php';

$pdo = getPDO();
$user_id = getUserIdFromToken();
$data = json_decode(file_get_contents("php://input"), true);

handleCreateReservation($pdo, $user_id, $data);


