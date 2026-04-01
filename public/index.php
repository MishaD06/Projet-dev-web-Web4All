<?php

declare(strict_types=1);

// ACTIVATION DU DEBUG (À retirer avant la mise en production)
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

// Chargement des variables d'environnement depuis .env
if (file_exists(dirname(__DIR__) . '/.env')) {
    foreach (file(dirname(__DIR__) . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        if (str_starts_with(trim($line), '#')) continue;
        if (str_contains($line, '=')) {
            [$key, $val] = explode('=', $line, 2);
            $_ENV[trim($key)] = trim($val);
            putenv(trim($key) . '=' . trim($val));
        }
    }
}

// Autoload Composer
require dirname(__DIR__) . '/vendor/autoload.php';

// Démarrage session sécurisée
\App\Core\Auth::start();

// Dispatch des routes
$router = require dirname(__DIR__) . '/routes.php';
$router->dispatch();
