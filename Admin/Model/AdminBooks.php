<?php
/**
 * Admin Books Model
 * 
 * Extended functionality for admin book management
 * Uses the main Books model with additional admin features
 */

// Include the main Books model
require_once __DIR__ . '/../../Model/Books.php';

class AdminBooks extends Books {
    
    /**
     * Get all books including inactive ones (Admin only)
     * 
     * @param int $page Current page
     * @param int $limit Items per page
     * @param string $status Filter by status (all/active/inactive)
     * @return array Books data
     */
    public function getAllBooksAdmin($page = 1, $limit = 20, $status = 'all') {
        $offset = ($page - 1) * $limit;
        
        $sql = "SELECT b.*, 
                       a.author_name, 
                       p.publisher_name, 
                       c.category_name
                FROM books b
                LEFT JOIN authors a ON b.id_author = a.id_author
                LEFT JOIN publishers p ON b.id_publisher = p.id_publisher
                LEFT JOIN categories c ON b.id_category = c.id_category";
        
        if ($status !== 'all') {
            $statusValue = ($status === 'active') ? 1 : 0;
            $sql .= " WHERE b.status = ?";
            $sql .= " ORDER BY b.created_at DESC LIMIT ? OFFSET ?";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("iii", $statusValue, $limit, $offset);
        } else {
            $sql .= " ORDER BY b.created_at DESC LIMIT ? OFFSET ?";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("ii", $limit, $offset);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        $books = [];
        while ($row = $result->fetch_assoc()) {
            $books[] = $row;
        }
        
        return $books;
    }
    
    /**
     * Get total book count for admin (including inactive)
     * 
     * @param string $status Filter by status
     * @return int Total count
     */
    public function getTotalBooksAdmin($status = 'all') {
        if ($status === 'all') {
            $sql = "SELECT COUNT(*) as total FROM books";
            $result = $this->conn->query($sql);
        } else {
            $statusValue = ($status === 'active') ? 1 : 0;
            $sql = "SELECT COUNT(*) as total FROM books WHERE status = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("i", $statusValue);
            $stmt->execute();
            $result = $stmt->get_result();
        }
        
        $row = $result->fetch_assoc();
        return $row['total'];
    }
    
    /**
     * Get book statistics for dashboard
     * 
     * @return array Statistics
     */
    public function getBookStats() {
        $sql = "SELECT 
                    COUNT(*) as total_books,
                    SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) as active_books,
                    SUM(CASE WHEN status = 0 THEN 1 ELSE 0 END) as inactive_books,
                    SUM(CASE WHEN is_featured = 1 THEN 1 ELSE 0 END) as featured_books,
                    SUM(stock_quantity) as total_stock,
                    SUM(CASE WHEN stock_quantity < 10 THEN 1 ELSE 0 END) as low_stock_count
                FROM books";
        
        $result = $this->conn->query($sql);
        return $result->fetch_assoc();
    }
    
    /**
     * Get low stock books (Admin alert)
     * 
     * @param int $threshold Stock threshold (default: 10)
     * @return array Books with low stock
     */
    public function getLowStockBooks($threshold = 10) {
        $sql = "SELECT b.*, 
                       a.author_name, 
                       c.category_name
                FROM books b
                LEFT JOIN authors a ON b.id_author = a.id_author
                LEFT JOIN categories c ON b.id_category = c.id_category
                WHERE b.stock_quantity < ? AND b.status = 1
                ORDER BY b.stock_quantity ASC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $threshold);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $books = [];
        while ($row = $result->fetch_assoc()) {
            $books[] = $row;
        }
        
        return $books;
    }
    
    /**
     * Bulk update book status
     * 
     * @param array $bookIds Array of book IDs
     * @param int $status New status (0 or 1)
     * @return bool Success status
     */
    public function bulkUpdateStatus($bookIds, $status) {
        if (empty($bookIds)) {
            return false;
        }
        
        $placeholders = implode(',', array_fill(0, count($bookIds), '?'));
        $sql = "UPDATE books SET status = ? WHERE id_book IN ({$placeholders})";
        
        $stmt = $this->conn->prepare($sql);
        $types = 'i' . str_repeat('i', count($bookIds));
        $params = array_merge([$status], $bookIds);
        $stmt->bind_param($types, ...$params);
        
        return $stmt->execute();
    }
    
    /**
     * Get top selling books for reports
     * 
     * @param int $limit Number of books
     * @param string $period Time period (today/week/month/all)
     * @return array Top selling books
     */
    public function getTopSellingBooksReport($limit = 10, $period = 'all') {
        $sql = "SELECT b.*, 
                       a.author_name,
                       c.category_name,
                       COUNT(oi.id_item) as order_count,
                       SUM(oi.quantity) as total_sold,
                       SUM(oi.total_price) as total_revenue
                FROM books b
                LEFT JOIN authors a ON b.id_author = a.id_author
                LEFT JOIN categories c ON b.id_category = c.id_category
                INNER JOIN order_items oi ON b.id_book = oi.id_book
                INNER JOIN orders o ON oi.id_order = o.id_order
                WHERE o.status = 'completed'";
        
        // Add time period filter
        switch ($period) {
            case 'today':
                $sql .= " AND DATE(o.order_date) = CURDATE()";
                break;
            case 'week':
                $sql .= " AND YEARWEEK(o.order_date) = YEARWEEK(NOW())";
                break;
            case 'month':
                $sql .= " AND YEAR(o.order_date) = YEAR(NOW()) AND MONTH(o.order_date) = MONTH(NOW())";
                break;
        }
        
        $sql .= " GROUP BY b.id_book ORDER BY total_sold DESC LIMIT ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $books = [];
        while ($row = $result->fetch_assoc()) {
            $books[] = $row;
        }
        
        return $books;
    }
}
?>
