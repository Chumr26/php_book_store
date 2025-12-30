<?php
/**
 * LoginController
 * Handles customer authentication
 */

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../Model/Customers.php';
require_once __DIR__ . '/helpers/SessionHelper.php';
require_once __DIR__ . '/helpers/Validator.php';

class LoginController extends BaseController {
    private $customersModel;
    
    public function __construct($conn) {
        parent::__construct($conn);
        $this->customersModel = new Customers($conn);
    }
    
    /**
     * Display login form
     */
    public function showForm() {
        SessionHelper::start();
        
        // Redirect if already logged in
        if (SessionHelper::isLoggedIn()) {
            header('Location: index.php');
            exit;
        }
        
        return [
            'csrf_token' => SessionHelper::generateCSRFToken(),
            'page_title' => 'Đăng nhập',
            // Fetch users for Quick Login (Dev Mode)
            'debug_users' => $this->customersModel->getAllCustomers(1, 20)
        ];
    }
    
    /**
     * Process login
     */
    public function login() {
        try {
            SessionHelper::start();
            
            // Check if already logged in
            if (SessionHelper::isLoggedIn()) {
                header('Location: index.php');
                exit;
            }
            
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                header('Location: index.php?page=login');
                exit;
            }
            
            // Verify CSRF token
            $token = isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '';
            if (!SessionHelper::verifyCSRFToken($token)) {
                SessionHelper::setFlash('error', 'Token không hợp lệ. Vui lòng thử lại.');
                header('Location: index.php?page=login');
                exit;
            }
            
            // Get form data
            $email = isset($_POST['email']) ? trim($_POST['email']) : '';
            $password = isset($_POST['password']) ? $_POST['password'] : '';
            $rememberMe = isset($_POST['remember_me']) ? true : false;
            
            // Validate input
            $validator = new Validator();
            $validator->required('email', $email, 'Email là bắt buộc.');
            $validator->email('email', $email, 'Email không hợp lệ.');
            $validator->required('password', $password, 'Mật khẩu là bắt buộc.');
            
            if ($validator->hasErrors()) {
                SessionHelper::setFlash('error', $validator->getFirstError());
                header('Location: index.php?page=login');
                exit;
            }
            
            // Verify credentials
            $customer = $this->verifyCredentials($email, $password);
            
            if ($customer) {
                // Check if account is active
                if ($customer['trang_thai'] !== 'active') {
                    SessionHelper::setFlash('error', 'Tài khoản đã bị khóa. Vui lòng liên hệ quản trị viên.');
                    header('Location: index.php?page=login');
                    exit;
                }
                
                // Login successful
                SessionHelper::setCustomerLogin(
                    $customer['id_khachhang'],
                    $customer['email'],
                    $customer['ten_khachhang']
                );
                
                // Handle remember me
                if ($rememberMe) {
                    // Set remember me cookie for 30 days
                    $rememberToken = bin2hex(random_bytes(32));
                    setcookie('remember_token', $rememberToken, time() + (30 * 24 * 60 * 60), '/');
                    // TODO: Store remember token in database
                }
                
                // SessionHelper::setFlash('success', 'Đăng nhập thành công!');
                
                // Redirect to intended page or homepage
                $redirectTo = SessionHelper::get('intended_url', 'index.php');
                SessionHelper::remove('intended_url');
                
                header('Location: ' . $redirectTo);
                exit;
                
            } else {
                // Login failed
                SessionHelper::setFlash('error', 'Email hoặc mật khẩu không đúng.');
                header('Location: index.php?page=login');
                exit;
            }
            
        } catch (Exception $e) {
            error_log("LoginController::login Error: " . $e->getMessage());
            SessionHelper::setFlash('error', 'Có lỗi xảy ra. Vui lòng thử lại sau.');
            header('Location: index.php?page=login');
            exit;
        }
    }
    
    /**
     * Verify login credentials
     * @param string $email Email address
     * @param string $password Password
     * @return array|null Customer data or null
     */
    public function verifyCredentials($email, $password) {
        try {
            // Get customer by email
            $customer = $this->customersModel->getCustomerByEmail($email);
            
            if (!$customer) {
                return null;
            }
            
            // Verify password
            if (password_verify($password, $customer['password'])) {
                return $customer;
            }
            
            return null;
            
        } catch (Exception $e) {
            error_log("Error verifying credentials: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Logout user
     */
    public function logout() {
        try {
            SessionHelper::start();
            
            // Clear remember me cookie if exists
            if (isset($_COOKIE['remember_token'])) {
                setcookie('remember_token', '', time() - 3600, '/');
                // TODO: Delete remember token from database
            }
            
            // Logout user
            SessionHelper::logout();
            
            // SessionHelper::setFlash('success', 'Đăng xuất thành công!');
            header('Location: index.php');
            exit;
            
        } catch (Exception $e) {
            error_log("LoginController::logout Error: " . $e->getMessage());
            header('Location: index.php');
            exit;
        }
    }
    
    /**
     * Check authentication for protected pages
     * @param string $redirectUrl URL to redirect after login
     * @return bool True if logged in
     */
    public static function requireLogin($redirectUrl = null) {
        SessionHelper::start();
        
        if (!SessionHelper::isLoggedIn()) {
            // Store intended URL
            if ($redirectUrl) {
                SessionHelper::set('intended_url', $redirectUrl);
            } else {
                SessionHelper::set('intended_url', $_SERVER['REQUEST_URI']);
            }
            
            SessionHelper::setFlash('warning', 'Vui lòng đăng nhập để tiếp tục.');
            header('Location: index.php?page=login');
            exit;
        }
        
        return true;
    }
    
    /**
     * AJAX login for quick login
     */
    public function ajaxLogin() {
        try {
            header('Content-Type: application/json');
            SessionHelper::start();
            
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                echo json_encode([
                    'success' => false,
                    'message' => 'Invalid request method'
                ]);
                exit;
            }
            
            // Get credentials
            $email = isset($_POST['email']) ? trim($_POST['email']) : '';
            $password = isset($_POST['password']) ? $_POST['password'] : '';
            
            // Validate
            if (empty($email) || empty($password)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Email và mật khẩu là bắt buộc.'
                ]);
                exit;
            }
            
            // Verify credentials
            $customer = $this->verifyCredentials($email, $password);
            
            if ($customer && $customer['trang_thai'] === 'active') {
                // Login successful
                SessionHelper::setCustomerLogin(
                    $customer['id_khachhang'],
                    $customer['email'],
                    $customer['ten_khachhang']
                );
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Đăng nhập thành công!',
                    'customer' => [
                        'id' => $customer['id_khachhang'],
                        'name' => $customer['ten_khachhang'],
                        'email' => $customer['email']
                    ]
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Email hoặc mật khẩu không đúng.'
                ]);
            }
            exit;
            
        } catch (Exception $e) {
            error_log("AJAX Login Error: " . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Có lỗi xảy ra. Vui lòng thử lại.'
            ]);
            exit;
        }
    }
}
