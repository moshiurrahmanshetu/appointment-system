<?php

namespace App\Core;

class Router
{
    private $routes = [];
    private $middleware = [];
    
    public function get($path, $handler, $middleware = [])
    {
        $this->addRoute('GET', $path, $handler, $middleware);
    }
    
    public function post($path, $handler, $middleware = [])
    {
        $this->addRoute('POST', $path, $handler, $middleware);
    }
    
    public function put($path, $handler, $middleware = [])
    {
        $this->addRoute('PUT', $path, $handler, $middleware);
    }
    
    public function delete($path, $handler, $middleware = [])
    {
        $this->addRoute('DELETE', $path, $handler, $middleware);
    }
    
    private function addRoute($method, $path, $handler, $middleware)
    {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'handler' => $handler,
            'middleware' => $middleware
        ];
    }
    
    public function dispatch()
    {
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        
        // Remove query string
        $requestUri = strtok($requestUri, '?');
        
        // Remove base path if present (for subdirectory installations)
        $scriptName = dirname($_SERVER['SCRIPT_NAME']);
        if ($scriptName !== '/' && strpos($requestUri, $scriptName) === 0) {
            $requestUri = substr($requestUri, strlen($scriptName));
        }
        
        // Ensure requestUri starts with /
        if (empty($requestUri) || $requestUri[0] !== '/') {
            $requestUri = '/' . $requestUri;
        }
        
        // Normalize empty path to /
        if ($requestUri === '') {
            $requestUri = '/';
        }
        
        foreach ($this->routes as $route) {
            if ($route['method'] === $requestMethod && $this->matchPath($route['path'], $requestUri, $params)) {
                return $this->handleRoute($route, $params);
            }
        }
        
        $this->handleNotFound();
    }
    
    private function matchPath($routePath, $requestUri, &$params)
    {
        $params = [];
        
        // Convert route path to regex pattern
        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '([^/]+)', $routePath);
        $pattern = '#^' . $pattern . '$#';
        
        if (preg_match($pattern, $requestUri, $matches)) {
            // Extract parameter names from route path
            preg_match_all('/\{([a-zA-Z0-9_]+)\}/', $routePath, $paramNames);
            
            // Build params array
            if (!empty($paramNames[1])) {
                foreach ($paramNames[1] as $index => $name) {
                    $params[$name] = $matches[$index + 1];
                }
            }
            
            return true;
        }
        
        return false;
    }
    
    private function handleRoute($route, $params)
    {
        // Apply middleware
        foreach ($route['middleware'] as $middlewareClass) {
            $middleware = new $middlewareClass();
            if (!$middleware->handle()) {
                return;
            }
        }
        
        // Call handler
        $handler = $route['handler'];
        
        // Parse handler if it's in string format "Controller@method"
        if (is_string($handler) && strpos($handler, '@') !== false) {
            list($controllerName, $methodName) = explode('@', $handler);
            $handler = [$controllerName, $methodName];
        }
        
        if (is_array($handler)) {
            $controllerName = $handler[0];
            $methodName = $handler[1];
            
            $controllerClass = "App\\Controllers\\{$controllerName}";
            
            if (class_exists($controllerClass)) {
                $controller = new $controllerClass();
                
                if (method_exists($controller, $methodName)) {
                    return call_user_func_array([$controller, $methodName], $params);
                }
            }
        }
        
        $this->handleNotFound();
    }
    
    private function handleNotFound()
    {
        http_response_code(404);
        require __DIR__ . '/../../app/views/errors/404.php';
        exit;
    }
}
