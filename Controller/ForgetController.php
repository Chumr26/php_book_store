<?php
/**
 * ForgetController
 * Handles password recovery and reset
 */

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../Model/Customers.php';
require_once __DIR__ . '/../Model/EmailSender.php';
require_once __DIR__ . '/helpers/SessionHelper.php';
require_once __DIR__ . '/helpers/Validator.php';

class ForgetController extends BaseController {
    private $customersModel;
    private $emailSender;
    
    public function __construct($conn) {
        parent::__construct($conn);
        $this->customersModel = new Customers($conn);
        try {
            $this->emailSender = new EmailSender();
        } catch (Throwable $e) {
            $this->emailSender = null;
            error_log('ForgetController: EmailSender not available: ' . $e->getMessage());
        }
    }
    
    /**
     * Display password recovery request form
     */
    public function showRequestForm() {
        SessionHelper::start();
        
        // Redirect if logged in
        if (SessionHelper::isLoggedIn()) {
            header('Location: index.php');
            exit;
        }
        
        return [
            'csrf_token' => SessionHelper::generateCSRFToken(),
            'page_title' => 'Quên mật khẩu'
        ];
    }
    
    /**
     * Process password reset request
     */
    public function requestPasswordReset() {
        try {
            SessionHelper::start();
            
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                header('Location: index.php?page=forgot_password');
                exit;
            }
            
            // Verify CSRF token
            $token = isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '';
            if (!SessionHelper::verifyCSRFToken($token)) {
                SessionHelper::setFlash('error', 'Token không hợp lệ. Vui lòng thử lại.');
                header('Location: index.php?page=forgot_password');
                exit;
            }
            
            // Get email
            $email = isset($_POST['email']) ? trim($_POST['email']) : '';
            
            // Validate email
            $validator = new Validator();
            $validator->required('email', $email, 'Email là bắt buộc.');
            $validator->email('email', $email, 'Email không hợp lệ.');
            
            if ($validator->hasErrors()) {
                SessionHelper::setFlash('error', $validator->getFirstError());
                header('Location: index.php?page=forgot_password');
                exit;
            }
            
            // Check if email exists
            $customer = $this->customersModel->getCustomerByEmail($email);
            
            if (!$customer) {
                // Don't reveal if email exists or not (security)
                SessionHelper::setFlash('success', 'Nếu email tồn tại trong hệ thống, bạn sẽ nhận được link đặt lại mật khẩu.');
                header('Location: index.php?page=login');
                exit;
            }
            
            // Generate reset token
            $resetToken = $this->generateResetToken();
            $expiry = date('Y-m-d H:i:s', strtotime('+1 hour')); // Token valid for 1 hour
            
            // Store token in session (or database in production)
            SessionHelper::set('password_reset_' . $customer['id_khachhang'], [
                'token' => $resetToken,
                'expiry' => $expiry,
                'email' => $email
            ]);
            
            // Send reset email
            $resetLink = BASE_URL . '/index.php?page=reset_password&token=' . $resetToken . '&email=' . urlencode($email);
            $mailOk = $this->sendResetEmail($email, $customer['ten_khachhang'], $resetLink);

            if (!$mailOk) {
                SessionHelper::setFlash('error', 'Không thể gửi email đặt lại mật khẩu lúc này. Vui lòng cấu hình Resend SMTP trong config/email.local.php (xem tmp/logs/email.log) và thử lại.');
                header('Location: index.php?page=forgot_password');
                exit;
            }

            SessionHelper::setFlash('success', 'Link đặt lại mật khẩu đã được gửi đến email của bạn. Vui lòng kiểm tra hộp thư.');
            header('Location: index.php?page=login');
            exit;
            
        } catch (Exception $e) {
            error_log("ForgetController::requestPasswordReset Error: " . $e->getMessage());
            SessionHelper::setFlash('error', 'Có lỗi xảy ra. Vui lòng thử lại sau.');
            header('Location: index.php?page=forgot_password');
            exit;
        }
    }
    
    /**
     * Display password reset form
     */
    public function showResetForm() {
        SessionHelper::start();
        
        // Get token and email from URL
        $token = isset($_GET['token']) ? $_GET['token'] : '';
        $email = isset($_GET['email']) ? $_GET['email'] : '';
        
        if (empty($token) || empty($email)) {
            SessionHelper::setFlash('error', 'Link không hợp lệ.');
            header('Location: index.php?page=login');
            exit;
        }
        
        // Verify token (basic check, full verification in resetPassword)
        $customer = $this->customersModel->getCustomerByEmail($email);
        if (!$customer) {
            SessionHelper::setFlash('error', 'Link không hợp lệ.');
            header('Location: index.php?page=login');
            exit;
        }
        
        return [
            'csrf_token' => SessionHelper::generateCSRFToken(),
            'token' => htmlspecialchars($token),
            'email' => htmlspecialchars($email),
            'page_title' => 'Đặt lại mật khẩu'
        ];
    }
    
    /**
     * Process password reset
     */
    public function resetPassword() {
        try {
            SessionHelper::start();
            
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                header('Location: index.php?page=login');
                exit;
            }
            
            // Verify CSRF token
            $csrfToken = isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '';
            if (!SessionHelper::verifyCSRFToken($csrfToken)) {
                SessionHelper::setFlash('error', 'Token không hợp lệ. Vui lòng thử lại.');
                header('Location: index.php?page=login');
                exit;
            }
            
            // Get form data
            $token = isset($_POST['token']) ? $_POST['token'] : '';
            $email = isset($_POST['email']) ? trim($_POST['email']) : '';
            $newPassword = isset($_POST['new_password']) ? $_POST['new_password'] : '';
            $confirmPassword = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';
            
            // Validate input
            $validator = new Validator();
            $validator->required('new_password', $newPassword, 'Mật khẩu mới là bắt buộc.');
            $validator->password('new_password', $newPassword, 8);
            $validator->passwordMatch('confirm_password', $newPassword, $confirmPassword);
            
            if ($validator->hasErrors()) {
                SessionHelper::setFlash('error', $validator->getFirstError());
                header('Location: index.php?page=reset_password&token=' . urlencode($token) . '&email=' . urlencode($email));
                exit;
            }
            
            // Get customer
            $customer = $this->customersModel->getCustomerByEmail($email);
            
            if (!$customer) {
                SessionHelper::setFlash('error', 'Link không hợp lệ hoặc đã hết hạn.');
                header('Location: index.php?page=login');
                exit;
            }
            
            // Verify token
            $resetData = SessionHelper::get('password_reset_' . $customer['id_khachhang']);
            
            if (!$resetData || $resetData['token'] !== $token || $resetData['email'] !== $email) {
                SessionHelper::setFlash('error', 'Link không hợp lệ.');
                header('Location: index.php?page=login');
                exit;
            }
            
            // Check if token expired
            if (strtotime($resetData['expiry']) < time()) {
                SessionHelper::setFlash('error', 'Link đã hết hạn. Vui lòng yêu cầu link mới.');
                SessionHelper::remove('password_reset_' . $customer['id_khachhang']);
                header('Location: index.php?page=forgot_password');
                exit;
            }
            
            // Update password
            // Customers model hashes the password
            $result = $this->customersModel->updatePassword($customer['id_khachhang'], $newPassword);
            
            if ($result) {
                // Clear reset token
                SessionHelper::remove('password_reset_' . $customer['id_khachhang']);
                
                SessionHelper::setFlash('success', 'Mật khẩu đã được đặt lại thành công. Vui lòng đăng nhập.');
                header('Location: index.php?page=login');
                exit;
            } else {
                SessionHelper::setFlash('error', 'Có lỗi xảy ra khi đặt lại mật khẩu.');
                header('Location: index.php?page=reset_password&token=' . urlencode($token) . '&email=' . urlencode($email));
                exit;
            }
            
        } catch (Exception $e) {
            error_log("ForgetController::resetPassword Error: " . $e->getMessage());
            SessionHelper::setFlash('error', 'Có lỗi xảy ra. Vui lòng thử lại sau.');
            header('Location: index.php?page=login');
            exit;
        }
    }
    
    /**
     * Generate reset token
     * @return string Reset token
     */
    public function generateResetToken() {
        return bin2hex(random_bytes(32));
    }
    
    /**
     * Send password reset email
     * @param string $email Customer email
     * @param string $name Customer name
     * @param string $resetLink Reset link
     * @return bool Success
     */
    public function sendResetEmail($email, $name, $resetLink) {
        try {
            if (!$this->emailSender) {
                throw new Exception('EmailSender is not configured. Please configure config/email.local.php.');
            }
            return $this->emailSender->sendPasswordResetEmail($email, $name, $resetLink);
        } catch (Exception $e) {
            error_log("Error sending reset email: " . $e->getMessage());
            return false;
        }
    }
}
