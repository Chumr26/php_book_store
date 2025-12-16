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
        $sql = "SELECT id_theloai as id_category,
                       ten_theloai as category_name,
                       mo_ta as description,
                       thu_tu as sort_order,
                       trang_thai as status
                FROM theloai 
                WHERE trang_thai = 'active' 
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
        $sql = "SELECT id_theloai as id_category,
                       ten_theloai as category_name,
                       mo_ta as description,
                       thu_tu as sort_order,
                       trang_thai as status
                FROM theloai WHERE id_theloai = ?";
        
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
        $sql = "INSERT INTO categories (category_name, description, sort_order, status) 
                VALUES (?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param(
            "ssii",
            $data['category_name'],
            $data['description'],
            $data['sort_order'],
            $data['status']
        );
        
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
        $sql = "UPDATE categories 
                SET category_name = ?, description = ?, sort_order = ?, status = ? 
                WHERE id_category = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param(
            "ssiii",
            $data['category_name'],
            $data['description'],
            $data['sort_order'],
            $data['status'],
            $id
        );
        
        return $stmt->execute();
    }
    
    /**
     * Delete category (Admin function)
     * 
     * @param int $id Category ID
     * @return bool Success status
     */
    public function deleteCategory($id) {
        $sql = "UPDATE categories SET status = 0 WHERE id_category = ?";
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
        $sql = "SELECT c.*, COUNT(b.id_book) as book_count
                FROM categories c
                LEFT JOIN books b ON c.id_category = b.id_category AND b.status = 1
                WHERE c.status = 1
                GROUP BY c.id_category
                ORDER BY c.sort_order ASC, c.category_name ASC";
        
        $result = $this->conn->query($sql);
        
        $categories = [];
        while ($row = $result->fetch_assoc()) {
            $categories[] = $row;
        }
        
        return $categories;
    }
}
?>
