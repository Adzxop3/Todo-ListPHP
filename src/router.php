<?php

namespace App;

use App\Controller\TaskController;

class Router
{
    private array $routes;

    public function __construct()
    {
        $this->routes = [
            '/' => [TaskController::class, 'index'],
            '/task/list' => [TaskController::class, 'index'],
            '/task/add' => [TaskController::class, 'add'],
            '/task/edit' => [TaskController::class, 'edit'],
            '/task/delete' => [TaskController::class, 'delete'],
            '/task/toggleCompletion' => [TaskController::class, 'toggleCompletion'], // Ajoutez cette ligne
        ];
        
    }

    public function dispatch(string $uri)
{
    $parsedUri = parse_url($uri, PHP_URL_PATH);
    $basePath = '/projet-php/public';
    $parsedUri = str_replace($basePath, '', $parsedUri);

    if (isset($this->routes[$parsedUri])) {
        [$controllerClass, $method] = $this->routes[$parsedUri];
        $controller = new $controllerClass();

        if (method_exists($controller, $method)) {
            $controller->$method();
            return;
        } else {
            echo "La méthode {$method} n'existe pas dans le contrôleur {$controllerClass}.";
        }
    } else {
        echo "La route {$parsedUri} n'est pas définie.";
    }

    http_response_code(404);
    echo "404 - Not Found";
}

}
?>