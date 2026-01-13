<?php
/**
 * EmailVerificationController
 * Handles email verification link clicks.
 */

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../Model/Customers.php';
require_once __DIR__ . '/helpers/SessionHelper.php';

class EmailVerificationController extends BaseController {
    private $customersModel;

    public function __construct($conn) {
        parent::__construct($conn);
        $this->customersModel = new Customers($conn);
    }

    public function verify() {
        SessionHelper::start();

        $token = $_GET['token'] ?? '';
        $token = is_string($token) ? trim($token) : '';

        if ($token === '') {
            SessionHelper::setFlash('error', 'Liên kết xác minh không hợp lệ.');
            header('Location: index.php?page=login');
            exit;
        }

        $customer = $this->customersModel->verifyEmailByToken($token);

        if (!$customer) {
            SessionHelper::setFlash('error', 'Liên kết xác minh không hợp lệ hoặc đã hết hạn (30 phút). Vui lòng gửi lại email xác minh.');
            header('Location: index.php?page=login');
            exit;
        }

        if (($customer['trang_thai'] ?? '') !== 'active') {
            SessionHelper::setFlash('error', 'Tài khoản đã bị khóa. Vui lòng liên hệ quản trị viên.');
            header('Location: index.php?page=login');
            exit;
        }

        // Auto-login after verify
        SessionHelper::setCustomerLogin(
            (int)$customer['id_khachhang'],
            $customer['email'],
            $customer['ten_khachhang']
        );

        SessionHelper::setFlash('success', 'Xác minh email thành công! Bạn đã được đăng nhập.');
        header('Location: index.php');
        exit;
    }
}
