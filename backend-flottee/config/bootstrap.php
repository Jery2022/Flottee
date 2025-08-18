<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

$envPath = __DIR__ . '/../';

if (file_exists($envPath . '.env')) {
    $dotenv = Dotenv::createImmutable($envPath);
    $dotenv->load();
    //echo ('JWT_SECRET_KEY: ' . var_export($_ENV['JWT_SECRET_KEY'] ?? null, true)); // log de débogage
} else {
    throw new RuntimeException('.env file not found in project root'); 
}
