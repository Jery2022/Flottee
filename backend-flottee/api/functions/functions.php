<?php 

require_once __DIR__ . '/../../vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;

function getBearerToken() {
    $headers = getAuthorizationHeader();

    if (!empty($headers)) {
        if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
            return $matches[1];
        }
    }

    return null;
}

function getAuthorizationHeader() {
    if (isset($_SERVER['Authorization'])) {
        return trim($_SERVER["Authorization"]);
    }

    if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
        return trim($_SERVER["HTTP_AUTHORIZATION"]);
    }

    // Pour les serveurs Apache avec mod_rewrite
    if (function_exists('apache_request_headers')) {
        $requestHeaders = apache_request_headers();
        if (isset($requestHeaders['Authorization'])) {
            return trim($requestHeaders['Authorization']);
        }
    }

    return null;
}

function authMiddleware($requiredRole = null) {
    $jwt = getBearerToken();

    if (!$jwt) {
        http_response_code(401);
        echo json_encode(['error' => 'Token manquant']);
        exit;
    }

    try {
        $secretKey = $_ENV['JWT_SECRET_KEY'] ?: getenv('JWT_SECRET_KEY');

        // LOG Vérifie si la clé secrète est définie et valide
        if (!is_string($secretKey) || empty($secretKey)) {
        throw new \Exception('JWT secret key is missing or invalid');
        }

        // Décode le JWT
        $decoded = JWT::decode($jwt, new Key($secretKey, 'HS256'));

        // Vérifie l'expiration
        if (isset($decoded->exp) && $decoded->exp < time()) {
            http_response_code(401);
            echo json_encode(['error' => 'Token expiré']);
            exit;
        }

        // Vérifie le rôle si nécessaire
        $userRole = $decoded->data->role ?? 'user';
        if ($requiredRole && $userRole !== $requiredRole) {
            http_response_code(403);
            echo json_encode(['error' => 'Accès interdit']);
            exit;
        }

        // Injecte les données utilisateur dans une variable globale
        $GLOBALS['auth_user'] = [
            'user_id' => $decoded->data->user_id ?? null,
            'role'    => $userRole,
            'email'   => $decoded->data->email ?? null,
            'pseudo'    => $decoded->data->pseudo ?? null,
            'status' => $decoded->data->status ?? null,
            'first_name' => $decoded->data->first_name ?? null,
        ];

    } catch (ExpiredException $e) {
        http_response_code(401);
        echo json_encode(['error' => 'Token expiré']);
        exit;
    } catch (Exception $e) {
        http_response_code(401);
        echo json_encode(['error' => 'Token invalide']);
        exit;
    }
}
