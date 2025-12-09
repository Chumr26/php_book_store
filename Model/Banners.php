<?php
/**
 * Banners Model Class
 * 
 * Handles all banner/promotional slider operations
 */

class Banners {
    private $conn;
    
    /**
     * Constructor - Initialize database connection
     */
    public function __construct($connection) {
        $this->conn = $connection;
    }
    
    /**
     * Get all active banners
     * 
     * @return array Banners
     */
    public function getActiveBanners() {
        $sql = "SELECT * FROM banners 
                WHERE status = 1 
                ORDER BY sort_order ASC, id_banner DESC";
        
        $result = $this->conn->query($sql);
        
        $banners = [];
        while ($row = $result->fetch_assoc()) {
            $banners[] = $row;
        }
        
        return $banners;
    }
    
    /**
     * Get all banners (Admin function)
     * 
     * @return array All banners
     */
    public function getAllBanners() {
        $sql = "SELECT * FROM banners ORDER BY sort_order ASC, id_banner DESC";
        $result = $this->conn->query($sql);
        
        $banners = [];
        while ($row = $result->fetch_assoc()) {
            $banners[] = $row;
        }
        
        return $banners;
    }
    
    /**
     * Get banner by ID
     * 
     * @param int $id Banner ID
     * @return array|null Banner data or null
     */
    public function getBannerById($id) {
        $sql = "SELECT * FROM banners WHERE id_banner = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_assoc();
    }
    
    /**
     * Add new banner (Admin function)
     * 
     * @param array $data Banner data
     * @return int|false New banner ID or false
     */
    public function addBanner($data) {
        $sql = "INSERT INTO banners (title, image, link_url, sort_order, status) 
                VALUES (?, ?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param(
            "sssii",
            $data['title'],
            $data['image'],
            $data['link_url'],
            $data['sort_order'],
            $data['status']
        );
        
        if ($stmt->execute()) {
            return $this->conn->insert_id;
        }
        
        return false;
    }
    
    /**
     * Update banner (Admin function)
     * 
     * @param int $id Banner ID
     * @param array $data Updated data
     * @return bool Success status
     */
    public function updateBanner($id, $data) {
        $sql = "UPDATE banners 
                SET title = ?, image = ?, link_url = ?, sort_order = ?, status = ? 
                WHERE id_banner = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param(
            "sssiii",
            $data['title'],
            $data['image'],
            $data['link_url'],
            $data['sort_order'],
            $data['status'],
            $id
        );
        
        return $stmt->execute();
    }
    
    /**
     * Delete banner (Admin function)
     * 
     * @param int $id Banner ID
     * @return bool Success status
     */
    public function deleteBanner($id) {
        $sql = "DELETE FROM banners WHERE id_banner = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
    
    /**
     * Toggle banner status (Admin function)
     * 
     * @param int $id Banner ID
     * @param int $status New status (0 or 1)
     * @return bool Success status
     */
    public function toggleStatus($id, $status) {
        $sql = "UPDATE banners SET status = ? WHERE id_banner = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $status, $id);
        return $stmt->execute();
    }
}
?>
