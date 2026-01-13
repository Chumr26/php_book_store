<?php
/**
 * RegistrationController
 * Handles customer registration
 */

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../Model/Customers.php';
require_once __DIR__ . '/../Model/EmailSender.php';
require_once __DIR__ . '/helpers/SessionHelper.php';
require_once __DIR__ . '/helpers/Validator.php';

class RegistrationController extends BaseController {
    private $customersModel;
    
    public function __construct($conn) {
        parent::__construct($conn);
        $this->customersModel = new Customers($conn);
    }
    
    /**
     * Display registration form
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
            'page_title' => 'Đăng ký tài khoản'
        ];
    }
    
    /**
     * Process registration form
     */
    public function register() {
        try {
            SessionHelper::start();
            
            // Check if already logged in
            if (SessionHelper::isLoggedIn()) {
                header('Location: index.php');
                exit;
            }
            
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                header('Location: index.php?page=register');
                exit;
            }
            
            // Verify CSRF token
            $token = isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '';
            if (!SessionHelper::verifyCSRFToken($token)) {
                SessionHelper::setFlash('error', 'Token không hợp lệ. Vui lòng thử lại.');
                header('Location: index.php?page=register');
                exit;
            }
            
            // Get form data
            $fullName = isset($_POST['full_name']) ? trim($_POST['full_name']) : '';
            $email = isset($_POST['email']) ? trim($_POST['email']) : '';
            $password = isset($_POST['password']) ? $_POST['password'] : '';
            $confirmPassword = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';
            $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
            $address = isset($_POST['address']) ? trim($_POST['address']) : '';
            $gender = isset($_POST['gender']) ? $_POST['gender'] : '';
            $dateOfBirth = isset($_POST['date_of_birth']) ? $_POST['date_of_birth'] : '';
            
            // Validate input
            $validator = new Validator();
            
            $validator->required('full_name', $fullName, 'Họ tên là bắt buộc.');
            
            $validator->required('email', $email, 'Email là bắt buộc.');
            $validator->email('email', $email, 'Email không hợp lệ.');
            
            $validator->required('password', $password, 'Mật khẩu là bắt buộc.');
            $validator->password('password', $password, 8);
            $validator->passwordMatch('confirm_password', $password, $confirmPassword);
            
            $validator->required('phone', $phone, 'Số điện thoại là bắt buộc.');
            $validator->phone('phone', $phone);
            
            $validator->required('address', $address, 'Địa chỉ là bắt buộc.');
            
            if (!empty($gender)) {
                $validator->inArray('gender', $gender, ['Nam', 'Nữ', 'Khác'], 'Giới tính không hợp lệ.');
            }
            
            if (!empty($dateOfBirth)) {
                $validator->date('date_of_birth', $dateOfBirth, 'Y-m-d', 'Ngày sinh không hợp lệ.');
            }
            
            if ($validator->hasErrors()) {
                SessionHelper::setFlash('error', $validator->getFirstError());
                
                // Store form data to repopulate
                SessionHelper::set('registration_data', $_POST);

                header('Location: index.php?page=register');
                exit;
            }
            
            // Check if email already exists
            if ($this->checkEmailExists($email)) {
                SessionHelper::setFlash('error', 'Email đã được sử dụng. Vui lòng sử dụng email khác.');
                SessionHelper::set('registration_data', $_POST);
                header('Location: index.php?page=register');
                exit;
            }
            
            // Prepare data for registration (Customers model hashes password)
            $customerData = [
                'full_name' => Validator::sanitizeString($fullName),
                'email' => Validator::sanitizeEmail($email),
                'password' => $password,
                'phone' => Validator::sanitizeString($phone),
                'address' => Validator::sanitizeString($address),
                'gender' => !empty($gender) ? $gender : null,
                'date_of_birth' => !empty($dateOfBirth) ? $dateOfBirth : null
            ];
            
            // Register customer
            $customerId = $this->customersModel->registerCustomer($customerData);
            
            if ($customerId) {
                // Registration successful
                SessionHelper::remove('registration_data');

                // Create verification token (30 minutes TTL)
                $tokenRaw = bin2hex(random_bytes(32));
                $tokenHash = hash('sha256', $tokenRaw);
                $this->customersModel->setEmailVerificationTokenWithTtlMinutes((int)$customerId, $tokenHash, 30);

                // Build verification URL
                $baseUrl = defined('BASE_URL')
                    ? BASE_URL
                    : ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http')
                        . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost')
                        . rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? '/'), '/') . '/');
                $verifyUrl = $baseUrl . 'index.php?page=verify_email&token=' . urlencode($tokenRaw);

                // Send verification email
                $mail = new EmailSender();
                $sent = $mail->sendEmailVerification($email, $fullName, $verifyUrl, 30);

                if ($sent) {
                    SessionHelper::setFlash('success', 'Đăng ký thành công! Vui lòng kiểm tra email để xác minh tài khoản (hiệu lực 30 phút).');
                } else {
                    SessionHelper::setFlash('warning', 'Đăng ký thành công nhưng không gửi được email xác minh. Vui lòng thử “Gửi lại email xác minh” ở trang đăng nhập.');
                }
                
                header('Location: index.php?page=login');
                exit;
            } else {
                SessionHelper::setFlash('error', 'Có lỗi xảy ra khi đăng ký. Vui lòng thử lại.');
                SessionHelper::set('registration_data', $_POST);
                header('Location: index.php?page=register');
                exit;
            }
            
        } catch (Exception $e) {
            error_log("RegistrationController::register Error: " . $e->getMessage());
            SessionHelper::setFlash('error', 'Có lỗi xảy ra. Vui lòng thử lại sau.');
            header('Location: index.php?page=register');
            exit;
        }
    }
    
    /**
     * Check if email already exists
     * @param string $email Email address
     * @return bool True if exists
     */
    public function checkEmailExists($email) {
        try {
            $customer = $this->customersModel->getCustomerByEmail($email);
            return !empty($customer);
        } catch (Exception $e) {
            error_log("Error checking email: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Validate email via AJAX
     */
    public function validateEmail() {
        try {
            header('Content-Type: application/json');
            
            if (!isset($_POST['email'])) {
                echo json_encode([
                    'valid' => false,
                    'message' => 'Email is required'
                ]);
                exit;
            }
            
            $email = trim($_POST['email']);
            
            // Validate format
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                echo json_encode([
                    'valid' => false,
                    'message' => 'Email không hợp lệ'
                ]);
                exit;
            }
            
            // Check if exists
            if ($this->checkEmailExists($email)) {
                echo json_encode([
                    'valid' => false,
                    'message' => 'Email đã được sử dụng'
                ]);
                exit;
            }
            
            echo json_encode([
                'valid' => true,
                'message' => 'Email hợp lệ'
            ]);
            exit;
            
        } catch (Exception $e) {
            error_log("Error validating email: " . $e->getMessage());
            echo json_encode([
                'valid' => false,
                'message' => 'Có lỗi xảy ra'
            ]);
            exit;
        }
    }
}
