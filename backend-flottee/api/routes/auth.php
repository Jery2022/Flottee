<?php

use App\Controllers\AuthController;

return [
    'POST' => function () {
        // Définition des en-têtes CORS 
        header("Content-Type: application/json");
        header("Access-Control-Allow-Origin: http://localhost:3000");
        header("Access-Control-Allow-Credentials: true");
        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Authorization");
        
        // Gérer les requêtes OPTIONS pour CORS
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(204);
            exit;
        }

        $controller = new AuthController();
        $controller->login();
    },
];