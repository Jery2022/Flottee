<?php
require_once __DIR__ . '/../controllers/usersController.php';


header('Content-Type: application/json');
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        $id = $_GET['id'] ?? null;
        handleGetUsers($id);
        break;

    case 'POST':
       $data = json_decode(file_get_contents("php://input"), true);
        if (isset($data['email'], $data['password'])) {
            $user = getUserByEmail($data['email']);
            if ($user && password_verify($data['password'], $user['password'])) {
                require_once __DIR__ . '/../helpers/jwtHelper.php';
                $token = generateJWT($user['id']);
                echo json_encode(['token' => $token]);
            } else {
                http_response_code(401);
                echo json_encode(['error' => 'Invalid credentials']);
            }
        }
        break;

    case 'PUT':
        parse_str(file_get_contents("php://input"), $data);
        $id = $_GET['id'] ?? null;
        handleUpdateUser($id, $data);
        break;

    case 'DELETE':
        $id = $_GET['id'] ?? null;
        handleDeleteUser($id);
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method Not Allowed']);
}
