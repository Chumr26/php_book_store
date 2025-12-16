<?php
/**
 * AdminCustomerController - Quản lý khách hàng
 * 
 * Các chức năng:
 * - Danh sách khách hàng với tìm kiếm
 * - Xem chi tiết khách hàng
 * - Xem lịch sử đơn hàng của khách hàng
 * - Quản lý trạng thái tài khoản (active/inactive/banned)
 * - Thống kê khách hàng
 */

require_once __DIR__ . '/../../Controller/BaseController.php';
require_once __DIR__ . '/../../Model/Customers.php';
require_once __DIR__ . '/../../Model/Orders.php';
require_once __DIR__ . '/../../Controller/helpers/SessionHelper.php';
require_once __DIR__ . '/../../Controller/helpers/Validator.php';

class AdminCustomerController extends BaseController {
    private $customerModel;
    private $orderModel;
    
    /**
     * Constructor
     * 
     * @param mysqli $db_connection Kết nối database
     */
    public function __construct($db_connection) {
        parent::__construct($db_connection);
        $this->customerModel = new Customers($db_connection);
        $this->orderModel = new Orders($db_connection);
        
        SessionHelper::start();
        $this->checkAdminAuth();
    }
    
    /**
     * Danh sách khách hàng
     * 
     * @return array
     */
    public function index() {
        try {
            $page = isset($_GET['p']) ? (int)$_GET['p'] : 1;
            $perPage = 20;
            $search = $_GET['search'] ?? '';
            $status = $_GET['status'] ?? '';
            $sortBy = $_GET['sort_by'] ?? 'ngay_tao';
            $order = $_GET['order'] ?? 'DESC';
            
            // Build query conditions
            $conditions = [];
            $params = [];
            $types = '';
            
            if (!empty($search)) {
                $conditions[] = "(ho_ten LIKE ? OR email LIKE ? OR so_dien_thoai LIKE ?)";
                $searchParam = "%$search%";
                $params[] = $searchParam;
                $params[] = $searchParam;
                $params[] = $searchParam;
                $types .= 'sss';
            }
            
            if (!empty($status)) {
                $conditions[] = "trang_thai = ?";
                $params[] = $status;
                $types .= 's';
            }
            
            $whereClause = !empty($conditions) ? 'WHERE ' . implode(' AND ', $conditions) : '';
            
            // Get total count
            $countQuery = "SELECT COUNT(*) as total FROM khach_hang $whereClause";
            $stmt = $this->conn->prepare($countQuery);
            
            if (!empty($params)) {
                $stmt->bind_param($types, ...$params);
            }
            
            $stmt->execute();
            $totalCustomers = $stmt->get_result()->fetch_assoc()['total'];
            $totalPages = ceil($totalCustomers / $perPage);
            
            // Get customers with pagination
            $offset = ($page - 1) * $perPage;
            
            // Validate sort column
            $allowedSortColumns = ['ho_ten', 'email', 'ngay_tao', 'ngay_cap_nhat'];
            $sortBy = in_array($sortBy, $allowedSortColumns) ? $sortBy : 'ngay_tao';
            $order = strtoupper($order) === 'ASC' ? 'ASC' : 'DESC';
            
            $query = "SELECT k.*,
                           COUNT(DISTINCT d.ma_don_hang) as order_count,
                           COALESCE(SUM(d.tong_thanh_toan), 0) as total_spent
                     FROM khach_hang k
                     LEFT JOIN don_hang d ON k.ma_khach_hang = d.ma_khach_hang
                     $whereClause
                     GROUP BY k.ma_khach_hang
                     ORDER BY k.$sortBy $order
                     LIMIT ? OFFSET ?";
            
            $stmt = $this->conn->prepare($query);
            $params[] = $perPage;
            $params[] = $offset;
            $types .= 'ii';
            
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $customers = [];
            while ($row = $result->fetch_assoc()) {
                $customers[] = $row;
            }
            
            // Get filter data
            $statuses = ['active' => 'Hoạt động', 'inactive' => 'Không hoạt động', 'banned' => 'Bị khóa'];
            
            return [
                'customers' => $customers,
                'statuses' => $statuses,
                'pagination' => [
                    'current_page' => $page,
                    'total_pages' => $totalPages,
                    'total_items' => $totalCustomers,
                    'per_page' => $perPage
                ],
                'filters' => [
                    'search' => $search,
                    'status' => $status,
                    'sort_by' => $sortBy,
                    'order' => $order
                ],
                'csrf_token' => SessionHelper::generateCSRFToken()
            ];
            
        } catch (Exception $e) {
            error_log("Error in AdminCustomerController::index: " . $e->getMessage());
            SessionHelper::setFlash('error', 'Không thể tải danh sách khách hàng');
            return ['customers' => []];
        }
    }
    
    /**
     * Xem chi tiết khách hàng
     * 
     * @return array
     */
    public function show() {
        try {
            $customerId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
            
            if ($customerId <= 0) {
                throw new Exception('ID khách hàng không hợp lệ');
            }
            
            // Get customer info
            $customer = $this->customerModel->getCustomerById($customerId);
            
            if (!$customer) {
                throw new Exception('Không tìm thấy khách hàng');
            }
            
            // Get customer orders
            $orders = $this->orderModel->getOrdersByCustomer($customerId);
            
            // Get customer statistics
            $stats = $this->getCustomerStatistics($customerId);
            
            return [
                'customer' => $customer,
                'orders' => $orders,
                'statistics' => $stats,
                'csrf_token' => SessionHelper::generateCSRFToken()
            ];
            
        } catch (Exception $e) {
            error_log("Error in show: " . $e->getMessage());
            SessionHelper::setFlash('error', $e->getMessage());
            header('Location: index.php?page=admin_customers');
            exit;
        }
    }
    
    /**
     * Cập nhật trạng thái tài khoản khách hàng
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
            
            $customerId = isset($_POST['customer_id']) ? (int)$_POST['customer_id'] : 0;
            $status = $_POST['status'] ?? '';
            
            if ($customerId <= 0) {
                throw new Exception('ID khách hàng không hợp lệ');
            }
            
            // Validate status
            $allowedStatuses = ['active', 'inactive', 'banned'];
            if (!in_array($status, $allowedStatuses)) {
                throw new Exception('Trạng thái không hợp lệ');
            }
            
            // Update status
            $success = $this->customerModel->updateCustomerStatus($customerId, $status);
            
            if ($success) {
                SessionHelper::setFlash('success', 'Cập nhật trạng thái tài khoản thành công');
                
                // TODO: Send notification email to customer
                
            } else {
                throw new Exception('Không thể cập nhật trạng thái');
            }
            
        } catch (Exception $e) {
            error_log("Error in updateStatus: " . $e->getMessage());
            SessionHelper::setFlash('error', $e->getMessage());
        }
        
        $customerId = $_POST['customer_id'] ?? 0;
        header('Location: index.php?page=admin_customer_detail&id=' . $customerId);
        exit;
    }
    
    /**
     * Export danh sách khách hàng ra CSV
     * 
     * @return void
     */
    public function exportCustomers() {
        try {
            $status = $_GET['status'] ?? '';
            
            // Build query
            $conditions = [];
            $params = [];
            $types = '';
            
            if (!empty($status)) {
                $conditions[] = "k.trang_thai = ?";
                $params[] = $status;
                $types .= 's';
            }
            
            $whereClause = !empty($conditions) ? 'WHERE ' . implode(' AND ', $conditions) : '';
            
            $query = "SELECT k.*,
                           COUNT(DISTINCT d.ma_don_hang) as order_count,
                           COALESCE(SUM(d.tong_thanh_toan), 0) as total_spent
                     FROM khach_hang k
                     LEFT JOIN don_hang d ON k.ma_khach_hang = d.ma_khach_hang
                     $whereClause
                     GROUP BY k.ma_khach_hang
                     ORDER BY k.ngay_tao DESC";
            
            $stmt = $this->conn->prepare($query);
            if (!empty($params)) {
                $stmt->bind_param($types, ...$params);
            }
            $stmt->execute();
            $result = $stmt->get_result();
            
            // Set headers for CSV download
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="customers_' . date('Y-m-d') . '.csv"');
            
            // Create file pointer
            $output = fopen('php://output', 'w');
            
            // Add BOM for UTF-8
            fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Add headers
            fputcsv($output, [
                'ID',
                'Họ tên',
                'Email',
                'Số điện thoại',
                'Giới tính',
                'Ngày sinh',
                'Địa chỉ',
                'Trạng thái',
                'Số đơn hàng',
                'Tổng chi tiêu',
                'Ngày tạo'
            ]);
            
            // Add data
            while ($row = $result->fetch_assoc()) {
                fputcsv($output, [
                    $row['ma_khach_hang'],
                    $row['ho_ten'],
                    $row['email'],
                    $row['so_dien_thoai'],
                    $row['gioi_tinh'],
                    $row['ngay_sinh'],
                    $row['dia_chi'],
                    $row['trang_thai'],
                    $row['order_count'],
                    $row['total_spent'],
                    $row['ngay_tao']
                ]);
            }
            
            fclose($output);
            exit;
            
        } catch (Exception $e) {
            error_log("Error in exportCustomers: " . $e->getMessage());
            SessionHelper::setFlash('error', 'Không thể export danh sách khách hàng');
            header('Location: index.php?page=admin_customers');
            exit;
        }
    }
    
    // ========== PRIVATE HELPER METHODS ==========
    
    /**
     * Lấy thống kê khách hàng
     * 
     * @param int $customerId
     * @return array
     */
    private function getCustomerStatistics($customerId) {
        try {
            $query = "SELECT 
                        COUNT(DISTINCT ma_don_hang) as total_orders,
                        COALESCE(SUM(CASE WHEN trang_thai_don_hang = 'Đã giao' THEN 1 ELSE 0 END), 0) as completed_orders,
                        COALESCE(SUM(CASE WHEN trang_thai_don_hang = 'Đã hủy' THEN 1 ELSE 0 END), 0) as cancelled_orders,
                        COALESCE(SUM(tong_thanh_toan), 0) as total_spent,
                        COALESCE(AVG(tong_thanh_toan), 0) as avg_order_value,
                        MAX(ngay_dat) as last_order_date
                     FROM don_hang
                     WHERE ma_khach_hang = ?";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("i", $customerId);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_assoc();
            
            return $result;
            
        } catch (Exception $e) {
            error_log("Error in getCustomerStatistics: " . $e->getMessage());
            return [];
        }
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
