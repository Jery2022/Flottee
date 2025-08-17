<?php
// Contrôleur pour afficher la page administration Amin ou Employé selon le rôle.

namespace App\Controllers;

class AdminController
{
    
    // Méthode pour afficher les pages de l'Administration
    public function show()
    {
        session_start();

        $role = $_SESSION['role'] ?? '';

        // Routing simple pour les vues
        $route = $_GET['route'] ?? '';

        $allowedViews = [
            'users',
            'accueil',
            'reservations',
            'vehicles',
            'contact',
        ]; // Vues autorisées

        // Si une vue est demandée et que l'utilisateur est connecté, on sert la vue
        if (isset($_SESSION['user_id']) && in_array($route, $allowedViews)) {
            require_once __DIR__ . '/../views/' . $route . 'View.php';
            exit;
        }

        // Si l'utilisateur est connecté sans route, on le redirige vers son dashboard
        if (isset($_SESSION['user_id'])) {
            if ($role === 'admin') {
                header('Location: /admin_dashboard.php');
                exit;
            } elseif ($role === 'employe') {
                header('Location: /employe_dashboard.php');
                exit;
            }
        }

        // Sinon, page de login
        header('Location: /login.php');
        exit;
    }
}