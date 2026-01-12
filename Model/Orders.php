<?php
/**
 * Orders Model Class
 * 
 * Handles all order-related operations using 'hoadon' table
 */

class Orders {
    private $conn;
    
    /**
     * Constructor - Initialize database connection
     */
    public function __construct($connection) {
        $this->conn = $connection;
    }
    
    /**
     * Generate unique order number
     * 
     * @return string Order number (format: ORD-YYYYMMDD-XXXXX)
     */
    public function generateOrderNumber() {
        // Based on SQL proc: HD + 6 random digits
        return 'HD' . str_pad(mt_rand(1, 999999), 6, '0', STR_PAD_LEFT);
    }
    
    /**
     * Create new order
     * 
     * @param int $customerId Customer ID
     * @param array $orderData Order information
     * @param array $items Cart items to order
     * @return int|false Order ID or false on failure
     */
    public function createOrder($customerId, $orderData, $items) {
        // Start transaction
        $this->conn->begin_transaction();
        
        try {
            // Generate / accept order number
            $orderNumber = $orderData['order_number'] ?? $orderData['ma_hoadon'] ?? null;
            if (empty($orderNumber)) {
                $orderNumber = $this->generateOrderNumber();
            }
            
            // Calculate total (allow controller to include shipping/tax)
            $totalAmount = isset($orderData['total_amount']) ? (float)$orderData['total_amount'] : $this->calculateTotal($items);

            // Normalize payment method to DB enum
            $paymentMethod = $orderData['payment_method'] ?? 'COD';
            $paymentMethodLower = strtolower((string)$paymentMethod);
            if ($paymentMethodLower === 'cod') {
                $paymentMethod = 'COD';
            }

            // Payment status enum: unpaid|paid
            $paymentStatus = $orderData['payment_status'] ?? 'unpaid';
            $paymentStatus = ($paymentStatus === 'paid') ? 'paid' : 'unpaid';
            
            // Insert order
            // Note: Schema has id_khachhang, ma_hoadon, tong_tien, trang_thai, phuong_thuc_thanh_toan, ...
            $sql = "INSERT INTO hoadon (
                        id_khachhang, ma_hoadon, tong_tien, trang_thai, 
                        phuong_thuc_thanh_toan, trang_thai_thanh_toan, ten_nguoi_nhan, 
                        dia_chi_giao, sdt_giao, email_giao, ghi_chu
                    ) VALUES (?, ?, ?, 'pending', ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $this->conn->prepare($sql);
            
            // Defaults
            $email = $orderData['email'] ?? '';
            $note = $orderData['note'] ?? '';
            
            $stmt->bind_param(
                "isdsssssss",
                $customerId,
                $orderNumber,
                $totalAmount,
                $paymentMethod,
                $paymentStatus,
                $orderData['recipient_name'],
                $orderData['delivery_address'],
                $orderData['phone'],
                $email,
                $note
            );
            
            if (!$stmt->execute()) {
                throw new Exception("Failed to create order: " . $stmt->error);
            }
            
            $orderId = $this->conn->insert_id;
            
            // Insert order items
            foreach ($items as $item) {
                // item keys from frontend/cart might differ. Assume standard keys.
                // Cart usually has 'id_sach', 'gia', 'so_luong'.
                $bookId = $item['id_book'] ?? $item['id_sach'] ?? $item['ma_sach'] ?? null;
                $quantity = $item['quantity'] ?? $item['so_luong'];
                $price = $item['price'] ?? $item['gia'];

                if (empty($bookId)) {
                    throw new Exception("Missing book id in cart item");
                }
                
                $itemTotal = $quantity * $price;
                
                $sqlItem = "INSERT INTO chitiet_hoadon (
                                id_hoadon, id_sach, so_luong, gia, thanh_tien
                            ) VALUES (?, ?, ?, ?, ?)";
                
                $stmtItem = $this->conn->prepare($sqlItem);
                $stmtItem->bind_param(
                    "iiidd",
                    $orderId,
                    $bookId,
                    $quantity,
                    $price,
                    $itemTotal
                );
                
                if (!$stmtItem->execute()) {
                    throw new Exception("Failed to add order item: " . $stmtItem->error);
                }
                
                // Update book stock (sach table)
                $sqlStock = "UPDATE sach 
                             SET so_luong_ton = so_luong_ton - ?,
                                 luot_ban = luot_ban + ?
                             WHERE id_sach = ? AND so_luong_ton >= ?";
                
                $stmtStock = $this->conn->prepare($sqlStock);
                $stmtStock->bind_param("iiii", $quantity, $quantity, $bookId, $quantity);
                
                if (!$stmtStock->execute()) {
                    throw new Exception("Failed to update stock for book " . $bookId);
                }
            }
            
            // Commit transaction
            $this->conn->commit();
            return $orderId;
            
        } catch (Exception $e) {
            // Rollback on error
            $this->conn->rollback();
            error_log("Order creation failed: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Calculate order total from items
     * 
     * @param array $items Order items
     * @return float Total amount
     */
    public function calculateTotal($items) {
        $total = 0;
        foreach ($items as $item) {
            $quantity = $item['quantity'] ?? $item['so_luong'];
            $price = $item['price'] ?? $item['gia'];
            $total += $quantity * $price;
        }
        return $total;
    }
    
    /**
     * Get order by ID
     * 
     * @param int $orderId Order ID
     * @return array|null Order data or null
     */
    public function getOrderById($orderId) {
        $sql = "SELECT o.*, 
                       o.id_hoadon as id_order,
                       o.ma_hoadon as order_number,
                       o.ngay_dat_hang as order_date,
                       o.trang_thai as status,
                       o.phuong_thuc_thanh_toan as payment_method,
                       o.trang_thai_thanh_toan as payment_status,
                       o.tong_tien as total_amount,
                       o.ten_nguoi_nhan as recipient_name,
                       o.dia_chi_giao as delivery_address,
                       o.sdt_giao as phone,
                       o.dia_chi_giao as dia_chi_giao_hang,
                       o.sdt_giao as sdt_nguoi_nhan,
                       o.trang_thai as trang_thai_don_hang,
                       o.tong_tien as tong_thanh_toan,
                       o.id_khachhang as ma_khach_hang,
                       c.ten_khachhang,
                       c.ten_khachhang as full_name, 
                       c.ten_khachhang as ho_ten,
                       c.dien_thoai as so_dien_thoai,
                       c.email
                FROM hoadon o
                INNER JOIN khachhang c ON o.id_khachhang = c.id_khachhang
                WHERE o.id_hoadon = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $orderId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_assoc();
    }
    
    /**
     * Get order by Order Number (ma_hoadon)
     */
    public function getOrderByNumber($orderNumber) {
        $sql = "SELECT * FROM hoadon WHERE ma_hoadon = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $orderNumber);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    
    /**
     * Get order items
     * 
     * @param int $orderId Order ID
     * @return array Order items
     */
    public function getOrderItems($orderId) {
        $sql = "SELECT oi.*, 
                       oi.id_chitiet as id,
                       oi.so_luong as quantity, 
                       oi.gia as price,
                       oi.gia as don_gia,
                       b.ten_sach, 
                       b.ten_sach as title,
                       b.isbn,
                       b.isbn as isbn,
                       b.hinh_anh, 
                       b.hinh_anh as cover_image, 
                       a.ten_tacgia,
                       a.ten_tacgia as author_name
                FROM chitiet_hoadon oi
                INNER JOIN sach b ON oi.id_sach = b.id_sach
                LEFT JOIN tacgia a ON b.id_tacgia = a.id_tacgia
                WHERE oi.id_hoadon = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $orderId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $items = [];
        while ($row = $result->fetch_assoc()) {
            $items[] = $row;
        }
        
        return $items;
    }
    
    /**
     * Get customer's orders
     * 
     * @param int $customerId Customer ID
     * @param int $page Current page
     * @param int $limit Items per page
     * @return array Orders
     */
    public function getOrdersByCustomer($customerId, $page = 1, $limit = 10) {
        $offset = ($page - 1) * $limit;
        
        $sql = "SELECT id_hoadon as id_order, ma_hoadon as order_number, 
                       ngay_dat_hang as order_date, tong_tien as total_amount,
                       trang_thai as status, trang_thai_thanh_toan as payment_status,
                       tong_tien, ngay_dat_hang, ma_hoadon, id_hoadon, trang_thai, trang_thai_thanh_toan,
                       ngay_dat_hang as ngay_dat,
                       tong_tien as tong_thanh_toan,
                       trang_thai as trang_thai_don_hang
                FROM hoadon 
                WHERE id_khachhang = ? 
                ORDER BY ngay_dat_hang DESC
                LIMIT ? OFFSET ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iii", $customerId, $limit, $offset);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $orders = [];
        while ($row = $result->fetch_assoc()) {
            $orders[] = $row;
        }
        
        return $orders;
    }
    
    /**
     * Get all orders (Admin function)
     * 
     * @param int $page Current page
     * @param int $limit Items per page
     * @param string $status Filter by status (optional)
     * @return array Orders
     */
    public function getAllOrders($page = 1, $limit = 20, $status = null) {
        $offset = ($page - 1) * $limit;
        
        if ($status) {
            $sql = "SELECT o.*, c.ten_khachhang as full_name, c.email
                    FROM hoadon o
                    INNER JOIN khachhang c ON o.id_khachhang = c.id_khachhang
                    WHERE o.trang_thai = ?
                    ORDER BY o.ngay_dat_hang DESC
                    LIMIT ? OFFSET ?";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("sii", $status, $limit, $offset);
        } else {
            $sql = "SELECT o.*, c.ten_khachhang as full_name, c.email
                    FROM hoadon o
                    INNER JOIN khachhang c ON o.id_khachhang = c.id_khachhang
                    ORDER BY o.ngay_dat_hang DESC
                    LIMIT ? OFFSET ?";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("ii", $limit, $offset);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        $orders = [];
        while ($row = $result->fetch_assoc()) {
             // Alias for controller compat
             $row['id_order'] = $row['id_hoadon'];
             $row['order_number'] = $row['ma_hoadon'];
             $row['order_date'] = $row['ngay_dat_hang'];
             $row['total_amount'] = $row['tong_tien'];
             $row['status'] = $row['trang_thai'];
             $row['payment_status'] = $row['trang_thai_thanh_toan'];
             $row['recipient_name'] = $row['ten_nguoi_nhan'];
            $orders[] = $row;
        }
        
        return $orders;
    }
    
    /**
     * Get total order count
     * 
     * @param string $status Filter by status (optional)
     * @return int Total count
     */
    public function getTotalOrders($status = null) {
        if ($status) {
            $sql = "SELECT COUNT(*) as total FROM hoadon WHERE trang_thai = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("s", $status);
            $stmt->execute();
            $result = $stmt->get_result();
        } else {
            $sql = "SELECT COUNT(*) as total FROM hoadon";
            $result = $this->conn->query($sql);
        }
        
        $row = $result->fetch_assoc();
        return $row['total'];
    }
    
    /**
     * Update order status (Admin function)
     * 
     * @param int $orderId Order ID
     * @param string $status New status (pending/confirmed/shipping/completed/cancelled)
     * @return bool Success status
     */
    public function updateOrderStatus($orderId, $status) {
        $sql = "UPDATE hoadon 
                SET trang_thai = ?, ngay_cap_nhat = CURRENT_TIMESTAMP 
                WHERE id_hoadon = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("si", $status, $orderId);
        return $stmt->execute();
    }
    
    /**
     * Update payment status
     * 
     * @param int $orderId Order ID
     * @param string $paymentStatus New payment status (unpaid/paid)
     * @return bool Success status
     */
    public function updatePaymentStatus($orderId, $paymentStatus) {
        $sql = "UPDATE hoadon 
                SET trang_thai_thanh_toan = ?, ngay_cap_nhat = CURRENT_TIMESTAMP 
                WHERE id_hoadon = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("si", $paymentStatus, $orderId);
        return $stmt->execute();
    }
    
    /**
     * Get order statistics (Admin function)
     * 
     * @return array Statistics data
     */
    public function getOrderStats() {
        $sql = "SELECT 
                    COUNT(*) as total_orders,
                    SUM(CASE WHEN trang_thai = 'pending' THEN 1 ELSE 0 END) as pending,
                    SUM(CASE WHEN trang_thai = 'confirmed' THEN 1 ELSE 0 END) as processing,
                    SUM(CASE WHEN trang_thai = 'completed' THEN 1 ELSE 0 END) as completed,
                    SUM(CASE WHEN trang_thai = 'cancelled' THEN 1 ELSE 0 END) as cancelled,
                    SUM(CASE WHEN trang_thai = 'completed' THEN tong_tien ELSE 0 END) as total_revenue,
                    SUM(CASE WHEN DATE(ngay_dat_hang) = CURDATE() THEN 1 ELSE 0 END) as today_orders,
                    SUM(CASE WHEN DATE(ngay_dat_hang) = CURDATE() AND trang_thai = 'completed' THEN tong_tien ELSE 0 END) as today_revenue
                FROM hoadon";
        
        $result = $this->conn->query($sql);
        return $result->fetch_assoc();
    }
    
    /**
     * Search orders by order number or customer name (Admin function)
     * 
     * @param string $keyword Search keyword
     * @return array Matching orders
     */
    public function searchOrders($keyword) {
        $searchTerm = "%{$keyword}%";
        
        $sql = "SELECT o.*, c.ten_khachhang as full_name, c.email
                FROM hoadon o
                INNER JOIN khachhang c ON o.id_khachhang = c.id_khachhang
                WHERE o.ma_hoadon LIKE ? OR c.ten_khachhang LIKE ?
                ORDER BY o.ngay_dat_hang DESC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ss", $searchTerm, $searchTerm);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $orders = [];
        while ($row = $result->fetch_assoc()) {
             // Alias for controller compat
             $row['id_order'] = $row['id_hoadon'];
             $row['order_number'] = $row['ma_hoadon'];
             $row['order_date'] = $row['ngay_dat_hang'];
             $row['total_amount'] = $row['tong_tien'];
             $row['status'] = $row['trang_thai'];
             $row['payment_status'] = $row['trang_thai_thanh_toan'];
             $row['recipient_name'] = $row['ten_nguoi_nhan'];
             $orders[] = $row;
        }
        
        return $orders;
    }
    
    /**
     * Cancel order (Customer or Admin function)
     * 
     * @param int $orderId Order ID
     * @return bool Success status
     */
    public function cancelOrder($orderId) {
        // Start transaction
        $this->conn->begin_transaction();
        
        try {
            // Get order items to restore stock
            $items = $this->getOrderItems($orderId);
            
            // Restore stock for each item
            foreach ($items as $item) {
                // Determine quantity column name from getOrderItems result
                $qty = $item['quantity'] ?? $item['so_luong'];
                $bookId = $item['id_sach'];
                
                $sqlStock = "UPDATE sach 
                             SET so_luong_ton = so_luong_ton + ?,
                                 luot_ban = luot_ban - ?
                             WHERE id_sach = ?";
                
                $stmtStock = $this->conn->prepare($sqlStock);
                $stmtStock->bind_param("iii", $qty, $qty, $bookId);
                
                if (!$stmtStock->execute()) {
                    throw new Exception("Failed to restore stock");
                }
            }
            
            // Update order status
            $sql = "UPDATE hoadon SET trang_thai = 'cancelled' WHERE id_hoadon = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("i", $orderId);
            
            if (!$stmt->execute()) {
                throw new Exception("Failed to cancel order");
            }
            
            // Commit transaction
            $this->conn->commit();
            return true;
            
        } catch (Exception $e) {
            // Rollback on error
            $this->conn->rollback();
            error_log("Order cancellation failed: " . $e->getMessage());
            return false;
        }
    }
}
?>
