<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/bootstrap.php';

use Core\Router;
use App\Helpers\JWTHelper;
use App\Helpers\AuthMiddleware;

$requestUri = $_SERVER['REQUEST_URI'];
$isApiRoute = str_starts_with($requestUri, '/api/');

if ($isApiRoute) {

    //Pour accepter plusieurs origines
    $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
    $allowed = ['http://localhost:3000', 'http://localhost:8000'];
    if (in_array($origin, $allowed)) {
        header("Access-Control-Allow-Origin: $origin");
    }
    header("Access-Control-Allow-Credentials: true");
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization");
    header("Content-Type: application/json");
}

if ($isApiRoute && $_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$router = new Router();

$router->add('GET', '/', 'AccueilController@index'); // Affiche la page d'accueil, OK
$router->add('GET', '/api/routes/test', 'TestController@status'); // Affiche la page de test, OK
$router->add('GET', '/api/debug/jwt', 'jwtController@handle'); // Affiche la page de test du jwt, OK
$router->add('GET', '/login', 'AdminController@show'); // Affiche la page de login, OK

$router->group('/api/routes', function ($r) {
    $r->resource('/users', 'UsersController'); // CRUD pour les utilisateurs
    $r->resource('/vehicles', 'VehiclesController'); // CRUD pour les véhicules
    $r->resource('/articles', 'ArticleController'); // CRUD pour les articles(informations, notes, notifications)
    $r->add('PUT', '/users/{id}', 'UsersController@update');
    $r->add('DELETE', '/users/{id}', 'UsersController@delete');
    $r->add('POST', '/users/{id}/password', 'UsersController@updatePassword');
    $r->add('PUT', '/vehicles/{id}', 'VehiclesController@update');
    $r->add('DELETE', '/vehicles/{id}', 'VehiclesController@delete');
    $r->add('POST', '/vehicles/{id}/reserve', 'VehiclesController@reserve');
    $r->add('GET', '/articles/{slug}', 'ArticleController@read');
    $r->add('GET', '/csrf-token', 'AuthController@getCsrfToken');
});

$router->group('/api/routes', function ($r) {
    $r->add('POST', '/auth', 'AuthController@login');
    $r->add('POST', '/auth/handleForm', 'AuthController@handleForm'); // Gère le formulaire de connexion
    $r->add('POST', '/auth/logout', 'AuthController@logout');
    $r->add('POST', '/auth/register', 'AuthController@register');
    $r->add('POST', '/auth/forgot-password', 'AuthController@forgotPassword');
    $r->add('POST', '/auth/reset-password', 'AuthController@resetPassword');
    $r->add('POST', '/auth/verify-email', 'AuthController@verifyEmail');
    $r->add('POST', '/auth/resend-verification', 'AuthController@resendVerification');
    $r->add('POST', '/auth/change-password', 'AuthController@changePassword');
    $r->add('POST', '/auth/update-profile', 'AuthController@updateProfile');
    $r->add('GET', '/users', 'UsersController@show');
    $r->add('GET', '/vehicles', 'VehiclesController@show');
});

$router->group('/admin', function ($r) {
    $r->add('GET', '/dashboard', 'DashboardController@index');
});


$router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
