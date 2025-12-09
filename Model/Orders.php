<?php
/**
 * Orders Model Class
 * 
 * Handles all order-related operations
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
        $date = date('Ymd');
        $random = str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
        return "ORD-{$date}-{$random}";
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
            // Generate order number
            $orderNumber = $this->generateOrderNumber();
            
            // Calculate total
            $totalAmount = $this->calculateTotal($items);
            
            // Insert order
            $sql = "INSERT INTO orders (
                        id_customer, order_number, total_amount, status, 
                        payment_method, payment_status, recipient_name, 
                        delivery_address, phone
                    ) VALUES (?, ?, ?, 'pending', ?, 'pending', ?, ?, ?)";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param(
                "isdsssss",
                $customerId,
                $orderNumber,
                $totalAmount,
                $orderData['payment_method'],
                $orderData['recipient_name'],
                $orderData['delivery_address'],
                $orderData['phone']
            );
            
            if (!$stmt->execute()) {
                throw new Exception("Failed to create order");
            }
            
            $orderId = $this->conn->insert_id;
            
            // Insert order items
            foreach ($items as $item) {
                $itemTotal = $item['quantity'] * $item['price'];
                
                $sqlItem = "INSERT INTO order_items (
                                id_order, id_book, quantity, unit_price, total_price
                            ) VALUES (?, ?, ?, ?, ?)";
                
                $stmtItem = $this->conn->prepare($sqlItem);
                $stmtItem->bind_param(
                    "iiidd",
                    $orderId,
                    $item['id_book'],
                    $item['quantity'],
                    $item['price'],
                    $itemTotal
                );
                
                if (!$stmtItem->execute()) {
                    throw new Exception("Failed to add order item");
                }
                
                // Update book stock
                $sqlStock = "UPDATE books 
                             SET stock_quantity = stock_quantity - ?,
                                 sale_count = sale_count + ?
                             WHERE id_book = ?";
                
                $stmtStock = $this->conn->prepare($sqlStock);
                $stmtStock->bind_param("iii", $item['quantity'], $item['quantity'], $item['id_book']);
                
                if (!$stmtStock->execute()) {
                    throw new Exception("Failed to update stock");
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
            $total += $item['quantity'] * $item['price'];
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
        $sql = "SELECT o.*, c.full_name, c.email
                FROM orders o
                INNER JOIN customers c ON o.id_customer = c.id_customer
                WHERE o.id_order = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $orderId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_assoc();
    }
    
    /**
     * Get order items
     * 
     * @param int $orderId Order ID
     * @return array Order items
     */
    public function getOrderItems($orderId) {
        $sql = "SELECT oi.*, b.title, b.cover_image, a.author_name
                FROM order_items oi
                INNER JOIN books b ON oi.id_book = b.id_book
                LEFT JOIN authors a ON b.id_author = a.id_author
                WHERE oi.id_order = ?";
        
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
    public function getCustomerOrders($customerId, $page = 1, $limit = 10) {
        $offset = ($page - 1) * $limit;
        
        $sql = "SELECT * FROM orders 
                WHERE id_customer = ? 
                ORDER BY order_date DESC
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
            $sql = "SELECT o.*, c.full_name, c.email
                    FROM orders o
                    INNER JOIN customers c ON o.id_customer = c.id_customer
                    WHERE o.status = ?
                    ORDER BY o.order_date DESC
                    LIMIT ? OFFSET ?";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("sii", $status, $limit, $offset);
        } else {
            $sql = "SELECT o.*, c.full_name, c.email
                    FROM orders o
                    INNER JOIN customers c ON o.id_customer = c.id_customer
                    ORDER BY o.order_date DESC
                    LIMIT ? OFFSET ?";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("ii", $limit, $offset);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        $orders = [];
        while ($row = $result->fetch_assoc()) {
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
            $sql = "SELECT COUNT(*) as total FROM orders WHERE status = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("s", $status);
            $stmt->execute();
            $result = $stmt->get_result();
        } else {
            $sql = "SELECT COUNT(*) as total FROM orders";
            $result = $this->conn->query($sql);
        }
        
        $row = $result->fetch_assoc();
        return $row['total'];
    }
    
    /**
     * Update order status (Admin function)
     * 
     * @param int $orderId Order ID
     * @param string $status New status (pending/processing/completed/cancelled)
     * @return bool Success status
     */
    public function updateOrderStatus($orderId, $status) {
        $sql = "UPDATE orders 
                SET status = ?, updated_at = CURRENT_TIMESTAMP 
                WHERE id_order = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("si", $status, $orderId);
        return $stmt->execute();
    }
    
    /**
     * Update payment status
     * 
     * @param int $orderId Order ID
     * @param string $paymentStatus New payment status (pending/paid/failed)
     * @return bool Success status
     */
    public function updatePaymentStatus($orderId, $paymentStatus) {
        $sql = "UPDATE orders 
                SET payment_status = ?, updated_at = CURRENT_TIMESTAMP 
                WHERE id_order = ?";
        
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
                    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                    SUM(CASE WHEN status = 'processing' THEN 1 ELSE 0 END) as processing,
                    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed,
                    SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled,
                    SUM(CASE WHEN status = 'completed' THEN total_amount ELSE 0 END) as total_revenue,
                    SUM(CASE WHEN DATE(order_date) = CURDATE() THEN 1 ELSE 0 END) as today_orders,
                    SUM(CASE WHEN DATE(order_date) = CURDATE() AND status = 'completed' THEN total_amount ELSE 0 END) as today_revenue
                FROM orders";
        
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
        
        $sql = "SELECT o.*, c.full_name, c.email
                FROM orders o
                INNER JOIN customers c ON o.id_customer = c.id_customer
                WHERE o.order_number LIKE ? OR c.full_name LIKE ?
                ORDER BY o.order_date DESC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ss", $searchTerm, $searchTerm);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $orders = [];
        while ($row = $result->fetch_assoc()) {
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
                $sqlStock = "UPDATE books 
                             SET stock_quantity = stock_quantity + ?,
                                 sale_count = sale_count - ?
                             WHERE id_book = ?";
                
                $stmtStock = $this->conn->prepare($sqlStock);
                $stmtStock->bind_param("iii", $item['quantity'], $item['quantity'], $item['id_book']);
                
                if (!$stmtStock->execute()) {
                    throw new Exception("Failed to restore stock");
                }
            }
            
            // Update order status
            $sql = "UPDATE orders SET status = 'cancelled' WHERE id_order = ?";
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
