<?php

namespace App\Controllers;

use Core\Response;

/**
 * Méthode pour tester le statut de l'API
 * @return void
*/

class TestController
{
  public function status()
    {
        Response::json(['status' => 'OK', 'message' => 'Route /api/routes/test fonctionne']);
    }
}