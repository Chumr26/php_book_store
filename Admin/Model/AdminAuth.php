<?php
/**
 * Admin Authentication Model
 * 
 * Handles admin login and authentication
 * Note: For this project, admin credentials can be stored in database
 * or hardcoded for simplicity
 */

class AdminAuth {
    private $conn;
    
    /**
     * Constructor - Initialize database connection
     */
    public function __construct($connection) {
        $this->conn = $connection;
    }
    
    /**
     * Verify admin login credentials
     * 
     * For Phase 2, using hardcoded credentials
     * In Phase 3+, this will check against an admin_users table
     * 
     * @param string $username Admin username
     * @param string $password Admin password
     * @return array|false Admin data or false
     */
    public function verifyAdmin($username, $password) {
        // TODO: Replace with database check in future phases
        // For now, using hardcoded admin credentials
        
        $adminUsername = 'admin';
        $adminPassword = 'admin123'; // In production, this should be hashed
        
        if ($username === $adminUsername && $password === $adminPassword) {
            return [
                'id_admin' => 1,
                'username' => $username,
                'full_name' => 'Administrator',
                'role' => 'admin'
            ];
        }
        
        return false;
    }
    
    /**
     * Create admin session
     * 
     * @param array $adminData Admin information
     * @return bool Success status
     */
    public function createAdminSession($adminData) {
        $_SESSION['admin_id'] = $adminData['id_admin'];
        $_SESSION['admin_username'] = $adminData['username'];
        $_SESSION['admin_name'] = $adminData['full_name'];
        $_SESSION['admin_role'] = $adminData['role'];
        $_SESSION['is_admin'] = true;
        $_SESSION['admin_login_time'] = time();
        
        return true;
    }
    
    /**
     * Check if admin is logged in
     * 
     * @return bool True if admin session exists
     */
    public function isAdminLoggedIn() {
        return isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true;
    }
    
    /**
     * Destroy admin session (logout)
     * 
     * @return bool Success status
     */
    public function logout() {
        unset($_SESSION['admin_id']);
        unset($_SESSION['admin_username']);
        unset($_SESSION['admin_name']);
        unset($_SESSION['admin_role']);
        unset($_SESSION['is_admin']);
        unset($_SESSION['admin_login_time']);
        
        return true;
    }
    
    /**
     * Get current admin info
     * 
     * @return array|null Admin data or null
     */
    public function getCurrentAdmin() {
        if ($this->isAdminLoggedIn()) {
            return [
                'id_admin' => $_SESSION['admin_id'],
                'username' => $_SESSION['admin_username'],
                'full_name' => $_SESSION['admin_name'],
                'role' => $_SESSION['admin_role']
            ];
        }
        
        return null;
    }
    
    /**
     * Check admin session timeout (30 minutes)
     * 
     * @return bool True if session is still valid
     */
    public function checkSessionTimeout() {
        if (!$this->isAdminLoggedIn()) {
            return false;
        }
        
        $timeout = 1800; // 30 minutes in seconds
        $lastActivity = $_SESSION['admin_login_time'] ?? 0;
        
        if ((time() - $lastActivity) > $timeout) {
            $this->logout();
            return false;
        }
        
        // Update last activity time
        $_SESSION['admin_login_time'] = time();
        return true;
    }
    
    /**
     * Future: Get admin from database
     * This method will be implemented when admin_users table is created
     * 
     * @param string $username Username
     * @return array|null Admin data or null
     */
    public function getAdminByUsername($username) {
        // TODO: Implement in future phases
        // $sql = "SELECT * FROM admin_users WHERE username = ? AND status = 1";
        // ...
        return null;
    }
}
?>
