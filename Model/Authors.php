<?php
/**
 * Authors Model Class
 * 
 * Handles all author-related operations using table 'tacgia'
 */

class Authors {
    private $conn;
    
    /**
     * Constructor - Initialize database connection
     */
    public function __construct($connection) {
        $this->conn = $connection;
    }
    
    /**
     * Get all authors
     * 
     * @return array Authors
     */
    public function getAllAuthors() {
        $sql = "SELECT id_tacgia as ma_tac_gia, ten_tacgia as ten_tac_gia, but_danh, tieu_su, ngay_sinh, quoc_tich, hinh_anh 
                FROM tacgia 
                ORDER BY ten_tacgia ASC";
        $result = $this->conn->query($sql);
        
        $authors = [];
        while ($row = $result->fetch_assoc()) {
            $authors[] = $row;
        }
        
        return $authors;
    }
    
    /**
     * Get author by ID
     * 
     * @param int $id Author ID
     * @return array|null Author data or null
     */
    public function getAuthorById($id) {
        $sql = "SELECT id_tacgia as ma_tac_gia, ten_tacgia as ten_tac_gia, but_danh, tieu_su, ngay_sinh, quoc_tich, hinh_anh 
                FROM tacgia 
                WHERE id_tacgia = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $row = $result->fetch_assoc();
        
        // Alias for compatibility
        if ($row) {
            $row['id_author'] = $row['ma_tac_gia'];
            $row['author_name'] = $row['ten_tac_gia'];
        }
        
        return $row;
    }
    
    /**
     * Add new author (Admin function)
     * 
     * @param array $data Author data
     * @return int|false New author ID or false
     */
    public function addAuthor($data) {
        $sql = "INSERT INTO tacgia (ten_tacgia, but_danh, tieu_su, ngay_sinh, quoc_tich) 
                VALUES (?, ?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($sql);
        
        // Handle input keys flexible
        $name = $data['ten_tacgia'] ?? $data['author_name'];
        $pen = $data['but_danh'] ?? $data['pen_name'];
        $bio = $data['tieu_su'] ?? $data['biography'];
        $dob = $data['ngay_sinh'] ?? $data['date_of_birth'];
        $nat = $data['quoc_tich'] ?? $data['nationality'];
        
        $stmt->bind_param("sssss", $name, $pen, $bio, $dob, $nat);
        
        if ($stmt->execute()) {
            return $this->conn->insert_id;
        }
        
        return false;
    }
    
    /**
     * Update author (Admin function)
     * 
     * @param int $id Author ID
     * @param array $data Updated data
     * @return bool Success status
     */
    public function updateAuthor($id, $data) {
        $sql = "UPDATE tacgia 
                SET ten_tacgia = ?, but_danh = ?, tieu_su = ?, 
                    ngay_sinh = ?, quoc_tich = ? 
                WHERE id_tacgia = ?";
        
        $stmt = $this->conn->prepare($sql);
        
        $name = $data['ten_tacgia'] ?? $data['author_name'];
        $pen = $data['but_danh'] ?? $data['pen_name'];
        $bio = $data['tieu_su'] ?? $data['biography'];
        $dob = $data['ngay_sinh'] ?? $data['date_of_birth'];
        $nat = $data['quoc_tich'] ?? $data['nationality'];
        
        $stmt->bind_param("sssssi", $name, $pen, $bio, $dob, $nat, $id);
        
        return $stmt->execute();
    }
    
    /**
     * Delete author (Admin function)
     * 
     * @param int $id Author ID
     * @return bool Success status
     */
    public function deleteAuthor($id) {
        $sql = "DELETE FROM tacgia WHERE id_tacgia = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
    
    /**
     * Search authors by name
     * 
     * @param string $keyword Search keyword
     * @return array Matching authors
     */
    public function searchAuthors($keyword) {
        $searchTerm = "%{$keyword}%";
        
        $sql = "SELECT * FROM tacgia 
                WHERE ten_tacgia LIKE ? OR but_danh LIKE ?
                ORDER BY ten_tacgia ASC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ss", $searchTerm, $searchTerm);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $authors = [];
        while ($row = $result->fetch_assoc()) {
            $authors[] = $row;
        }
        
        return $authors;
    }
}
?>
