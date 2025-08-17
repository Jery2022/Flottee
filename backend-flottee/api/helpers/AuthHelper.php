<?php

namespace App\Helpers;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Exception;
use Core\Response;

class AuthHelper
{
    public static function isAuthenticated(): bool
    {
        $headers = getallheaders();
        $authHeader = $headers['Authorization'] ?? '';

        if (!preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            return false;
        }

        $token = $matches[1];

        try {
            $secretKey = getenv('JWT_SECRET_KEY');
            JWT::decode($token, new Key($secretKey, 'HS256'));
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function getAuthenticatedUser(): ?array
    {
        if (!self::isAuthenticated()) {
            Response::json(['error' => 'Token manquant ou invalide'], 401);
            return null;
        }

        $headers = getallheaders();
        preg_match('/Bearer\s(\S+)/', $headers['Authorization'], $matches);
        $token = $matches[1];

        try {
            $secretKey = getenv('JWT_SECRET_KEY');
            $decoded = JWT::decode($token, new Key($secretKey, 'HS256'));

            return [
                'user_id' => $decoded->data->user_id ?? null,
                'role' => $decoded->data->role ?? 'user'
            ];
        } catch (Exception $e) {
            Response::json(['error' => 'Token invalide'], 403);
            return null;
        }
    }

    public static function getAuthenticatedUserId(): ?int
    {
        $user = self::getAuthenticatedUser();
        return $user['user_id'] ?? null;
    }
    
}
