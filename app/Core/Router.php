<?php

namespace App\Core;

class Router
{
    private array $routes = [];

    /** Enregistre une route GET */
    public function get(string $path, string $controller, string $method): void
    {
        $this->addRoute('GET', $path, $controller, $method);
    }

    /** Enregistre une route POST */
    public function post(string $path, string $controller, string $method): void
    {
        $this->addRoute('POST', $path, $controller, $method);
    }

    private function addRoute(string $httpMethod, string $path, string $controller, string $action): void
    {
        // Convertit /offres/{id} en regex : /offres/(?P<id>[^/]+)
        $pattern = preg_replace('/\{([a-zA-Z_]+)\}/', '(?P<$1>[^/]+)', $path);
        $pattern = '#^' . $pattern . '$#';

        $this->routes[] = [
            'method'     => $httpMethod,
            'pattern'    => $pattern,
            'controller' => $controller,
            'action'     => $action,
        ];
    }

    /** Résout l'URL courante et appelle le bon contrôleur */
    public function dispatch(): void
    {
        $uri    = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $method = $_SERVER['REQUEST_METHOD'];

        if ($method === 'POST' && isset($_POST['_method'])) {
            $method = strtoupper($_POST['_method']);
        }

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            if (preg_match($route['pattern'], $uri, $matches)) {
                // Filtre pour ne garder que les paramètres nommés (ex: 'id')
                $params = array_filter(
                    $matches,
                    fn($k) => !is_int($k),
                    ARRAY_FILTER_USE_KEY
                );

                $controllerClass = 'App\\Controllers\\' . $route['controller'];

                if (!class_exists($controllerClass)) {
                    $this->abort(500, "Contrôleur {$controllerClass} introuvable.");
                    return;
                }

                $controller = new $controllerClass();

                if (!method_exists($controller, $route['action'])) {
                    $this->abort(500, "Méthode {$route['action']} introuvable.");
                    return;
                }

                /**
                 * MODIFICATION CRUCIALE ICI :
                 * On utilise array_values($params) pour envoyer les arguments par position.
                 * Cela empêche PHP 8 de comparer le nom de la clé (ex: 'id') avec le nom 
                 * de la variable dans ton contrôleur (ex: $offerId).
                 */
                call_user_func_array([$controller, $route['action']], array_values($params));
                return;
            }
        }

        $this->abort(404, 'Page non trouvée.');
    }

    private function abort(int $code, string $message): void
    {
        http_response_code($code);
        echo "<h1>Erreur {$code}</h1><p>{$message}</p>";
    }
}
