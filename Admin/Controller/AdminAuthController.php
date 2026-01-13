<?php
/**
 * AdminAuthController
 * Handles admin authentication (Login/Logout)
 */

require_once __DIR__ . '/../../Model/connect.php';
require_once __DIR__ . '/../Model/AdminAuth.php';
require_once __DIR__ . '/../../Controller/helpers/SessionHelper.php';

class AdminAuthController {
    private $conn;
    private $authModel;
    
    public function __construct($dbConnection) {
        $this->conn = $dbConnection;
        $this->authModel = new AdminAuth($this->conn);
    }
    
    /**
     * Handle Login
     * GET: Show form
     * POST: Process login
     */
    public function login() {
        SessionHelper::start();
        
        // If already logged in, redirect to dashboard
        if ($this->authModel->isAdminLoggedIn()) {
            header('Location: ' . ADMIN_BASE_URL . 'index.php?page=dashboard');
            exit;
        }
        
        $viewData = [];
        
        // Handle POST request
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            // Verify CSRF
            $token = $_POST['csrf_token'] ?? '';
            if (!SessionHelper::verifyCSRFToken($token)) {
                SessionHelper::setFlash('error', 'Token bảo mật không hợp lệ. Vui lòng thử lại.');
                header('Location: ' . ADMIN_BASE_URL . 'index.php?page=login');
                exit;
            }
            
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';
            
            if (empty($username) || empty($password)) {
                SessionHelper::setFlash('error', 'Vui lòng nhập đầy đủ tên đăng nhập và mật khẩu.');
                header('Location: ' . ADMIN_BASE_URL . 'index.php?page=login');
                exit;
            }
            
            // Verify credentials
            $adminUser = $this->authModel->verifyAdmin($username, $password);
            
            if ($adminUser) {
                // Login Success
                $this->authModel->createAdminSession($adminUser);
                SessionHelper::setFlash('success', 'Đăng nhập thành công! Xin chào ' . $adminUser['full_name']);
                header('Location: ' . ADMIN_BASE_URL . 'index.php?page=dashboard');
                exit;
            } else {
                // Login Failed
                SessionHelper::setFlash('error', 'Tên đăng nhập hoặc mật khẩu không đúng.');
                header('Location: ' . ADMIN_BASE_URL . 'index.php?page=login');
                exit;
            }
        }
        
        // Handle GET (Show Form)
        // Add Quick Login data (Dev Mode) - only allow on local environment
        if (SessionHelper::isLocalRequest()) {
            $viewData['debug_users'] = [
                [
                    'username' => 'admin',
                    'full_name' => 'Super Administrator',
                    'email' => 'admin@bookstore.com'
                ]
            ];
        }
        
        return $viewData;
    }

    /**
     * Dev-only: login as an admin without needing the password.
     * Localhost-only to prevent security issues.
     */
    public function devQuickLogin() {
        SessionHelper::start();

        if (!SessionHelper::isLocalRequest()) {
            SessionHelper::setFlash('error', 'Tính năng này chỉ dành cho môi trường local.');
            header('Location: ' . ADMIN_BASE_URL . 'index.php?page=login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . ADMIN_BASE_URL . 'index.php?page=login');
            exit;
        }

        $token = $_POST['csrf_token'] ?? '';
        if (!SessionHelper::verifyCSRFToken($token)) {
            SessionHelper::setFlash('error', 'Token bảo mật không hợp lệ. Vui lòng thử lại.');
            header('Location: ' . ADMIN_BASE_URL . 'index.php?page=login');
            exit;
        }

        $username = trim($_POST['username'] ?? '');
        if ($username === '') {
            SessionHelper::setFlash('error', 'Tài khoản admin không hợp lệ.');
            header('Location: ' . ADMIN_BASE_URL . 'index.php?page=login');
            exit;
        }

        // Try DB first
        try {
            $sql = "SELECT id_admin, ten_admin, username, email, quyen FROM admin WHERE username = ? OR email = ? LIMIT 1";
            $stmt = $this->conn->prepare($sql);
            if ($stmt) {
                $stmt->bind_param('ss', $username, $username);
                $stmt->execute();
                $result = $stmt->get_result();
                $row = $result ? $result->fetch_assoc() : null;
                if ($row) {
                    $this->authModel->createAdminSession([
                        'id_admin' => (int)$row['id_admin'],
                        'username' => $row['username'],
                        'full_name' => $row['ten_admin'],
                        'email' => $row['email'],
                        'role' => $row['quyen'] ?? 'admin'
                    ]);
                    header('Location: ' . ADMIN_BASE_URL . 'index.php?page=dashboard');
                    exit;
                }
            }
        } catch (Exception $e) {
            // fall back
        }

        // Fallback: hardcoded admin (for environments without DB seed)
        if ($username === 'admin' || $username === 'admin@bookstore.com') {
            $this->authModel->createAdminSession([
                'id_admin' => 1,
                'username' => 'admin',
                'full_name' => 'Administrator',
                'email' => 'admin@bookstore.com',
                'role' => 'admin'
            ]);
            header('Location: ' . ADMIN_BASE_URL . 'index.php?page=dashboard');
            exit;
        }

        SessionHelper::setFlash('error', 'Không tìm thấy admin trong hệ thống.');
        header('Location: ' . ADMIN_BASE_URL . 'index.php?page=login');
        exit;
    }
    
    /**
     * Handle Logout
     */
    public function logout() {
        SessionHelper::start();
        $this->authModel->logout();
        SessionHelper::setFlash('success', 'Đăng xuất thành công.');
        header('Location: ' . ADMIN_BASE_URL . 'index.php?page=login');
        exit;
    }
}
