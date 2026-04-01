<?php

namespace App\Middleware;

use App\Core\Auth;
use App\Core\Template;

class AuthMiddleware
{
    public static function requireAuth(): void
    {
        if (!Auth::check()) {
            header('Location: /connexion');
            exit;
        }
    }

    public static function requireRole(string ...$roles): void
    {
        self::requireAuth();
        $userRole = Auth::role();

        if (in_array($userRole, $roles)) {
            return;
        }

        // Si visiteur essaie d'aller ailleurs, on le bloque sur la page d'attente
        if ($userRole === 'visiteur') {
            if ($_SERVER['REQUEST_URI'] !== '/inscription/en-attente') {
                header('Location: /inscription/en-attente');
                exit;
            }
            return;
        }

        http_response_code(403);
        echo "Accès refusé : Vous n'avez pas les droits nécessaires.";
        exit;
    }

    public static function verifyCsrf(): void
    {
        if (!Auth::verifyCsrf()) {
            http_response_code(403);
            die("Erreur CSRF : Session expirée ou formulaire invalide.");
        }
    }
}
