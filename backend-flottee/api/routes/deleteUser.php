<?php

use App\Helpers\AuthHelper;
use App\Models\UsersModel;

use Core\Response;

if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    Response::badRequest('Méthode non autorisée.');
}

$id = AuthHelper::getAuthenticatedUserId();

if (!$id) {
    Response::unauthorized('Utilisateur non authentifié.');
}

// Suppression de l'utilisateur
$userModel = new UsersModel();

if ($userModel->delete($id)) {
    Response::json(['success' => true, 'message' => 'Utilisateur supprimé.']);
} else {
    Response::error('Erreur lors de la suppression.');
}