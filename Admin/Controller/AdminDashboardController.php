<?php
/**
 * AdminDashboardController - Quản lý dashboard admin
 * 
 * Hiển thị các thống kê và báo cáo tổng quan:
 * - Thống kê tổng quan (doanh thu, đơn hàng, khách hàng, sách)
 * - Biểu đồ doanh thu theo thời gian
 * - Top sách bán chạy
 * - Đơn hàng gần đây
 * - Khách hàng mới
 * - Sách sắp hết hàng
 */

require_once __DIR__ . '/../../Controller/BaseController.php';
require_once __DIR__ . '/../../Model/Orders.php';
require_once __DIR__ . '/../../Model/Books.php';
require_once __DIR__ . '/../../Model/Customers.php';
require_once __DIR__ . '/../../Controller/helpers/SessionHelper.php';

class AdminDashboardController extends BaseController {
    private $orderModel;
    private $bookModel;
    private $customerModel;
    
    /**
     * Constructor
     * 
     * @param mysqli $db_connection Kết nối database
     */
    public function __construct($db_connection) {
        parent::__construct($db_connection);
        $this->orderModel = new Orders($db_connection);
        $this->bookModel = new Books($db_connection);
        $this->customerModel = new Customers($db_connection);
        
        // Khởi động session
        SessionHelper::start();
        
        // Kiểm tra quyền admin
        $this->checkAdminAuth();
    }
    
    /**
     * Hiển thị dashboard
     * 
     * @return array
     */
    public function index() {
        try {
            $period = $_GET['period'] ?? 'month'; // day, week, month, year
            
            $data = [
                'statistics' => $this->getStatistics($period),
                'revenue_chart' => $this->getRevenueChart($period),
                'top_selling_books' => $this->getTopSellingBooks(10),
                'recent_orders' => $this->getRecentOrders(10),
                'new_customers' => $this->getNewCustomers(10),
                'low_stock_books' => $this->getLowStockBooks(10),
                'order_status_summary' => $this->getOrderStatusSummary(),
                'period' => $period
            ];
            
            return $data;
            
        } catch (Exception $e) {
            error_log("Error in AdminDashboardController::index: " . $e->getMessage());
            SessionHelper::setFlash('error', 'Không thể tải dữ liệu dashboard');
            return [];
        }
    }
    
    /**
     * Lấy thống kê tổng quan
     * 
     * @param string $period
     * @return array
     */
    public function getStatistics($period = 'month') {
        try {
            $orderDateCondition = $this->getDateCondition($period, 'ngay_dat_hang');
            $customerDateCondition = $this->getDateCondition($period, 'ngay_dang_ky');
            
            // Tổng doanh thu
            $revenueQuery = "SELECT COALESCE(SUM(tong_tien), 0) as total_revenue 
                           FROM hoadon 
                           WHERE trang_thai_thanh_toan = 'paid' 
                           AND trang_thai != 'cancelled'
                           $orderDateCondition";
            $revenueResult = $this->conn->query($revenueQuery);
            $totalRevenue = $revenueResult->fetch_assoc()['total_revenue'];
            
            // Tổng số đơn hàng
            $orderQuery = "SELECT COUNT(*) as total_orders 
                         FROM hoadon 
                         WHERE 1=1 $orderDateCondition";
            $orderResult = $this->conn->query($orderQuery);
            $totalOrders = $orderResult->fetch_assoc()['total_orders'];
            
            // Tổng số đơn hàng thành công
            $successOrderQuery = "SELECT COUNT(*) as success_orders 
                                FROM hoadon 
                                WHERE trang_thai = 'completed' 
                                $orderDateCondition";
            $successOrderResult = $this->conn->query($successOrderQuery);
            $successOrders = $successOrderResult->fetch_assoc()['success_orders'];
            
            // Tổng số khách hàng (mới trong kỳ)
            $customerQuery = "SELECT COUNT(*) as total_customers FROM khachhang WHERE 1=1 $customerDateCondition";
            $customerResult = $this->conn->query($customerQuery);
            $totalCustomers = $customerResult->fetch_assoc()['total_customers'];
            
            // Tổng số sách
            $bookQuery = "SELECT COUNT(*) as total_books FROM sach";
            $bookResult = $this->conn->query($bookQuery);
            $totalBooks = $bookResult->fetch_assoc()['total_books'];
            
            // Tổng số lượt xem
            $viewQuery = "SELECT COALESCE(SUM(luot_xem), 0) as total_views FROM sach";
            $viewResult = $this->conn->query($viewQuery);
            $totalViews = $viewResult->fetch_assoc()['total_views'];
            
            // Đơn hàng chờ xử lý
            $pendingQuery = "SELECT COUNT(*) as pending_orders 
                           FROM hoadon 
                           WHERE trang_thai = 'pending'";
            $pendingResult = $this->conn->query($pendingQuery);
            $pendingOrders = $pendingResult->fetch_assoc()['pending_orders'];
            
            // Tính tỷ lệ chuyển đổi
            $conversionRate = $totalOrders > 0 ? ($successOrders / $totalOrders) * 100 : 0;
            
            // Giá trị trung bình đơn hàng
            $avgOrderValue = $successOrders > 0 ? $totalRevenue / $successOrders : 0;
            
            return [
                'total_revenue' => $totalRevenue,
                'total_orders' => $totalOrders,
                'success_orders' => $successOrders,
                'total_customers' => $totalCustomers,
                'total_books' => $totalBooks,
                'total_views' => $totalViews,
                'pending_orders' => $pendingOrders,
                'conversion_rate' => round($conversionRate, 2),
                'avg_order_value' => round($avgOrderValue, 0)
            ];
            
        } catch (Exception $e) {
            error_log("Error in getStatistics: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Lấy dữ liệu biểu đồ doanh thu
     * 
     * @param string $period
     * @return array
     */
    public function getRevenueChart($period = 'month') {
        try {
            $data = [];
            
            switch ($period) {
                case 'day':
                    // 24 giờ qua
                    $query = "SELECT 
                                HOUR(ngay_dat_hang) as hour,
                                COALESCE(SUM(tong_tien), 0) as revenue,
                                COUNT(*) as order_count
                            FROM hoadon
                            WHERE DATE(ngay_dat_hang) = CURDATE()
                            AND trang_thai_thanh_toan = 'paid'
                            AND trang_thai != 'cancelled'
                            GROUP BY hour
                            ORDER BY hour";
                    break;
                    
                case 'week':
                    // 7 ngày qua
                    $query = "SELECT 
                                DATE(ngay_dat_hang) as date,
                                DAYNAME(ngay_dat_hang) as day_name,
                                COALESCE(SUM(tong_tien), 0) as revenue,
                                COUNT(*) as order_count
                            FROM hoadon
                            WHERE ngay_dat_hang >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
                            AND trang_thai_thanh_toan = 'paid'
                            AND trang_thai != 'cancelled'
                            GROUP BY date, day_name
                            ORDER BY date";
                    break;
                    
                case 'year':
                    // 12 tháng qua
                    $query = "SELECT 
                                MONTH(ngay_dat_hang) as month,
                                YEAR(ngay_dat_hang) as year,
                                COALESCE(SUM(tong_tien), 0) as revenue,
                                COUNT(*) as order_count
                            FROM hoadon
                            WHERE ngay_dat_hang >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
                            AND trang_thai_thanh_toan = 'paid'
                            AND trang_thai != 'cancelled'
                            GROUP BY year, month
                            ORDER BY year, month";
                    break;
                    
                case 'month':
                default:
                    // 30 ngày qua
                    $query = "SELECT 
                                DATE(ngay_dat_hang) as date,
                                COALESCE(SUM(tong_tien), 0) as revenue,
                                COUNT(*) as order_count
                            FROM hoadon
                            WHERE ngay_dat_hang >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                            AND trang_thai_thanh_toan = 'paid'
                            AND trang_thai != 'cancelled'
                            GROUP BY date
                            ORDER BY date";
                    break;
            }
            
            $result = $this->conn->query($query);
            
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
            
            return $data;
            
        } catch (Exception $e) {
            error_log("Error in getRevenueChart: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Lấy top sách bán chạy
     * 
     * @param int $limit
     * @return array
     */
    public function getTopSellingBooks($limit = 10) {
        try {
            $query = "SELECT s.*, 
                           COUNT(ct.id_chitiet) as order_count,
                           SUM(ct.so_luong) as total_sold,
                           SUM(ct.thanh_tien) as total_revenue
                    FROM sach s
                    LEFT JOIN chitiet_hoadon ct ON s.id_sach = ct.id_sach
                    LEFT JOIN hoadon d ON ct.id_hoadon = d.id_hoadon
                    WHERE d.trang_thai != 'cancelled'
                    GROUP BY s.id_sach
                    ORDER BY total_sold DESC
                    LIMIT ?";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("i", $limit);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $books = [];
            while ($row = $result->fetch_assoc()) {
                $books[] = $row;
            }
            
            return $books;
            
        } catch (Exception $e) {
            error_log("Error in getTopSellingBooks: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Lấy đơn hàng gần đây
     * 
     * @param int $limit
     * @return array
     */
    public function getRecentOrders($limit = 10) {
        try {
            $query = "SELECT d.*, k.ten_khachhang as ho_ten, k.email
                    FROM hoadon d
                    JOIN khachhang k ON d.id_khachhang = k.id_khachhang
                    ORDER BY d.ngay_dat_hang DESC
                    LIMIT ?";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("i", $limit);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $orders = [];
            while ($row = $result->fetch_assoc()) {
                $orders[] = $row;
            }
            
            return $orders;
            
        } catch (Exception $e) {
            error_log("Error in getRecentOrders: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Lấy khách hàng mới
     * 
     * @param int $limit
     * @return array
     */
    public function getNewCustomers($limit = 10) {
        try {
            $query = "SELECT k.*,
                           COUNT(d.id_hoadon) as order_count,
                           COALESCE(SUM(d.tong_tien), 0) as total_spent
                    FROM khachhang k
                    LEFT JOIN hoadon d ON k.id_khachhang = d.id_khachhang
                    GROUP BY k.id_khachhang
                    ORDER BY k.ngay_dang_ky DESC
                    LIMIT ?";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("i", $limit);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $customers = [];
            while ($row = $result->fetch_assoc()) {
                $customers[] = $row;
            }
            
            return $customers;
            
        } catch (Exception $e) {
            error_log("Error in getNewCustomers: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Lấy sách sắp hết hàng
     * 
     * @param int $limit
     * @return array
     */
    public function getLowStockBooks($limit = 10) {
        try {
            $query = "SELECT * FROM sach 
                    WHERE so_luong_ton <= 10 
                    AND trang_thai = 'available'
                    ORDER BY so_luong_ton ASC
                    LIMIT ?";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("i", $limit);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $books = [];
            while ($row = $result->fetch_assoc()) {
                $books[] = $row;
            }
            
            return $books;
            
        } catch (Exception $e) {
            error_log("Error in getLowStockBooks: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Lấy tóm tắt trạng thái đơn hàng
     * 
     * @return array
     */
    public function getOrderStatusSummary() {
        try {
            $query = "SELECT 
                        trang_thai as trang_thai_don_hang,
                        COUNT(*) as count,
                        COALESCE(SUM(tong_tien), 0) as total_amount
                    FROM hoadon
                    GROUP BY trang_thai";
            
            $result = $this->conn->query($query);
            
            $summary = [];
            while ($row = $result->fetch_assoc()) {
                $summary[$row['trang_thai_don_hang']] = [
                    'count' => $row['count'],
                    'total_amount' => $row['total_amount']
                ];
            }
            
            return $summary;
            
        } catch (Exception $e) {
            error_log("Error in getOrderStatusSummary: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Export dữ liệu thống kê ra JSON (AJAX endpoint)
     * 
     * @return void
     */
    public function exportStatistics() {
        try {
            $period = $_GET['period'] ?? 'month';
            
            $data = [
                'statistics' => $this->getStatistics($period),
                'revenue_chart' => $this->getRevenueChart($period),
                'order_status_summary' => $this->getOrderStatusSummary()
            ];
            
            header('Content-Type: application/json');
            echo json_encode($data);
            exit;
            
        } catch (Exception $e) {
            error_log("Error in exportStatistics: " . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Không thể export dữ liệu']);
            exit;
        }
    }
    
    // ========== PRIVATE HELPER METHODS ==========
    
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
    
    /**
     * Tạo điều kiện date cho query
     * 
     * @param string $period
     * @return string
     */
    private function getDateCondition($period, $column = 'ngay_dat_hang') {
        switch ($period) {
            case 'day':
                return "AND DATE($column) = CURDATE()";
            case 'week':
                return "AND $column >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
            case 'year':
                return "AND $column >= DATE_SUB(CURDATE(), INTERVAL 1 YEAR)";
            case 'month':
            default:
                return "AND $column >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
        }
    }
}
