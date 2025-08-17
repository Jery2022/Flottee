<?php

namespace Core;

use Core\Response;

class Router
{
    private array $routes = [];
    private string $groupPrefix = '';

    // Méthodes raccourcies pour les routes
    public function get(string $path, string $action): void
    {
        $this->add('GET', $path, $action);
    }

    public function post(string $path, string $action): void
    {
        $this->add('POST', $path, $action);
    }

    public function put(string $path, string $action): void
    {
        $this->add('PUT', $path, $action);
    }

    public function delete(string $path, string $action): void
    {
        $this->add('DELETE', $path, $action);
    }

    public function add(string $method, string $path, string $action): void
    {
        // Normalisation du préfixe et du chemin
        $fullPath = rtrim($this->groupPrefix, '/') . '/' . ltrim($path, '/');
        $fullPath = preg_replace('#/+#', '/', $fullPath); // évite les doubles slash

        $pattern = preg_replace('#\{([a-zA-Z_][a-zA-Z0-9_]*)\}#', '(?P<\1>[^/]+)', $fullPath);
        $pattern = '#^' . $pattern . '$#';

        $this->routes[] = [
            'method' => strtoupper($method),
            'path' => $fullPath,
            'action' => $action,
            'pattern' => $pattern
        ];
    }

    public function group(string $prefix, callable $callback): void
    {
        $previousPrefix = $this->groupPrefix;
        $this->groupPrefix .= rtrim($prefix, '/');
        $callback($this);
        $this->groupPrefix = $previousPrefix;
    }

    public function resource(string $basePath, string $controller): void
    {
        $this->add('GET', $basePath, "$controller@index");
        $this->add('GET', "$basePath/{id}", "$controller@show");
        $this->add('POST', $basePath, "$controller@store");
        $this->add('PUT', "$basePath/{id}", "$controller@update");
        $this->add('DELETE', "$basePath/{id}", "$controller@destroy");
    }

    public function dispatch(string $uri, string $requestMethod): void
    {
        $uri = preg_replace('#^/index\.php#', '', $uri);
        $uri = parse_url($uri, PHP_URL_PATH);
        $method = strtoupper($requestMethod);

        $allowedMethods = ['GET', 'POST', 'PUT', 'DELETE'];
        if (!in_array($method, $allowedMethods)) {
            Response::json(['error' => 'Méthode HTTP non autorisée'], 405);
            return;
        }

        foreach ($this->routes as $route) {
            if ($route['method'] === $method && preg_match($route['pattern'], $uri, $matches)) {
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                $this->callAction($route['action'], $params);
                return; // Ajout du return pour éviter plusieurs réponses
            }
        }

        Response::json(['error' => 'Route non trouvée'], 404);
    }

    protected function callAction(string $action, array $params): void
    {
        [$controllerName, $methodName] = explode('@', $action);
        $controllerClass = "\\App\\Controllers\\" . $controllerName;

        if (!class_exists($controllerClass)) {
            Response::json(['error' => "Contrôleur $controllerClass introuvable"], 500);
            return;
        }

        $controller = new $controllerClass();

        if (!method_exists($controller, $methodName)) {
            Response::json(['error' => "Méthode $methodName introuvable dans $controllerClass"], 500);
            return;
        }

        call_user_func_array([$controller, $methodName], $params);
    }
}