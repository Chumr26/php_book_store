<?php
/**
 * AdminProfileController
 * Handles admin profile (view/update + change password)
 */

require_once __DIR__ . '/../Model/AdminAuth.php';
require_once __DIR__ . '/../../Controller/helpers/SessionHelper.php';
require_once __DIR__ . '/../../Controller/helpers/Validator.php';

class AdminProfileController {
    private $conn;
    private $authModel;

    public function __construct($dbConnection) {
        $this->conn = $dbConnection;
        $this->authModel = new AdminAuth($this->conn);
    }

    public function show() {
        SessionHelper::start();

        if (!SessionHelper::isAdminLoggedIn()) {
            SessionHelper::setFlash('warning', 'Vui lòng đăng nhập để tiếp tục');
            header('Location: ' . ADMIN_BASE_URL . 'index.php?page=login');
            exit;
        }

        $adminId = (int) SessionHelper::getAdminId();
        $admin = $this->authModel->getAdminById($adminId);

        // Fallback to session data if DB not available
        if (!$admin) {
            $admin = $this->authModel->getCurrentAdmin();
        }

        return [
            'csrf_token' => SessionHelper::generateCSRFToken(),
            'admin' => $admin,
            'page_title' => 'Hồ sơ'
        ];
    }

    public function handlePost() {
        SessionHelper::start();

        if (!SessionHelper::isAdminLoggedIn()) {
            SessionHelper::setFlash('warning', 'Vui lòng đăng nhập để tiếp tục');
            header('Location: ' . ADMIN_BASE_URL . 'index.php?page=login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . ADMIN_BASE_URL . 'index.php?page=profile');
            exit;
        }

        $token = $_POST['csrf_token'] ?? '';
        if (!SessionHelper::verifyCSRFToken($token)) {
            SessionHelper::setFlash('error', 'Token bảo mật không hợp lệ. Vui lòng thử lại.');
            header('Location: ' . ADMIN_BASE_URL . 'index.php?page=profile');
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
        $email = trim($_POST['email'] ?? '');

        $validator->required('full_name', $fullName, 'Họ tên là bắt buộc.');
        $validator->required('email', $email, 'Email là bắt buộc.');
        $validator->email('email', $email, 'Email không hợp lệ.');

        if ($validator->hasErrors()) {
            SessionHelper::setFlash('error', $validator->getFirstError());
            header('Location: ' . ADMIN_BASE_URL . 'index.php?page=profile');
            exit;
        }

        $adminId = (int) SessionHelper::getAdminId();
        $ok = $this->authModel->updateAdminProfile($adminId, [
            'full_name' => $fullName,
            'email' => $email
        ]);

        if ($ok) {
            $_SESSION['admin_name'] = $fullName;
            $_SESSION['admin_email'] = $email;
            SessionHelper::setFlash('success', 'Cập nhật hồ sơ thành công.');
        } else {
            SessionHelper::setFlash('error', 'Cập nhật hồ sơ thất bại. Vui lòng thử lại.');
        }

        header('Location: ' . ADMIN_BASE_URL . 'index.php?page=profile');
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
            header('Location: ' . ADMIN_BASE_URL . 'index.php?page=profile');
            exit;
        }

        $adminId = (int) SessionHelper::getAdminId();

        // Verify current password against DB (if available)
        if (!$this->authModel->verifyAdminPassword($adminId, $currentPassword)) {
            SessionHelper::setFlash('error', 'Mật khẩu hiện tại không đúng.');
            header('Location: ' . ADMIN_BASE_URL . 'index.php?page=profile');
            exit;
        }

        $ok = $this->authModel->updateAdminPassword($adminId, $newPassword);

        if ($ok) {
            SessionHelper::setFlash('success', 'Đổi mật khẩu thành công.');
        } else {
            SessionHelper::setFlash('error', 'Đổi mật khẩu thất bại. Vui lòng thử lại.');
        }

        header('Location: ' . ADMIN_BASE_URL . 'index.php?page=profile');
        exit;
    }
}
