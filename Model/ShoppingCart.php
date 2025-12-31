<?php
/**
 * ShoppingCart Model Class
 * 
 * Handles all shopping cart operations and database interaction
 */

class ShoppingCart {
    private $conn;
    
    /**
     * Constructor - Initialize database connection
     */
    public function __construct($connection) {
        $this->conn = $connection;
    }
    
    /**
     * Add book to cart
     * 
     * @param int $customerId Customer ID
     * @param int $bookId Book ID
     * @param int $quantity Quantity to add
     * @return bool Success status
     */
    public function addItem($customerId, $bookId, $quantity = 1) {
        // Check if item already exists in cart
        $existing = $this->getItem($customerId, $bookId);
        
        if ($existing) {
            // Update quantity if item exists
            $newQuantity = $existing['so_luong'] + $quantity;
            return $this->updateQuantity($customerId, $bookId, $newQuantity);
        } else {
            // Insert new item
            $sql = "INSERT INTO giohang (id_khachhang, id_sach, so_luong) 
                    VALUES (?, ?, ?)";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("iii", $customerId, $bookId, $quantity);
            return $stmt->execute();
        }
    }
    
    /**
     * Get specific cart item
     * 
     * @param int $customerId Customer ID
     * @param int $bookId Book ID
     * @return array|null Cart item or null
     */
    public function getItem($customerId, $bookId) {
        $sql = "SELECT * FROM giohang 
                WHERE id_khachhang = ? AND id_sach = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $customerId, $bookId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_assoc();
    }
    
    /**
     * Get all cart items for a customer
     * 
     * @param int $customerId Customer ID
     * @return array Cart items with book details
     */
    public function getCartItems($customerId) {
        $sql = "SELECT gh.*, 
                       s.id_sach as ma_sach,
                       s.ten_sach, 
                       s.gia, 
                       s.hinh_anh as anh_bia, 
                       s.so_luong_ton,
                       tg.ten_tacgia as ten_tac_gia,
                       s.isbn,
                       (gh.so_luong * s.gia) as thanh_tien
                FROM giohang gh
                INNER JOIN sach s ON gh.id_sach = s.id_sach
                LEFT JOIN tacgia tg ON s.id_tacgia = tg.id_tacgia
                WHERE gh.id_khachhang = ? AND s.trang_thai = 'available'
                ORDER BY gh.ngay_them DESC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $customerId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $items = [];
        while ($row = $result->fetch_assoc()) {
            $items[] = $row;
        }
        
        return $items;
    }
    
    /**
     * Get cart item count
     * 
     * @param int $customerId Customer ID
     * @return int Total items in cart
     */
    public function getItemCount($customerId) {
        $sql = "SELECT COUNT(*) as total 
                FROM giohang 
                WHERE id_khachhang = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $customerId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        return $row['total'] ?? 0;
    }

    /**
     * Get list of book IDs in cart
     * 
     * @param int $customerId Customer ID
     * @return array List of book IDs
     */
    public function getCartBookIds($customerId) {
        $sql = "SELECT id_sach FROM giohang WHERE id_khachhang = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $customerId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $ids = [];
        while ($row = $result->fetch_assoc()) {
            $ids[] = $row['id_sach'];
        }
        
        return $ids;
    }
    
    /**
     * Update cart item quantity
     * 
     * @param int $customerId Customer ID
     * @param int $bookId Book ID
     * @param int $quantity New quantity
     * @return bool Success status
     */
    public function updateQuantity($customerId, $bookId, $quantity) {
        if ($quantity <= 0) {
            // If quantity is 0 or negative, remove the item
            return $this->removeItem($customerId, $bookId);
        }
        
        $sql = "UPDATE giohang 
                SET so_luong = ? 
                WHERE id_khachhang = ? AND id_sach = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iii", $quantity, $customerId, $bookId);
        return $stmt->execute();
    }
    
    /**
     * Remove item from cart
     * 
     * @param int $customerId Customer ID
     * @param int $bookId Book ID
     * @return bool Success status
     */
    public function removeItem($customerId, $bookId) {
        $sql = "DELETE FROM giohang WHERE id_khachhang = ? AND id_sach = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $customerId, $bookId);
        return $stmt->execute();
    }
    
    /**
     * Clear entire cart for customer
     * 
     * @param int $customerId Customer ID
     * @return bool Success status
     */
    public function clearCart($customerId) {
        $sql = "DELETE FROM giohang WHERE id_khachhang = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $customerId);
        return $stmt->execute();
    }
    
    /**
     * Calculate cart total
     * 
     * @param int $customerId Customer ID
     * @return float Total amount
     */
    public function getCartTotal($customerId) {
        $sql = "SELECT SUM(gh.so_luong * s.gia) as total
                FROM giohang gh
                INNER JOIN sach s ON gh.id_sach = s.id_sach
                WHERE gh.id_khachhang = ? AND s.trang_thai = 'available'";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $customerId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        return $row['total'] ?? 0;
    }
}
?>
