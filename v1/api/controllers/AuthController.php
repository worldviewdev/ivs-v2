<?php

class AuthController
{
    /**
     * Get current authenticated agent information
     */
    public function me()
    {
        $agent = AuthMiddleware::getCurrentAgent();
        
        if (!$agent) {
            Response::json([
                'error' => 'Unauthorized',
                'message' => 'Tidak ada session agent yang aktif'
            ], 401);
            return;
        }

        Response::json([
            'data' => $agent,
            'message' => 'Agent information retrieved successfully'
        ]);
    }

    /**
     * Login agent
     */
    public function login()
    {
        $input = json_decode(file_get_contents('php://input'), true);

        if (!$input) {
            Response::json(['error' => 'Invalid JSON input'], 400);
            return;
        }

        // Validasi input yang diperlukan
        $email = $input['email'] ?? '';
        $password = $input['password'] ?? '';

        if (empty($email) || empty($password)) {
            Response::json([
                'error' => 'Validation Error',
                'message' => 'Email dan password harus diisi'
            ], 400);
            return;
        }

        try {
            $db = Database::conn();
            
            // Cari agent berdasarkan email
            $stmt = $db->prepare("
                SELECT 
                    agent_id,
                    agent_name,
                    agent_email,
                    agent_password,
                    agent_role,
                    agent_status,
                    last_login
                FROM agents 
                WHERE agent_email = ? AND agent_status = 'active'
            ");
            
            $stmt->execute([$email]);
            $agent = $stmt->fetch();

            if (!$agent) {
                Response::json([
                    'error' => 'Authentication Failed',
                    'message' => 'Email atau password salah'
                ], 401);
                return;
            }

            // Verifikasi password
            if (!password_verify($password, $agent['agent_password'])) {
                Response::json([
                    'error' => 'Authentication Failed',
                    'message' => 'Email atau password salah'
                ], 401);
                return;
            }

            // Set session
            session_start();
            $_SESSION['sess_agent_id'] = $agent['agent_id'];
            $_SESSION['sess_agent_name'] = $agent['agent_name'];
            $_SESSION['sess_agent_email'] = $agent['agent_email'];
            $_SESSION['sess_agent_role'] = $agent['agent_role'];
            $_SESSION['sess_last_activity'] = time();

            // Set super admin flag jika diperlukan
            if ($agent['agent_role'] === 'super_admin') {
                $_SESSION['sess_super_admin'] = 'SuperAdmin';
            }

            // Update last login
            $updateStmt = $db->prepare("UPDATE agents SET last_login = NOW() WHERE agent_id = ?");
            $updateStmt->execute([$agent['agent_id']]);

            // Log activity
            AuthService::logActivity($agent['agent_id'], 'login');

            Response::json([
                'message' => 'Login berhasil',
                'data' => [
                    'agent_id' => $agent['agent_id'],
                    'agent_name' => $agent['agent_name'],
                    'agent_email' => $agent['agent_email'],
                    'agent_role' => $agent['agent_role'],
                    'is_super_admin' => $agent['agent_role'] === 'super_admin'
                ]
            ]);

        } catch (Exception $e) {
            error_log("AuthController::login Error: " . $e->getMessage());
            Response::json([
                'error' => 'Internal Server Error',
                'message' => 'Terjadi kesalahan pada server'
            ], 500);
        }
    }

    /**
     * Logout agent
     */
    public function logout()
    {
        $agentId = $_SESSION['sess_agent_id'] ?? null;
        
        // Log activity sebelum logout
        if ($agentId) {
            AuthService::logActivity($agentId, 'logout');
        }

        // Clear session
        AuthService::logout();

        Response::json([
            'message' => 'Logout berhasil'
        ]);
    }

    /**
     * Refresh session
     */
    public function refresh()
    {
        if (!AuthService::refreshSession()) {
            Response::json([
                'error' => 'Session Invalid',
                'message' => 'Session tidak valid atau sudah expired'
            ], 401);
            return;
        }

        $agent = AuthMiddleware::getCurrentAgent();
        
        Response::json([
            'message' => 'Session refreshed successfully',
            'data' => $agent
        ]);
    }

    /**
     * Check session status
     */
    public function status()
    {
        $isLoggedIn = AuthMiddleware::getCurrentAgent() !== null;
        
        if (!$isLoggedIn) {
            Response::json([
                'authenticated' => false,
                'message' => 'Tidak ada session yang aktif'
            ]);
            return;
        }

        $agent = AuthMiddleware::getCurrentAgent();
        
        Response::json([
            'authenticated' => true,
            'data' => $agent,
            'message' => 'Session aktif'
        ]);
    }
}
