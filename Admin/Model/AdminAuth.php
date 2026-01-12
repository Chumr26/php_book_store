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
        // Preferred: verify against database (table: admin)
        try {
            $sql = "SELECT id_admin, ten_admin, username, password, email, quyen FROM admin WHERE username = ? OR email = ? LIMIT 1";
            $stmt = $this->conn->prepare($sql);
            if ($stmt) {
                $stmt->bind_param('ss', $username, $username);
                $stmt->execute();
                $result = $stmt->get_result();
                $row = $result ? $result->fetch_assoc() : null;

                if ($row && isset($row['password']) && password_verify($password, $row['password'])) {
                    return [
                        'id_admin' => (int)$row['id_admin'],
                        'username' => $row['username'],
                        'full_name' => $row['ten_admin'],
                        'email' => $row['email'],
                        'role' => $row['quyen'] ?? 'admin'
                    ];
                }
            }
        } catch (Exception $e) {
            // Fall through to legacy fallback
        }

        // Legacy fallback: hardcoded (keeps admin login working if DB not imported)
        $adminUsername = 'admin';
        $adminPassword = 'admin123';

        if ($username === $adminUsername && $password === $adminPassword) {
            return [
                'id_admin' => 1,
                'username' => $username,
                'full_name' => 'Administrator',
                'email' => 'admin@bookstore.com',
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
        if (isset($adminData['email'])) {
            $_SESSION['admin_email'] = $adminData['email'];
        }
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
                'email' => $_SESSION['admin_email'] ?? null,
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

    /**
     * Get admin by ID from database
     * @param int $id
     * @return array|null
     */
    public function getAdminById($id) {
        $sql = "SELECT id_admin, ten_admin, username, email, quyen, ngay_tao FROM admin WHERE id_admin = ? LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            return null;
        }
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result ? $result->fetch_assoc() : null;
        return $row ?: null;
    }

    /**
     * Update admin profile (name + email)
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function updateAdminProfile($id, $data) {
        $sql = "UPDATE admin SET ten_admin = ?, email = ? WHERE id_admin = ?";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            return false;
        }
        $stmt->bind_param('ssi', $data['full_name'], $data['email'], $id);
        return $stmt->execute();
    }

    /**
     * Verify current admin password
     * @param int $id
     * @param string $currentPassword
     * @return bool
     */
    public function verifyAdminPassword($id, $currentPassword) {
        $sql = "SELECT password FROM admin WHERE id_admin = ? LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            return false;
        }
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result ? $result->fetch_assoc() : null;
        if (!$row || !isset($row['password'])) {
            return false;
        }
        return password_verify($currentPassword, $row['password']);
    }

    /**
     * Update admin password
     * @param int $id
     * @param string $newPassword
     * @return bool
     */
    public function updateAdminPassword($id, $newPassword) {
        $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
        $sql = "UPDATE admin SET password = ? WHERE id_admin = ?";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            return false;
        }
        $stmt->bind_param('si', $hashedPassword, $id);
        return $stmt->execute();
    }
}
?>
