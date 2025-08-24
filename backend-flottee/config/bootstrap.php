<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

$envPath = __DIR__ . '/../';

if (file_exists($envPath . '.env')) {
    $dotenv = Dotenv::createImmutable($envPath);
    $dotenv->load();
} else {
    throw new RuntimeException('.env file not found in project root');
}
