<?php

namespace App\Helpers;

class EnvLoader
{
    public static function load(): void
    {
        if (!isset($_ENV['JWT_SECRET_KEY'])) {
            $dotenv = \Dotenv\Dotenv::createImmutable(__DIR__ . '/../..');
            $dotenv->load();
        }
    }

    public static function getJwtSecretKey(): string
    {
        self::load();
        return $_ENV['JWT_SECRET_KEY'] ?? '';
    }

    public static function isTokenExpired(string $token): bool
    {
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            return true; // Token mal formé
        }

        $payload = json_decode(base64_decode(strtr($parts[1], '-_', '+/')), true);
        if (!isset($payload['exp'])) {
            return true; // Pas de date d'expiration
        }

        return time() > $payload['exp'];
    }
}
