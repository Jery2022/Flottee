<?php
namespace App\Controllers;

class AccueilController
{
    public function index()
    {
        header('Content-Type: application/json');
        echo json_encode([
            'message' => 'Bienvenue sur l’API !',
            'status' => 'success'
        ]);
        exit;
    }
}
