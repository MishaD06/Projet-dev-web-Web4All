<?php

namespace App\Core;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFunction;

class Template
{
    private static ?Environment $twig = null;

    public static function getInstance(): Environment
    {
        if (self::$twig === null) {
            $cfg     = require dirname(__DIR__, 2) . '/config/app.php';
            $loader  = new FilesystemLoader(dirname(__DIR__) . '/Views');

            self::$twig = new Environment($loader, [
                'cache'       => $cfg['env'] === 'production'
                    ? dirname(__DIR__, 2) . '/storage/cache'
                    : false,
                'auto_reload' => true,
                'autoescape'  => 'html',   // protection XSS automatique
            ]);

            // Fonctions globales disponibles dans tous les templates
            self::$twig->addFunction(new TwigFunction('csrf_field', function () {
                $token = Auth::csrfToken();
                return "<input type=\"hidden\" name=\"_csrf\" value=\"{$token}\">";
            }, ['is_safe' => ['html']]));

            self::$twig->addFunction(new TwigFunction('method_field', function (string $method) {
                return "<input type=\"hidden\" name=\"_method\" value=\"{$method}\">";
            }, ['is_safe' => ['html']]));

            self::$twig->addFunction(new TwigFunction('asset', function (string $path) use ($cfg) {
                return rtrim($cfg['url'], '/') . '/public/' . ltrim($path, '/');
            }));

            // Variable globale : utilisateur connecté
            self::$twig->addGlobal('auth_user', Auth::user());
            self::$twig->addGlobal('app_name', $cfg['name']);
        }

        return self::$twig;
    }

    /** Rend un template et envoie la réponse */
    public static function render(string $template, array $data = []): void
    {
        echo self::getInstance()->render($template, $data);
    }
}
