<?php
session_start();

require_once __DIR__ . '/core/Response.php';
require_once __DIR__ . '/core/Database.php';
require_once __DIR__ . '/core/AuthMiddleware.php';
require_once __DIR__ . '/core/AuthService.php';

// Autoload controller & repo
spl_autoload_register(function ($class) {
    foreach (['controllers', 'repositories', 'core'] as $dir) {
        $file = __DIR__ . "/$dir/$class.php";
        if (file_exists($file)) require_once $file;
    }
});

$routes = require __DIR__ . '/routes.php';

$method = $_SERVER['REQUEST_METHOD'];
$path = strtok($_SERVER['REQUEST_URI'], '?');
$path = str_replace('/v1/api', '', $path); // kalau API ada di /v1/api/

// Cek route exact match dulu
if (isset($routes[$method][$path])) {
    $route = $routes[$method][$path];
    
    // Cek authentication
    if (!AuthMiddleware::checkAuth($method, $path, $route)) {
        return; // Response sudah dikirim oleh middleware
    }
    
    // Cek permission jika ada
    if (isset($route['permission'])) {
        if (!AuthMiddleware::hasPermission($route['permission'])) {
            Response::json([
                'error' => 'Forbidden',
                'message' => 'Anda tidak memiliki permission untuk mengakses endpoint ini',
                'code' => 'INSUFFICIENT_PERMISSION'
            ], 403);
            return;
        }
    }
    
    $controller = $route['controller'];
    $action = $route['action'];
    $instance = new $controller();
    $instance->$action();
} else {
    // Cek route dengan parameter dinamis
    $matched = false;
    foreach ($routes[$method] ?? [] as $route => $handler) {
        if (strpos($route, '{') !== false) {
            // Convert route pattern to regex
            $pattern = str_replace('{id}', '(\d+)', $route);
            $pattern = '#^' . $pattern . '$#';
            
            if (preg_match($pattern, $path)) {
                // Cek authentication
                if (!AuthMiddleware::checkAuth($method, $path, $handler)) {
                    return; // Response sudah dikirim oleh middleware
                }
                
                // Cek permission jika ada
                if (isset($handler['permission'])) {
                    if (!AuthMiddleware::hasPermission($handler['permission'])) {
                        Response::json([
                            'error' => 'Forbidden',
                            'message' => 'Anda tidak memiliki permission untuk mengakses endpoint ini',
                            'code' => 'INSUFFICIENT_PERMISSION'
                        ], 403);
                        return;
                    }
                }
                
                $controller = $handler['controller'];
                $action = $handler['action'];
                $instance = new $controller();
                $instance->$action();
                $matched = true;
                break;
            }
        }
    }
    
    if (!$matched) {
        Response::json(['error' => 'Not Found'], 404);
    }
}
