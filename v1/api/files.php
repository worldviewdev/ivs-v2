<?php
session_start();

require_once __DIR__ . '/core/Response.php';
require_once __DIR__ . '/core/Database.php';
require_once __DIR__ . '/core/StatusHelper.php';

// Autoload controller & repo
spl_autoload_register(function ($class) {
    foreach (['controllers', 'repositories', 'core'] as $dir) {
        $file = __DIR__ . "/$dir/$class.php";
        if (file_exists($file)) require_once $file;
    }
});

$method = $_SERVER['REQUEST_METHOD'];

// Set default agent_id untuk testing
if (!isset($_SESSION['sess_agent_id'])) {
    $_SESSION['sess_agent_id'] = 1;
}

try {
    $controller = new FileController();
    
    switch ($method) {
        case 'GET':
            $controller->index();
            break;
        case 'POST':
            $controller->store();
            break;
        case 'PUT':
            $controller->update();
            break;
        case 'DELETE':
            $controller->destroy();
            break;
        default:
            Response::json(['error' => 'Method not allowed'], 405);
    }
} catch (Exception $e) {
    Response::json([
        'error' => 'Server Error',
        'message' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ], 500);
}
