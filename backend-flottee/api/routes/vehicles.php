<?php
require_once __DIR__ . '/../controllers/vehiclesController.php';
require_once __DIR__ . '/../helpers/jwtHelper.php';

function isAuthorized() {
    $headers = getallheaders();
    if (!isset($headers['Authorization'])) return false;

    $token = str_replace('Bearer ', '', $headers['Authorization']);
    return validateJWT($token);
}

if (!isAuthorized()) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

header('Content-Type: application/json');
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        $id = $_GET['id'] ?? null;
        handleGetVehicles($id);
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);
        handleCreateVehicle($data);
        break;

    case 'PUT':
        parse_str(file_get_contents("php://input"), $data);
        $id = $_GET['id'] ?? null;
        handleUpdateVehicle($id, $data);
        break;

    case 'DELETE':
        $id = $_GET['id'] ?? null;
        handleDeleteVehicle($id);
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method Not Allowed']);
}
