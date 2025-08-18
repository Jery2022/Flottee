<?php


namespace App\Controllers;

use Core\Response; 
use App\Models\UsersModel; 
use App\Helpers\AuthHelper;

class UsersController { protected UsersModel $model;

public function __construct()
{
    $this->model = new UsersModel();
}

public function index()
{
    $users = $this->model->getAll();
    Response::json($users);
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
    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data) {
        Response::error('Données invalides', 400);
    }

    if (isset($data['password'])) {
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
    }

    $success = $this->model->create($data);
    Response::success($success);
}

public function update($id)
{
    $auth = AuthHelper::getAuthenticatedUser(); 
    $authId = AuthHelper::getAuthenticatedUserId();

    if (!$auth) {
        Response::error('Utilisateur non authentifié', 401);
    }

    if ($authId != $id && $auth['role'] !== 'admin') {
        Response::error('Accès interdit', 403);
    }

    $data = json_decode(file_get_contents('php://input'), true);

    if (empty($data)) {
        Response::error('Données manquantes', 400);
    }

    $success = $this->model->update($id, $data); 
    if ($success) {
        Response::success($success);


    } else {
        Response::error("Échec de la mise à jour", 500);
    }
}

public function destroy($id)
{
    $auth = AuthHelper::getAuthenticatedUser();

    if (!$id) {
        Response::error('ID manquant', 400);
    }

    if ($auth['user_id'] !== $id && $auth['role'] !== 'admin') {
        Response::error('Action interdite', 403);
    }

    $user = $this->model->getById($id);
    if (!$user) {
        Response::error('Utilisateur non trouvé', 404);
    }

    $success = $this->model->delete($id);
    if ($success) {
        Response::success(true);
    } else {
        Response::error('Échec de la suppression', 500);
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

}