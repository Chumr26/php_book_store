<?php

require_once __DIR__ . '/../../Controller/BaseController.php';
require_once __DIR__ . '/../../Model/Coupon.php';
require_once __DIR__ . '/../../Controller/helpers/SessionHelper.php';
require_once __DIR__ . '/../../Controller/helpers/Validator.php';

class AdminCouponController extends BaseController
{
    private $couponModel;

    public function __construct($db_connection)
    {
        parent::__construct($db_connection);
        $this->couponModel = new Coupon($db_connection);

        SessionHelper::start();
        $this->checkAdminAuth();
    }

    public function index()
    {
        try {
            $coupons = $this->couponModel->getAllCoupons();

            return [
                'coupons' => $coupons,
                'csrf_token' => SessionHelper::generateCSRFToken()
            ];
        } catch (Exception $e) {
            error_log("Error in AdminCouponController::index: " . $e->getMessage());
            SessionHelper::setFlash('error', 'Lỗi: ' . $e->getMessage());
            return [
                'coupons' => [],
                'csrf_token' => ''
            ];
        }
    }

    public function create()
    {
        return [
            'csrf_token' => SessionHelper::generateCSRFToken()
        ];
    }

    public function store()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Invalid request method');
            }

            // CSRF validation
            $token = $_POST['csrf_token'] ?? '';
            if (!SessionHelper::verifyCSRFToken($token)) {
                throw new Exception('Invalid CSRF token');
            }

            // Validate input
            $validator = new Validator();
            $validator->required('ma_code', $_POST['ma_code'] ?? '', 'Vui lòng nhập mã giảm giá');
            $validator->required('ten_chuongtrinh', $_POST['ten_chuongtrinh'] ?? '', 'Vui lòng nhập tên chương trình');
            $validator->required('gia_tri_giam', $_POST['gia_tri_giam'] ?? '', 'Vui lòng nhập giá trị giảm');
            $validator->required('ngay_bat_dau', $_POST['ngay_bat_dau'] ?? '', 'Vui lòng nhập ngày bắt đầu');
            $validator->required('ngay_ket_thuc', $_POST['ngay_ket_thuc'] ?? '', 'Vui lòng nhập ngày kết thúc');

            if ($validator->hasErrors()) {
                SessionHelper::setFlash('error', $validator->getFirstError());
                header('Location: index.php?page=admin_coupon_create');
                exit;
            }

            // Check if code exists
            if ($this->couponModel->getCouponByCode($_POST['ma_code'])) {
                SessionHelper::setFlash('error', 'Mã giảm giá đã tồn tại');
                header('Location: index.php?page=admin_coupon_create');
                exit;
            }

            // Prepare data
            $couponData = [
                'ma_code' => strtoupper(trim(Validator::sanitizeString($_POST['ma_code']))),
                'ten_chuongtrinh' => Validator::sanitizeString($_POST['ten_chuongtrinh']),
                'loai_giam' => $_POST['loai_giam'] ?? 'percent',
                'gia_tri_giam' => floatval($_POST['gia_tri_giam']),
                'gia_tri_toi_thieu' => floatval($_POST['gia_tri_toi_thieu'] ?? 0),
                'giam_toi_da' => !empty($_POST['giam_toi_da']) ? floatval($_POST['giam_toi_da']) : null,
                'so_luong' => intval($_POST['so_luong'] ?? 1),
                'ngay_bat_dau' => date('Y-m-d H:i:s', strtotime(str_replace('T', ' ', $_POST['ngay_bat_dau']))),
                'ngay_ket_thuc' => date('Y-m-d H:i:s', strtotime(str_replace('T', ' ', $_POST['ngay_ket_thuc']))),
                'trang_thai' => $_POST['trang_thai'] ?? 'active'
            ];

            // Create coupon
            if ($this->couponModel->createCoupon($couponData)) {
                SessionHelper::setFlash('success', 'Thêm mã giảm giá thành công');
                header('Location: index.php?page=admin_coupons');
            } else {
                throw new Exception('Không thể thêm mã giảm giá');
            }
        } catch (Exception $e) {
            error_log("Error in store: " . $e->getMessage());
            SessionHelper::setFlash('error', $e->getMessage());
            header('Location: index.php?page=admin_coupon_create');
            exit;
        }
    }

    public function edit()
    {
        try {
            $couponId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
            if ($couponId <= 0) {
                throw new Exception('ID không hợp lệ');
            }

            $coupon = $this->couponModel->getCouponById($couponId);

            if (!$coupon) {
                throw new Exception('Không tìm thấy mã giảm giá');
            }

            return [
                'coupon' => $coupon,
                'csrf_token' => SessionHelper::generateCSRFToken()
            ];
        } catch (Exception $e) {
            error_log("Error in edit: " . $e->getMessage());
            SessionHelper::setFlash('error', $e->getMessage());
            header('Location: index.php?page=admin_coupons');
            exit;
        }
    }

    public function update()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Invalid request method');
            }

            $token = $_POST['csrf_token'] ?? '';
            if (!SessionHelper::verifyCSRFToken($token)) {
                throw new Exception('Invalid CSRF token');
            }

            $couponId = isset($_POST['id_magiamgia']) ? (int)$_POST['id_magiamgia'] : 0;

            if ($couponId <= 0) {
                throw new Exception('ID không hợp lệ');
            }

            // Validate
            $validator = new Validator();
            $validator->required('ma_code', $_POST['ma_code'] ?? '', 'Vui lòng nhập mã giảm giá');
            $validator->required('ten_chuongtrinh', $_POST['ten_chuongtrinh'] ?? '', 'Vui lòng nhập tên chương trình');
            $validator->required('gia_tri_giam', $_POST['gia_tri_giam'] ?? '', 'Vui lòng nhập giá trị giảm');
            $validator->required('ngay_bat_dau', $_POST['ngay_bat_dau'] ?? '', 'Vui lòng nhập ngày bắt đầu');
            $validator->required('ngay_ket_thuc', $_POST['ngay_ket_thuc'] ?? '', 'Vui lòng nhập ngày kết thúc');

            if ($validator->hasErrors()) {
                SessionHelper::setFlash('error', $validator->getFirstError());
                header('Location: index.php?page=admin_coupon_edit&id=' . $couponId);
                exit;
            }

            // Validate date inputs
            $startDate = trim($_POST['ngay_bat_dau'] ?? '');
            $endDate = trim($_POST['ngay_ket_thuc'] ?? '');

            if (empty($startDate) || empty($endDate)) {
                SessionHelper::setFlash('error', 'Ngày bắt đầu và ngày kết thúc không được để trống');
                header('Location: index.php?page=admin_coupon_edit&id=' . $couponId);
                exit;
            }


            // Prepare data
            $couponData = [
                'ma_code' => strtoupper(trim(Validator::sanitizeString($_POST['ma_code']))),
                'ten_chuongtrinh' => Validator::sanitizeString($_POST['ten_chuongtrinh']),
                'loai_giam' => $_POST['loai_giam'] ?? 'percent',
                'gia_tri_giam' => floatval($_POST['gia_tri_giam']),
                'gia_tri_toi_thieu' => floatval($_POST['gia_tri_toi_thieu'] ?? 0),
                'giam_toi_da' => !empty($_POST['giam_toi_da']) ? floatval($_POST['giam_toi_da']) : null,
                'so_luong' => intval($_POST['so_luong'] ?? 1),
                'ngay_bat_dau' => date('Y-m-d H:i:s', strtotime(str_replace('T', ' ', $_POST['ngay_bat_dau']))),
                'ngay_ket_thuc' => date('Y-m-d H:i:s', strtotime(str_replace('T', ' ', $_POST['ngay_ket_thuc']))),
                'trang_thai' => $_POST['trang_thai'] ?? 'active'
            ];

            if ($this->couponModel->updateCoupon($couponId, $couponData)) {
                SessionHelper::setFlash('success', 'Cập nhật mã giảm giá thành công');
                header('Location: index.php?page=admin_coupons');
            } else {
                throw new Exception('Không thể cập nhật mã giảm giá');
            }
        } catch (Exception $e) {
            error_log("Error in update: " . $e->getMessage());
            SessionHelper::setFlash('error', $e->getMessage());
            header('Location: index.php?page=admin_coupon_edit&id=' . ($_POST['id_magiamgia'] ?? 0));
            exit;
        }
    }

    public function delete()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Invalid request method');
            }

            $token = $_POST['csrf_token'] ?? '';
            if (!SessionHelper::verifyCSRFToken($token)) {
                throw new Exception('Invalid CSRF token');
            }

            $couponId = isset($_POST['id']) ? (int)$_POST['id'] : 0;

            if ($couponId <= 0) {
                throw new Exception('ID không hợp lệ');
            }

            if ($this->couponModel->deleteCoupon($couponId)) {
                SessionHelper::setFlash('success', 'Xóa thành công');
            } else {
                throw new Exception('Không thể xóa');
            }
        } catch (Exception $e) {
            error_log("Error in delete: " . $e->getMessage());
            SessionHelper::setFlash('error', $e->getMessage());
        }

        header('Location: index.php?page=admin_coupons');
        exit;
    }

    private function checkAdminAuth()
    {
        if (!SessionHelper::isAdminLoggedIn()) {
            SessionHelper::setFlash('error', 'Vui lòng đăng nhập với quyền admin');
            header('Location: /Admin/login.php');
            exit;
        }
    }
}
