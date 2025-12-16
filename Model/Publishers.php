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
        $sql = "SELECT id_nxb as id_publisher,
                       ten_nxb as publisher_name,
                       dia_chi as address,
                       dien_thoai as phone,
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
        $sql = "SELECT id_nxb as id_publisher,
                       ten_nxb as publisher_name,
                       dia_chi as address,
                       dien_thoai as phone,
                       email,
                       website
                FROM nhaxuatban 
                WHERE id_nxb = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_assoc();
    }
    
    /**
     * Add new publisher (Admin function)
     * 
     * @param array $data Publisher data
     * @return int|false New publisher ID or false
     */
    public function addPublisher($data) {
        $sql = "INSERT INTO publishers (publisher_name, address, phone, email, website) 
                VALUES (?, ?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param(
            "sssss",
            $data['publisher_name'],
            $data['address'],
            $data['phone'],
            $data['email'],
            $data['website']
        );
        
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
        $sql = "UPDATE publishers 
                SET publisher_name = ?, address = ?, phone = ?, email = ?, website = ? 
                WHERE id_publisher = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param(
            "sssssi",
            $data['publisher_name'],
            $data['address'],
            $data['phone'],
            $data['email'],
            $data['website'],
            $id
        );
        
        return $stmt->execute();
    }
    
    /**
     * Delete publisher (Admin function)
     * 
     * @param int $id Publisher ID
     * @return bool Success status
     */
    public function deletePublisher($id) {
        $sql = "DELETE FROM publishers WHERE id_publisher = ?";
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
        
        $sql = "SELECT * FROM publishers 
                WHERE publisher_name LIKE ?
                ORDER BY publisher_name ASC";
        
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
