<?php

class Router
{
    private $routes = [];

    public function add($method, $uri, $action, $middleware = [])
    {
        // Convert route like '/api/patients/{id}' to a regex '/^\/api\/patients\/([a-zA-Z0-9_-]+)$/'
        $pattern = preg_replace('/\{([a-zA-Z0-9_-]+)\}/', '([a-zA-Z0-9_-]+)', $uri);
        $pattern = '#^' . $pattern . '$#';

        $this->routes[] = [
            'method' => $method,
            'pattern' => $pattern,
            'action' => $action,
            'middleware' => $middleware
        ];
    }

    public function dispatch($uri, $method)
    {
        // Remove query string from URI if present
        $uri = explode('?', $uri)[0];

        foreach ($this->routes as $route) {
            if ($route['method'] === $method && preg_match($route['pattern'], $uri, $matches)) {
                array_shift($matches); // Remove the full match

                // Execute middleware
                foreach ($route['middleware'] as $middlewareClass) {
                    $middleware = new $middlewareClass();
                    $middleware->handle();
                }

                // Parse action
                list($controller, $methodName) = explode('@', $route['action']);
                
                // Initialize controller
                $controllerInstance = new $controller();
                
                // Call method with extracted parameters
                call_user_func_array([$controllerInstance, $methodName], $matches);
                return;
            }
        }

        // 404 Not Found
        Response::json(['error' => 'Route not found'], 404);
    }
}
