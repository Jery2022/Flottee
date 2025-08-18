<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtDebugController
{
    public function handle()
    {
        header('Content-Type: application/json');

        // 🔍 1. Récupération du header Authorization
        $headers = getallheaders();
        $authHeader = $headers['Authorization'] ?? $_SERVER['HTTP_AUTHORIZATION'] ?? null;

        if (!$authHeader || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            http_response_code(400);
            echo json_encode(['error' => 'Header Authorization manquant ou mal formé']);
            return;
        }

        $token = $matches[1];

        // 🔑 2. Récupération de la clé secrète
        $secret = getenv('JWT_SECRET_KEY') ?: 'LaLocaledeMonApplicationMarcheEn2010@'; // Remplace si nécessaire

        if (!$secret) {
            http_response_code(500);
            echo json_encode(['error' => 'Clé secrète JWT absente']);
            return;
        }

        // 📦 3. Décodage du token
        try {
            $decoded = JWT::decode($token, new Key($secret, 'HS256'));
        } catch (Exception $e) {
            http_response_code(401);
            echo json_encode(['error' => 'Décodage échoué', 'message' => $e->getMessage()]);
            return;
        }

        // 🕒 4. Vérification des timestamps
        $currentTime = time();
        $iat = $decoded->iat ?? null;
        $exp = $decoded->exp ?? null;
        $validity = 'OK';

        if ($exp && $currentTime > $exp) {
            $validity = 'expiré';
        } elseif ($iat && $currentTime < $iat) {
            $validity = 'pas encore valide';
        }

        // 🧩 5. Réponse JSON
        echo json_encode([
            'token_recu' => $token,
            'cle_secrete_utilisee' => substr($secret, 0, 5) . '...', // Masquée pour sécurité
            'horloge_serveur' => $currentTime,
            'iat' => $iat,
            'exp' => $exp,
            'validite_temporelle' => $validity,
            'payload' => $decoded,
        ]);
    }
}
