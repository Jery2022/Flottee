<?php

namespace App\Controllers;

use Core\Response;
use App\Helpers\JWTHelper;

class DashboardController
{
    public function index()
    {
        session_start();

        var_dump($_SESSION); // Pour débogage, à supprimer en production

        \App\Helpers\AuthMiddleware::requireRole('admin');
        
        if (!isset($_SESSION['jwt'])) {
            Response::json(['error' => true, 'message' => 'Authentification requise'], 401);
            exit;
        }

        $headers = getallheaders();
        $authHeader = $headers['Authorization'] ?? '';

        if (!preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            Response::error('Token manquant', 401);
        }

        $token = $matches[1];
        $jwtHelper = new JWTHelper();

        try {
            $payload = $jwtHelper->decodeJWT($token);
            $data = $payload['data'] ?? [];

            if ($data['role'] !== 'admin' || $data['status'] !== 'active') {
                Response::error('Accès refusé', 403);
            }

            Response::json([
                'message' => 'Bienvenue dans le dashboard admin',
                'user' => $data
            ]);
        } catch (\Exception $e) {
            Response::error('Token invalide', 401);
        }
    }
}