<?php

// backend-flottee/api/routes/web.php

use Core\Router;

/**
 * Définit toutes les routes de l'application.
 *
 * @param Router $router L'instance du routeur.
 */
return function (Router $router) {
    $router->add('GET', '/', 'AccueilController@index');
    $router->add('GET', '/api/routes/test', 'TestController@status');
    $router->add('GET', '/api/debug/jwt', 'jwtController@handle');
    $router->add('GET', '/login', 'AdminController@show');

    // Groupe pour les routes de l'API (ressources, etc.)
    $router->group('/api/routes', function ($r) {
        // CRUD pour les utilisateurs
        $r->resource('/users', 'UsersController');
        $r->add('PUT', '/users/{id}', 'UsersController@update');
        $r->add('DELETE', '/users/{id}', 'UsersController@delete');
        $r->add('POST', '/users/{id}/password', 'UsersController@updatePassword');
        $r->add('POST', '/users/{id}/restore', 'UsersController@restore');
        $r->add('GET', '/users/{id}', 'UsersController@show');
        $r->add('POST', '/user', 'UsersController@store');

        // CRUD pour les véhicules
        $r->resource('/vehicles', 'VehiclesController');
        $r->add('PUT', '/vehicles/{id}', 'VehiclesController@update');
        $r->add('DELETE', '/vehicles/{id}', 'VehiclesController@delete');
        $r->add('POST', '/vehicles/{id}/reserve', 'VehiclesController@reserve');
        $r->add('GET', '/vehicles/{id}', 'VehiclesController@show');

        // CRUD pour les articles
        $r->resource('/articles', 'ArticleController');
        $r->add('GET', '/articles/{slug}', 'ArticleController@read');

        // Autres routes API
        $r->add('GET', '/csrf-token', 'AuthController@getCsrfToken');
    });

    // Groupe pour les routes d'authentification
    $router->group('/api/routes', function ($r) {
        $r->add('POST', '/auth', 'AuthController@login');
        $r->add('POST', '/auth/handleForm', 'AuthController@handleForm');
        $r->add('POST', '/auth/logout', 'AuthController@logout');
        $r->add('POST', '/auth/register', 'AuthController@register');
        $r->add('POST', '/auth/forgot-password', 'AuthController@forgotPassword');
        $r->add('POST', '/auth/reset-password', 'AuthController@resetPassword');
        $r->add('POST', '/auth/verify-email', 'AuthController@verifyEmail');
        $r->add('POST', '/auth/resend-verification', 'AuthController@resendVerification');
        $r->add('POST', '/auth/change-password', 'AuthController@changePassword');
        $r->add('POST', '/auth/update-profile', 'AuthController@updateProfile');
    });

    // Groupe pour les routes d'administration
    $router->group('/admin', function ($r) {
        $r->add('GET', '/dashboard', 'DashboardController@index');
    });
};
