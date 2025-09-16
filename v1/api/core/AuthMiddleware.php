<?php

class AuthMiddleware
{
    /**
     * Middleware untuk memeriksa authentication session agent
     * 
     * @param string $method HTTP method
     * @param string $path Request path
     * @param array $route Route configuration
     * @return bool|void Returns true if authenticated, otherwise sends error response
     */
    public static function checkAuth($method, $path, $route)
    {
        // Cek apakah route memerlukan authentication
        if (!self::requiresAuth($route)) {
            return true;
        }

        // Cek apakah session sudah dimulai
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Cek apakah ada session agent yang valid
        if (!self::isAgentLoggedIn()) {
            Response::json([
                'error' => 'Unauthorized',
                'message' => 'Session agent not found or expired',
                'code' => 'AUTH_REQUIRED'
            ], 401);
            return false;
        }

        // Cek apakah session masih valid (optional - bisa ditambahkan timeout)
        if (!self::isSessionValid()) {
            Response::json([
                'error' => 'Session Expired',
                'message' => 'Session agent expired, please login again',
                'code' => 'SESSION_EXPIRED'
            ], 401);
            return false;
        }

        return true;
    }

    /**
     * Cek apakah route memerlukan authentication
     * 
     * @param array $route Route configuration
     * @return bool
     */
    private static function requiresAuth($route)
    {
        // If route has property 'auth' => false, then no need to auth
        if (isset($route['auth']) && $route['auth'] === false) {
            return false;
        }

        // Default: all routes require authentication
        return true;
    }

    /**
     * Check if agent is logged in
     * 
     * @return bool
     */
    private static function isAgentLoggedIn()
    {
        // Cek session variables yang diperlukan
        $requiredSessions = ['sess_agent_id', 'sess_agent_name'];
        
        foreach ($requiredSessions as $sessionKey) {
            if (!isset($_SESSION[$sessionKey]) || empty($_SESSION[$sessionKey])) {
                return false;
            }
        }

        return true;
    }

    /**
     * Cek apakah session masih valid
     * 
     * @return bool
     */
    private static function isSessionValid()
    {
        // Cek session timeout (optional)
        if (isset($_SESSION['sess_last_activity'])) {
            $timeout = 7200; // 2 jam timeout
            if (time() - $_SESSION['sess_last_activity'] > $timeout) {
                return false;
            }
        }

        // Update last activity
        $_SESSION['sess_last_activity'] = time();

        return true;
    }

    /**
     * Cek permission khusus untuk agent
     * 
     * @param string $permission Permission yang diperlukan
     * @return bool
     */
    public static function hasPermission($permission)
    {
        if (!self::isAgentLoggedIn()) {
            return false;
        }

        // Cek apakah agent adalah super admin
        if (isset($_SESSION['sess_super_admin']) && $_SESSION['sess_super_admin'] === 'SuperAdmin') {
            return true;
        }

        // Cek permission berdasarkan role agent
        $agentRole = $_SESSION['sess_agent_role'] ?? 'agent';
        
        switch ($permission) {
            case 'read_files':
                return in_array($agentRole, ['agent', 'admin', 'super_admin']);
            case 'write_files':
                return in_array($agentRole, ['agent', 'admin', 'super_admin']);
            case 'delete_files':
                return in_array($agentRole, ['admin', 'super_admin']);
            case 'view_all_files':
                return in_array($agentRole, ['admin', 'super_admin']);
            default:
                return false;
        }
    }

    /**
     * Get current agent information
     * 
     * @return array|null
     */
    public static function getCurrentAgent()
    {
        if (!self::isAgentLoggedIn()) {
            return null;
        }

        return [
            'id' => $_SESSION['sess_agent_id'],
            'name' => $_SESSION['sess_agent_name'],
            'role' => $_SESSION['sess_agent_role'] ?? 'agent',
            'is_super_admin' => isset($_SESSION['sess_super_admin']) && $_SESSION['sess_super_admin'] === 'SuperAdmin'
        ];
    }

    /**
     * Logout agent dan clear session
     */
    public static function logout()
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
    }
}
