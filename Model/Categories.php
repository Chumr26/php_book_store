<?php
/**
 * Categories Model Class
 * 
 * Handles all category-related operations
 */

class Categories {
    private $conn;
    
    /**
     * Constructor - Initialize database connection
     */
    public function __construct($connection) {
        $this->conn = $connection;
    }
    
    /**
     * Get all active categories
     * 
     * @return array Categories
     */
    public function getAllCategories() {
        $sql = "SELECT 
                    id_theloai   AS ma_danh_muc,
                    ten_theloai  AS ten_danh_muc,
                    mo_ta        AS mo_ta,
                    thu_tu       AS thu_tu,
                    trang_thai   AS trang_thai
                FROM theloai
                WHERE (trang_thai = 'active' OR trang_thai = 1)
                ORDER BY thu_tu ASC, ten_theloai ASC";
        
        $result = $this->conn->query($sql);
        
        $categories = [];
        while ($row = $result->fetch_assoc()) {
            $categories[] = $row;
        }
        
        return $categories;
    }
    
    /**
     * Get category by ID
     * 
     * @param int $id Category ID
     * @return array|null Category data or null
     */
    public function getCategoryById($id) {
        $sql = "SELECT 
                    id_theloai   AS ma_danh_muc,
                    ten_theloai  AS ten_danh_muc,
                    mo_ta        AS mo_ta,
                    thu_tu       AS thu_tu,
                    trang_thai   AS trang_thai
                FROM theloai
                WHERE id_theloai = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_assoc();
    }
    
    /**
     * Add new category (Admin function)
     * 
     * @param array $data Category data
     * @return int|false New category ID or false
     */
    public function addCategory($data) {
        $sql = "INSERT INTO theloai (ten_theloai, mo_ta, thu_tu, trang_thai) 
                VALUES (?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($sql);
        
        // Handle input keys flexible
        $name = $data['ten_theloai'] ?? $data['category_name'];
        $desc = $data['mo_ta'] ?? $data['description'];
        $order = $data['thu_tu'] ?? $data['sort_order'] ?? 0;
        $status = $data['trang_thai'] ?? $data['status'] ?? 1;
        
        $stmt->bind_param("ssii", $name, $desc, $order, $status);
        
        if ($stmt->execute()) {
            return $this->conn->insert_id;
        }
        
        return false;
    }
    
    /**
     * Update category (Admin function)
     * 
     * @param int $id Category ID
     * @param array $data Updated data
     * @return bool Success status
     */
    public function updateCategory($id, $data) {
        $sql = "UPDATE theloai 
                SET ten_theloai = ?, mo_ta = ?, thu_tu = ?, trang_thai = ? 
                WHERE id_theloai = ?";
        
        $stmt = $this->conn->prepare($sql);
        
        $name = $data['ten_theloai'] ?? $data['category_name'];
        $desc = $data['mo_ta'] ?? $data['description'];
        $order = $data['thu_tu'] ?? $data['sort_order'] ?? 0;
        $status = $data['trang_thai'] ?? $data['status'] ?? 1;
        
        $stmt->bind_param("ssiii", $name, $desc, $order, $status, $id);
        
        return $stmt->execute();
    }
    
    /**
     * Delete category (Admin function)
     * 
     * @param int $id Category ID
     * @return bool Success status
     */
    public function deleteCategory($id) {
        // Soft delete
        $sql = "UPDATE theloai SET trang_thai = 'deleted' WHERE id_theloai = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
    
    /**
     * Get category with book count
     * 
     * @return array Categories with book counts
     */
    public function getCategoriesWithBookCount() {
        $sql = "SELECT c.id_theloai as ma_danh_muc, 
                       c.ten_theloai as ten_danh_muc, 
                       COUNT(s.id_sach) as book_count
                FROM theloai c
                LEFT JOIN sach s ON c.id_theloai = s.id_theloai AND s.trang_thai = 'available'
                WHERE c.trang_thai != 'deleted'
                GROUP BY c.id_theloai
                ORDER BY c.thu_tu ASC, c.ten_theloai ASC";
        
        $result = $this->conn->query($sql);
        
        $categories = [];
        while ($row = $result->fetch_assoc()) {
            $categories[] = $row;
        }
        
        return $categories;
    }
}
?>
