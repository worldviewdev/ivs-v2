<?php

class AuthService
{
    /**
     * Validasi session agent dari database
     * 
     * @param int $agentId Agent ID
     * @return array|false Agent data jika valid, false jika tidak
     */
    public static function validateAgentSession($agentId)
    {
        try {
            $db = Database::conn();
            
            // Query untuk mendapatkan data agent
            $stmt = $db->prepare("
                SELECT 
                    agent_id,
                    agent_name,
                    agent_email,
                    agent_role,
                    agent_status,
                    last_login,
                    created_at
                FROM agents 
                WHERE agent_id = ? AND agent_status = 'active'
            ");
            
            $stmt->execute([$agentId]);
            $agent = $stmt->fetch();
            
            if (!$agent) {
                return false;
            }
            
            // Cek apakah agent masih aktif
            if ($agent['agent_status'] !== 'active') {
                return false;
            }
            
            return $agent;
            
        } catch (Exception $e) {
            error_log("AuthService::validateAgentSession Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Refresh session data dari database
     * 
     * @return bool
     */
    public static function refreshSession()
    {
        if (!isset($_SESSION['sess_agent_id'])) {
            return false;
        }

        $agentId = $_SESSION['sess_agent_id'];
        $agentData = self::validateAgentSession($agentId);

        if (!$agentData) {
            self::logout();
            return false;
        }

        // Update session dengan data terbaru
        $_SESSION['sess_agent_id'] = $agentData['agent_id'];
        $_SESSION['sess_agent_name'] = $agentData['agent_name'];
        $_SESSION['sess_agent_email'] = $agentData['agent_email'];
        $_SESSION['sess_agent_role'] = $agentData['agent_role'];
        $_SESSION['sess_last_activity'] = time();

        return true;
    }

    /**
     * Cek apakah agent memiliki akses ke file tertentu
     * 
     * @param int $fileId File ID
     * @param int $agentId Agent ID
     * @return bool
     */
    public static function canAccessFile($fileId, $agentId)
    {
        try {
            $db = Database::conn();
            
            // Cek apakah agent adalah super admin
            if (isset($_SESSION['sess_super_admin']) && $_SESSION['sess_super_admin'] === 'SuperAdmin') {
                return true;
            }
            
            // Cek apakah file milik agent atau agent adalah admin
            $stmt = $db->prepare("
                SELECT f.file_id 
                FROM files f 
                WHERE f.file_id = ? 
                AND (f.fk_agent_id = ? OR ? IN (
                    SELECT agent_id FROM agents WHERE agent_role IN ('admin', 'super_admin')
                ))
            ");
            
            $stmt->execute([$fileId, $agentId, $agentId]);
            $result = $stmt->fetch();
            
            return $result !== false;
            
        } catch (Exception $e) {
            error_log("AuthService::canAccessFile Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Logout dan clear session
     */
    public static function logout()
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            // Log logout activity (optional)
            if (isset($_SESSION['sess_agent_id'])) {
                self::logActivity($_SESSION['sess_agent_id'], 'logout');
            }
            
            session_destroy();
        }
    }

    /**
     * Log aktivitas agent (optional)
     * 
     * @param int $agentId Agent ID
     * @param string $activity Activity description
     */
    public static function logActivity($agentId, $activity)
    {
        try {
            $db = Database::conn();
            
            $stmt = $db->prepare("
                INSERT INTO agent_activity_log (agent_id, activity, ip_address, user_agent, created_at) 
                VALUES (?, ?, ?, ?, NOW())
            ");
            
            $stmt->execute([
                $agentId,
                $activity,
                $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
            ]);
            
        } catch (Exception $e) {
            error_log("AuthService::logActivity Error: " . $e->getMessage());
        }
    }

    /**
     * Generate CSRF token untuk form protection
     * 
     * @return string
     */
    public static function generateCSRFToken()
    {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        
        return $_SESSION['csrf_token'];
    }

    /**
     * Validasi CSRF token
     * 
     * @param string $token Token yang akan divalidasi
     * @return bool
     */
    public static function validateCSRFToken($token)
    {
        if (!isset($_SESSION['csrf_token'])) {
            return false;
        }
        
        return hash_equals($_SESSION['csrf_token'], $token);
    }
}
