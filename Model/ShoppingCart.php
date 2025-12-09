<?php
/**
 * ShoppingCart Model Class
 * 
 * Handles all shopping cart operations
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
    public function addToCart($customerId, $bookId, $quantity = 1) {
        // Check if item already exists in cart
        $existing = $this->getCartItem($customerId, $bookId);
        
        if ($existing) {
            // Update quantity if item exists
            $newQuantity = $existing['quantity'] + $quantity;
            return $this->updateCartItem($existing['id_cart_item'], $newQuantity);
        } else {
            // Insert new item
            $sql = "INSERT INTO shopping_cart (id_customer, id_book, quantity) 
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
    private function getCartItem($customerId, $bookId) {
        $sql = "SELECT * FROM shopping_cart 
                WHERE id_customer = ? AND id_book = ?";
        
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
        $sql = "SELECT sc.*, 
                       b.title, 
                       b.price, 
                       b.cover_image, 
                       b.stock_quantity,
                       a.author_name,
                       (sc.quantity * b.price) as item_total
                FROM shopping_cart sc
                INNER JOIN books b ON sc.id_book = b.id_book
                LEFT JOIN authors a ON b.id_author = a.id_author
                WHERE sc.id_customer = ? AND b.status = 1
                ORDER BY sc.added_at DESC";
        
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
    public function getCartCount($customerId) {
        $sql = "SELECT SUM(quantity) as total 
                FROM shopping_cart 
                WHERE id_customer = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $customerId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        return $row['total'] ?? 0;
    }
    
    /**
     * Update cart item quantity
     * 
     * @param int $cartItemId Cart item ID
     * @param int $quantity New quantity
     * @return bool Success status
     */
    public function updateCartItem($cartItemId, $quantity) {
        if ($quantity <= 0) {
            // If quantity is 0 or negative, remove the item
            return $this->removeFromCart($cartItemId);
        }
        
        $sql = "UPDATE shopping_cart 
                SET quantity = ? 
                WHERE id_cart_item = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $quantity, $cartItemId);
        return $stmt->execute();
    }
    
    /**
     * Remove item from cart
     * 
     * @param int $cartItemId Cart item ID
     * @return bool Success status
     */
    public function removeFromCart($cartItemId) {
        $sql = "DELETE FROM shopping_cart WHERE id_cart_item = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $cartItemId);
        return $stmt->execute();
    }
    
    /**
     * Clear entire cart for customer
     * 
     * @param int $customerId Customer ID
     * @return bool Success status
     */
    public function clearCart($customerId) {
        $sql = "DELETE FROM shopping_cart WHERE id_customer = ?";
        
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
        $sql = "SELECT SUM(sc.quantity * b.price) as total
                FROM shopping_cart sc
                INNER JOIN books b ON sc.id_book = b.id_book
                WHERE sc.id_customer = ? AND b.status = 1";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $customerId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        return $row['total'] ?? 0;
    }
    
    /**
     * Get cart summary (count and total)
     * 
     * @param int $customerId Customer ID
     * @return array Summary data
     */
    public function getCartSummary($customerId) {
        return [
            'item_count' => $this->getCartCount($customerId),
            'total_amount' => $this->getCartTotal($customerId)
        ];
    }
    
    /**
     * Validate cart items (check stock availability)
     * 
     * @param int $customerId Customer ID
     * @return array Validation results with errors if any
     */
    public function validateCart($customerId) {
        $items = $this->getCartItems($customerId);
        $errors = [];
        $valid = true;
        
        foreach ($items as $item) {
            if ($item['stock_quantity'] < $item['quantity']) {
                $errors[] = [
                    'id_cart_item' => $item['id_cart_item'],
                    'title' => $item['title'],
                    'requested' => $item['quantity'],
                    'available' => $item['stock_quantity'],
                    'message' => "Không đủ hàng cho '{$item['title']}'. Chỉ còn {$item['stock_quantity']} cuốn."
                ];
                $valid = false;
            }
        }
        
        return [
            'valid' => $valid,
            'errors' => $errors,
            'items' => $items
        ];
    }
    
    /**
     * Merge guest cart with customer cart (after login)
     * 
     * @param int $customerId Customer ID
     * @param array $guestCart Guest cart items from session
     * @return bool Success status
     */
    public function mergeGuestCart($customerId, $guestCart) {
        if (empty($guestCart)) {
            return true;
        }
        
        foreach ($guestCart as $item) {
            $this->addToCart($customerId, $item['id_book'], $item['quantity']);
        }
        
        return true;
    }
}
?>
