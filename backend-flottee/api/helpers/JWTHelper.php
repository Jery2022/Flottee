<?php

namespace App\Helpers;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Exception;
use App\Helpers\EnvLoader;

class JWTHelper
{
    protected string $secretKey;
    protected string $issuer;
    protected string $audience;
    protected int $expiration;

    public function __construct()
    {
        $this->secretKey = EnvLoader::getJwtSecretKey(); //getenv('JWT_SECRET_KEY');
        $this->issuer = $_ENV['JWT_ISSUER'] ?? 'http://localhost:8000/backend/api/';
        $this->audience = $_ENV['JWT_AUDIENCE'] ?? 'http://localhost:3000';
        $this->expiration = 60 * 60 * 24;

        if (empty($this->secretKey)) {
            throw new Exception('La clé secrète JWT est absente ou invalide.');
        }
    }

    public function generateJWT(array $data): string
    {
        $issuedAt = time();
        $payload = [
            'iat' => $issuedAt,
            'exp' => $issuedAt + $this->expiration,
            'iss' => $this->issuer,
            'aud' => $this->audience,
            'data' => $data
        ];

        return JWT::encode($payload, $this->secretKey, 'HS256');
    }

    public function decodeJWT(string $token): array
    {
        try {
            $decoded = JWT::decode($token, new Key($this->secretKey, 'HS256'));
            return (array) $decoded;
        } catch (Exception $e) {
            throw new Exception('Token invalide: ' . $e->getMessage());
        }
    }

    public static function validateJWT(string $token): ?object
    {
        try {
            $jwtHelper = new self();
            return JWT::decode($token, new Key($jwtHelper->secretKey, 'HS256'));
        } catch (Exception $e) {
            error_log('JWT validation error: ' . $e->getMessage());
            return null;
        }
    }

    public static function getUserDataFromJWT(): ?array
    {
        $headers = getallheaders();
        $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? null;

        if (!$authHeader) {
            error_log('Authorization header absent');
            return null;
        }

        $token = str_replace('Bearer ', '', $authHeader);

        try {
            $jwtHelper = new self();
            $decoded = $jwtHelper->decodeJWT($token);
            return (array) $decoded['data'];
        } catch (Exception $e) {
            error_log('JWT decode error: ' . $e->getMessage());
            return null;
        }
    }
}
