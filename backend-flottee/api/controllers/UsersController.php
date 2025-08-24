<?php


namespace App\Controllers;

use Core\Response;
use App\Models\UsersModel;
use App\Helpers\AuthHelper;
use App\Helpers\AuthMiddleware;

class UsersController
{
    protected UsersModel $model;

    public function __construct()
    {
        $this->model = new UsersModel();
    }

    public function index()
    {
        AuthMiddleware::requireRole('admin');

        $filters = [
            'name' => $_GET['name'] ?? null,
            'role' => $_GET['role'] ?? null,
            'status' => $_GET['status'] ?? null,
            'visibility' => $_GET['visibility'] ?? 'visible',
        ];
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $perPage = isset($_GET['perPage']) ? (int)$_GET['perPage'] : 10;

        $data = $this->model->getPaginated($filters, $page, $perPage);

        Response::json(['status' => 'success', 'data' => $data]);
    }

    public function show($id)
    {
        $auth = AuthHelper::getAuthenticatedUser();

        if (!$id) {
            Response::error('ID manquant', 400);
        }

        if ($auth['role'] !== 'admin' && $auth['user_id'] != $id) {
            Response::error('Accès interdit', 403);
        }

        $user = $this->model->getById($id);
        if ($user) {
            Response::json($user);
        } else {
            Response::error('Utilisateur non trouvé', 404);
        }
    }

    public function store()
    {
        $auth = AuthHelper::getAuthenticatedUser();

        // Seul un admin peut créer un utilisateur
        if (!$auth || $auth['role'] !== 'admin') {
            return Response::error('Accès interdit : seuls les administrateurs peuvent créer des utilisateurs', 403);
        }

        $data = json_decode(file_get_contents('php://input'), true);

        if (!is_array($data) || count($data) === 0) {
            return Response::error('Données invalides ou manquantes', 400);
        }

        $allowedFields = ['first_name', 'last_name', 'pseudo', 'email', 'password', 'role'];
        $data = array_intersect_key($data, array_flip($allowedFields));

        if (empty($data['email']) || empty($data['password'])) {
            return Response::error('Email et mot de passe sont requis', 400);
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return Response::error('Format d’email invalide', 422);
        }

        if (strlen($data['password']) < 8) {
            return Response::error('Le mot de passe doit contenir au moins 8 caractères', 422);
        }

        $existingUserByEmail = $this->model->findByEmail($data['email']);
        $existingUserByPseudo = $this->model->findByPseudo($data['pseudo'] ?? '');

        if ($existingUserByEmail || $existingUserByPseudo) {
            return Response::error('Un utilisateur avec cet email ou pseudo existe déjà', 409);
        }

        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

        $newUserId = $this->model->create($data);

        if ($newUserId) {
            $newUser = $this->model->getById($newUserId);
            return Response::json([
                'success' => true,
                'message' => 'Utilisateur créé avec succès',
                'data' => $newUser
            ]);
        } else {
            return Response::error('Échec de la création', 500);
        }
    }


    public function update($id)
    {
        $auth = AuthHelper::getAuthenticatedUser();

        // Seul un admin peut modifier un utilisateur
        if (!$auth || $auth['role'] !== 'admin') {
            return Response::error('Accès interdit : seuls les administrateurs peuvent modifier des utilisateurs', 403);
        }

        $user = $this->model->getById($id);
        if (!$user) {
            return Response::error('Utilisateur inexistant', 404);
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $allowedFields = ['first_name', 'last_name', 'pseudo', 'email', 'password', 'role'];
        $data = array_intersect_key($data, array_flip($allowedFields));

        if (!is_array($data) || count($data) === 0) {
            return Response::error('Données manquantes ou mal formées', 400);
        }

        if (isset($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return Response::error('Format d’email invalide', 422);
        }

        if (isset($data['password']) && strlen($data['password']) < 8) {
            return Response::error('Le mot de passe doit contenir au moins 8 caractères', 422);
        }

        if (isset($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        $success = $this->model->update($id, $data);

        if ($success) {
            $updatedUser = $this->model->getById($id);
            return Response::json([
                'success' => true,
                'message' => 'Utilisateur mis à jour avec succès',
                'data' => $updatedUser
            ]);
        } else {
            return Response::error("Échec de la mise à jour", 500);
        }
    }


    public function destroy($id)
    {
        $auth = AuthHelper::getAuthenticatedUser();

        if (!$auth || $auth['role'] !== 'admin') {
            return Response::error('Accès interdit : seuls les administrateurs peuvent supprimer des utilisateurs', 403);
        }

        if (!$id || !is_numeric($id)) {
            return Response::error('ID invalide ou manquant', 400);
        }

        // Vérification de l'existence en base
        $user = $this->model->getById($id);
        if (!$user) {
            return Response::error('Utilisateur non trouvé', 404);
        }

        // Vérifie s’il est déjà supprimé
        if (!empty($user['deleted_at'])) {
            return Response::error('Utilisateur déjà supprimé', 410); // 410 Gone
        }

        // Suppression logique
        $success = $this->model->delete($id);

        if ($success) {
            return Response::json([
                'success' => true,
                'message' => 'Utilisateur supprimé avec succès',
                'data' => [
                    'id' => $id,
                    'deleted_at' => date('Y-m-d H:i:s')
                ]
            ]);
        } else {
            return Response::error('Échec de la suppression', 500);
        }
    }

    public function findByEmail($email)
    {
        $user = $this->model->getByEmail($email);
        if ($user) {
            Response::success(json_encode($user));
        } else {
            Response::error('Utilisateur non trouvé', 404);
        }
    }

    public function restore($id)
    {
        $auth = AuthHelper::getAuthenticatedUser();

        if (!$auth || $auth['role'] !== 'admin') {
            return Response::error('Accès interdit : seuls les administrateurs peuvent supprimer des utilisateurs', 403);
        }

        if (!$id || !is_numeric($id)) {
            return Response::error('ID invalide ou manquant', 400);
        }

        // Vérification des droits
        if ($auth['user_id'] !== $id && $auth['role'] !== 'admin') {
            return Response::error('Action interdite', 403);
        }

        // Vérification de l'existence
        $user = $this->model->getByIdIncludingDeleted($id);
        if (!$user) {
            return Response::error('Utilisateur non trouvé', 404);
        }

        // Vérification qu’il est bien supprimé
        if (empty($user['deleted_at'])) {
            return Response::error('Utilisateur déjà actif', 409); // 409 Conflict
        }

        // Restauration
        $success = $this->model->restore($id);

        if ($success) {
            return Response::json([
                'success' => true,
                'message' => 'Utilisateur restauré avec succès',
                'data' => [
                    'id' => $id,
                    'restored_at' => date('Y-m-d H:i:s')
                ]
            ]);
        } else {
            return Response::error('Échec de la restauration', 500);
        }
    }
}
