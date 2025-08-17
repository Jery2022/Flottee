<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../controllers/UserController.php';

// $pdo = getPDO();
$targetId = $_GET['id'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    handleGetUser(/*$pdo, */$targetId);
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Méthode non autorisée']);
}
