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
        // Add Quick Login data (Hardcoded for now as per AdminAuth.php)
        $viewData['debug_users'] = [
            [
                'username' => 'admin',
                'full_name' => 'Super Administrator',
                'email' => 'admin@bookstore.com',
                'password' => 'admin123'
            ]
        ];
        
        return $viewData;
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
