<?php

// index.php ou routes.php
require_once __DIR__ . '/../controllers/JwtDebugController.php';

if ($_SERVER['REQUEST_URI'] === '/api/debug/jwt') {
    $controller = new JwtDebugController();
    $controller->handle();
    exit;
}
