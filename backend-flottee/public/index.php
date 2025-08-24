<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/bootstrap.php';

use Core\Router;
use App\Helpers\JWTHelper;
use App\Helpers\AuthMiddleware;

$requestUri = $_SERVER['REQUEST_URI'];
$isApiRoute = str_starts_with($requestUri, '/api/');

if ($isApiRoute) {

    //Pour accepter plusieurs origines
    $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
    $allowed = ['http://localhost:3000', 'http://localhost:8000'];
    if (in_array($origin, $allowed)) {
        header("Access-Control-Allow-Origin: $origin");
    }
    header("Access-Control-Allow-Credentials: true");
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization");
    header("Content-Type: application/json");
}

if ($isApiRoute && $_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$router = new Router();

// Chargement des définitions de routes 
$routes = require __DIR__ . '/../api/routes/web.php';
$routes($router);

// Lancer le routeur
$router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
