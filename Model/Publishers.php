<?php
/**
 * Publishers Model Class
 * 
 * Handles all publisher-related operations
 */

class Publishers {
    private $conn;
    
    /**
     * Constructor - Initialize database connection
     */
    public function __construct($connection) {
        $this->conn = $connection;
    }
    
    /**
     * Get all publishers
     * 
     * @return array Publishers
     */
    public function getAllPublishers() {
        $sql = "SELECT id_nxb as ma_nxb,
                       ten_nxb,
                       dia_chi,
                       dien_thoai,
                       email,
                       website
                FROM nhaxuatban 
                ORDER BY ten_nxb ASC";
        $result = $this->conn->query($sql);
        
        $publishers = [];
        while ($row = $result->fetch_assoc()) {
            $publishers[] = $row;
        }
        
        return $publishers;
    }
    
    /**
     * Get publisher by ID
     * 
     * @param int $id Publisher ID
     * @return array|null Publisher data or null
     */
    public function getPublisherById($id) {
        $sql = "SELECT id_nxb as ma_nxb,
                       ten_nxb,
                       dia_chi,
                       dien_thoai,
                       email,
                       website
                FROM nhaxuatban 
                WHERE id_nxb = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $row = $result->fetch_assoc();
        
        // Add alias for backward compatibility if needed in admin
        if ($row) {
            $row['id_publisher'] = $row['ma_nxb'];
            $row['publisher_name'] = $row['ten_nxb'];
        }
        
        return $row;
    }
    
    /**
     * Add new publisher (Admin function)
     * 
     * @param array $data Publisher data
     * @return int|false New publisher ID or false
     */
    public function addPublisher($data) {
        $sql = "INSERT INTO nhaxuatban (ten_nxb, dia_chi, dien_thoai, email, website) 
                VALUES (?, ?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($sql);
        
        // Handle input keys flexible (either new Vietnamese or old English keys)
        $name = $data['ten_nxb'] ?? $data['publisher_name'];
        $addr = $data['dia_chi'] ?? $data['address'];
        $phone = $data['dien_thoai'] ?? $data['phone'];
        $email = $data['email'];
        $web = $data['website'];
        
        $stmt->bind_param("sssss", $name, $addr, $phone, $email, $web);
        
        if ($stmt->execute()) {
            return $this->conn->insert_id;
        }
        
        return false;
    }
    
    /**
     * Update publisher (Admin function)
     * 
     * @param int $id Publisher ID
     * @param array $data Updated data
     * @return bool Success status
     */
    public function updatePublisher($id, $data) {
        $sql = "UPDATE nhaxuatban 
                SET ten_nxb = ?, dia_chi = ?, dien_thoai = ?, email = ?, website = ? 
                WHERE id_nxb = ?";
        
        $stmt = $this->conn->prepare($sql);
        
        $name = $data['ten_nxb'] ?? $data['publisher_name'];
        $addr = $data['dia_chi'] ?? $data['address'];
        $phone = $data['dien_thoai'] ?? $data['phone'];
        $email = $data['email'];
        $web = $data['website'];
        
        $stmt->bind_param("sssssi", $name, $addr, $phone, $email, $web, $id);
        
        return $stmt->execute();
    }
    
    /**
     * Delete publisher (Admin function)
     * 
     * @param int $id Publisher ID
     * @return bool Success status
     */
    public function deletePublisher($id) {
        $sql = "DELETE FROM nhaxuatban WHERE id_nxb = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
    
    /**
     * Search publishers by name
     * 
     * @param string $keyword Search keyword
     * @return array Matching publishers
     */
    public function searchPublishers($keyword) {
        $searchTerm = "%{$keyword}%";
        
        $sql = "SELECT id_nxb as ma_nxb, ten_nxb, dia_chi, dien_thoai, email, website 
                FROM nhaxuatban 
                WHERE ten_nxb LIKE ?
                ORDER BY ten_nxb ASC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $searchTerm);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $publishers = [];
        while ($row = $result->fetch_assoc()) {
            $publishers[] = $row;
        }
        
        return $publishers;
    }
}
?>
