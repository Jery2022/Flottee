<?php // backend-flottee/api/helpers/AuthHelper.php

namespace App\Helpers;

use App\Helpers\Logger;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Exception;
use Core\Response;
use App\Helpers\EnvLoader;

class AuthHelper
{
     public static function isAuthenticated(): bool
     {
          try {
               EnvLoader::load(); // Charge les variables .env

               $token = self::extractToken();
               if (!$token) return false;

               if (EnvLoader::isTokenExpired($token)) {
                    logger::log('[Auth] Token expiré.', 'WARNING');
                    Response::json(['error' => 'Token expiré'], 403);
                    return false;
               }

               $decoded = self::decodeToken($token);
               return isset($decoded->data->user_id);
          } catch (\Exception $e) {
               logger::log('[Auth] Échec d’authentification : ' . $e->getMessage(), 'ERROR');
               Response::json(['error' => 'Token invalide'], 403);
               return false;
          }
     }


     public static function getAuthenticatedUser(): ?array
     {
          try {
               EnvLoader::load(); // Charge les variables .env

               $token = self::extractToken();

               if (!$token) {
                    Response::json([['error' => 'Token manquant ou invalide']], 401);
                    return null;
               }

               if (EnvLoader::isTokenExpired($token)) {
                    logger::log('[Auth] Token expiré.', 'WARNING');
                    Response::json(['error' => 'Token expiré'], 403);
                    return null;
               }

               $decoded = self::decodeToken($token);
               return [
                    'user_id' => $decoded->data->user_id ?? null,
                    'role' => $decoded->data->role ?? 'user'
               ];
          } catch (\Exception $e) {
               Logger::log('[Auth] Token invalide : ' . $e->getMessage(), 'ERROR');
               Response::json(['error' => 'Token invalide'], 403);
               return null;
          }
     }

     public static function getAuthenticatedUserId(): ?int
     {
          $user = self::getAuthenticatedUser();
          return $user['user_id'] ?? null;
     }

     public static function checkAdmin(): void
     {
          $user = self::getAuthenticatedUser();
          if (!$user || $user['role'] !== 'admin') {
               Response::error('Accès interdit. Vous devez être administrateur.', 403);
               exit;
          }
     }

     // Méthodes privées

     private static function extractToken(): ?string
     {
          $headers = function_exists('getallheaders') ? getallheaders() : [];
          $authHeader = $headers['Authorization'] ?? $_SERVER['HTTP_AUTHORIZATION'] ?? '';

          if (preg_match('/Bearer\\s(\\S+)/', $authHeader, $matches)) {
               return $matches[1];
          }

          Logger::log('[Auth] En-tête Authorization invalide ou absent.', 'WARNING');
          return null;
     }

     private static function getSecretKey(): string
     {
          $secretKey =  EnvLoader::getJwtSecretKey();

          if (!is_string($secretKey) || empty($secretKey)) {
               throw new \Exception('Clé secrète JWT absente ou invalide.');
          }
          return $secretKey;
     }

     private static function decodeToken(string $token): object
     {
          return JWT::decode($token, new Key(self::getSecretKey(), 'HS256'));
     }
}
