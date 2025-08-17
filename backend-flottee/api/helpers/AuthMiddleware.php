<?php

namespace App\Helpers;

use App\Helpers\JWTHelper;
use Core\Response;

/**
 * AuthMiddleware gère les autorisations d'accès basées sur le rôle de l'utilisateur.
 * Il vérifie si l'utilisateur est authentifié et s'il a le rôle requis pour accéder à certaines routes.
 * Il enregistre également les tentatives d'accès non autorisées dans un fichier de log pour des raisons de sécurité.
 * Il utilise JWTHelper pour extraire les données de l'utilisateur à partir du token JWT.
 */

class AuthMiddleware
{
    private static function logSecurity(string $message)
    {
        $logDir = __DIR__ . '/../../logs';
        if (!is_dir($logDir)) {
            mkdir($logDir, 0775, true);
        }

        $logFile = $logDir . '/security.log';
        $timestamp = date('Y-m-d H:i:s');
        file_put_contents($logFile, "[$timestamp] $message\n", FILE_APPEND);
    }

    public static function requireRole(string $role)
    {
        $user = JWTHelper::getUserDataFromJWT();

        if (!$user) {
            self::logSecurity("Accès refusé : utilisateur non authentifié");
            Response::error('Authentification requise', 401);
            exit;
        }

        if ($user['status'] !== 'active') {
            self::logSecurity("Accès refusé : compte inactif pour l'utilisateur ID {$user['id']}");
            Response::error('Votre compte est désactivé, contactez l\'administrateur.', 403);
            exit;
        }

        if ($user['role'] !== $role) {
            self::logSecurity("Accès refusé : rôle insuffisant pour l'utilisateur ID {$user['id']}");
            Response::error('Accès interdit : rôle insuffisant', 403);
            exit;
        }
    }

    public static function requireAuth()
    {
        $user = JWTHelper::getUserDataFromJWT();

        if (!$user) {
            self::logSecurity("Accès refusé : utilisateur non authentifié");
            Response::error('Authentification requise', 401);
            exit;
        }

        if ($user['status'] !== 'active') {
            self::logSecurity("Accès refusé : compte inactif pour l'utilisateur ID {$user['id']}");
            Response::error('Votre compte est désactivé, contactez l\'administrateur.', 403);
            exit;
        }
    }
}