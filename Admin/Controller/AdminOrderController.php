<?php
/**
 * AdminOrderController - Quản lý đơn hàng
 * 
 * Các chức năng:
 * - Danh sách đơn hàng với lọc (trạng thái, ngày, khách hàng)
 * - Xem chi tiết đơn hàng
 * - Cập nhật trạng thái đơn hàng
 * - Export đơn hàng ra CSV/Excel
 * - In hóa đơn PDF
 * - Thống kê đơn hàng
 */

require_once __DIR__ . '/../../Controller/BaseController.php';
require_once __DIR__ . '/../../Model/Orders.php';
require_once __DIR__ . '/../../Model/Customers.php';
require_once __DIR__ . '/../../Controller/helpers/SessionHelper.php';
require_once __DIR__ . '/../../Controller/helpers/Validator.php';

class AdminOrderController extends BaseController {
    private $orderModel;
    private $customerModel;
    
    /**
     * Constructor
     * 
     * @param mysqli $db_connection Kết nối database
     */
    public function __construct($db_connection) {
        parent::__construct($db_connection);
        $this->orderModel = new Orders($db_connection);
        $this->customerModel = new Customers($db_connection);
        
        SessionHelper::start();
        $this->checkAdminAuth();
    }
    
    /**
     * Danh sách đơn hàng
     * 
     * @return array
     */
    public function index() {
        try {
            $page = isset($_GET['p']) ? (int)$_GET['p'] : 1;
            $perPage = 20;
            $status = $_GET['status'] ?? '';
            $paymentStatus = $_GET['payment_status'] ?? '';
            $search = $_GET['search'] ?? '';
            $fromDate = $_GET['from_date'] ?? '';
            $toDate = $_GET['to_date'] ?? '';
            
            // Build query conditions
            $conditions = [];
            $params = [];
            $types = '';
            
            if (!empty($status)) {
                $conditions[] = "d.trang_thai = ?";
                $params[] = $status;
                $types .= 's';
            }
            
            if (!empty($paymentStatus)) {
                $conditions[] = "d.trang_thai_thanh_toan = ?";
                $params[] = $paymentStatus;
                $types .= 's';
            }
            
            if (!empty($search)) {
                $conditions[] = "(d.ma_hoadon LIKE ? OR k.ten_khachhang LIKE ? OR k.email LIKE ? OR d.ten_nguoi_nhan LIKE ?)";
                $searchParam = "%$search%";
                $params[] = $searchParam;
                $params[] = $searchParam;
                $params[] = $searchParam;
                $params[] = $searchParam;
                $types .= 'ssss';
            }
            
            if (!empty($fromDate)) {
                $conditions[] = "DATE(d.ngay_dat_hang) >= ?";
                $params[] = $fromDate;
                $types .= 's';
            }
            
            if (!empty($toDate)) {
                $conditions[] = "DATE(d.ngay_dat_hang) <= ?";
                $params[] = $toDate;
                $types .= 's';
            }
            
            $whereClause = !empty($conditions) ? 'WHERE ' . implode(' AND ', $conditions) : '';
            
            // Get total count
            $countQuery = "SELECT COUNT(*) as total 
                          FROM hoadon d
                          JOIN khachhang k ON d.id_khachhang = k.id_khachhang
                          $whereClause";
            
            $stmt = $this->conn->prepare($countQuery);
            if (!empty($params)) {
                $stmt->bind_param($types, ...$params);
            }
            $stmt->execute();
            $totalOrders = $stmt->get_result()->fetch_assoc()['total'];
            $totalPages = ceil($totalOrders / $perPage);
            
            // Get orders with pagination
            $offset = ($page - 1) * $perPage;
            
            // Get orders with pagination
            $offset = ($page - 1) * $perPage;
            
            $query = "SELECT d.id_hoadon, d.ma_hoadon AS ma_don_hang, d.ngay_dat_hang AS ngay_dat, 
                             d.tong_tien AS tong_thanh_toan, d.trang_thai AS trang_thai_don_hang, d.trang_thai_thanh_toan,
                             k.ten_khachhang AS ho_ten, k.email, k.dien_thoai AS so_dien_thoai,
                             d.phuong_thuc_thanh_toan, d.ten_nguoi_nhan
                     FROM hoadon d
                     JOIN khachhang k ON d.id_khachhang = k.id_khachhang
                     $whereClause
                     ORDER BY d.ngay_dat_hang DESC
                     LIMIT ? OFFSET ?";
            
            $stmt = $this->conn->prepare($query);
            $params[] = $perPage;
            $params[] = $offset;
            $types .= 'ii';
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $orders = [];
            while ($row = $result->fetch_assoc()) {
                $orders[] = $row;
            }
            
            return [
                'orders' => $orders,
                'pagination' => [
                    'current_page' => $page,
                    'total_pages' => $totalPages,
                    'total_items' => $totalOrders,
                    'per_page' => $perPage
                ],
                'filters' => [
                    'status' => $status,
                    'payment_status' => $paymentStatus,
                    'search' => $search,
                    'from_date' => $fromDate,
                    'to_date' => $toDate
                ],
                'order_statuses' => $this->getOrderStatuses(),
                'payment_statuses' => $this->getPaymentStatuses(),
                'csrf_token' => SessionHelper::generateCSRFToken()
            ];
            
        } catch (Exception $e) {
            error_log("Error in AdminOrderController::index: " . $e->getMessage());
            SessionHelper::setFlash('error', 'Lỗi: ' . $e->getMessage());
            return [
                'orders' => [],
                'pagination' => [
                    'current_page' => 1,
                    'total_pages' => 0,
                    'total_items' => 0,
                    'per_page' => 20
                ],
                'filters' => [
                    'status' => '',
                    'payment_status' => '',
                    'search' => '',
                    'from_date' => '',
                    'to_date' => ''
                ],
                'order_statuses' => [],
                'payment_statuses' => [],
                'csrf_token' => ''
            ];
        }
    }
    
    /**
     * Xem chi tiết đơn hàng
     * 
     * @return array
     */
    public function show() {
        try {
            $orderId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
            
            if ($orderId <= 0) {
                throw new Exception('ID đơn hàng không hợp lệ');
            }
            
            // Get order details
            $order = $this->orderModel->getOrderById($orderId);
            
            if (!$order) {
                throw new Exception('Không tìm thấy đơn hàng');
            }
            
            // Get order items
            $orderItems = $this->orderModel->getOrderItems($orderId);
            
            // Get customer info
            $customer = $this->customerModel->getCustomerById($order['ma_khach_hang']);
            
            return [
                'order' => $order,
                'order_items' => $orderItems,
                'customer' => $customer,
                'order_statuses' => $this->getOrderStatuses(),
                'csrf_token' => SessionHelper::generateCSRFToken()
            ];
            
        } catch (Exception $e) {
            error_log("Error in show: " . $e->getMessage());
            SessionHelper::setFlash('error', $e->getMessage());
            header('Location: index.php?page=admin_orders');
            exit;
        }
    }
    
    /**
     * Cập nhật trạng thái đơn hàng
     * 
     * @return void
     */
    public function updateStatus() {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Invalid request method');
            }
            
            // CSRF validation
            $token = $_POST['csrf_token'] ?? '';
            if (!SessionHelper::verifyCSRFToken($token)) {
                throw new Exception('Invalid CSRF token');
            }
            
            $orderId = isset($_POST['order_id']) ? (int)$_POST['order_id'] : 0;
            $newStatus = $_POST['status'] ?? '';
            
            if ($orderId <= 0) {
                throw new Exception('ID đơn hàng không hợp lệ');
            }
            
            // Validate status
            $allowedStatuses = array_keys($this->getOrderStatuses());
            if (!in_array($newStatus, $allowedStatuses)) {
                throw new Exception('Trạng thái không hợp lệ');
            }
            
            // Get current order
            $order = $this->orderModel->getOrderById($orderId);
            
            if (!$order) {
                throw new Exception('Không tìm thấy đơn hàng');
            }
            
            // Validate status transition
            if (!$this->isValidStatusTransition($order['trang_thai_don_hang'], $newStatus)) {
                throw new Exception('Không thể chuyển đổi trạng thái này');
            }
            
            // Update status
            $success = $this->orderModel->updateOrderStatus($orderId, $newStatus);
            
            if ($success) {
                SessionHelper::setFlash('success', 'Cập nhật trạng thái đơn hàng thành công');
                
                // TODO: Send notification email to customer
                
            } else {
                throw new Exception('Không thể cập nhật trạng thái');
            }
            
        } catch (Exception $e) {
            error_log("Error in updateStatus: " . $e->getMessage());
            SessionHelper::setFlash('error', $e->getMessage());
        }
        
        $orderId = $_POST['order_id'] ?? 0;
        header('Location: index.php?page=admin_order_detail&id=' . $orderId);
        exit;
    }
    
    /**
     * Export đơn hàng ra CSV
     * 
     * @return void
     */
    public function exportOrders() {
        try {
            $status = $_GET['status'] ?? '';
            $fromDate = $_GET['from_date'] ?? '';
            $toDate = $_GET['to_date'] ?? '';
            
            // Build query
            $conditions = [];
            $params = [];
            $types = '';
            
            if (!empty($status)) {
                $conditions[] = "d.trang_thai = ?";
                $params[] = $status;
                $types .= 's';
            }
            
            if (!empty($fromDate)) {
                $conditions[] = "DATE(d.ngay_dat_hang) >= ?";
                $params[] = $fromDate;
                $types .= 's';
            }
            
            if (!empty($toDate)) {
                $conditions[] = "DATE(d.ngay_dat_hang) <= ?";
                $params[] = $toDate;
                $types .= 's';
            }
            
            $whereClause = !empty($conditions) ? 'WHERE ' . implode(' AND ', $conditions) : '';
            
            // Corrected table names: don_hang -> hoadon, khach_hang -> khachhang
            // Corrected columns: ngay_dat -> ngay_dat_hang, ma_don_hang -> ma_hoadon
            $query = "SELECT d.ma_hoadon, k.ten_khachhang as ho_ten, k.email, 
                             d.ngay_dat_hang, d.tong_tien, d.trang_thai, 
                             d.trang_thai_thanh_toan, d.phuong_thuc_thanh_toan
                     FROM hoadon d
                     JOIN khachhang k ON d.id_khachhang = k.id_khachhang
                     $whereClause
                     ORDER BY d.ngay_dat_hang DESC";
            
            $stmt = $this->conn->prepare($query);
            if (!empty($params)) {
                $stmt->bind_param($types, ...$params);
            }
            $stmt->execute();
            $result = $stmt->get_result();
            
            // Set headers for CSV download
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="orders_' . date('Y-m-d') . '.csv"');
            
            // Create file pointer
            $output = fopen('php://output', 'w');
            
            // Add BOM for UTF-8
            fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Add headers
            fputcsv($output, [
                'Mã đơn hàng',
                'Khách hàng',
                'Email',
                'Ngày đặt',
                'Tổng tiền',
                'Trạng thái đơn hàng',
                'Trạng thái thanh toán',
                'Phương thức thanh toán'
            ]);
            
            // Add data
            while ($row = $result->fetch_assoc()) {
                fputcsv($output, [
                    $row['ma_hoadon'],
                    $row['ho_ten'],
                    $row['email'],
                    $row['ngay_dat_hang'],
                    number_format($row['tong_tien']) . 'đ',
                    $row['trang_thai'],
                    $row['trang_thai_thanh_toan'],
                    $row['phuong_thuc_thanh_toan']
                ]);
            }
            
            fclose($output);
            exit;
            
        } catch (Exception $e) {
            error_log("Error in exportOrders: " . $e->getMessage());
            SessionHelper::setFlash('error', 'Không thể export đơn hàng');
            header('Location: index.php?page=admin_orders');
            exit;
        }
    }
    
    /**
     * In hóa đơn (placeholder - cần implement PDF library)
     * 
     * @return void
     */
    public function printInvoice() {
        try {
            $orderId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
            
            if ($orderId <= 0) {
                throw new Exception('ID đơn hàng không hợp lệ');
            }
            
            // TODO: Implement PDF generation using libraries like TCPDF or FPDF
            
            SessionHelper::setFlash('info', 'Tính năng in hóa đơn PDF đang được phát triển');
            header('Location: index.php?page=admin_order_detail&id=' . $orderId);
            exit;
            
        } catch (Exception $e) {
            error_log("Error in printInvoice: " . $e->getMessage());
            SessionHelper::setFlash('error', $e->getMessage());
            header('Location: index.php?page=admin_orders');
            exit;
        }
    }
    
    // ========== PRIVATE HELPER METHODS ==========
    
    /**
     * Lấy danh sách trạng thái đơn hàng
     * 
     * @return array
     */
    private function getOrderStatuses() {
        // Keep values as Vietnamese labels for UI, keys as DB enum codes.
        return [
            'pending' => 'Chờ xác nhận',
            'confirmed' => 'Đã xác nhận',
            'shipping' => 'Đang giao hàng',
            'completed' => 'Đã giao',
            'cancelled' => 'Đã hủy'
        ];
    }
    
    /**
     * Lấy danh sách trạng thái thanh toán
     * 
     * @return array
     */
    private function getPaymentStatuses() {
        // DB enum: unpaid|paid
        return [
            'unpaid' => 'Chờ thanh toán',
            'paid' => 'Đã thanh toán'
        ];
    }
    
    /**
     * Kiểm tra chuyển đổi trạng thái hợp lệ
     * 
     * @param string $currentStatus
     * @param string $newStatus
     * @return bool
     */
    private function isValidStatusTransition($currentStatus, $newStatus) {
        // Define allowed transitions
        $allowedTransitions = [
            'pending' => ['confirmed', 'cancelled'],
            'confirmed' => ['shipping', 'cancelled'],
            'shipping' => ['completed', 'cancelled'],
            'completed' => [],
            'cancelled' => []
        ];
        
        if (!isset($allowedTransitions[$currentStatus])) {
            return false;
        }
        
        return in_array($newStatus, $allowedTransitions[$currentStatus]);
    }
    
    /**
     * Kiểm tra quyền admin
     * 
     * @return void
     */
    private function checkAdminAuth() {
        if (!SessionHelper::isAdminLoggedIn()) {
            SessionHelper::setFlash('error', 'Vui lòng đăng nhập với quyền admin');
            header('Location: /Admin/login.php');
            exit;
        }
    }
}
