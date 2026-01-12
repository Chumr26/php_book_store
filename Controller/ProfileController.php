<?php
/**
 * ProfileController
 * Handles customer profile (view/update + change password)
 */

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../Model/Customers.php';
require_once __DIR__ . '/helpers/SessionHelper.php';
require_once __DIR__ . '/helpers/Validator.php';

class ProfileController extends BaseController {
    private $customersModel;

    public function __construct($conn) {
        parent::__construct($conn);
        $this->customersModel = new Customers($conn);
    }

    /**
     * Show profile page
     * @return array
     */
    public function show() {
        SessionHelper::start();

        if (!SessionHelper::isLoggedIn()) {
            SessionHelper::set('intended_url', 'index.php?page=profile');
            SessionHelper::setFlash('warning', 'Vui lòng đăng nhập để tiếp tục.');
            header('Location: index.php?page=login');
            exit;
        }

        $customerId = (int) SessionHelper::getCustomerId();
        $customer = $this->customersModel->getCustomerById($customerId);

        if (!$customer) {
            SessionHelper::setFlash('error', 'Không tìm thấy thông tin tài khoản.');
            header('Location: index.php');
            exit;
        }

        return [
            'csrf_token' => SessionHelper::generateCSRFToken(),
            'customer' => $customer,
            'page_title' => 'Thông tin cá nhân'
        ];
    }

    /**
     * Handle POST requests from profile page
     */
    public function handlePost() {
        SessionHelper::start();

        if (!SessionHelper::isLoggedIn()) {
            SessionHelper::set('intended_url', 'index.php?page=profile');
            SessionHelper::setFlash('warning', 'Vui lòng đăng nhập để tiếp tục.');
            header('Location: index.php?page=login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?page=profile');
            exit;
        }

        $token = $_POST['csrf_token'] ?? '';
        if (!SessionHelper::verifyCSRFToken($token)) {
            SessionHelper::setFlash('error', 'Token không hợp lệ. Vui lòng thử lại.');
            header('Location: index.php?page=profile');
            exit;
        }

        $formType = $_POST['form_type'] ?? 'profile';

        if ($formType === 'password') {
            $this->changePassword();
            return;
        }

        $this->updateProfile();
    }

    private function updateProfile() {
        $validator = new Validator();

        $fullName = trim($_POST['full_name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $dateOfBirth = trim($_POST['date_of_birth'] ?? '');
        $gender = trim($_POST['gender'] ?? '');

        $validator->required('full_name', $fullName, 'Họ tên là bắt buộc.');
        if (!empty($phone)) {
            $validator->phone('phone', $phone);
        }

        $allowedGenders = ['', 'Nam', 'Nữ', 'Khác'];
        if (!in_array($gender, $allowedGenders, true)) {
            $validator->addError('gender', 'Giới tính không hợp lệ.');
        }

        if ($validator->hasErrors()) {
            SessionHelper::setFlash('error', $validator->getFirstError());
            header('Location: index.php?page=profile');
            exit;
        }

        $customerId = (int) SessionHelper::getCustomerId();

        $ok = $this->customersModel->updateCustomer($customerId, [
            'full_name' => $fullName,
            'phone' => $phone,
            'address' => $address,
            'date_of_birth' => $dateOfBirth,
            'gender' => $gender
        ]);

        if ($ok) {
            SessionHelper::set('customer_name', $fullName);
            SessionHelper::setFlash('success', 'Cập nhật thông tin thành công.');
        } else {
            SessionHelper::setFlash('error', 'Cập nhật thất bại. Vui lòng thử lại.');
        }

        header('Location: index.php?page=profile');
        exit;
    }

    private function changePassword() {
        $validator = new Validator();

        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        $validator->required('current_password', $currentPassword, 'Vui lòng nhập mật khẩu hiện tại.');
        $validator->password('new_password', $newPassword, 8);
        $validator->passwordMatch('confirm_password', $newPassword, $confirmPassword);

        if ($validator->hasErrors()) {
            SessionHelper::setFlash('error', $validator->getFirstError());
            header('Location: index.php?page=profile');
            exit;
        }

        $email = SessionHelper::get('customer_email', '');
        $customer = $this->customersModel->getCustomerByEmail($email);

        if (!$customer || !isset($customer['password']) || !password_verify($currentPassword, $customer['password'])) {
            SessionHelper::setFlash('error', 'Mật khẩu hiện tại không đúng.');
            header('Location: index.php?page=profile');
            exit;
        }

        $customerId = (int) SessionHelper::getCustomerId();
        $ok = $this->customersModel->updatePassword($customerId, $newPassword);

        if ($ok) {
            SessionHelper::setFlash('success', 'Đổi mật khẩu thành công.');
        } else {
            SessionHelper::setFlash('error', 'Đổi mật khẩu thất bại. Vui lòng thử lại.');
        }

        header('Location: index.php?page=profile');
        exit;
    }
}
