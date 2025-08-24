<?php

namespace App\Controllers;

use Core\Response;
use App\Models\UsersModel;
use App\Helpers\JWTHelper;
use App\Helpers\AuthMiddleware;

class AuthController
{
    public function handleForm()
    {
        session_start();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? null;
            $password = $_POST['password'] ?? '';
            $csrf_token = $_POST['csrf_token'] ?? '';

            require_once __DIR__ . '/../core/Csrf.php';
            if (!verifyCsrfToken($csrf_token)) {
                error_log("[" . date('Y-m-d H:i:s') . "] CSRF token invalide pour l'email: $email", 3, __DIR__ . '/../../logs/auth.log');
                Response::error('Erreur de sécurité CSRF.', 403);
                return;
            }

            try {
                $userModel = new UsersModel();
                $user = $userModel->getByEmail($email);

                if (!$user) {
                    error_log("[" . date('Y-m-d H:i:s') . "] Utilisateur non trouvé: $email", 3, __DIR__ . '/../../logs/auth.log');
                    Response::error('Utilisateur non trouvé.', 404);
                    return;
                }

                if (password_verify($password, $user['password'])) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['role'] = $user['role'];
                    $_SESSION['status'] = $user['status'];

                    session_regenerate_id(true);

                    $jwtHelper = new JWTHelper();
                    $_SESSION['jwt'] = $jwtHelper->generateJWT([
                        'id' => $user['id'],
                        'email' => $user['email'],
                        'role' => $user['role'],
                        'status' => $user['status']
                    ]);

                    error_log("[" . date('Y-m-d H:i:s') . "] Connexion réussie pour l'utilisateur ID: {$user['id']} avec rôle: {$user['role']} et statut: {$user['status']}", 3, __DIR__ . '/../../logs/auth.log');

                    $redirect = '';
                    if ($user['role'] === 'admin' && $user['status'] === 'active') {
                        $redirect = '/admin_dashboard.php';
                    } elseif ($user['role'] === 'employe' && $user['status'] === 'active') {
                        $redirect = '/employe_dashboard.php';
                    } else {
                        $redirect = '/login.php';
                    }

                    Response::json([
                        'status' => 'success',
                        'redirect' => $redirect,
                        'token' => $_SESSION['jwt'], // Ajouter le token à la réponse
                        'user' => [
                            'id' => $user['id'],
                            'email' => $user['email'],
                            'role' => $user['role'],
                            'status' => $user['status']
                        ]
                    ]);
                } else {
                    error_log("[" . date('Y-m-d H:i:s') . "] Mot de passe incorrect pour l'utilisateur: $email", 3, __DIR__ . '/../../logs/auth.log');
                    Response::error('Identifiants incorrects.', 401);
                }
            } catch (\PDOException $e) {
                error_log("Erreur de base de données dans AuthController: " . $e->getMessage(), 3, __DIR__ . '/../../logs/auth.log');
                Response::error("Erreur de connexion au serveur. Veuillez réessayer plus tard.", 500);
            } catch (\Exception $e) {
                error_log("Erreur inattendue dans AuthController: " . $e->getMessage(), 3, __DIR__ . '/../../logs/auth.log');
                Response::error("Une erreur inattendue est survenue.", 500);
            }
        } else {
            error_log("[" . date('Y-m-d H:i:s') . "] Méthode HTTP non autorisée: {$_SERVER['REQUEST_METHOD']} ", 3, __DIR__ . '/../../logs/auth.log');
            Response::error('Méthode non autorisée.', 405);
        }
    }

    public function login()
    {
        session_start();

        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input || !is_array($input) || empty($input['email']) || empty($input['password'])) {
            error_log("[" . date('Y-m-d H:i:s') . "] Requête JSON invalide ou champs manquants", 3, __DIR__ . '/../../logs/auth.log');
            Response::error('Requête invalide', 400);
        }

        $email = filter_var($input['email'], FILTER_SANITIZE_EMAIL);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            error_log("[" . date('Y-m-d H:i:s') . "] Format d'email invalide: $email", 3, __DIR__ . '/../../logs/auth.log');
            Response::error("Format d'email invalide", 422);
        }

        $userModel = new UsersModel();
        $user = $userModel->getByEmail($email);

        if (!$user || !password_verify($input['password'], $user['password'])) {
            error_log("[" . date('Y-m-d H:i:s') . "] Échec de l'authentification pour: $email", 3, __DIR__ . '/../../logs/auth.log');
            Response::error('Identifiants incorrects', 401);
        }

        $jwtHelper = new JWTHelper();
        $token = $jwtHelper->generateJWT([
            'id' => $user['id'],
            'email' => $user['email'],
            'role' => $user['role'],
            'status' => $user['status'],
            'first_name' => $user['first_name'] ?? null
        ]);


        $_SESSION['jwt'] = $token;
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['status'] = $user['status'];

        error_log("[" . date('Y-m-d H:i:s') . "] Connexion API réussie pour l'utilisateur ID: {$user['id']} avec rôle: {$user['role']} et statut: {$user['status']}", 3, __DIR__ . '/../../logs/auth.log');

        $redirect = '';
        if ($user['status'] === 'active') {
            if ($user['role'] === 'admin') {
                $redirect = 'admin_dashboard.php';
            } elseif ($user['role'] === 'employe') {
                $redirect = 'employe_dashboard.php';
            } else {
                $redirect = 'login.php';
            }
        } else {
            $redirect = 'access_denied.php';
        }

        Response::json([
            'status' => 'success',
            'token' => $token,
            'redirect' => $redirect,
            'user' => [
                'id' => $user['id'],
                'email' => $user['email'],
                'name' => $user['name'] ?? null,
                'status' => $user['status'] ?? null
            ]
        ]);
    }

    public function getCsrfToken()
    {
        session_start();
        require_once __DIR__ . '/../core/Csrf.php';
        $token = generateCsrfToken();
        Response::json(['csrf_token' => $token]);
    }

    public function route($uri)
    {
        switch ($uri) {
            case '/api/routes/csrf-token':
                $this->getCsrfToken();
                break;
            case '/api/routes/auth/handleForm':
                $this->handleForm();
                break;
            case '/api/routes/auth/login':
                $this->login();
                break;
            default:
                Response::error('Route non trouvée', 404);
        }
    }
}
